function dynweb_add_condition (type, param1='', param2='', param3='') {
    var conditions_container = jQuery('.dynweb_conditions_container[data-type="'+type+'"');
    var is_first = conditions_container.find('.dynweb_condition').length == 0;
    
    if (type == 'time') {
        let time_from_options_html = '';
        let time_till_options_html = '';
        for (let i = 0 ; i <= 23 ; i++) {
            time_from_options_html += '<option';
            time_till_options_html += '<option';
            if (i == param1 && param1.length > 0) time_from_options_html += ' selected';
            if (i == param2 && param2.length > 0) time_till_options_html += ' selected';
            time_from_options_html += '>'+i+'</option>';
            time_till_options_html += '>'+i+'</option>';
        }
        let pretext = is_first ? dynweb_admin_functions.visible_from_time : dynweb_admin_functions.and_visible_from_time;
        let remove_button = is_first ? '' : '<i class="fa fa-minus-circle dynweb_remove_condition" style="cursor:pointer">';
        let html = '<p class="dynweb_condition">'+pretext+' <select class="dynweb_param1"><option value="-1"></option>'+time_from_options_html+'</select> '+dynweb_admin_functions.oclock_till+' <select class="dynweb_param2"><option value="-1"></option>'+time_till_options_html+'</select> '+dynweb_admin_functions.oclock+' <i class="fa fa-plus-circle dynweb_add_condition" style="cursor:pointer"></i> '+remove_button+'</p>';
        conditions_container.append(html);
        return true;
    }
    
    if (type == 'weekday') {
        let weekday_options = [ dynweb_admin_functions.weekdays[1], dynweb_admin_functions.weekdays[2], dynweb_admin_functions.weekdays[3], dynweb_admin_functions.weekdays[4], dynweb_admin_functions.weekdays[5], dynweb_admin_functions.weekdays[6], dynweb_admin_functions.weekdays[0]];
        let weekday_options_html = '';
        weekday_options.forEach(function(weekday, index) {
            let day_index = index + 1;
            weekday_options_html += '<option value="'+day_index+'" ';
            if (day_index == param1) weekday_options_html += ' selected';
            weekday_options_html += '>'+weekday+'</option>';
        });
        let pretext = is_first ? dynweb_admin_functions.visible_on_weekday : dynweb_admin_functions.and_visible_on_weekday;
        let remove_button = is_first ? '' : '<i class="fa fa-minus-circle dynweb_remove_condition" style="cursor:pointer">';
        let html = '<p class="dynweb_condition">'+pretext+' <select class="dynweb_param1"><option value="0">'+weekday_options_html+'</select> <i class="fa fa-plus-circle dynweb_add_condition" style="cursor:pointer"></i> '+remove_button+'</i></p>';
        conditions_container.append(html);
        return true;
    }
    
    if (type == 'month') {
        let month_options = dynweb_admin_functions.months;
        let month_options_html = '';
        month_options.forEach(function(month, index) {
            //let month_index = index + 1;
            month_options_html += '<option value="'+index+'" ';
            if (index == param1) month_options_html += ' selected';
            month_options_html += '>'+month+'</option>';
        });
        let pretext = is_first ? dynweb_admin_functions.visible_in_month : dynweb_admin_functions.and_visible_in_month;
        let remove_button = is_first ? '' : '<i class="fa fa-minus-circle dynweb_remove_condition" style="cursor:pointer">';
        let html = '<p class="dynweb_condition">'+pretext+' <select class="dynweb_param1">'+month_options_html+'</select> <i class="fa fa-plus-circle dynweb_add_condition" style="cursor:pointer"></i> '+remove_button+'</p>';
        conditions_container.append(html);
        return true;
    }
    
    if (type == 'datetime') {
        let html = '<p class="dynweb_condition">'+dynweb_admin_functions.visible_datetime_from+' <input type="text" class="dynweb_param1 dynweb_datetimepicker" value="'+param1+'"> '+dynweb_admin_functions.visible_datetime_till+' <input type="text" class="dynweb_param2 dynweb_datetimepicker" value="'+param2+'"></p>';
        conditions_container.append(html);
        return true;
    }
    
    if (type == 'visit_count') {
        let checked_first = param1 == 'new' ? 'checked' : '';
        let checked_further = param1 == 'further' ? 'checked' : '';
        
        let html = '<p class="dynweb_condition"><input type="checkbox" id="dynweb_visit_count_new" class="dynweb_param1 dynweb_checkbox" value="new" '+checked_first+'> <label for="dynweb_visit_count_new">'+dynweb_admin_functions.new_visitor+'</label> <br> <input type="checkbox" id="dynweb_visit_count_further" class="dynweb_param1 dynweb_checkbox" value="further" '+checked_further+'> <label for="dynweb_visit_count_further">'+dynweb_admin_functions.returning_visitor+'</label></p>';
        conditions_container.append(html);
        return true;
    }
    
    if (type == 'referrer') {
        let checked_contains = param2 == 'contains' ? 'selected' : '';
        let checked_not_contains = param2 == 'not_contains' ? 'selected' : '';
    
        let pretext = is_first ? dynweb_admin_functions.referrer : dynweb_admin_functions.or_referrer;
        let remove_button = is_first ? '' : '<i class="fa fa-minus-circle dynweb_remove_condition" style="cursor:pointer">';
        let html = '<p class="dynweb_condition">'+pretext+' <select class="dynweb_param2"><option value="contains" '+checked_contains+'>'+dynweb_admin_functions.contains+'</option><option value="not_contains" '+checked_not_contains+'>'+dynweb_admin_functions.does_not_contain+'</option></select> <input type="text" class="dynweb_param1" value="'+param1+'"> <i class="fa fa-plus-circle dynweb_add_condition" style="cursor:pointer"></i> '+remove_button+'</p>';
        conditions_container.append(html);
        return true;
    }
    
    if (typeof dynweb_pro_add_condition === "function") { 
        let pro_result = dynweb_pro_add_condition (type, conditions_container, is_first, param1, param2, param3);
        if (pro_result) return true;
    }
    
    let html = '<p>'+dynweb_admin_functions.pro_only+'</p><p><a href="https://neeed.me" target="_blank">'+dynweb_admin_functions.more_info+'</a></p>';
    conditions_container.append(html);
    return false;
}


function dynweb_validate_condition(type, param1='', param2='', param3='') {

    if (type == 'time') {
        if (param1 > -1 && param2 > -1) return true;
        else return false;
    }
    
    if (type == 'weekday' || type == 'month') {
        if (param1 > 0) return true;
        else return false;
    }
    
    if (type == 'datetime') {
        const datetime_pattern = /^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/;
        if (param1.match(datetime_pattern) || param2.match(datetime_pattern)) return true;
        else return false;
    }
    
    if (type == 'weather' || type == 'visit_count' || type == 'perception') {
        if (param1.length > 0 ) return true;
        else return false;
    }
    
    if (type == 'flag_custom' || type == 'referrer') {
        if (param1.length > 0 && param2.length > 0) return true;
        else return false;
    }
	
	 if (type == 'temperature') {
		if (!isNaN(param1) && !isNaN(param2)) return true;
        else return false;
	 }

}


function refresh_builders() {

    //divi
    for (var prop in localStorage) {
        if (prop.indexOf('et_pb_templates_et_pb_') > -1) localStorage.removeItem(prop);
    }
    
}

