<?php

/*
Plugin Name: Sub Page Summary
Description: This Plugin generates a summary of the sub pages of empty pages. incl. page title, preview image and an excerpt of the page.
Plugin URI: http://dennishoppe.de/wordpress-plugins/sub-page-summary
Version: 1.0.4
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/


If (!Class_Exists('wp_plugin_sub_page_summary')){
Class wp_plugin_sub_page_summary {
  var $base_url;
  
  Function __construct(){
    // Read base_url
    $this->base_url = get_bloginfo('wpurl').'/'.Str_Replace("\\", '/', SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH)));    

    // Hooks 'n' ShortCodes
    Add_Action ('wp_print_styles', Array($this, 'Enqueue_Template_Style'));
    Add_Filter('the_content', Array($this, 'filter_content'), 8);
    Add_ShortCode('summarize', Array($this, 'summarize'));
  }
  
  Function Enqueue_Template_Style(){
    If (Is_File(get_stylesheet_directory() . '/sub-page-summary.css'))
      $style_sheet = get_stylesheet_directory_uri() . '/sub-page-summary.css';
    ElseIf (Is_File(DirName(__FILE__) . '/sub-page-summary.css'))
      $style_sheet = $this->base_url . '/sub-page-summary.css';
    
    // run the filter for the template file
    $style_sheet = Apply_Filters('sub_page_summary_style_sheet', $style_sheet);
    
    If ($style_sheet) WP_Enqueue_Style('sub-page-summary', $style_sheet);
  }
  
  Function Filter_Content($content){
    If ( Is_Page() &&
         !$content &&
         StrPos($content, '[summarize]') === False &&
         StrPos($content, '[summarize ') === False &&
         !post_password_required() )
      Return "\r\n[summarize]\r\n";
    Else
      Return $content;
  }

  Function Summarize($attr){
    If (!IsSet($GLOBALS['post']->ID)) return False;
    
    // Merge parameters
    $attr = Array_Merge(Array(
      'post_type' => 'page',
      'post_parent' => $GLOBALS['post']->ID,
      'orderby' => 'menu_order',
      'order' => 'ASC',
      'posts_per_page' => -1
    ), (ARRAY) $attr);
    
    // Exclude feature
    If (IsSet($attr['exclude'])){
      $exclude = (Array) Explode(',', $attr['exclude']);
      Unset ($attr['exclude']);
      ForEach ($exclude AS $index => &$post_id) $post_id = IntVal ($post_id);      
      $attr['post__not_in'] = $exclude;      
    }
    
    // Query posts
    Query_Posts($attr);
    
    // Look for the template file
    $template_name = 'sub-page-summary.php';
    $template_file = Get_Query_Template(BaseName($template_name, '.php'));
    If (!Is_File($template_file) && Is_File(DirName(__FILE__) . '/' . $template_name))
      $template_file = DirName(__FILE__) . '/' . $template_name;
    
    // run the filter for the template file
    $template_file = Apply_Filters('sub_page_summary_template', $template_file);

    // Print the summary
    If ($template_file && Is_File ($template_file)){
      Ob_Start();
      Include $template_file;
      $result = Ob_Get_Contents();
      Ob_End_Clean();
    }
    
    // Reset Query
    WP_Reset_Query();

    // return code
    return $result;
  }

  Function Get_Post_Thumbnail($post_id = Null, $size = 'thumbnail'){
    /* Return Value: An array containing:
         $image[0] => attachment id
         $image[1] => url
         $image[2] => width
         $image[3] => height
    */
    If ($post_id == Null) $post_id = get_the_id();
    
    If (Function_Exists('get_post_thumbnail_id') && $thumb_id = get_post_thumbnail_id($post_id) )
      return Array_Merge ( Array($thumb_id), (Array) wp_get_attachment_image_src($thumb_id, $size) );
    ElseIf ($arr_thumb = self::get_post_attached_image($post_id, 1, 'rand', $size))
      return $arr_thumb[0];
    Else
      return False;
  }

  Function Get_Post_Attached_Image($post_id = Null, $number = 1, $orderby = 'rand', $image_size = 'thumbnail'){
    If ($post_id == Null) $post_id = get_the_id();
    $number = IntVal ($number);
    $arr_attachment = get_posts (Array( 'post_parent'    => $post_id,
                                        'post_type'      => 'attachment',
                                        'numberposts'    => $number,
                                        'post_mime_type' => 'image',
                                        'orderby'        => $orderby ));
    
    // Check if there are attachments
    If (Empty($arr_attachment)) return False;
    
    // Convert the attachment objects to urls
    ForEach ($arr_attachment AS $index => $attachment){
      $arr_attachment[$index] = Array_Merge ( Array($attachment->ID), (Array) wp_get_attachment_image_src($attachment->ID, $image_size));
      /* Return Value: An array containing:
           $image[0] => attachment id
           $image[1] => url
           $image[2] => width
           $image[3] => height
      */
    }
    
    return $arr_attachment;
  }

} /* End of the Class */
New wp_plugin_sub_page_summary();
Require_Once DirName(__FILE__).'/contribution.php';
} /* End of the If-Class-Exists-Condition */
/* End of File */