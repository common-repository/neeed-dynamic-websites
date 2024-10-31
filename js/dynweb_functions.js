
function dynweb_set_cookie(cookie, cookie_name="dynweb", stringify=true, session=false) {
  if (stringify) var cookie_str = JSON.stringify(cookie);
  else var cookie_str = cookie;
  
  if (session) var expires = '';
  else {
  var d = new Date();
    d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
    var expires = ";expires="+d.toUTCString();
  }
  
  document.cookie = cookie_name + "=" + cookie_str + expires + ";path=/";
  //console.log(cookie);
}



function dynweb_get_cookie(cookie_name="dynweb") {
  var name = cookie_name + "=";
  var ca = document.cookie.split(';');
  for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      var cookie_str = c.substring(name.length, c.length);
      var cookie = JSON.parse(cookie_str);
      return cookie;
    }
  }
  return '';
}




function dynweb_execute_rules() {
    var all_rules = JSON.parse(dynweb_global_all_rules);

    jQuery('[data-dynweb-rule]').each(function() {
        var existing_condition_types = new Array();
        var fullfilled_condition_types = new Array();
        var block_rule_id = parseInt( jQuery(this).data('dynweb-rule').replace('id_', '') );
        
        for (let i = 0; i < all_rules.length; i++) {
            if (all_rules[i].rule_id != block_rule_id) continue;
            
            for (let j = 0; j < all_rules[i].conditions.length; j++) {
                let type = all_rules[i].conditions[j].type;
                
                if (type == 'flag_custom' || type == 'perception') jQuery(this).data('dynweb-live', 1);
                
                if (fullfilled_condition_types.indexOf(type) != -1) continue;
                if (existing_condition_types.indexOf(type) == -1) existing_condition_types.push(type);
                
                let result = dynweb_check_condition(all_rules[i].conditions[j]);
                if (result == true) fullfilled_condition_types.push(type);
            }
        }
        
        if (!dynweb_global_scroll_rules_reached || dynweb.update_invisible_only == 0) {
        
            if (fullfilled_condition_types.length == existing_condition_types.length) {
                window.setTimeout(function(element) { jQuery(element).show(); }, 100, this);
            }
            else window.setTimeout(function(element) { jQuery(element).hide(); }, 100, this);
        
        }
        
        //console.log('rule '+block_rule_id+': '+fullfilled_condition_types);
        
    });
}



function dynweb_check_condition(condition) {

    if (condition.type == 'time') {
        let now;
        if (typeof dynweb_global_cookie.admin_datetime != 'undefined') now = new Date(dynweb_global_cookie.admin_datetime);
        else now = new Date();
        let now_hour = now.getHours();
        condition.param2 = parseInt(condition.param2);
        condition.param1 = parseInt(condition.param1);
        if (condition.param2 > condition.param1 && now_hour >= condition.param1 && now_hour < condition.param2 || condition.param2 < condition.param1 && (now_hour >= condition.param1 || now_hour < condition.param2)) return true;
        else return false;
    }
    
    if (condition.type == 'weekday') {
        let now;
        if (typeof dynweb_global_cookie.admin_datetime != 'undefined') now = new Date(dynweb_global_cookie.admin_datetime);
        else now = new Date();
        let weekday_index = condition.param1 == '7' ? 0 : parseInt(condition.param1);
        if (now.getDay() == weekday_index) return true;
        else return false;
    }
    
    if (condition.type == 'month') {
        let now;
        if (typeof dynweb_global_cookie.admin_datetime != 'undefined') now = new Date(dynweb_global_cookie.admin_datetime);
        else now = new Date();
        let month_index = parseInt(condition.param1) - 1;
        if (now.getMonth() == month_index) return true;
        else return false;
    }
    
    if (condition.type == 'datetime') {
        let now, check_date;
        if (typeof dynweb_global_cookie.admin_datetime != 'undefined') now = new Date(dynweb_global_cookie.admin_datetime);
        else now = new Date();
        if (condition.param1 != '') {
            check_date = dynweb_parse_date(condition.param1);
            if (now.getTime() < check_date.getTime()) return false;
        }
        if (condition.param2 != '') {
            check_date = dynweb_parse_date(condition.param2);
            if (now.getTime() > check_date.getTime()) return false;
        }
        return true;
    }
    
    if (condition.type == 'visit_count') {
        if (condition.param1 == 'new' && dynweb_global_cookie.visits.length == 1 || condition.param1 == 'further' && dynweb_global_cookie.visits.length > 1) return true;
        else return false;
    }
    
    if (condition.type == 'referrer') {
        let contains = dynweb_global_cookie.referrer.toLowerCase().indexOf(condition.param1.toLowerCase()) !== -1;
        if (condition.param2 == 'contains') return contains;
        else return !contains;
    }
    
    if (typeof dynweb_pro_check_condition === "function") { 
        return dynweb_pro_check_condition (condition);
    }
}


function dynweb_parse_date(str_date) {
    const arr_datetime = str_date.split(' ');
    const arr_date = arr_datetime[0].split('.');
    const arr_time = arr_datetime[1].split(':');
    const obj_date = new Date(arr_date[2], arr_date[1] - 1, arr_date[0], arr_time[0], arr_time[1]);
    return obj_date;
}


function dynweb_format_date(obj_date) {
    let year = obj_date.getFullYear();
    let month = parseInt(obj_date.getMonth()) + 1;
    if (month < 10) month = '0' + String(month);
    let day = obj_date.getDate()
    if (day < 10) day = '0' + String(day);
    let hour = obj_date.getHours()
    if (hour < 10) hour = '0' + String(hour);
    let minute = obj_date.getMinutes()
    if (minute < 10) minute = '0' + String(minute);
    let str_date = day + '.' + month + '.' + year + ' ' + hour + ':' + minute;
    return str_date;
}
