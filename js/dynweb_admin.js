jQuery(function($) {

    var admin_cookie = dynweb_get_cookie('dynweb_admin');
    if (admin_cookie == '') {
        admin_cookie = {};
        admin_cookie.hidden_tabinfo = [];
        dynweb_set_cookie(admin_cookie, 'dynweb_admin');
    }
    
    
    $('.dynweb_tabinfo').show();
    
    admin_cookie.hidden_tabinfo.forEach(function(hidden_tabinfo) {
        $('.dynweb_close_tabinfo[data-tab="'+hidden_tabinfo+'"]').closest('.dynweb_tabinfo').hide();
    });


    if (typeof dynweb_global_conditions != 'undefined') {
        var conditions = JSON.parse(dynweb_global_conditions);
        conditions.forEach(function(condition) {
            dynweb_add_condition(condition.type, condition.param1, condition.param2, condition.param3);
        });
        
        $('.dynweb_conditions_container').each(function(container_index) {
            if ($(this).find('.dynweb_condition').length == 0) {
                dynweb_add_condition( $(this).data('type') );
            }
        });
    }
    
    $('.dynweb_tabs').tabs();
    
    $('.dynweb_accordion').accordion({
        heightStyle: 'content',
        icons: {header:'ui-icon-caret-1-s', activeHeader:'ui-icon-caret-1-n'},
    });
    
    
    $('.dynweb_datetimepicker').datetimepicker({
		dateFormat : 'dd.mm.yy',
		timeText: dynweb_admin.time,
		hourText: dynweb_admin.hour,
		minuteText: dynweb_admin.minute,
		currentText: dynweb_admin.now,
		closeText: dynweb_admin.done,
		monthNames: dynweb_admin.months,
		dayNamesMin: dynweb_admin.weekdays_min, 
		numberOfMonths: 1,
		beforeShow: function(input, inst) {
            $(inst.dpDiv).addClass('dynweb_datetimepicker');
        },
        onClose: function(selected_date) {
            if ($(this).parent('.ab-item').length > 0) {
                let admin_datetime = dynweb_parse_date(selected_date);
                let cookie = dynweb_get_cookie();
                cookie.admin_datetime = admin_datetime.getTime();
                dynweb_set_cookie(cookie);
                document.location.reload();
            }
        }
	});

    
    $('.dynweb_condition .dynweb_checkbox').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).siblings('.dynweb_checkbox').prop('checked', false);
        }
    });


    $('.dynweb_conditions_container').on('click', '.dynweb_add_condition', function() {
        var type = $(this).closest('.dynweb_conditions_container').data('type');
        dynweb_add_condition(type);   
        $(this).closest('.dynweb_accordion').accordion('refresh');
    });
    
    
    $('.dynweb_conditions_container').on('click', '.dynweb_remove_condition', function() {
        $(this).closest('.dynweb_condition').remove();
        $(this).closest('.dynweb_accordion').accordion('refresh');
    });
    

    $('.dynweb_save_rule').on('click', function() {
    
        $('<span class="spinner dynweb_spinner" style="visibility:visible; float:none; margin:-2px 10px"></span>').insertAfter(this);
        var rule = new Object();
        rule.rule_id = $('.dynweb_rule_name').data('id');
        rule.rule_name = $('.dynweb_rule_name').val();
        rule.conditions = new Array();
        var param1, param2, param3;
        
        $('.dynweb_conditions_container').each(function(container_index) {
            var type = $(this).data('type');
            var position = 1;
            $(this).find('.dynweb_condition').each(function(condition_index) {
               
                if ( $(this).find('.dynweb_param1[type="checkbox"]').length > 0) param1 = $(this).find('.dynweb_param1:checked').val();
                else param1 = $(this).find('.dynweb_param1').val();
                
                if ( $(this).find('.dynweb_param2[type="checkbox"]').length > 0) param2 = $(this).find('.dynweb_param2:checked').val();
                else param2 = $(this).find('.dynweb_param2').val();
                
                if ( $(this).find('.dynweb_param3[type="checkbox"]').length > 0) param3 = $(this).find('.dynweb_param3:checked').val();
                else param3 = $(this).find('.dynweb_param3').val();
                
                if ( dynweb_validate_condition(type, param1, param2, param3) ) {
                    rule.conditions.push({type:type, position:position, param1:param1, param2:param2, param3:param3});
                    position++;
                }
            });
        });
        
        $.post(ajaxurl, {action:'dynweb_save_rule', rule:rule, nonce:dynweb_admin.nonce_save_rule}, function(response) {
            refresh_builders();
            $('.dynweb_spinner').remove();
            if (rule.rule_id == 0) window.location.href = 'admin.php?page=dynweb-editrule&id='+parseInt(response);
        });
        
    });
    
    
    $('.dynweb_delete_rule').on('click', function() {
        let rule_id = $(this).data('ruleid');
        let rule_name = $(this).data('rulename');
        if (!confirm(' "'+rule_name+'" '+dynweb_admin.really_delete)) return;
        
        $.post(ajaxurl, {action:'dynweb_delete_rule', rule_id:rule_id, nonce:dynweb_admin.nonce_delete_rule}, function(response) {
            refresh_builders();
            document.location.reload();
        });
    });
    
    
    $('.dynweb_close_tabinfo').on('click', function() {
        let tab = $(this).data('tab');
        let admin_cookie = dynweb_get_cookie('dynweb_admin');
        admin_cookie.hidden_tabinfo.push(tab);
        dynweb_set_cookie(admin_cookie, 'dynweb_admin');
        $(this).parent().hide('medium');
    });
    
    
    $('.dynweb_reset_tabinfo').on('click', function() {
        $('<span class="spinner dynweb_spinner" style="visibility:visible; float:none; margin:-2px 10px"></span>').insertAfter(this);
        let admin_cookie = dynweb_get_cookie('dynweb_admin');
        admin_cookie.hidden_tabinfo = [];
        dynweb_set_cookie(admin_cookie, 'dynweb_admin');
        setTimeout(function() { $('.dynweb_spinner').remove(); }, 1000);
    });
    
    $('.dynweb_start_tutorial').on('click', function() {
        $('<span class="spinner dynweb_spinner" style="visibility:visible; float:none; margin:-2px 10px"></span>').insertAfter(this);
        $.post(ajaxurl, {action:'dynweb_start_tutorial'}, function(response) {
            document.location.href = 'index.php';
        });
        
   
    });
    
    
    //Admin Bar
    let now;
    let cookie = dynweb_get_cookie();
    
    if (typeof cookie.admin_datetime != 'undefined') now = new Date(cookie.admin_datetime);
    else now = new Date();
    const datetime_formatted = dynweb_format_date(now);
    $('#dynweb_admin_bar_datetime_text').val(datetime_formatted);
    
    $('#dynweb_admin_bar_referrer_text').val(cookie.referrer);
    
    $('#wp-admin-bar-dynweb-reset a').on('click', function() {
        document.cookie = 'dynweb=; expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/;';
        document.cookie = 'dynweb_remote_data=; expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/;';
        document.location.reload();
    });
        
    
    $('#wp-admin-bar-dynweb-visit-count-new a').on('click', function() {
        let cookie = dynweb_get_cookie();
        cookie.visits = [ Date.now() ];
        dynweb_set_cookie(cookie);
        document.location.reload();
    });
    
    
    $('#wp-admin-bar-dynweb-visit-count-returning a').on('click', function() {
        let cookie = dynweb_get_cookie();
        cookie.visits = [ Date.now()-1000*86400, Date.now() ];
        dynweb_set_cookie(cookie);
        document.location.reload();
    });
    
    
    $('#dynweb_admin_bar_referrer_text').on('change', function() {
        let cookie = dynweb_get_cookie();
        let referrer = $(this).val();
        cookie.referrer = referrer;
        dynweb_set_cookie(cookie);
        document.location.reload();
    });
    
    //see dynweb_pro_admin.js for pro only features
    
    
    //todo: this should move to the divi module at some point
    $('body').on('click', '.et-fb-tabs__item', function() {
    
        if ($(this).text() != 'NEEED') return;
        
        window.setTimeout(function() {
            if ( $('.et-fb-tabs__panel--active .et-fb-form__toggle-opened').length == 0 ) {
                 $('.et-fb-tabs__panel--active .et-fb-form__toggle-title').trigger('click');
            }
        }, 100);
        
    });
    
	
});