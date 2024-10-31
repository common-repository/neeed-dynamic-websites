<?php if (!defined('ABSPATH')) die; //no direct access


Dynweb_Pointer::prepare();


class Dynweb_Pointer {


    static function prepare() {
        add_action('wp_ajax_dynweb_start_tutorial', array('Dynweb_Pointer', 'start_tutorial'));
        add_action('wp_ajax_dynweb_stop_tutorial', array('Dynweb_Pointer', 'stop_tutorial'));
        
        $meta_options = get_option('dynweb-meta-options');
		if ($meta_options['tutorial_active'] == 1) {
            add_action('admin_enqueue_scripts', array('Dynweb_Pointer', 'enqueue'));
            add_action('wp_enqueue_scripts', array('Dynweb_Pointer', 'enqueue'));
        }
        
    }
    
    static function start_tutorial() {
		$options = get_option('dynweb-meta-options');
		$options['tutorial_active'] = 1;
		update_option('dynweb-meta-options', $options);
		die;
	}
    
    static function stop_tutorial() {
		$options = get_option('dynweb-meta-options');
		$options['tutorial_active'] = 0;
		if (!DYNWEB_DEMO) update_option('dynweb-meta-options', $options);
		die;
	}
    
    
    static function enqueue() {
        wp_enqueue_style('wp-pointer');
		wp_enqueue_script('wp-pointer');
		if (is_admin()) add_action('admin_print_footer_scripts', array('Dynweb_Pointer', 'add_pointers'));
		else add_action('print_footer_scripts', array('Dynweb_Pointer', 'add_pointers'));
    }


	static function add_pointers() {
		$meta_options = get_option('dynweb-meta-options');
		if ($meta_options['tutorial_active'] == 0 ||  !current_user_can(DYNWEB_CAPABILTY)) return;
		
		$pointers = array();
		$pointers[] = array('target' => '#menu-pages', 'id' => 'dynweb_pointer_0_pages', 'edge' => 'left', 'align' => 'center', 'title' => __('Do you want a short tutorial for NEEED?', 'neeed-dynamic-websites'), 'content' => __('Hello and welcome to NEEED. We will show you around the tool with this short tutorial.<br /><br />If you do not need that, you can always quit the tutorial by clicking &quot;Dismiss&quot;. You can always restart the tutorial in the NEEED settings.<br /><br /><a href=# class=dynweb_tutorial_next>Next</a>', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#menu-pages', 'id' => 'dynweb_pointer_1_pages', 'edge' => 'left', 'align' => 'center', 'title' => __('Test Page', 'neeed-dynamic-websites'), 'content' => __('Click on Pages in the menu and create a new page, which we will use to give NEEED a try.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '.page-title-action, .split-page-title-action', 'id' => 'dynweb_pointer_2_new_page', 'edge' => 'top', 'align' => 'left', 'title' => __('Test Page', 'neeed-dynamic-websites'), 'content' => __('Add a new page.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#et_pb_toggle_builder, #et-switch-to-divi', 'id' => 'dynweb_pointer_3_builder', 'edge' => 'bottom', 'align' => 'left', 'title' => __('Divi Builder', 'neeed-dynamic-websites'), 'content' => __('Activate the Divi Builder and create a new page &quot;from scratch&quot;.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '.et-fb-page-creation-card-build_from_scratch button', 'id' => 'dynweb_pointer_4_builder_sratch', 'edge' => 'bottom', 'align' => 'left', 'title' => __('From Scratch', 'neeed-dynamic-websites'), 'content' => __('Create a new page &quot;from scratch&quot;.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '.et-fb-columns-layout li:eq(0)', 'id' => 'dynweb_pointer_5_builder_newrow', 'edge' => 'right', 'align' => 'center', 'title' => __('One column', 'neeed-dynamic-websites'), 'content' => __('Create a new row with one column.', 'neeed-dynamic-websites'));	
		$pointers[] = array('target' => '#et-fb-filterByTitle', 'id' => 'dynweb_pointer_6_builder_text', 'edge' => 'right', 'align' => 'center', 'title' => __('New text module', 'neeed-dynamic-websites'), 'content' => __('Create a text module. The easiest way to find it, is via the search field.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#main_content_content_vb_tiny_mce_ifr, #main_content_content_vb_tiny_mce', 'id' => 'dynweb_pointer_7_builder_text', 'edge' => 'bottom', 'align' => 'center', 'title' => __('Good morning greeting', 'neeed-dynamic-websites'), 'content' => __('Write a little greeting. For example &quot;Have a wonderful morning!&quot;.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '.et-fb-tabs__item:eq(-1)', 'id' => 'dynweb_pointer_8_builder_tabs', 'edge' => 'top', 'align' => 'left', 'title' => __('Time for NEEED', 'neeed-dynamic-websites'), 'content' => __('When you finished your greeting, go to the new tab &quot;NEEED&quot;.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#et-fb-dynweb_rule', 'id' => 'dynweb_pointer_9_builder_rule', 'edge' => 'bottom', 'align' => 'left', 'title' => __('Select Rule', 'neeed-dynamic-websites'), 'content' => __('Select the rule &quot;Situation: Morning (04:00 - 11:00)&quot;, so the greeting will only be visible in the morning.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '.et-fb-button--block.et-fb-button--success', 'id' => 'dynweb_pointer_10_builder_save', 'edge' => 'bottom', 'align' => 'left', 'title' => __('Save Module', 'neeed-dynamic-websites'), 'content' => __('When you selected the rule, save the module.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#post-preview', 'id' => 'dynweb_pointer_11_preview', 'edge' => 'right', 'align' => 'center', 'title' => __('Preview', 'neeed-dynamic-websites'), 'content' => __('Have a look at your new page in the preview.', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#wp-admin-bar-edit', 'id' => 'dynweb_pointer_12_preview', 'edge' => 'top', 'align' => 'right', 'title' => __('Preview', 'neeed-dynamic-websites'), 'content' => __('Is it currently between 4am and 11am at your destination? Then you should already see the greeting. You can use the admin bar to change your hour in order to see how the page looks to a visitor in a different timezone. Just click on the &quot;Date&quot; field under &quot;NEEED&quot;, &quot;Own situation&quot;. Give it a try now.<br><br><a href=# class=dynweb_tutorial_next>Next</a>', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#wp-admin-bar-edit', 'id' => 'dynweb_pointer_13_preview', 'edge' => 'top', 'align' => 'right', 'title' => __('Preview', 'neeed-dynamic-websites'), 'content' => __('Congratulations! You have learned the easiest use of a NEEED rule. There are a lot of more rules. You can show different content depending on the weather, show a special greeting to returning visitors, only show videos to people who like to watch videos and much more.<br><br><a href=# class=dynweb_tutorial_next>Next</a>', 'neeed-dynamic-websites'));
		$pointers[] = array('target' => '#wp-admin-bar-edit', 'id' => 'dynweb_pointer_14_preview', 'edge' => 'top', 'align' => 'right', 'title' => __('Preview', 'neeed-dynamic-websites'), 'content' => __('We hope that you liked the tutorial and that it showed you how to use NEEED in the future.<br /><br />If you have any further questions, feel free to send as a message to <a href=mailto:support@neeed.me>support@neeed.me</a>. See you around!<br><br><a href=# class=dynweb_tutorial_exit >Conclude tutorial</a>', 'neeed-dynamic-websites'));
		
		
		$pointers[104] = array('target' => 'a.et-bfb-optin-cta__button', 'id' => 'dynweb_pointer_104_switch_builder', 'edge' => 'bottom', 'align' => 'right', 'title' => __('Switch Builder', 'neeed-dynamic-websites'), 'content' => __('Please activate the new Divi Builder.', 'neeed-dynamic-websites'));
		$pointers[111] = array('target' => '#wp-admin-bar-et-disable-visual-builder', 'id' => 'dynweb_pointer_111_preview', 'edge' => 'top', 'align' => 'left', 'title' => __('Quit Builder', 'neeed-dynamic-websites'), 'content' => __('Quit the visual builder. Select &quot;Save & Exit&quot; if the builder asks if you want to save.', 'neeed-dynamic-websites'));
		
		$obj_pointers = new stdClass;
		$obj_pointers->pointers = $pointers;
		$json_pointers = json_encode($obj_pointers);
		
		global $pagenow;

		if ($pagenow == 'edit.php') $active_pointer_index = 2;
		elseif ($pagenow == 'post-new.php') $active_pointer_index = 3;
		elseif ($pagenow == 'post.php' || isset($_GET['et_fb'])) $active_pointer_index = 4;
		elseif (is_page() && isset($_GET['page_id'])) $active_pointer_index = 12;
		else $active_pointer_index = 0;
		
		?>
			<script type="text/javascript">
				var dynweb_str_pointers = '<?php echo $json_pointers; ?>';
				var dynweb_obj_pointers = jQuery.parseJSON(dynweb_str_pointers);
				var current_pointer_index = <?php echo $active_pointer_index; ?>;
				var pointer_interval;
				dynweb_gl_pointers_active = true;
				
				
				function dynweb_show_next_pointer(delay, fixed_id, scroll_to, scroll_gutenberg_sidebar) {
				    console.log('dynweb_show_next_pointer. fixed_id:'+fixed_id);
					jQuery('.wp-pointer').hide();
					if (fixed_id) current_pointer_index = fixed_id; 
					else current_pointer_index += 1;
					
					/*
					if (scroll_to) jQuery('html, body').animate({
                        scrollTop: jQuery(scroll_to).offset().top - 100
                    }, 500);
                    if (scroll_gutenberg_sidebar == 'down') jQuery('.edit-post-sidebar').scrollTop(jQuery('.edit-post-sidebar').prop('scrollHeight'));
                    if (scroll_gutenberg_sidebar == 'up') jQuery('.edit-post-sidebar').scrollTop(0);
                    */
                    
					if (delay == 0) dynweb_show_pointer(current_pointer_index);
					else pointer_interval = setInterval(function(){ dynweb_show_pointer(current_pointer_index); }, delay);
				}
				
				
				function dynweb_show_pointer(index) {
					if (dynweb_gl_pointers_active == false) return;
					if (typeof(jQuery().pointer) != 'undefined' && index != -1) {
                        
                        if (jQuery(dynweb_obj_pointers.pointers[index].target).filter(':visible').length == 0) return;
                        else clearInterval(pointer_interval);
					
						jQuery(dynweb_obj_pointers.pointers[index].target).pointer({
							content: '<h3>'+dynweb_obj_pointers.pointers[index].title+'</h3><p>'+dynweb_obj_pointers.pointers[index].content+'</p>',
							position: {
								edge: dynweb_obj_pointers.pointers[index].edge,
								align: dynweb_obj_pointers.pointers[index].align
							},
							close: function() {
								jQuery.post( dynweb_admin.ajaxurl, {
									pointer: dynweb_obj_pointers.pointers[index].id,
									action: 'dynweb_stop_tutorial'
								});
								dynweb_gl_pointers_active = false;
							}
						}).pointer('open');
						
						current_pointer_index = index;
					}
				}

					
				jQuery(document).ready(function($) {
				
			        pointer_interval = setInterval(function(){ dynweb_show_pointer(current_pointer_index); }, 500);
					
					//show next pointer
					$('body').on('click', '.dynweb_tutorial_next', function(e) {
						e.preventDefault();
						dynweb_show_next_pointer(100);
					});
					
					
					$('body').on('click', '#et_pb_toggle_builder', function() {
					    if ($('a.et-bfb-optin-cta__button[href*="enable=1"]').length > 0) {
						    dynweb_show_next_pointer(100, 104); //switch builder if necessary, if new builder is already active, a new page will be loaded
						    $('html, body').animate({
                                scrollTop: jQuery('a.et-bfb-optin-cta__button').offset().top - 100
                            }, 500);
						}
					});
					
					
					$('body').on('click', '.et-fb-page-creation-card-build_from_scratch button', function() {
						dynweb_show_next_pointer(500, 5);
					});
					
					$('body').on('mousedown', '.et-fb-columns-layout li', function() {
						dynweb_show_next_pointer(500, 6);
					});
					
					$('body').on('mousedown', 'li.et_fb_text', function() {
						dynweb_show_next_pointer(500, 7);
					});
					
					$('body').on('mouseenter', '#main_content_content_vb_tiny_mce_ifr, #main_content_content_vb_tiny_mce', function() {
						dynweb_show_next_pointer(4000, 8);
					});
					
					$('body').on('mousedown', '.et-fb-tabs__item', function() {
						dynweb_show_next_pointer(500, 9);
					});
					
					$('body').on('mousedown', '#et-fb-dynweb_rule', function() {
						dynweb_show_next_pointer(2000, 10);
					});
					
					$('body').on('mousedown', '.et-fb-button--success', function() {
					    if ($('#wp-admin-bar-et-disable-visual-builder').length > 0) dynweb_show_next_pointer(500, 111);
						else dynweb_show_next_pointer(500, 11);
					});
					
					$('body').on('click', '.dynweb_tutorial_exit', function(e) {
						e.preventDefault();
						$.post(dynweb_admin.ajaxurl, {action: 'dynweb_stop_tutorial'});
						$('.wp-pointer').hide();
					});
					
					
				});
			</script>
			
			<style>
			    .wp-pointer-content {
			        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
			    }
			</style>
		<?php
		
	}


}