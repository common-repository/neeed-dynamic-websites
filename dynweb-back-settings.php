<?php
if (!defined('ABSPATH')) die;

Dynweb_Back_Settings::prepare();


class Dynweb_Back_Settings {

    static function prepare() {
        add_action('admin_init', array('Dynweb_Back_Settings', 'add_settings'));
    }
    
    
    static public function add_settings() {
		register_setting('dynweb-options', 'dynweb-options', array('Dynweb_Back_Settings', 'validate_settings'));
		add_settings_section('dynweb-options-main', '', '', 'dynweb-options');
		add_settings_field('dynweb-license-key', __('License key', 'neeed-dynamic-websites'), array('Dynweb_Back_Settings', 'add_license_key_field'), 'dynweb-options', 'dynweb-options-main');
		add_settings_field('dynweb-blue-filter', __('Intensity blue light filter during the night', 'neeed-dynamic-websites'), array('Dynweb_Back_Settings', 'add_blue_filter_field'), 'dynweb-options', 'dynweb-options-main');
		add_settings_field('dynweb-temperature-filter', __('Intensity light filter to counter current temperature', 'neeed-dynamic-websites'), array('Dynweb_Back_Settings', 'add_temperature_filter_field'), 'dynweb-options', 'dynweb-options-main');
		add_settings_field('dynweb-update-invisible-only', __('Deactivate live updates for visible elements', 'neeed-dynamic-websites'), array('Dynweb_Back_Settings', 'add_update_invisible_only_field'), 'dynweb-options', 'dynweb-options-main');
		add_settings_field('dynweb-keep-rules', __('Keep rules when deleting the plugin', 'neeed-dynamic-websites'), array('Dynweb_Back_Settings', 'add_keep_rules_field'), 'dynweb-options', 'dynweb-options-main');
	}
	
	
	static function add_license_key_field() {
		$options = get_option('dynweb-options');
		if (!isset($options['license_key'])) $options['license_key'] = '';
		echo '<input type="password" name="dynweb-options[license_key]" size="40" value="'.$options['license_key'].'" /> ';
	}
	
	
	static function add_blue_filter_field() {
		$options = get_option('dynweb-options');
		if (!isset($options['blue_filter'])) $options['blue_filter'] = 0;
		if (DYNWEB_PRO) echo '<input type="number" name="dynweb-options[blue_filter]" value="'.esc_attr($options['blue_filter']).'" min="0" max="100" style="width:70px" /> %';
		else echo '<input type="number" value="'.esc_attr($options['blue_filter']).'" style="width:70px" disabled> %<br>'.__('Only in the Pro version', 'neeed-dynamic-websites').'<br><a href="https://neeed.me" target="_blank">'.__('More information', 'neeed-dynamic-websites').'</a>';
	}
	
	
	static function add_temperature_filter_field() {
		$options = get_option('dynweb-options');
		if (!isset($options['temperature_filter'])) $options['temperature_filter'] = 0;
		if (DYNWEB_PRO) echo '<input type="number" name="dynweb-options[temperature_filter]" value="'.esc_attr($options['temperature_filter']).'" min="0" max="100" style="width:70px" /> %';
		else echo '<input type="number" value="'.esc_attr($options['temperature_filter']).'" style="width:70px" disabled> %<br>'.__('Only in the Pro version', 'neeed-dynamic-websites').'<br><a href="https://neeed.me" target="_blank">'.__('More information', 'neeed-dynamic-websites').'</a>';
	}
	
	
	static function add_update_invisible_only_field() {
		$options = get_option('dynweb-options');
		if (!isset($options['update_invisible_only'])) $options['update_invisible_only'] = 0;
		if (DYNWEB_PRO) echo '<input type="checkbox" name="dynweb-options[update_invisible_only]" value="1" '.checked($options['update_invisible_only'], 1, false).' /> ';
		else echo '<input type="checkbox" disabled><br>'.__('Only in the Pro version', 'neeed-dynamic-websites').'<br><a href="https://neeed.me" target="_blank">'.__('More information', 'neeed-dynamic-websites').'</a>';
	}
	
	
	static function add_keep_rules_field() {
		$options = get_option('dynweb-options');
		if (!isset($options['keep_rules'])) $options['keep_rules'] = 0;
		echo '<input type="checkbox" name="dynweb-options[keep_rules]" value="1" '.checked($options['keep_rules'], 1, false).' /> ';
	}
    
    
    static function settings_page() {
        include_once 'options-head.php'; //we need this to show error messages
		$options = get_option('dynweb-options');
		?>
		<div class="wrap dynweb_page">
            <h2>NEEED <?php _e('Settings', 'neeed-dynamic-websites'); ?></h2>
            <div class="dynweb_content">
                <form action="options.php" method="post">
                <?php settings_fields('dynweb-options'); ?>
                <?php do_settings_sections('dynweb-options'); ?>
                <input name="Submit" type="submit" class="dynweb_save" value="<?php _e('Save', 'neeed-dynamic-websites'); ?>" />
                </form>
                <p>&nbsp;</p>
                <p><input type="button" class="dynweb_save dynweb_reset_tabinfo" value="<?php _e('Show all info boxes again', 'neeed-dynamic-websites'); ?>"></p>
                <p><input type="button" class="dynweb_save dynweb_start_tutorial" value="<?php _e('Restart tutorial', 'neeed-dynamic-websites'); ?>"></p>
            </div>
            
		</div>
		<?php
    }
    
    
    static function validate_settings($input) {
		$whitelist = array();
		
		if ($input['license_key'] != '' && ctype_alnum($input['license_key'])) {
			$check_result = Dynweb::check_license_key($input['license_key']);
			$obj_result = json_decode($check_result);
			if ($obj_result->status == 'error') add_settings_error('dynweb-options', 'dynweb-error-license-key', __('Invalid license key. Please check the key and enter it again. If you are sure that you entered the correct key, send us a message at', 'neeed-dynamic-websites'). ' <a href="mailto:support@neeed.me">support@neeed.me</a>');
			elseif ($obj_result->status == 'success') {
				$whitelist['license_key'] = $input['license_key'];
				$whitelist['orderid'] = $obj_result->payload->orderid;
				$whitelist['domains'] = $obj_result->payload->domains;
				$whitelist['valid_till'] = $obj_result->payload->valid_till;
			}
		}
		
		
		if (isset($input['blue_filter'])) $whitelist['blue_filter'] = min((int)$input['blue_filter'], 100);
		else $whitelist['blue_filter'] = 0;
		
		if (isset($input['temperature_filter'])) $whitelist['temperature_filter'] = min((int)$input['temperature_filter'], 100);
		else $whitelist['temperature_filter'] = 0;
		
		if (isset($input['update_invisible_only']) && $input['update_invisible_only'] == 1) $whitelist['update_invisible_only'] = 1;
		else $whitelist['update_invisible_only'] = 0;
		
		if (isset($input['keep_rules']) && $input['keep_rules'] == 1) $whitelist['keep_rules'] = 1;
		else $whitelist['keep_rules'] = 0;
		
		return $whitelist;
	}
    
    
    
}