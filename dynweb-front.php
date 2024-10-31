<?php
if (!defined('ABSPATH')) die;

Dynweb_Front::prepare();


class Dynweb_Front {

    static $all_rules;
    
    
    static function prepare() {
        add_action('wp_enqueue_scripts', array('Dynweb_Front', 'enqueue'));
        if (isset($_GET['dynweb_debug'])) add_filter('the_content', array('Dynweb_Front', 'debug_content'));
    }
    
    
    static function enqueue() {
    
        $options = get_option('dynweb-options');
    
        //todo: get this json string already when saving a rule, so we don't need to create it for every single visitor
    
        global $wpdb;
        $sql = 'select * from '.$wpdb->prefix.'dynweb_rules';
        $rules = $wpdb->get_results($sql);
        $all_rules = array();
        foreach ($rules as $rule) {
            $rule_conditions = array();
            $remote_data = array();
            $sql = $wpdb->prepare('select * from '.$wpdb->prefix.'dynweb_conditions where rule_id = %d', $rule->rule_id);
            $conditions = $wpdb->get_results($sql);
            foreach ($conditions as $condition) {
                $rule_conditions[] = array('type' => $condition->type, 'param1' => $condition->param1, 'param2' => $condition->param2, 'param3' => $condition->param3);
                if ($condition->type == 'weather' || $condition->type == 'temperature') $remote_data[] = 'weather';
            }
            $all_rules[] = array('rule_id' => $rule->rule_id, 'conditions' => $rule_conditions, 'remote_data' => $remote_data);
        }
        self::$all_rules = $all_rules;
        $json_all_rules = json_encode($all_rules);
        
        wp_enqueue_style('dynweb', plugins_url('css/dynweb.css', __FILE__), array(), DYNWEB_VERSION);
    
        wp_enqueue_script('dynweb_functions', plugins_url('js/dynweb_functions.js', __FILE__), array(), DYNWEB_VERSION);
        wp_enqueue_script('dynweb', plugins_url('js/dynweb.js', __FILE__), array('jquery', 'dynweb_functions'), DYNWEB_VERSION);
        wp_localize_script('dynweb', 'dynweb', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
        wp_add_inline_script('dynweb', 'var dynweb_global_all_rules = \''.$json_all_rules.'\'');
        
        
        if (current_user_can(DYNWEB_CAPABILTY)) {
            Dynweb_Back::enqueue();
        }
        
    }
    

    static function debug_content($content) {
        if (isset($_COOKIE['dynweb'])) {
            $cookie_info = json_decode(stripslashes($_COOKIE['dynweb']));
            $content .= 'cookie_info:<pre>' . print_r($cookie_info, true) . '</pre>';
        }
        return $content;
    }




}