=== Sub Page Summary ===
Contributors: dhoppe
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1220480
Tags: page, pages, sub page, sub pages, summary, empty page, list sub pages 
Requires at least: 3.0
Tested up to: 3.2
Stable tag: trunk

Sub Page Summary generates a summary of the sub pages of a page incl. page title, preview image and an excerpt of the page.

== Description ==

Sub Page Summary generates a summary of the sub pages of a page incl. page title, preview image and an excerpt of the page. Useful to fill empty pages with the content of their sub pages.

By default empty pages will auto filled with the summary. If you want to write some content before or after the summary you can easily use the <code>[summarize]</code> ShortCode to include the summary somewhere in your page.

To change the excerpt of your pages you could use the [Page Excerpt Plugin](http://wordpress.org/extend/plugins/page-excerpt-plugin/).

= Requirements =
* **Sub Page Summary requires PHP5!**
* WordPress 3.0 or higher

= ShortCode =
In case you won't have the summary at the end of your page you can use the <code>[summarize]</code> ShortCode anywhere in your pages content. So the sub pages will be shown at the place you insert the ShortCode.
As attributes for the ShortCode you can use all parameters from the"Query_Posts" WP function. So you could use the "orderby" parameter to change the order of the pages. Possible values are:

* orderby=author
* orderby=date
* orderby=title
* orderby=modified
* orderby=menu_order
* orderby=ID
* orderby=rand
* orderby=comment_count

= Customization =
If you need a customized template to display the sub pages (e.g. as list or with author, date, time or meta data) feel free to send me an E-Mail. For a small fee I will write a customized template for you.

= How to write an own customization =
A template is a php file which renders the output of the sub pages (a WP Query). You can find an example template file in the plugin folder (*sub-page-summary.php*). Read more how this works in the "For Theme Designers" paragraph below.

= For Theme Designers =
If you want to integrate this plug-in in your theme you have to add a new file to your theme directory: *sub-page-summary.php*

The plugin will use your template to show the sub pages on pages by default. You can find a working example file of this template in the plug-in directory (*sub-page-summary.php*). Just copy it in your template directory and modify it until it fits in your theme.

= For developers =
If you want to use a customized template file outside the theme directory you can use the *sub_page_summary_template* filter. Just write a path to a file in the filter to bypass the template. Here is an example that shows how you can write a plugin which changes the template path to a file in the same directory.

<code>
Function bypass_template($template_file){
  /* the $template_file is the file which is currently set as template so
     you can also use the filter to read the current template file. 
  */
  return DirName(__FILE__) . '/my-template.php';
}
Add_Filter('sub_page_summary_template', 'bypass_template');
</code>

Analogical you can change the style sheet with the *sub_page_summary_style_sheet* filter. Here is an example:
<code>
Function bypass_style_sheet($css_file){
  /* the $css_file is the file (URL) which is currently set as style sheet so
     you can also use the filter to read the current css file. 
  */
  // Url to your CSS File
  return get_bloginfo('wpurl') . '/my-style.css';
}
Add_Filter('sub_page_summary_style_sheet', 'bypass_style_sheet');
</code>

= Questions =
If you have any questions feel free to leave a comment in my blog. But please think about this: I will not add features, write customizations or write tutorials for free. Please think about a donation. I'm a human and to write code is hard work.

= Language =
This Plugin uses core translation strings only. So if you use WordPress in your language this plugin will use the same translations. You do not need any language file.


== Installation ==

Installation as usual.

1. Unzip and Upload all files to a sub directory in "/wp-content/plugins/".
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to edit a page.
1. Insert the new <code>[summarize]</code> ShortCode.


== Changelog ==

= 1.0.4 =
* Added "exclude" Parameter.

= 1.0.3 =
* Added ShortCode parameters.

= 1.0.2 =
* Fixed the sub page limitation bug.

= 1.0.1 =
* Fixed some misspelled class names in the template file (php/css)

= 1.0 =
* Everything works fine.


