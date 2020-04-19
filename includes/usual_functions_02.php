<?php

add_action( 'admin_init', 'rasolo_may_be_a_luck_of_right' );
function rasolo_may_be_a_luck_of_right()
       {
if(isset($_GET['my_msg']) && $_GET['my_msg']=='disable_trash'){
    add_action( 'admin_notices', 'admin_notice_alackofrights' );
};
       } // The end of rasolo_may_be_a_luck_of_right

function admin_notice_alackofrights()
       {
?>
<div class="notice notice-success is-dismissible">
    <p><?php echo 'Недостаточно административных прав для операции удаления материалов'
   ?></p>
</div>
<?php
       }; // The end of admin_notice_alackofrights

add_action( 'admin_init', 'rasolo_remove_unwanted_roles' );
function rasolo_remove_unwanted_roles()
       {
global $wp_roles;

$doomed_role='shop_manager';
$shop_manager_role=get_role($doomed_role);
if(!is_object($shop_manager_role))return;
$manager_caps=array_keys($shop_manager_role->capabilities);

$wp_roles->add_cap( $doomed_role, 'edit_pages' );
$wp_roles->add_cap( $doomed_role, 'edit_others_pages' );
$wp_roles->add_cap( $doomed_role, 'edit_published_pages' );
$wp_roles->add_cap( $doomed_role, 'edit_private_pages' );
$wp_roles->add_cap( $doomed_role, 'publish_pages' );
$wp_roles->add_cap( $doomed_role, 'publish_posts' );

$doomed_caps=array('edit_users',
                   'edit_others_posts',
//  the source state             'edit_others_pages',
//                   'publish_posts',
//  the source state                 'publish_pages',
                   'delete_posts',
//  the source state                 'edit_pages',
                   'delete_pages',
                   'delete_private_pages',
                   'delete_private_pages',
                   'delete_published_pages',
                   'delete_published_posts',
                   'delete_others_posts',
                   'delete_others_pages',
                   'manage_links',
                   'moderate_comments',
                   'export',
                   'import',
                   'list_users'
);
//$doomed_caps=array();

//$caps_for_add=array('edit_pages');
//foreach($caps_for_add as $nth_cap){
//    $wp_roles->add_cap($doomed_role,$nth_cap);
//}

//echo '<pre>';
//print_r($shop_manager_role);
//echo '</pre>';
foreach($doomed_caps as $nth_cap){
    if(!in_array($nth_cap,$manager_caps))continue;
    if(in_array($nth_cap,$doomed_caps))$wp_roles->remove_cap($doomed_role,$nth_cap);
};
       }; //The end of rasolo_remove_unwanted_roles

// The selected posts (i.e. pages) deletion restriction
add_action('trash_post', 'restrict_post_trashing', 10, 1);
add_action('wp_trash_post', 'restrict_post_trashing', 10, 1 );
add_action('before_delete_post', 'restrict_post_trashing', 10, 1 );
function restrict_post_trashing($post_ID){
//            This procedure restricts selected post trashing for any users except of admin
    $post_buildup=7200; // How many seconds need the post to buildup
    $thispostdate=intval(strtotime(get_post_field( 'post_date', $post_ID )));
    $time_limit=intval(current_time('timestamp'))-$post_buildup;
    $user = get_current_user_id();
                  $restricted_users = array(5,6);
    $allowed_users = array(1,2);  // Admins who can purge pages
    $this_type=get_post_field( 'post_type', $post_ID );

    if( (!in_array($user, $allowed_users)) &&
        ($this_type=='page' || $this_type=='post' || $time_limit>$thispostdate)
                ){
        wp_redirect(site_url().'/wp-admin/edit.php?post_type=page&my_msg=disable_trash');
        exit;
    };
}; // The end of the function restrict_post_trashing


// The all posts deletion restriction
add_action('delete_post', 'altair_restrict_post_deletion', 10, 1);
add_action( 'before_delete_post', 'altair_restrict_post_deletion', 10, 1 );
function altair_restrict_post_deletion($post_ID)
       {

$user = get_current_user_id();
//    $restricted_users = array(5,6);
$allowed_users = array(1,2);
$this_type=get_post_field( 'post_type', $post_ID );

if( !in_array($user, $allowed_users) && $this_type=='page'){
    wp_redirect(site_url().'/wp-admin/edit.php?post_type=page&my_msg=disable_trash');
    exit;
};
       }; // The end of the function altair_restrict_post_deletion

add_action( 'admin_init', 'vodka_remove_admin_bar_links' );
function vodka_remove_admin_bar_links()
       {
global $wp_admin_bar, $current_user;
$allowed_users=array(1,2); // These are the admin IDs
global $submenu, $menu, $pagenow;
$existing_menus=array();
$existing_submenus=array();
$doomed_menus=array('tools.php',
//                    'index.php',
                    'edit.php?post_type=page',
                    'edit-comments.php',
                    'themes.php',
                    'edit-tags.php',
                    'tools.php');
//$doomed_submenus=array('edit.php'=>'post-new.php');
$doomed_submenus=array(
//    array('root'=>'edit.php','item'=>'post-new.php'),
    array('root'=>'edit.php','item'=>'edit-tags.php?taxonomy=category'),
    array('root'=>'edit.php','item'=>'edit-tags.php?taxonomy=post_tag'),
    array('root'=>'options-general.php','item'=>'rustolat/rus-to-lat.php')
);
if(!empty($menu) && is_array($menu)){
    foreach($menu as $menu_item){
        if(!empty($menu_item[2]))$existing_menus[]=$menu_item[2];
    };
}

if(is_array($submenu)){
    foreach($submenu as $submenu_root=>$nth_submenu){
        foreach($nth_submenu as $nth_submenu_item){
    //        myvar_dump($submenu_root,'$submenu_root',1);
    //        myvar_dump($nth_submenu);
            if(!empty($nth_submenu_item[2])){
                $existing_submenus[]=array('root'=>$submenu_root,
                        'item'=>$nth_submenu_item[2]);
            };
        };
    };
};
if (  !in_array($current_user->ID,$allowed_users) ) {

    foreach($doomed_menus as $doomed){
      if(in_array($doomed,$existing_menus))remove_menu_page($doomed);
    };
    foreach($doomed_submenus as $doomed){
        if(in_array($doomed,$existing_submenus))remove_submenu_page($doomed['root'],$doomed['item']);
    };
//    myvar_dump($existing_menus,'$existing_manus',1);
//    remove_menu_page( 'wpcf7' );      // Contact form 7

} else {
//
//    myvar_dump($existing_submenus,'$existing_submenus',1);
//    myvar_dump($existing_menus,'$existing_menus',1);
//    myvar_dump($menu,'$menu',1);
//    myvar_dump($submenu,'$submenu',1);
//    myvar_dump($pagenow,'$pagenow',1);
};
       }; // The end of altair_remove_admin_bar_links

add_action( 'init','rasolo_add_default_theme_support' );
//add_theme_support( 'wc-product-gallery-zoom' );
//add_theme_support( 'wc-product-gallery-lightbox' );
//add_theme_support( 'wc-product-gallery-slider' );
function rasolo_add_default_theme_support()
       {

//die('rasolo_add_default_theme_support');
//if ( in_array( get_option( 'template' ), wc_get_core_supported_themes() ) ) {
add_theme_support( 'wc-product-gallery-zoom' );
add_theme_support( 'wc-product-gallery-lightbox' );
add_theme_support( 'wc-product-gallery-slider' );
//};
       };  // The end of rasolo_add_default_theme_support

if(!is_admin()){
    add_filter( 'woocommerce_default_catalog_orderby_options', 'rasolo_custom_wc_catalog_orderby' );
    add_filter( 'woocommerce_catalog_orderby', 'rasolo_custom_wc_catalog_orderby' );
};
function rasolo_custom_wc_catalog_orderby( $sortby ) {
$sortby['alphabetical'] = 'Сортировка по имени: Алфавит';
return $sortby;
       };  // The end of rasolo_custom_wc_catalog_orderby

if(!is_admin()){
    add_filter('woocommerce_pagination_args','rasolo_wc_pagination_args', 20,2);
};
function rasolo_wc_pagination_args($pag_args)
       {
global $current_sort_args;

if(!isset($current_sort_args['orderby']))$current_sort_args['orderby']='title';
if(!isset($current_sort_args['order']))$current_sort_args['order']='asc';
if(!isset($current_sort_args['meta_key']))$current_sort_args['meta_key']='';

//myvar_dump($current_sort_args,'$current_sort_args',1);
if(!empty($current_sort_args['meta_key'])){
    $order_key=$current_sort_args['meta_key'];
} else {
    list($order_key,$some_rest)=explode(' ',$current_sort_args['orderby'].' thisgagisforsomerest');
};
if(empty($order_key))$order_key='unknown order';
if($order_key=='total_sales'){
    $pag_args['prev_text']='К&nbsp; более&nbsp; популярным';
    $pag_args['next_text']='К&nbsp; менее&nbsp; популярным';
} else if($order_key=='_wc_average_rating'){
    $pag_args['prev_text']='К&nbsp; большему&nbsp; рейтингу';
    $pag_args['next_text']='К&nbsp; меньшему&nbsp; рейтингу';
} else if($order_key=='date'){
    $pag_args['prev_text']='К&nbsp; более&nbsp; свежим';
    $pag_args['next_text']='К&nbsp; менее&nbsp; свежим';
} else {
    $pag_args['prev_text']='Вперед';
    $pag_args['next_text']='Назад';
};
return $pag_args;
       }; //  The end of rasolo_wc_pagination_args

//Adding Alphabetical sorting option to shop and product settings pages
if(!is_admin()){
    add_filter( 'woocommerce_get_catalog_ordering_args', 'rasolo_alphabetical_shop_ordering' );
};
function rasolo_alphabetical_shop_ordering( $sort_args )
       {
global $current_sort_args;
$current_sort_args=$sort_args;
            //$orderby_value = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
            //if ( 'alphabetical' == $orderby_value ) {
            //    $sort_args['orderby'] = 'title';
            //    $sort_args['order'] = 'asc';
            //    $sort_args['meta_key'] = '';
            //};
return $sort_args;
       };  // rasolo_alphabetical_shop_ordering

add_filter('wp_generate_attachment_metadata','rasolo_replace_uploaded_image');
function rasolo_replace_uploaded_image($image_data)
       {
if (!isset($image_data['sizes']['large'])) return $image_data;

// paths to the uploaded image and the large image
$upload_dir = wp_upload_dir();
$uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];
// $large_image_location = $upload_dir['path'] . '/'.$image_data['sizes']['large']['file']; // ** This only works for new image uploads - fixed for older images below.
$current_subdir = substr($image_data['file'],0,strrpos($image_data['file'],"/"));
$large_image_location = $upload_dir['basedir'] . '/'.$current_subdir.'/'.$image_data['sizes']['large']['file'];

// delete the uploaded image
unlink($uploaded_image_location);

// rename the large image
rename($large_image_location,$uploaded_image_location);

// update image metadata and return them
$image_data['width'] = $image_data['sizes']['large']['width'];
$image_data['height'] = $image_data['sizes']['large']['height'];
unset($image_data['sizes']['large']);

return $image_data;
       };   // The end of rasolo_replace_uploaded_image


add_action('login_head', 'vodka_login_customization');
function vodka_login_customization() {
  echo '<script src="https://www.google.com/recaptcha/api.js?ver=5.2.3"></script>'.chr(10);
} // The end of vodka_login_customization


add_filter('sanitize_file_name', 'rasolo_sanitize_filename_chars', 10);
function rasolo_sanitize_filename_chars($filename)
       {
$file_name_arr=explode('.',$filename);
if(isset($file_name_arr[1])){

    $file_name_reverse=array_reverse($file_name_arr);

    $file_type=array_shift($file_name_reverse);

    $new_filename_raw=implode('.',array_reverse($file_name_reverse));
    return rasolo_bad_text_to_lat($new_filename_raw).'.'.$file_type;
} else {
    return strval(time()).'jpeg';
}

       }; // The end of rasolo_sanitize_filename_chars


add_filter('wp_handle_upload_prefilter',  'rasolo_upload_image_filter');
function rasolo_upload_image_filter($file) {

//    myvar_dump($file);
//    die('_252352342_');
/*
$image_editor = wp_get_image_editor($file['tmp_name']);

if (!is_wp_error($image_editor)) {

    // Resize to 400px
    $image_editor->resize(400);
    // Generate a new filename with suffix abcd
    $filename = $image_editor->generate_filename('abcd');
    $saved = $image_editor->save($filename);

    // Try to alter the original $file and inject the new name and path for our new image
    $file['name'] = $saved['file'];
    $file['tmp_name'] = $saved['path'];
}
*/
// Return the filtered $file variable
// .'.jpg'
$file['name']=rasolo_bad_text_to_lat($file['name']);
return $file;
        }; // The end of rasolo_upload_image_filter

function rasolo_bad_text_to_lat($src)
        {


$gost = array(
                "Є"=>"EH","І"=>"I","і"=>"i","№"=>"#","є"=>"eh",
                "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
                "Е"=>"E","Ё"=>"JO","Ж"=>"ZH",
                "З"=>"Z","И"=>"I","Й"=>"JJ","К"=>"K","Л"=>"L",
                "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
                "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"KH",
                "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
                "Ы"=>"Y","Ь"=>"","Э"=>"EH","Ю"=>"YU","Я"=>"YA",
                "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
                "е"=>"e","ё"=>"jo","ж"=>"zh",
                "з"=>"z","и"=>"i","й"=>"jj","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"kh",
                "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
                "ы"=>"y","ь"=>"","э"=>"eh","ю"=>"yu","я"=>"ya",
                "—"=>"-","«"=>"","»"=>"","…"=>""
);

/*            $iso = array(
                "Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye","ѓ"=>"g",
                "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
                "Е"=>"E","Ё"=>"YO","Ж"=>"ZH",
                "З"=>"Z","И"=>"I","Й"=>"J","К"=>"K","Л"=>"L",
                "М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
                "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X",
                "Ц"=>"C","Ч"=>"CH","Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'",
                "Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU","Я"=>"YA",
                "а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d",
                "е"=>"e","ё"=>"yo","ж"=>"zh",
                "з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
                "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
                "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"x",
                "ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
                "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
                "—"=>"-","«"=>"","»"=>"","…"=>""
            );
*/

$src_rus_to_lat=strtr($src, $gost);

$spanish_chars = array( '/á/', '/é/', '/í/', '/ó/', '/ú/',
                        '/ü/', '/ñ/', '/Á/', '/É/', '/Í/',
                        '/Ó/', '/Ú/', '/Ü/', '/Ñ/', '/º/', '/ª/',
                        '/è/', '/«/', '/¬/', '/Ä/', '/ñ/',
                        '/½/', '/ß/', '/¿/', '/Ñ/', '/ª/'


                        );
$sanitized_chars = array('a', 'e', 'i', 'o', 'u',
                         'u', 'n', 'A', 'E', 'I',
                         'O', 'U', 'U', 'N', 'o', 'a',
                         'b', 'c', 'd', 'f', 'g',
                         'h','j', 'k', 'm', 'n', 'p',

);
$friendly_filename = preg_replace($spanish_chars, $sanitized_chars, $src_rus_to_lat);
//    return $friendly_filename;
//$my_string = "some სოფო text èaet«¬ Ä.jpg great";
$new_string = preg_replace('/[^a-z0-9_.]/i', '', $friendly_filename);
//myvar_dump($new_string ,'$new_string ',1);
if(strlen($new_string)<4){
    $new_string=substr(strval(rand(10000000,99999999)).$new_string,-8);
};
return $new_string;
        }; // The end of rasolo_bad_text_to_lat
