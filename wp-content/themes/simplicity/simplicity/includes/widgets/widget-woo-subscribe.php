<?php
/*---------------------------------------------------------------------------------*/
/* Subscribe widget */
/*---------------------------------------------------------------------------------*/
class Woo_Subscribe extends WP_Widget {

   function Woo_Subscribe() {
	   $widget_ops = array('description' => 'Add a subscribe/connect widget.' );
       parent::WP_Widget(false, __('Woo - Subscribe / Connect', 'woothemes'),$widget_ops);      
   }
   
   function widget($args, $instance) {  
		extract( $args );
		$title = $instance['title']; if ($title == '') $title = __('Subscribe', 'woothemes');
	   	$form = ''; if ( array_key_exists( 'form', $instance ) ) $form = $instance['form'];
	   	$social = ''; if ( array_key_exists( 'social', $instance ) ) $social = $instance['social'];
	   	$single = ''; if ( array_key_exists( 'single', $instance ) ) $single = $instance['single'];		
	   	$page = ''; if ( array_key_exists( 'page', $instance ) ) $page = $instance['page'];		
		
		if ( !is_singular() OR ($single == 'on' AND is_single()) OR ($page == 'on' AND is_page()) ) {
		?>
			<?php echo $before_widget; ?>
			<?php woo_subscribe_connect('true', $title, $form, $social); ?>
	        <?php echo $after_widget; ?>        
		<?php
		}
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
		$title = esc_attr($instance['title']);
        $form = ''; if ( array_key_exists( 'form', $instance ) ) $form = esc_attr($instance['form']);
        $social = ''; if ( array_key_exists( 'social', $instance ) ) $social = esc_attr($instance['social']);
        $single = ''; if ( array_key_exists( 'single', $instance ) ) $single = esc_attr($instance['single']);
        $page = ''; if ( array_key_exists( 'page', $instance ) ) $page = esc_attr($instance['page']);
        
       	?>
		<!-- No options -->
		<p><em>Setup this widget in your <a href="<?php echo home_url() ?>/wp-admin/admin.php?page=woothemes">options panel</a> under <strong>Subscribe & Connect</strong></em>.</p>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
       	<p>
        	<input id="<?php echo $this->get_field_id('form'); ?>" name="<?php echo $this->get_field_name('form'); ?>" type="checkbox" <?php if($form == 'on') echo 'checked="checked"'; ?>><?php _e('Disable Subscription Form', 'woothemes'); ?></input>
	   	</p>
       	<p>
        	<input id="<?php echo $this->get_field_id('social'); ?>" name="<?php echo $this->get_field_name('social'); ?>" type="checkbox" <?php if($social == 'on') echo 'checked="checked"'; ?>><?php _e('Disable Social Icons', 'woothemes'); ?></input>
	   	</p>
       	<p>
        	<input id="<?php echo $this->get_field_id('single'); ?>" name="<?php echo $this->get_field_name('single'); ?>" type="checkbox" <?php if($single == 'on') echo 'checked="checked"'; ?>><?php _e('Enable in Posts', 'woothemes'); ?></input>
	   	</p>
       	<p>
        	<input id="<?php echo $this->get_field_id('page'); ?>" name="<?php echo $this->get_field_name('page'); ?>" type="checkbox" <?php if($page == 'on') echo 'checked="checked"'; ?>><?php _e('Enable in Pages', 'woothemes'); ?></input>
	   	</p>
      	<?php
   }
   
} 
register_widget('Woo_Subscribe');
?>