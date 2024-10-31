<?php
/*
Plugin Name:    NEEED - Dynamic Websites
Plugin URI:     https://neeed.me
Description:    NEEED helps you to individually communicate with your visitors. Show dynamic content based on the situation, history and behavior of each visitor.
Version:        0.12.0
Author:         Sebastian Eisenbuerger, Jonas Breuer
Author URI:     https://neeed.me
Text Domain:    neeed-dynamic-websites
Min WP Version: 4.6
Max WP Version: 6.0
*/
if (!defined('ABSPATH')) die;

define('DYNWEB_VERSION', '0.12.0');


Dynweb::prepare();


class Dynweb {

    static function prepare() {
    
        define('DYNWEB_CAPABILTY', 'manage_options');
        define('DYNWEB_API_URL', 'https://dynwebapi.j-breuer.de/api.php');
        define('DYNWEB_DEMO', false);
        
        $options = get_option('dynweb-options');
        
        if (is_dir(plugin_dir_path(__FILE__).'pro') && isset($options['license_key'])) define('DYNWEB_PRO', true);
        else define('DYNWEB_PRO', false);
    
        register_activation_hook(__FILE__, array('Dynweb', 'activation'));
        register_deactivation_hook(__FILE__, array('Dynweb', 'deactivation'));
        register_uninstall_hook(__FILE__, array('Dynweb', 'uninstall'));
        
        add_action('init', array('Dynweb', 'init'));
        add_filter('pre_set_site_transient_update_plugins', array('Dynweb', 'check_version'));  
        add_filter('plugins_api', array('Dynweb', 'get_new_version_info'), 10, 3);

        
        include_once('builders/dynweb-divi.php');
        
        if (DYNWEB_PRO) {
            include_once('pro/neeed-dynweb-pro.php');
            include_once('pro/dynweb-pro-front.php');
            include_once('pro/dynweb-pro-back.php');
            include_once('pro/builders/dynweb-pro-divi.php');
        }
        
        include_once('dynweb-front.php');
        include_once('dynweb-back.php');
        include_once('dynweb-pointer.php');
    }
    

    static function activation() {
		global $wpdb;
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$version = get_option('dynweb-version', '0.0.0');
		
		if ($version == '0.0.0') add_action('admin_notices', array('Dynweb', 'activation_message'));
		else add_action('admin_notices', array('Dynweb', 'update_message'));
		
		$sql = 'CREATE TABLE '.$wpdb->prefix.'dynweb_rules (
                rule_id int(10) unsigned NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                PRIMARY KEY  (rule_id)
            );';
        dbDelta($sql);
        
        $sql = 'CREATE TABLE '.$wpdb->prefix.'dynweb_conditions (
                condition_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                rule_id int(10) UNSIGNED NOT NULL,
                type varchar(255) NOT NULL,
                position smallint(6) NOT NULL,
                param1 varchar(255) NOT NULL,
                param2 varchar(255) NOT NULL,
                param3 varchar(255) NOT NULL,
                PRIMARY KEY  (condition_id)
                );';
        dbDelta($sql);
        
        
        //change system of radio button conditions to not waste params
        if (version_compare($version, '0.5.0', '<')) {
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "new" where type = "visit_count" and param1 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "further", param2 = 0 where type = "visit_count" and param2 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "video" where type = "perception" and param1 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "image", param2 = 0 where type = "perception" and param2 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "text", param3 = 0 where type = "perception" and param3 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "clear" where type = "weather" and param1 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "clouds", param2 = 0 where type = "weather" and param2 = 1');
            $wpdb->query('update '.$wpdb->prefix.'dynweb_conditions set param1 = "rain", param3 = 0 where type = "weather" and param3 = 1');
        }
        
        $sql = 'select rule_id from '.$wpdb->prefix.'dynweb_rules';
        $existing_rule_id = $wpdb->get_var($sql);
        if (empty($existing_rule_id)) {
        
            $sql = '
                insert into '.$wpdb->prefix.'dynweb_rules 
                    (rule_id, name) 
                values 
                    (1, \''.__('Situation: Morning (04:00 - 11:00)', 'neeed-dynamic-websites').'\'),
                    (2, \''.__('Situation: Day (11:00 - 18:00)', 'neeed-dynamic-websites').'\'),
                    (3, \''.__('Situation: Evening (18:00 - 21:00)', 'neeed-dynamic-websites').'\'),
                    (4, \''.__('Situation: Night (21:00 - 04:00)', 'neeed-dynamic-websites').'\'),
                    (5, \''.__('History: New Visitor', 'neeed-dynamic-websites').'\'),
                    (6, \''.__('History: Returning Visitor', 'neeed-dynamic-websites').'\'),
                    (7, \''.__('Source: Facebook', 'neeed-dynamic-websites').'\'),
                    (8, \''.__('Source: Google', 'neeed-dynamic-websites').'\')
            ';
            $wpdb->query($sql);
            
            $sql = '
                insert into '.$wpdb->prefix.'dynweb_conditions
                    (condition_id, rule_id, type, position, param1, param2, param3)
                values
                    (1, 1, \'time\', 1, 4, 11, 0),
                    (2, 2, \'time\', 1, 11, 18, 0),
                    (3, 3, \'time\', 1, 18, 21, 0),
                    (4, 4, \'time\', 1, 21, 4, 0),
                    (5, 5, \'visit_count\', 1, \'new\', 0, 0),
                    (6, 6, \'visit_count\', 1, \'further\', 0, 0),
                    (7, 7, \'referrer\', 1, \'facebook.com\', \'contains\', 0),
                    (8, 8, \'referrer\', 1, \'google.\', \'contains\', 0)
                    
            ';
             $wpdb->query($sql);
             
        }
        
        //standard options if this is a new installation
        $options = get_option('dynweb-options');
		if (!$options) $options = array();
		if (!isset($options['blue_filter'])) $options['blue_filter'] = 0;
		if (!isset($options['temperature_filter'])) $options['temperature_filter'] = 0;
		if (!isset($options['update_invisible_only'])) $options['update_invisible_only'] = 0;
		if (!isset($options['keep_rules'])) $options['keep_rules'] = 1;
		
		//change filters to percent
		if (version_compare($version, '0.12.0', '<')) {
			if ($options['blue_filter'] == 1) $options['blue_filter'] = 100;
			if ($options['temperature_filter'] == 1) $options['temperature_filter'] = 100;
		}
		
		update_option('dynweb-options', $options);
		
		$default_meta_options = array('templates_installed' => false, 'tutorial_active' => 0);
		$meta_options = get_option('dynweb-meta-options');
		if (!$meta_options) $meta_options = $default_meta_options;
		$meta_options = array_merge($default_meta_options, $meta_options);
		update_option('dynweb-meta-options', $meta_options);
		
		do_action('dynweb_activation');
        
    }
    
    
    static function deactivation() {
		
	}
	
	
	static function uninstall() {
		global $wpdb;
		delete_option('dynweb-version');
		$options = get_option('dynweb-options');
		
		if (!isset($options['keep_rules']) || $options['keep_rules'] != 1) {
		
            delete_option('dynweb-meta-options');
            delete_option('dynweb-options');
        
            $sql = 'DROP TABLE IF EXISTS '.$wpdb->prefix.'dynweb_rules;';
            $wpdb->query($sql);
            $sql = 'DROP TABLE IF EXISTS '.$wpdb->prefix.'dynweb_conditions;';
            $wpdb->query($sql);
            
            do_action('dynweb_delete_all_rules');
        }
	}
	
	
	static function check_license_key($license_key) {
		$license_key_hash = md5($license_key);
		$http_answer = wp_remote_post(DYNWEB_API_URL, array(
			'body' => array('action' => 'check_license_key', 'license_key' => $license_key_hash, 'referer' => $_SERVER['HTTP_HOST'], 'plugin_version' => DYNWEB_VERSION)
		));
		
		if (is_wp_error($http_answer) || $http_answer['response']['code'] != 200) return false;
		else return $http_answer['body'];
	}
	
	
	static function init() {
	    $version = get_option('dynweb-version', '0.0.0');
	    $pro = get_option('dynweb-pro', false);
		
		if ($version != DYNWEB_VERSION || $pro != DYNWEB_PRO) {
			self::activation();
			update_option('dynweb-version', DYNWEB_VERSION);
			update_option('dynweb-pro', DYNWEB_PRO);
		}
		
		$options = get_option('dynweb-options');
		if (isset($options['license_key']) && !empty($options['license_key']) && !DYNWEB_PRO) {
		    add_action('admin_notices', array('Dynweb', 'update_to_pro_message'));
		}
		
		include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$theme = wp_get_theme(); // gets the current theme
        if (!is_plugin_active('divi-builder/divi-builder.php') && $theme->name != 'Divi' && $theme->parent_theme != 'Divi' && $theme->name != 'Extra' && $theme->parent_theme != 'Extra') {
	        add_action('admin_notices', array('Dynweb', 'divi_missing_message'));
        }
		
	}
	
	
	static function check_version($transient) {
	
		if (empty($transient->checked)) {  
			return $transient;  
		}
		
		$options = get_option('dynweb-options');
		$license_valid = false;
		
		if (isset($options['license_key']) && !empty($options['license_key'])) {
			$check_result = self::check_license_key($options['license_key']);
			$obj_result = json_decode($check_result);
			
			if ($obj_result->status == 'success') {
			    $license_valid = true;
			}
			
			elseif ($obj_result->status == 'error') {
			    unset($options['license_key']);
			    update_option('dynweb-options', $options);
			}
		}
		
		$updating_to_pro = !DYNWEB_PRO && $license_valid;
		
		$http_answer = wp_remote_post(DYNWEB_API_URL, array('body' => array('action' => 'version', 'plugin_version' => DYNWEB_VERSION, 'referer' => $_SERVER['HTTP_HOST'])));
		if (is_wp_error($http_answer) || $http_answer['response']['code'] != 200) $new_version = DYNWEB_VERSION;
		else $new_version = $http_answer['body'];
		
	    if ( DYNWEB_PRO && version_compare(DYNWEB_VERSION, $new_version, '<') || $updating_to_pro ) {  
            $obj = new stdClass();  
            $obj->slug = 'neeed-dynamic-websites';
            $obj->new_version = $new_version;  
            $obj->url = DYNWEB_API_URL; 
            $obj->package = DYNWEB_API_URL . '?action=download&license_key=' . md5($options['license_key']).'&plugin_version='.DYNWEB_VERSION.'&referer='.$_SERVER['HTTP_HOST'];
            $transient->response['neeed-dynamic-websites/neeed-dynamic-websites.php'] = $obj;  
        }
	
        return $transient;
    }  


	static function get_new_version_info($false, $action, $arg) {
		
		if (isset($arg->slug) && $arg->slug == 'neeed-dynamic-websites' && DYNWEB_PRO) {  
			
			$http_answer = wp_remote_post(DYNWEB_API_URL, array('body' => array('action' => 'info', 'plugin_version' => DYNWEB_VERSION, 'referer' => $_SERVER['HTTP_HOST'])));  
			if (is_wp_error($http_answer) || $http_answer['response']['code'] != 200) return $false;
			
			$information = unserialize($http_answer['body']);
			return $information;  
		}  
		
		return $false;  
	}
	
	
	static function activation_message() {
        ob_start();
        echo '<div class="dynweb_tabinfo">'.__('<h2>Welcome to NEEED :-)</h2><p>Congratulations to your decision to take care of the individual needs of your visitors!<br>Have a look around the plugin and start by using the standard rules, which we have already created for you.<br>Edit an element in one of your posts or pages with the Divi Builder. You will find the new tab "NEEED" in the builder. In this tab, you can give each element a rule, that controls the visibility of the element. As an example, you could show a special greeting to returning visitors. Or you could make sure that your background images change with the time of the day. Or you can find one of the many other possibilities, how you can react to the situation and behavior of your visitors.</p><p>Start our tutorial to be guided through the first steps with NEEED. You can also start the tutorial later in the NEEED settings.</p><p class="dynweb_page"><input type="button" class="dynweb_save dynweb_start_tutorial" value="Start Tutorial"></p>', 'neeed-dynamic-websites').'</div><script>refresh_builders();</script>';
		echo ob_get_clean();
	}
	
	
	static function update_message() {
        ob_start();
		echo '
		    <div class="dynweb_tabinfo">
                <h3>NEEED '.DYNWEB_VERSION.'</h3>
                '.__('<p>Hey!</p><p>Thank you for updating to the new version of NEEED!</p><p>These are the changes in the new version:</p>', 'neeed-dynamic-websites').'
                <ol>
                    <li>'.__('You can now create rules to make elements visible at a specific temperature.', 'neeed-dynamic-websites').'</li>
					<li>'.__('You can now adjust the intensity of the blue light and temperature filter.', 'neeed-dynamic-websites').'</li>
                </ol>
            </div>
        ';
		echo ob_get_clean();
	}
	
	
	static function update_to_pro_message() {
        ob_start();
		echo '<div class="dynweb_tabinfo">'.__('<h2>Update to NEEED Pro</h2><p>You entered a valid license key, but you didn\'t download the pro version yet. Please go to the <a href="update-core.php">update page</a> and update to the pro version. It might take up to 5 minutes, until WordPress informs you about the new version.</p>', 'neeed-dynamic-websites').'</div>';
		echo ob_get_clean();
	}
	
	
	static function divi_missing_message() {
        ob_start();
		echo '<div class="dynweb_tabinfo">'.__('<h2>Divi Builder not found</h2><p>NEEED currently needs the Divi Builder to connect rules to elements. Please install and activate the <a href="https://www.elegantthemes.com/gallery/divi/" target="_blank">Divi Builder</a> or the Divi Theme, to use NEEED.</p>', 'neeed-dynamic-websites').'</div>';
		echo ob_get_clean();
	}
	


}