<?php

add_action('init','rasolo_helper_block_wp_admin_init');
function rasolo_helper_block_wp_admin_init()
       {

if(wp_doing_ajax()){
    return;
}
if(is_user_logged_in()){
    return;
}
if( strpos(strtolower($_SERVER['REQUEST_URI']),'/wp-admin/') === false ){
    return;
}
//    if ( !is_admin() ) {
$remote_addr=$_SERVER['REMOTE_ADDR'];
if('194.6.231'==substr($remote_addr,0,9)){
    $redir_url=wp_login_url();
} else {
    $redir_url=get_option('siteurl');
}
//        die('_2352='.$remote_addr.'{==3523_');
wp_redirect( $redir_url, 302 );
exit;
//    };

       };  // The end of block_wp_admin_init


add_action('admin_init','rasolo_remove_yoast_notifications',2);

function rasolo_remove_yoast_notifications()
       {
if (! (is_plugin_active('wordpress-seo/wp-seo.php') || is_plugin_active('rasolo-seo/wp-seo.php') )) {
    return;
}
/* Remove HTML Comments */
add_action('get_header',function() { ob_start(function ($o) {
return preg_replace('/\n?<.*?Yoast SEO plugin.*?>/mi','',$o); }); });
add_action('wp_head',function() { ob_end_flush(); }, 999);

add_action('admin_init', 'ntp_disable_yoast_notifications',99);

add_filter('wpseo_metabox_prio', 'ntp_yoast_bottom');

add_filter('option_wpseo', 'ntp_filter_yst_wpseo_option',99);

add_action('admin_bar_menu', 'ntp_remove_yoast_bar', 99);

       } // The end of rasolo_remove_yoast_notifications

/* Disable Yoast SEO Notifications */
function ntp_disable_yoast_notifications()
       {
remove_action('admin_notices', array(Yoast_Notification_Center::get(), 'display_notifications'));
remove_action('all_admin_notices', array(Yoast_Notification_Center::get(), 'display_notifications'));
       } // The end of ntp_disable_yoast_notifications

/* Yoast SEO Low Priority */
function ntp_yoast_bottom() {
	return 'low';
       } // The end of ntp_yoast_bottom

/* Disable screen after update */
function ntp_filter_yst_wpseo_option($option)
       {
	if (is_array($option)) {
		$option['seen_about'] = true;
	}
	return $option;
       } // The end of ntp_filter_yst_wpseo_option

/* Remove Node in Toolbar */
function ntp_remove_yoast_bar($wp_admin_bar)
       {
	$wp_admin_bar->remove_node('wpseo-menu');
       } // The end of ntp_remove_yoast_bar


