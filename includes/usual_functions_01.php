<?php

add_filter( 'auth_cookie_expiration', 'keep_me_logged_in_for_1_year' );
function keep_me_logged_in_for_1_year( $expirein ) {
    return 31556926; // 1 year in seconds
}

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

if (current_user_can('activate_plugins')  ) {
    add_action('wp_dashboard_setup', 'helper_remove_dashboard_widgets');
};



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


add_filter( 'woocommerce_background_image_regeneration', '__return_false' );

add_filter('xmlrpc_enabled', '__return_false');

remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );

show_admin_bar(false);

add_action( 'widgets_init', 'remove_recent_comments_style' );
function remove_recent_comments_style()
       {
global $wp_widget_factory;
remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
       };  // The end of  remove_recent_comments_style


function hide_update_notice_to_all_but_admin_users()
       {
//if (!current_user_can('administrator')) {
    remove_action( 'admin_notices', 'update_nag', 3 );
//};
       };   // The end of hide_update_notice_to_all_but_admin_users
add_action( 'admin_head', 'hide_update_notice_to_all_but_admin_users', 1 );


// Total disable plugin update
function filter_plugin_updates( $value )
       {
//unset( $value->response['akismet/akismet.php'] );
unset( $value->response['woocommerce/woocommerce.php'] );
unset( $value->response );
//myvar_dump($value,'$value',true);
return $value;
    };   // The end of filter_plugin_updates
add_filter( 'site_transient_update_plugins', 'filter_plugin_updates' );


if(is_admin()){
    add_action( 'init', 'disable_wp_emojicons' );
} else {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
};
function disable_wp_emojicons()
       {
// all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
       };  // The end of disable_wp_emojicons

function disable_emojicons_tinymce( $plugins )
        {
if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
} else {
    return array();
};
       };   // The end of disable_emojicons_tinymce

add_action('after_setup_theme', 'remove_core_updates');
function remove_core_updates()
       {
//if(! current_user_can('update_core')){
//    return;
//};
add_action('init', create_function('$a',"remove_action( 'init', 'wp_version_check' );"),2);
add_filter('pre_option_update_core','__return_null');
add_filter('pre_site_transient_update_core','__return_null');
       };      // The end of remove_core_updates

remove_action('load-update-core.php','wp_update_plugins');
add_filter('pre_site_transient_update_plugins','__return_null');

add_filter( 'auto_update_plugin', '__return_false' );

add_filter( 'auto_update_theme', '__return_false' );

add_filter( 'allow_major_auto_core_updates', '__return_null' );
add_filter('pre_site_transient_update_core',create_function('$a', "return null;"));
wp_clear_scheduled_hook('wp_version_check');

add_filter( 'pre_http_request', '__return_true', 100 );

// disable default dashboard widgets
function helper_remove_dashboard_widgets()
       {

global $wp_meta_boxes;
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
unset($wp_meta_boxes['dashboard']['normal']['core']['wpseo-dashboard-overview']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
//myvar_dd($wp_meta_boxes,'$wp_meta_boxes');
//           die('swefwsvswsvd_234223');

       }; // The end of helper_remove_dashboard_widgets


add_action('wp_dashboard_setup', 'helper_remove_admin_dashboard_widgets',17);
remove_action( 'welcome_panel', 'wp_welcome_panel' );
// disable admin default dashboard widgets
function helper_remove_admin_dashboard_widgets()
       {

global $wp_meta_boxes;

unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);

unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);

       };   // The end of remove_admin_dashboard_widgets

