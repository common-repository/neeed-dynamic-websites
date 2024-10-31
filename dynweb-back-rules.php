<?php
if (!defined('ABSPATH')) die;

Dynweb_Back_Rules::prepare();


class Dynweb_Back_Rules {

    static function prepare() {
        add_action('wp_ajax_dynweb_save_rule', array('Dynweb_Back_Rules', 'save_rule'));
        add_action('wp_ajax_dynweb_delete_rule', array('Dynweb_Back_Rules', 'delete_rule'));
    }
    
    
    static function overview_page() {
        global $wpdb;
        
        $sql = 'select * from '.$wpdb->prefix.'dynweb_rules order by name asc';
        $rules = $wpdb->get_results($sql);
        $rule_rows = '';
        foreach ($rules as $rule) {
            $rule_rows .= '<tr><td><a href="'.admin_url('admin.php?page=dynweb-editrule&id='.$rule->rule_id).'">'.esc_html($rule->name).'</a></td><td class="icons"><i class="fa fa-minus-circle dynweb_delete_rule" data-ruleid="'.$rule->rule_id.'" data-rulename="'.esc_attr($rule->name).'" style="cursor:pointer" title="'.__('Delete Rule', 'neeed-dynamic-websites').'"></td></tr>';
        }
        
        echo '
            <div class="wrap dynweb_page">
                <h2>NEEED</h2>
                <div class="dynweb_tabinfo">
                    '.__('<p>You can find the rules we\'ve already created for you on this page. Use the button at the top to create a new rule. Delete rules with the <i class="fa fa-minus-circle"></i> icon. (Warning: When deleting a rule, all affected elements will be shown to all visitors.</p>', 'neeed-dynamic-websites').'
                    <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="overview" style="cursor:pointer"></i>
                </div>
                <br>
                <div class="dynweb_header">
                    <p><a href="'.admin_url('admin.php?page=dynweb-editrule').'" class="dynweb_button">'.__('Create New Rule', 'neeed-dynamic-websites').'</a></p>
                </div>
                <div class="dynweb_content">
                    <table class="dynweb_table">
                        <tr><th>'.__('RULE NAME', 'neeed-dynamic-websites').'</th><td class="icons"></td></tr>
                        '.$rule_rows.'
                    </table>
                </div>
            </div>
        ';
    }
    
    
    static function editrule_page() {
        global $wpdb;
    
        if (!isset($_GET['id'])) {
            $headline = __('New Rule', 'neeed-dynamic-websites');
            $rule_id = 0;
            $rule_name = '';
            $conditions_json = '[]';
        }
        else {
            $rule_id = (int)$_GET['id'];
            $headline = __('Edit Rule', 'neeed-dynamic-websites');
            $sql = $wpdb->prepare('select name from '.$wpdb->prefix.'dynweb_rules where rule_id = %d', $rule_id);
            $rule_name = esc_attr($wpdb->get_var($sql));
            $sql = $wpdb->prepare('select * from '.$wpdb->prefix.'dynweb_conditions where rule_id = %d order by type asc, position asc', $rule_id);
            $conditions = $wpdb->get_results($sql);
            $conditions_json = json_encode($conditions);
        }
    
        echo '
            <div class="wrap dynweb_page">
                <h2>NEEED - '.$headline.'</h2>
                <p><input type="text" class="dynweb_rule_name" placeholder="'.__('RULE NAME', 'neeed-dynamic-websites').'" size="40" value="'.$rule_name.'" data-id="'.$rule_id.'"></p>
                <p><input type="button" class="dynweb_save dynweb_save_rule" value="'.__('Save Changes', 'neeed-dynamic-websites').'"></p>
                <div class="dynweb_tabs">
                    <ul>
                        <li><a href="#dynweb_tab_situation">'.__('Situation', 'neeed-dynamic-websites').'</a></li>
                        <li><a href="#dynweb_tab_history">'.__('History', 'neeed-dynamic-websites').'</a></li>
                        <li><a href="#dynweb_tab_referrer">'.__('Source', 'neeed-dynamic-websites').'</a></li>
                        <li><a href="#dynweb_tab_behaviour">'.__('Behavior', 'neeed-dynamic-websites').'</a></li>
                        <li><a href="#dynweb_tab_perception">'.__('Perception', 'neeed-dynamic-websites').'</a></li>
                    </ul>
                    <div id="dynweb_tab_situation">
                        <div class="dynweb_tabinfo">
                        
                            '.__('<p>Use this area to create conditions depending on the situation of the visitor. How about a motivational quote on Monday? Or an inviting background picture depending on the time of day? Select a category first. Use the <i class="fa fa-plus-circle"></i> and <i class="fa fa-minus-circle"></i> buttons to add and delete conditions. This way, you can also create complex rules.</p>', 'neeed-dynamic-websites').'
                            <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="situation" style="cursor:pointer"></i>
                        </div>
                        <div class="dynweb_accordion">
                            <h3>'.__('Hour', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="time"></div>
                            <h3>'.__('Weekday', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="weekday"></div>
                            <h3>'.__('Month', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="month"></div>
                            <h3>'.__('Date', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="datetime"></div>
                            <h3>'.__('Weather', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="weather"></div>
							 <h3>'.__('Temperature', 'neeed-dynamic-websites').'</h3>
                            <div class="dynweb_conditions_container" data-type="temperature"></div>
                        </div>
                    </div>
                    <div id="dynweb_tab_history">
                        <div class="dynweb_tabinfo">
                            '.__('<p>How would it feel, if a person you already know, would ask you the same questions over and over again every time you meet them? In this area, you can create conditions, so new visitors who are on your site for the first time, get different information than returning visitors.</p>', 'neeed-dynamic-websites').'
                            <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="history" style="cursor:pointer"></i>
                        </div>
                        <div class="dynweb_conditions_container" data-type="visit_count"></div>
                        <!--<div class="dynweb_conditions_container" data-type="visit_time"></div>-->
                    </div>
                    <div id="dynweb_tab_referrer">
                        <div class="dynweb_tabinfo">
                            '.__('<p>Your visitors will find your site in different ways. In this area, you can show them different content depending on how they found you.</p>', 'neeed-dynamic-websites').'
                            <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="referrer" style="cursor:pointer"></i>
                        </div>
                        <div class="dynweb_conditions_container" data-type="referrer"></div>
                    </div>
                    <div id="dynweb_tab_behaviour">
                        <div class="dynweb_tabinfo">
                            '.__('<p>The behavior of your visitors can tell you a lot about them. If a visitor interacts with a specific element, you can react and show them different content. For example, if someone hovers over a "Buy" button put doesn\'t press it, it\'s a clear sign that they want to use your offer, but are unsure if it\'s really the right thing for them. Therefore, you could show them a few reviews, testimonials and certificates.</p>', 'neeed-dynamic-websites').'
                            <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="behaviour" style="cursor:pointer"></i>
                        </div>
                        <div class="dynweb_conditions_container" data-type="flag_custom"></div>
                    </div>
                    <div id="dynweb_tab_perception">
                        <div class="dynweb_tabinfo">
                            '.__('<p>Everyone processes information in a different way and prefers a different type of media. In this area, you can make sure that everyone gets your information in the format that\'s best for them. Video, Image or Text.</p>', 'neeed-dynamic-websites').'
                            <i class="fa fa-times-circle dynweb_close_tabinfo" data-tab="perception" style="cursor:pointer"></i>
                        </div>
                        <div class="dynweb_conditions_container" data-type="perception"></div>
                    </div>
                </div>
                <p><input type="button" class="dynweb_save dynweb_save_rule" value="'.__('Save Changes', 'neeed-dynamic-websites').'"></p>
            </div>
            <script>
                var dynweb_global_conditions = \''.$conditions_json.'\';
            </script>
        ';
    
    }
    
    
    static function save_rule() {
        global $wpdb;
        check_ajax_referer('dynweb_save_rule', 'nonce'); 
        if (!current_user_can(DYNWEB_CAPABILTY) || !isset($_POST['rule']) || !is_array($_POST['rule'])) die;
        
        if (!isset($_POST['rule']['rule_name']) || empty($_POST['rule']['rule_name'])) $_POST['rule']['rule_name'] = 'unbenannt';
        $rule_name = sanitize_text_field(stripslashes($_POST['rule']['rule_name']));
        
        if ($_POST['rule']['rule_id'] == 0) {
            $sql = $wpdb->prepare('insert into '.$wpdb->prefix.'dynweb_rules set name = %s', $rule_name);
            $wpdb->query($sql);
            $rule_id = $wpdb->insert_id;
        }
        else {
            $rule_id = (int)$_POST['rule']['rule_id'];
            $sql = $wpdb->prepare('update '.$wpdb->prefix.'dynweb_rules set name = %s where rule_id = %d limit 1', $rule_name, $rule_id);
            $wpdb->query($sql);
            $sql = $wpdb->prepare('delete from '.$wpdb->prefix.'dynweb_conditions where rule_id = %d', $rule_id);
            $wpdb->query($sql);
        }
        
        foreach ($_POST['rule']['conditions'] as $condition) {
        
            $condition = array_map('sanitize_text_field', $condition);
        
            if ($condition['type'] == 'flag_custom') {
                $sql = $wpdb->prepare('select element_id from '.$wpdb->prefix.'dynweb_elements where name = %s', $condition['param1']);
                $element_id = $wpdb->get_var($sql);
                if (empty($element_id)) {
                    $sql = $wpdb->prepare('insert into '.$wpdb->prefix.'dynweb_elements set name = %s', $condition['param1']);
                    $wpdb->query($sql);
                    $element_id = $wpdb->insert_id;
                }
                //$condition['param1'] .= '~'.$element_id;
            }
        
            $sql = $wpdb->prepare('insert into '.$wpdb->prefix.'dynweb_conditions set rule_id = %d, type = %s, position = %d, param1 = %s, param2 = %s, param3 = %s', $rule_id, $condition['type'], $condition['position'], $condition['param1'], $condition['param2'], $condition['param3']);
            $wpdb->query($sql);
            
        }
        
        echo $rule_id;
        die;
    }
    
    
    static function delete_rule() {
        global $wpdb;
        check_ajax_referer('dynweb_delete_rule', 'nonce'); 
        if (!current_user_can(DYNWEB_CAPABILTY) || !isset($_POST['rule_id']) || !ctype_digit($_POST['rule_id'])) die;
        $rule_id = (int)$_POST['rule_id'];
        
        $sql = $wpdb->prepare('delete from '.$wpdb->prefix.'dynweb_rules where rule_id = %d limit 1', $rule_id);
        $wpdb->query($sql);
        $sql = $wpdb->prepare('delete from '.$wpdb->prefix.'dynweb_conditions where rule_id = %d', $rule_id);
        $wpdb->query($sql);
        
        die;
    }


}