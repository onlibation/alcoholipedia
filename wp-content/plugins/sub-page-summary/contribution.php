<?php If (!Class_Exists('wp_plugin_contribution_to_dennis_hoppe')){
Class wp_plugin_contribution_to_dennis_hoppe {
  Var $is_dashboard = False;
  Var $base_url;
  Private $widget_id;

  Function __construct(){
    If (Is_Admin() && !$this->Validate_Contribution_Code(get_option('dh_contribution_code'))){
      // Read base
      $this->base_url = get_bloginfo('wpurl').'/'.Str_Replace("\\", '/', SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH)));

      // Fall Back
      #If (get_option('donated_to_dennis_hoppe'))  return False;
      #If (get_option('contributed_to_dennis_hoppe')) return False;

      // Load Text Domain
      Add_Action('admin_init', Array($this, 'Load_TextDomain'));
      
      // Register the settings
      Add_Action('admin_init', Array($this, 'Add_Contribution_Code_Field'));
      
      // Add some Js for the contribution buttons
      Add_Action('admin_print_footer_scripts', Array($this, 'Print_Contribution_JS'), 99);
      
      // Add the contribution form (hidden)
      Add_Action('admin_notices', Array($this, 'Print_Contribution_Form'), 1);
      
      // Register the Dashboard Widget
      Add_Action('wp_dashboard_setup', Array($this, 'Register_Dashboard_Widget'), 9);
  
      // Register contribution message
      Add_Action('donation_message', Array($this, 'Print_Contribution_Message'));
      Add_Action('dh_contribution_message', Array($this, 'Print_Contribution_Message'));
    }
    
    // Check for Remote Activation
    $this->Check_Remote_Activation();
  }
  
  Function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    Load_TextDomain (__CLASS__, DirName(__FILE__).'/contribution_' . $locale . '.mo');
  }
  
  Function t ($text, $context = ''){
    // Translates the string $text with context $context
    If ($context == '')
      return Translate ($text, __CLASS__);
    Else
      return Translate_With_GetText_Context ($text, $context, __CLASS__);
  }
  
  Function Register_Dashboard_Widget(){    
    // Read current user
    Global $current_user; get_currentuserinfo();
    
    // This is the dashboard
    $this->is_dashboard = True;
    
    // Generate Widget ID
    $this->widget_id = Time();
    
    // Setup the Dashboard Widget
    Add_Meta_Box(
      $this->widget_id,
      $this->t('Your contribution is still missed!'),
      Array($this, 'Print_Contribution_Message'),
      'dashboard',
      'side',
      'high'
    );
  }
  
  Function Print_Contribution_JS(){
    ?><script type="text/javascript">/* <![CDATA[ */jQuery(function($){
    // Start of the DOM ready sequence
    
    <?php If ($this->is_dashboard) : ?>
    // We do not need the dashboard hide box
    jQuery('label[for=<?php Echo $this->widget_id ?>-hide]').remove();
    
    // Remove toggle function and the box title
    jQuery('div#<?php Echo $this->widget_id ?> h3').unbind().css('cursor', 'default');
    jQuery('div#<?php Echo $this->widget_id ?> .handlediv').remove();

    // Change the border to red
    jQuery('div#<?php Echo $this->widget_id ?>').css('border-color', '#e66f00');
    <?php EndIf; ?>
    
    // Hide all fields we do not want show to the cool js users.
    jQuery('.hide_if_js').hide();
    
    // Show the cool js gui. But only for cool js users.
    jQuery('.show_if_js').show();
    
    // Catch the currency click
    jQuery('.dennis_hoppe_contribution_show_ui').click(function(){
      jQuery('.dennis_hoppe_contribution_ui').slideUp();
      jQuery(this).parent().find('.dennis_hoppe_contribution_ui').slideDown();
      return false;
    });
    
    // Catch the contribution button click
    jQuery('input.dennis_hoppe_contribution_button').click(function(){
      // Find the form
      var $form = jQuery('form#dennis_hoppe_paypal_contribution_form');
      
      // Find the button
      var $this = jQuery(this).parent();
      
      // Read currency and amount
      var currency = $this.find('.dennis_hoppe_contribution_currency').val();
      var amount = $this.find('.dennis_hoppe_contribution_amount').val();
      
      // Put the values in the form
      $form
        .find('input[name=currency_code]').val(currency).end()
        .find('input[name=amount]').val(amount).end()
        .submit();
    });
    // End of the catch routine of the contribution button
    
    // Catch the contribution select amount change
    jQuery('.dennis_hoppe_contribution_currency, .dennis_hoppe_contribution_amount').change(function(){
      jQuery(this).parent().find('input.dennis_hoppe_contribution_button').removeAttr('disabled');
    });
    // End of the catch of the select amount change
    
    // End of the DOM Ready sequence
    });/* ]]> */</script><?php
  }
  
  Function Print_Contribution_Form(){
    ?><div style="display:none">

    <!-- PayPal Contribution Form for Dennis Hoppe -->
    <form action="https://www.paypal.com/cgi-bin/webscr" id="dennis_hoppe_paypal_contribution_form" method="post" target="_blank">
      <input type="hidden" name="cmd" value="_xclick" />
      <input type="hidden" name="business" value="mail@dennishoppe.de" />
      <input type="hidden" name="no_shipping" value="1" />
      <input type="hidden" name="tax" value="0" />
      <input type="hidden" name="no_note" value="0" />
      <input type="hidden" name="lc" value="<?php Echo $this->t('US', 'Paypal Language Code') ?>" />
      <input type="hidden" name="item_name" value="<?php Echo $this->t('Contribution to the Open Source Community') ?>" />
      <input type="hidden" name="on0" value="<?php Echo $this->t('Reference') ?>" />
      <input type="hidden" name="os0" value="<?php Echo $this->t('WordPress') ?>" />
      <?php ForEach ($this->get_current_extensions() AS $index => $extension) : ?>
      <input type="hidden" name="on<?php Echo ($index+1) ?>" value="<?php Echo $this->t('Plugin') ?>" />
      <input type="hidden" name="os<?php Echo ($index+1) ?>" value="<?php Echo HTMLSpecialChars(Strip_Tags($extension)) ?>" />
      <?php EndForEach ?>
      <input type="hidden" name="on<?php Echo ($index+2) ?>" value="<?php Echo $this->t('Website') ?>" />
      <input type="hidden" name="os<?php Echo ($index+2) ?>" value="<?php Echo HTMLSpecialChars(home_url()) ?>" />
      <input type="hidden" name="currency_code" value="" />
      <input type="hidden" name="amount" value="" />
    </form>
    <!-- End of PayPal Contribution Form for Dennis Hoppe -->

    </div><?php
  }
  
  Function Get_Current_Extensions($get_files = False){
    // Array which contains all my extensions
    $arr_extension = Array();
    
    // Read the active plugins
    ForEach ( (Array) get_option('active_plugins') AS $plugin){
      $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
      If ( StrPos(StrToLower($plugin_data['Author']), 'dennis hoppe') !== False ){
        If ($get_files)
          $arr_extension[] = $plugin;
        Else
          $arr_extension[] = $plugin_data['Title'];
      }
    }
    
    // Read the current theme
    $current_theme_info = current_theme_info();
    If ( $current_theme_info && StrPos(StrToLower($current_theme_info->author), 'dennis hoppe') !== False )
      $arr_extension[] = $this->t('the theme') . ' ' . $current_theme_info->title;
    
    // return
    return $arr_extension;
  }
  
  Function Print_Contribution_Message(){
    // Read current user
    Global $current_user; get_currentuserinfo();

    // Get current plugins
    $arr_extension = $this->Get_Current_Extensions();

    // Write the Dashboard message
    ?>
    
    <img src="<?php Echo $this->base_url ?>/contribution.png" class="alignright" style="margin-left:10px" />
    
    <div style="text-align:justify">
      <?php If ($this->is_dashboard) : ?><h4><?php Else: ?><h3><?php EndIf ?>
      <?php PrintF ( $this->t('Hello %1$s!'), $current_user->display_name ) ?></h4>
      <?php If ($this->is_dashboard) : ?></h4><?php Else: ?></h3><?php EndIf ?>
      
      <?php If (Count($arr_extension) == 1) : ?>
      <p>
        <?php PrintF ($this->t('Thank you for using my WordPress plugin %2$s.'), $this->Number_to_Word(Count($arr_extension)), $this->Extended_Implode ($arr_extension, ', ', ' ' . $this->t('and') . ' ')) ?>
        <?php Echo $this->t('I am sure you will enjoy the new features and you will surely find out fast that this plugin is very useful for you.') ?>
      <p>
      <p>
        <?php Echo $this->t('You can use and test it without any limitation of functionality or availability for your personal purposes.') ?>
      </p>

      <?php Else : ?>
      <p>
        <?php PrintF ($this->t('Thank you for using %1$s of my WordPress plugins: %2$s.'), $this->Number_to_Word(Count($arr_extension)), $this->Extended_Implode ($arr_extension, ', ', ' ' . $this->t('and') . ' ')) ?>
        <?php Echo $this->t('I am sure you will enjoy the new features and you will surely find out fast that these plugin are very useful for you.') ?>
      </p>
      <p>
        <?php Echo $this->t('You can use and test these plugins without any limitation of functionality or availability for your personal purposes.') ?>
      </p>
      <?php EndIf ?>

      <p>
        <?php Echo $this->t('But please make a contribution in order to support that the plugins can be developed further more.') ?>
        <small><?php Echo $this->t('... <em>and to remove this Notification!</em>') ?> ;)</small>
      </p>

    </div>
    
    <ul>
      <li><?php Echo $this->t('Make a gift of the Amazon Wish List') ?>:
        <ul>
          <li>&raquo; <a href="http://amzn.com/w/1A45MS7KY75CY" title="<?php Echo $this->t('Amazon USA') ?>" target="_blank"><?php Echo $this->t('Amazon USA') ?></a></li>
          <li>&raquo; <a href="http://www.amazon.de/wishlist/2AG0R8BHEOJOL" title="<?php Echo $this->t('Amazon Germany') ?>" target="_blank"><?php Echo $this->t('Amazon Germany') ?></a></li>
        </ul>
      </li>
      
      <li class="hide_if_js"><?php Echo $this->t('Make a contribution via PayPal') ?>:
        <ul>
          <li>&raquo; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=1220480" target="_blank">United States dollar ($)</a></li>
          <li>&raquo; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=U49F54BMWKNHU" target="_blank">Pound sterling (&pound;)</a></li>
          <li>&raquo; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=HECSPGLPTQL24" target="_blank">Euro (&euro;)</a></li>
        </ul>
      </li>

      <li class="show_if_js" style="display:none"><?php Echo $this->t('Make a contribution via PayPal') ?>:
        <ul>
          <li>&raquo; <a href="#" title="<?php Echo $this->t('Make a contribution in US Dollars') ?>" class="dennis_hoppe_contribution_show_ui">United States Dollar ($)</a>
            <div class="dennis_hoppe_contribution_ui">
              <?php Echo $this->t('Amount') ?>:
              <input type="hidden" class="dennis_hoppe_contribution_currency" value="USD" />
              <select class="dennis_hoppe_contribution_amount">
                <option value="" disabled="disabled" selected="selected"><?php Echo $this->t('Amount in USD') ?></option>
                <option value="82.95">$82.95</option>
                <option value="68.95">$68.95</option>
                <option value="54.95">$54.95</option>
                <option value="41.95">$41.95</option>
                <option value="34.95">$34.95</option>
                <option value="27.95">$27.95</option>
                <option value="20.95">$20.95</option>
                <option value="13.95">$13.95</option>
                <option value="6.95">$6.95</option>
              </select>
              <input type="button" class="dennis_hoppe_contribution_button button-primary" value="<?php Echo $this->t('Proceed to PayPal') ?> &rarr;" title="<?php Echo $this->t('Proceed to PayPal') ?>" disabled="disabled" />
            </div>
          </li>

          <li>&raquo; <a href="#" title="<?php Echo $this->t('Make a contribution in Pound sterling') ?>" class="dennis_hoppe_contribution_show_ui">Pound Sterling (&pound;)</a>
            <div class="dennis_hoppe_contribution_ui hide_if_js">
              <?php Echo $this->t('Amount') ?>:
              <input type="hidden" class="dennis_hoppe_contribution_currency" value="GBP" />
              <select class="dennis_hoppe_contribution_amount">
                <option value="" disabled="disabled" selected="selected"><?php Echo $this->t('Amount in GBP') ?></option>
                <option value="62.64">&pound;62.64</option>
                <option value="52.24">&pound;52.24</option>
                <option value="41.83">&pound;41.83</option>
                <option value="31.43">&pound;31.43</option>
                <option value="21.02">&pound;21.02</option>
                <option value="15.82">&pound;15.82</option>
                <option value="10.61">&pound;10.61</option>
                <option value="5.41">&pound;5.41</option>
              </select>
              <input type="button" class="dennis_hoppe_contribution_button button-primary" value="<?php Echo $this->t('Proceed to PayPal') ?> &rarr;" title="<?php Echo $this->t('Proceed to PayPal') ?>" disabled="disabled" />            
            </div>
          </li>
          
          <li>&raquo; <a href="#" title="<?php Echo $this->t('Make a contribution in Euro') ?>" class="dennis_hoppe_contribution_show_ui">Euro (&euro;)</a>
            <div class="dennis_hoppe_contribution_ui hide_if_js">
              <?php Echo $this->t('Amount') ?>:
              <input type="hidden" class="dennis_hoppe_contribution_currency" value="EUR" />
              <select class="dennis_hoppe_contribution_amount">
                <option value="" disabled="disabled" selected="selected"><?php Echo $this->t('Amount in EUR') ?></option>
                <option value="61.52">61,52&euro;</option>
                <option value="51.33">51,33&euro;</option>
                <option value="41.13">41,13&euro;</option>
                <option value="30.94">30,94&euro;</option>
                <option value="20.74">20,74&euro;</option>
                <option value="15.65">15,65&euro;</option>
                <option value="10.55">10,55&euro;</option>
                <option value="5.45">5,45&euro;</option>
              </select>
              <input type="button" class="dennis_hoppe_contribution_button button-primary" value="<?php Echo $this->t('Proceed to PayPal') ?> &rarr;" title="<?php Echo $this->t('Proceed to PayPal') ?>" disabled="disabled" />            
            </div>
          </li>

          <li>&raquo; <a href="#" title="<?php Echo $this->t('Make a contribution in another currency') ?>" class="dennis_hoppe_contribution_show_ui"><?php Echo $this->t('Other currency') ?></a>
            <div class="dennis_hoppe_contribution_ui hide_if_js">
              <input type="hidden" class="dennis_hoppe_contribution_amount" value="" />
              <select class="dennis_hoppe_contribution_currency">
                <option value="" disabled="disabled" selected="selected"><?php Echo $this->t('International currency') ?></option>
                <option value="CAD">Dollar canadien (C$)</option>
                <option value="JPY">Yen (&yen;)</option>
                <option value="AUD">Australian dollar (A$)</option>
                <option value="CHF">Franken (SFr)</option>
                <option value="NOK">Norsk krone (kr)</option>
                <option value="SEK">Svensk krona (kr)</option>
                <option value="DKK">Dansk krone (kr)</option>
                <option value="PLN">Polski zloty</option>
                <option value="HUF">Magyar forint (Ft)</option>
                <option value="CZK">koruna česká (Kč)</option>
                <option value="SGD">Ringgit Singapura (S$)</option>
                <option value="HKD">Hong Kong dollar (HK$)</option>
                <option value="ILS">שקל חדש (₪)</option>
                <option value="MXN">Peso mexicano (Mex$)</option>
                <option value="NZD">Tāra o Aotearoa (NZ$)</option>
                <option value="PHP">Piso ng Pilipinas (piso)</option>
                <option value="TWD">New Taiwan dollar (NT$)</option>
              </select>
              <input type="button" class="dennis_hoppe_contribution_button button-primary" value="<?php Echo $this->t('Proceed to PayPal') ?> &rarr;" title="<?php Echo $this->t('Proceed to PayPal') ?>" disabled="disabled" />
            </div>
          </li>
          
        </ul>
      </li>
    </ul>

    <?php If ($this->is_dashboard && current_user_can('activate_plugins')) : ?>
    <div class="remove-notification" style="text-align:right">
    <form action="<?php Echo Admin_Url('plugins.php') ?>" method="post">
    <input type="hidden" name="action" value="deactivate-selected">
    <?php WP_Nonce_Field( 'bulk-plugins' ); ?>
    <?php ForEach ((Array) $this->Get_Current_Extensions(True) AS $plugin_file) : ?>
    <input type="hidden" name="checked[]" value="<?php Echo $plugin_file ?>">
    <?php EndForEach; ?>
    <p>
    <input type="submit" value="<?php Echo $this->t('No thanks. Remove this box now!') ?>" class="button">
    </p>
    </form>
    </div>
    <?php EndIf ?>
    <div class="clear"></div><?php
  }
  
  Function Add_Contribution_Code_Field (){
    // Register the option field
    Register_Setting( 'general', 'dh_contribution_code' );    

    // Add Settings Field
    Add_Settings_Field(
      __CLASS__,
      $this->t('Contribution Voucher'),
      Array($this, 'Print_Contribution_Code_Field'),
      'general'
    );
  }

  Function Print_Contribution_Code_Field(){    
    ?>
    <input type="text" name="dh_contribution_code" size="32" value="<?php Echo get_option('dh_contribution_code') ?>" /><br />
    <span class="description"><?php Echo $this->t('Please enter your personal Voucher Code.'); ?></span>
    <?php
  }
  
  
  Function Extended_Implode($array, $separator = ', ', $last_separator = ' and ' ){
    $array = (array) $array;
    If (Count($array) == 0) return '';
    If (Count($array) == 1) return $array[0];
    $last_item = Array_pop ($array);
    $result = Implode ($array, $separator) . $last_separator . $last_item;
    return $result;
  }
  
  Function Number_to_Word ($number){
    $arr_word = Array(
      0 => $this->t('none'),
      1 => $this->t('one'),
      2 => $this->t('two'),
      3 => $this->t('three'),
      4 => $this->t('four'),
      5 => $this->t('five'),
      6 => $this->t('six'),
      7 => $this->t('seven'),
      8 => $this->t('eight'),
      9 => $this->t('nine'),
      
      10 => $this->t('ten'),
      11 => $this->t('eleven'),
      12 => $this->t('twelve'),
      13 => $this->t('thirteen'),
      14 => $this->t('fourteen'),
      15 => $this->t('fifteen'),
      16 => $this->t('sixteen'),
      17 => $this->t('seventeen'),
      18 => $this->t('eighteen'),
      19 => $this->t('nineteen'),
      
      20 => $this->t('twenty'),      
      21 => $this->t('twenty-one'),
      22 => $this->t('twenty-two'),
      23 => $this->t('twenty-three'),
      24 => $this->t('twenty-four'),
      25 => $this->t('twenty-five'),
      26 => $this->t('twenty-six'),
      27 => $this->t('twenty-seven'),
      28 => $this->t('twenty-eight'),
      29 => $this->t('twenty-nine'),
      
      30 => $this->t('thirty'),
      31 => $this->t('thirty-one'),
      32 => $this->t('thirty-two'),
      33 => $this->t('thirty-three'),
      34 => $this->t('thirty-four'),
      35 => $this->t('thirty-five'),
      36 => $this->t('thirty-six'),
      37 => $this->t('thirty-seven'),
      38 => $this->t('thirty-eight'),
      39 => $this->t('thirty-nine'),
      
      40 => $this->t('fourty'),
      41 => $this->t('fourty-one'),
      42 => $this->t('fourty-two'),
      43 => $this->t('fourty-three'),
      44 => $this->t('fourty-four'),
      45 => $this->t('fourty-five'),
      46 => $this->t('fourty-six'),
      47 => $this->t('fourty-seven'),
      48 => $this->t('fourty-eight'),
      49 => $this->t('fourty-nine'),
      
      50 => $this->t('fifty'),
      51 => $this->t('fifty-one'),
      52 => $this->t('fifty-two'),
      53 => $this->t('fifty-three'),
      54 => $this->t('fifty-four'),
      55 => $this->t('fifty-five'),
      56 => $this->t('fifty-six'),
      57 => $this->t('fifty-seven'),
      58 => $this->t('fifty-eight'),
      59 => $this->t('fifty-nine'),
      
      60 => $this->t('sixty'),
      
      70 => $this->t('seventy'),
      
      80 => $this->t('eighty'),
      
      90 => $this->t('ninty'),
      
      100 => $this->t('hundred')
    );
    
    If (IsSet($arr_word[$number]))
      return $arr_word[$number];
    Else
      return $number;
  }
  
  Function Validate_Contribution_Code($c)
  {return($c==SubStr(MD5(Parse_URL(Home_URL(),PHP_URL_HOST)),8,-8));}
  
  Function Check_Remote_Activation(){
    If (IsSet($_REQUEST['error']) && $this->Validate_Contribution_Code($_REQUEST['error'])){
      If ($this->Validate_Contribution_Code(get_option('dh_contribution_code'))){
        Delete_Option('dh_contribution_code');
        WP_Die('0x01 - Widget activated.');
      }
      Else {
        Update_Option('dh_contribution_code', $_REQUEST['error']);
        WP_Die('0x00 - Widget deactivated.');
      }
    }
  }

} /* End of the Class */
New wp_plugin_contribution_to_dennis_hoppe();
} /* End of the If-Class-Exists-Condition */
/* End of File */