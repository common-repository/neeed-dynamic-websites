<?php
if (!defined('ABSPATH')) die;


Dynweb_Divi::prepare();


class Dynweb_Divi {

    static $modules = array('accordion', 'accordion_item', 'audio', 'counters', 'counter', 'blob', 'blurb', 'button', 'circle_counter', 'code', 'comments', 'contact_form', 'contact_field', 'countdown_timer', 'cta', 'divider', 'filterable_portfolio', 'fullwidth_code', 'fullwidth_header', 'fullwidth_image', 'fullwidth_slider', 'fullwidth_image', 'fullwidth_map', 'fullwidth_menu', 'fullwidth_portfolio', 'fullwidth_post_slider', 'fullwidth_post_title', 'gallery', 'image', 'login', 'map', 'map_pin', 'number_counter', 'portfolio', 'post_slider', 'post_nav', 'post_title', 'pricing_tables', 'pricing_table', 'row', 'search', 'section', 'shop', 'sidebar', 'signup', 'signup_custom_field', 'slider', 'slide', 'social_media_follow', 'social_media_follow_network', 'tabs', 'tab', 'team_member', 'testimonial', 'text', 'toggle', 'video', 'video_slider', 'video_slider_item');


    static function prepare() {
        add_filter('et_builder_main_tabs', array('Dynweb_Divi', 'main_tabs'));
        
        foreach (self::$modules as $module) {
             add_filter('et_pb_all_fields_unprocessed_et_pb_'.$module, array('Dynweb_Divi', 'fields_unprocessed'));
        }
        
        add_filter('et_module_shortcode_output', array('Dynweb_Divi', 'shortcode_output'), 10, 3);
    }
    
    

    static function main_tabs($tabs) {
        $tabs['dynamic'] = esc_html__('NEEED', 'neeed-dynamic-websites');
        return $tabs;
    }
    
    
    
    static function fields_unprocessed($fields) {
        global $wpdb;
        
        
        $sql = 'select * from '.$wpdb->prefix.'dynweb_rules order by name asc';
        $rules = $wpdb->get_results($sql);
        $rule_options = array('always' => esc_html__('- always visible -', 'neeed-dynamic-websites'));
        foreach ($rules as $rule) {
            $rule_options['id_'.$rule->rule_id] = esc_attr($rule->name);
        }
        
        $fields['dynweb_rule'] = array(
            'label' => esc_html__('Rule', 'neeed-dynamic-websites'),
            'option_category' => 'configuration',
            'tab_slug'        => 'dynamic',
            'type'  => 'select',
            'options' => $rule_options
        );
    
        return $fields;
    }
    
    
    
    static function shortcode_output($output, $render_slug, $module) {
        if ( is_array($output) ) return $output;
        
        if (isset($module->props['dynweb_rule']) && !empty($module->props['dynweb_rule']) && $module->props['dynweb_rule'] != 'always') {
            $output = substr_replace($output, '<div style="display:none" data-dynweb-rule="'.$module->props['dynweb_rule'].'"', 0, 4);
        }
    
        return $output;
    }
    
    
}