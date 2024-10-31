<?php
if (!defined('ABSPATH')) die;

Dynweb_Back::prepare();


class Dynweb_Back {

    static $weekdays, $months;
    
    
    static function prepare() {
        add_action('admin_enqueue_scripts', array('Dynweb_Back', 'enqueue'));
        add_action('admin_menu', array('Dynweb_Back', 'admin_menu'));
        add_action('admin_bar_menu',  array('Dynweb_Back', 'admin_bar_menu'), 100);
        add_action('admin_notices', array('Dynweb_Back', 'admin_notices'));
        
        include_once('dynweb-back-rules.php');
        include_once('dynweb-back-settings.php');
    }
    
    
    static function enqueue() {
    
        self::$weekdays = array(
            __('Sunday', 'neeed-dynamic-websites'),
            __('Monday', 'neeed-dynamic-websites'),
            __('Tuesday', 'neeed-dynamic-websites'),
            __('Wednesday', 'neeed-dynamic-websites'),
            __('Thursday', 'neeed-dynamic-websites'),
            __('Friday', 'neeed-dynamic-websites'),
            __('Saturday', 'neeed-dynamic-websites')
        );
        
        self::$months = array(
		     1 => __('January', 'neeed-dynamic-websites'),
		     2 => __('February', 'neeed-dynamic-websites'),
		     3 => __('March', 'neeed-dynamic-websites'),
		     4 => __('April', 'neeed-dynamic-websites'),
		     5 => __('May', 'neeed-dynamic-websites'),
		     6 => __('June', 'neeed-dynamic-websites'),
		     7 => __('July', 'neeed-dynamic-websites'),
		     8 => __('August', 'neeed-dynamic-websites'),
		     9 => __('September', 'neeed-dynamic-websites'),
		    10 => __('October', 'neeed-dynamic-websites'),
		    11 => __('November', 'neeed-dynamic-websites'),
		    12 => __('December', 'neeed-dynamic-websites')
		);
		
		$weekdays_min = array_map(function($weekday) {
		    return substr($weekday, 0, 2);
		}, self::$weekdays);
		
		$months_zero_start = array_merge(array(0 => ''), self::$months);
		array_shift($months_zero_start);
    
        wp_enqueue_style('dynweb_admin', plugins_url('css/dynweb_admin.css', __FILE__), array(), DYNWEB_VERSION);
        wp_register_style('jquery-ui-dynweb-custom-style', plugins_url('css/jquery-ui.min.css', __FILE__), array(), DYNWEB_VERSION, 'screen');
        
        /*
        if (isset($_GET['page']) && ($_GET['page'] == 'dynweb' || $_GET['page'] == 'dynweb-editrule')) {
		    wp_enqueue_style('jquery-ui-dynweb-custom-style');
		}
		*/
		wp_enqueue_style('jquery-ui-dynweb-custom-style');
		
		wp_enqueue_style('jquery-ui-datetimepicker', plugins_url('css/jquery-ui-timepicker-addon.min.css', __FILE__), array(), DYNWEB_VERSION);
		
		wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), DYNWEB_VERSION);
		
		wp_enqueue_script('dynweb_functions', plugins_url('js/dynweb_functions.js', __FILE__), array(), DYNWEB_VERSION);
		
		wp_enqueue_script('dynweb_admin_functions', plugins_url('js/dynweb_admin_functions.js', __FILE__), array('jquery'), DYNWEB_VERSION);
		wp_localize_script('dynweb_admin_functions', 'dynweb_admin_functions', array(
		    'visible_from_time' => __('Element will be visible from', 'neeed-dynamic-websites'),
		    'and_visible_from_time' => __('and from', 'neeed-dynamic-websites'),
		    'oclock_till' => __('o\'clock till', 'neeed-dynamic-websites'),
		    'oclock' => __('o\'clock', 'neeed-dynamic-websites'),
		    'weekdays' => self::$weekdays,
		    'visible_on_weekday' => __('Element will be visible on', 'neeed-dynamic-websites'),
		    'and_visible_on_weekday' => __('and on', 'neeed-dynamic-websites'),
		    'months' => array_merge(array(0 => ''), self::$months),
		    'visible_in_month' => __('Element will be visible in', 'neeed-dynamic-websites'),
		    'and_visible_in_month' => __('and in', 'neeed-dynamic-websites'),
		    'visible_datetime_from' => __('Element will be visible from', 'neeed-dynamic-websites'),
		    'visible_datetime_till' => __('till', 'neeed-dynamic-websites'),
		    'new_visitor' => __('New visitor', 'neeed-dynamic-websites'),
		    'returning_visitor' => __('Returning visitor', 'neeed-dynamic-websites'),
		    'referrer' => __('The referrer', 'neeed-dynamic-websites'),
		    'or_referrer' => __('or', 'neeed-dynamic-websites'),
		    'contains' => __('contains', 'neeed-dynamic-websites'),
		    'does_not_contain' => __('does not contain', 'neeed-dynamic-websites'),
		    'pro_only' => __('Only in the Pro version', 'neeed-dynamic-websites'),
		    'more_info' => __('More information', 'neeed-dynamic-websites'),
		));
		
		wp_enqueue_script('jquery-ui-datetimepicker', plugins_url('js/jquery-ui-timepicker-addon.min.js', __FILE__), array('jquery', 'jquery-ui-datepicker'), DYNWEB_VERSION);
		
        wp_enqueue_script('dynweb_admin', plugins_url('js/dynweb_admin.js', __FILE__), array('jquery', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-ui-datetimepicker', 'dynweb_functions'), DYNWEB_VERSION);
        wp_localize_script('dynweb_admin', 'dynweb_admin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
		    'nonce_save_rule' => wp_create_nonce('dynweb_save_rule'),
		    'nonce_delete_rule' => wp_create_nonce('dynweb_delete_rule'),
		    'really_delete' => __('Do you really want to delete this rule?', 'neeed-dynamic-websites'),
		    'time' => __('Time', 'neeed-dynamic-websites'),
		    'hour' => __('Hour', 'neeed-dynamic-websites'),
		    'minute' => __('Minute', 'neeed-dynamic-websites'),
		    'now' => __('Now', 'neeed-dynamic-websites'),
		    'done' => __('Done', 'neeed-dynamic-websites'),
		    'weekdays_min' => $weekdays_min,
		    'months' => $months_zero_start
		));
    }
    
    
    static function admin_menu() {
        add_menu_page('NEEED', 'NEEED', DYNWEB_CAPABILTY, 'dynweb', '', 'dashicons-smiley');
        add_submenu_page('dynweb', __('Overview', 'neeed-dynamic-websites'), __('Overview', 'neeed-dynamic-websites'), DYNWEB_CAPABILTY, 'dynweb', array('Dynweb_Back_Rules', 'overview_page') );
        add_submenu_page('dynweb', __('New Rule', 'neeed-dynamic-websites'), __('New Rule', 'neeed-dynamic-websites'), DYNWEB_CAPABILTY, 'dynweb-editrule', array('Dynweb_Back_Rules', 'editrule_page') );
        add_submenu_page('dynweb', __('Settings', 'neeed-dynamic-websites'), __('Settings', 'neeed-dynamic-websites'), DYNWEB_CAPABILTY, 'dynweb-settings', array('Dynweb_Back_Settings', 'settings_page') );
        add_submenu_page('dynweb', __('Help', 'neeed-dynamic-websites'), __('Help', 'neeed-dynamic-websites'), DYNWEB_CAPABILTY, 'dynweb-help', array('Dynweb_Back', 'help_page') );
    }
    
    
    static function help_page() {
        echo '
            <div class="wrap dynweb_page">
                <h2>NEEED - '.__('Help', 'neeed-dynamic-websites').'</h2>

                <div class="dynweb_content">
                    <p>'.__('Our help videos are currently only available in German. If you have any questions, problems or wishes, write us an email at', 'neeed-dynamic-websites').' <a href="mailto:support@neeed.me">support@neeed.me</a></p>
                    
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/CbekR-DfAuw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/52WirQiNkOY" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/gBjl1DFZilw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/nCQ2RpXGhfU?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/RWJlAdSfTVc?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/oboSd9uM8KE?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                    <br><br>
                    <div><iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/N0B5rhdi1Zc?rel=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
                </div>
            </div>
        ';
    }
    
    
    static function admin_bar_menu($wp_admin_bar) {
    
		$wp_admin_bar->add_menu( array('id' => 'dynweb', 'title' => 'NEEED', 'href' => admin_url('admin.php?page=dynweb')) );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-reset', 'title' => __('Reset own visitor data', 'neeed-dynamic-websites'), 'parent' => 'dynweb', 'href' => '#') );
		
		$time_options = '';
		for ($i = 0 ; $i <= 23 ; $i++) $time_options .= '<option value="'.$i.'">'.$i.'</option>';
		
		$weekday_options = '';
		for ($i = 0 ; $i <= 6 ; $i++) $weekday_options .= '<option value="'.$i.'">'.self::$weekdays[$i].'</option>';
		
		$month_options = '';
		for ($i = 1 ; $i <= 12 ; $i++) $month_options .= '<option value="'.$i.'">'.self::$months[$i].'</option>';
		
		$wp_admin_bar->add_menu( array('id' => 'dynweb-situation', 'title' => __('Own situation', 'neeed-dynamic-websites'), 'parent' => 'dynweb') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-situation-datetime', 'title' => __('Date', 'neeed-dynamic-websites').' <input type="text" id="dynweb_admin_bar_datetime_text" class="dynweb_datetimepicker" value="">', 'parent' => 'dynweb-situation') );
		/*
		$wp_admin_bar->add_menu( array('id' => 'dynweb-situation-time', 'title' => __('Hour', 'neeed-dynamic-websites').' <select id="dynweb_admin_bar_select_time">'.$time_options.'</select>', 'parent' => 'dynweb-situation') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-situation-weekday', 'title' => __('Weekday', 'neeed-dynamic-websites').' <select id="dynweb_admin_bar_select_weekday">'.$weekday_options.'</select>', 'parent' => 'dynweb-situation') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-situation-month', 'title' => __('Month', 'neeed-dynamic-websites').' <select id="dynweb_admin_bar_select_month">'.$month_options.'</select>', 'parent' => 'dynweb-situation') );
		*/
		
		$wp_admin_bar->add_menu( array('id' => 'dynweb-visit-count', 'title' => __('Own history', 'neeed-dynamic-websites'), 'parent' => 'dynweb') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-visit-count-new', 'title' => __('New visitor', 'neeed-dynamic-websites'), 'parent' => 'dynweb-visit-count', 'href' => '#') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-visit-count-returning', 'title' => __('Returning visitor', 'neeed-dynamic-websites'), 'parent' => 'dynweb-visit-count', 'href' => '#') );
		
		$wp_admin_bar->add_menu( array('id' => 'dynweb-referrer', 'title' => __('Own source', 'neeed-dynamic-websites'), 'parent' => 'dynweb') );
		$wp_admin_bar->add_menu( array('id' => 'dynweb-referrer-text', 'title' => '<input type="text" id="dynweb_admin_bar_referrer_text" value="" size="30">', 'parent' => 'dynweb-referrer') );
		
		//check dynweb-pro-back.php for pro version only entries
		
		return $wp_admin_bar;
    }
    
    
    static function admin_notices() {
        global $pagenow;
        
        $meta_options = get_option('dynweb-meta-options');
		if ($meta_options['tutorial_active'] == 1) return;
        
        $current_screen = get_current_screen();
        if (method_exists($current_screen, 'is_block_editor') && $current_screen->is_block_editor()) return;

		if ($pagenow == 'edit.php' || $pagenow == 'post.php' || $pagenow == 'post-new.php') {
		    ob_start();
		    echo '
		        <br><br>
                <div class="dynweb_tabinfo">
                    '.__('<p>You will find the new tab "NEEED" in the divi builder. In this tab, you can choose a rule that controls the visibility of this element. No rule choosen = the element is always visible. Rule choosen = the element is only visible, if the rule is true. You can attach a rule to all divi elements. They apply to all child elements as well.</p>', 'neeed-dynamic-websites').'
                    <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="divi" style="cursor:pointer"></i>
                </div>
            ';
		    echo ob_get_clean();
		}
    }
    

}