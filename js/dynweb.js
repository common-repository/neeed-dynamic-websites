var dynweb_global_cookie;
var dynweb_global_scroll_rules_reached = false;

var dynweb_global_cookie = dynweb_get_cookie();
if (dynweb_global_cookie == '') {
    dynweb_global_cookie = {};
    dynweb_global_cookie.visits = [ Date.now() ];
    dynweb_global_cookie.referrer = document.referrer;
    dynweb_global_cookie.flags = [];
    dynweb_global_cookie.perception = { video:0, image:0, text:0 }; 
    dynweb_set_cookie(dynweb_global_cookie);
}
else {
    let last_visit = dynweb_global_cookie.visits[dynweb_global_cookie.visits.length-1];
    if (Date.now() - last_visit > 1000*3600*6) {
        dynweb_global_cookie.visits.push(Date.now());
        dynweb_global_cookie.referrer = document.referrer;
        dynweb_set_cookie(dynweb_global_cookie);
    }
}


jQuery(function($) {
 
    if (typeof dynweb_global_all_rules != 'undefined') {
        dynweb_execute_rules();
    }
	
});