<?php
add_action('init', function(){
	
	show_admin_bar(false);
	
	add_theme_support('post-thumbnails');
	
	register_post_type('site', array(
		'labels'=>array(
			'name'=>'营业厅',
			'all_items'=>'所有营业厅',
			'add_new'=>'添加',
			'add_new_item'=>'添加营业厅',
			'edit_item'=>'编辑营业厅',
			'new_item'=>'新营业厅',
			'view_item'=>'查看营业厅',
			'search_items'=>'搜索营业厅',
			'not_found'=>'未找到营业厅'
		),
		'public'=>true,
		'supports'=>array('title','thumbnail'),
		'has_archive'=>true,
		'register_meta_box_cb'=>function($post){
			add_meta_box('properties', '营业厅信息', function($post){
				$regions = json_decode(get_option('regions', '[]'));
				require get_template_directory() . '/admin/site-info-meta-box.php';
			}, 'site', 'normal');
			remove_meta_box( 'postimagediv', 'site', 'side');
			add_meta_box('postimagediv', __('营业厅点位图'), 'post_thumbnail_meta_box', 'site', 'side');
		},
		'menu_icon'=>'dashicons-admin-home'
	));
	
	register_post_type('decoration', array(
		'labels'=>array(
			'name'=>'换装',
			'all_items'=>'所有换装',
			'add_new'=>'发布',
			'add_new_item'=>'发布换装',
			'edit_item'=>'编辑换装',
			'new_item'=>'新换装',
			'view_item'=>'查看换装',
			'search_items'=>'搜索换装',
			'not_found'=>'未找到换装'
		),
		'public'=>true,
		'supports'=>array('title','thumbnail'),
		'menu_icon'=>'dashicons-art',
		'register_meta_box_cb'=>function($post){
			add_meta_box('pictures', '画面', function($post){
				$pictures = json_decode(get_post_meta($post->ID, 'pictures', true), JSON_OBJECT_AS_ARRAY);
				!$pictures && $pictures = array();
				require get_template_directory() . '/admin/decoration-picture.php';
			}, 'decoration', 'normal');
			
			add_meta_box('frame-picture-sheet', '各营业厅物料画面对应表', function($post){
				require get_template_directory() . '/admin/decoration-frame-picture-sheet.php';
			}, 'decoration', 'normal');
		}
	));
	
	register_post_type('site_decoration', array(
		'label'=>'营业厅换装',
	));
	
	add_action('save_post', function($post_id){

		$metas = array(
			'region',
			'site_map',
			'manager',
			'manager_phone',
			'phone',
			'address',
			
			'pictures',
			
		);

		foreach($metas as $field){
			if(isset($_POST[$field])){
				update_post_meta($post_id, $field, $_POST[$field]);
			}
		}
	});
	
});

add_action('wp_enqueue_scripts', function(){
	wp_register_style('bootstrap', 'http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css');
	wp_register_style('font-awesome', '//libs.baidu.com/fontawesome/4.0.3/css/font-awesome.min.css');
	wp_register_script('bootstrap', 'http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js');
	wp_register_script('swipe', get_template_directory_uri() . '/js/swipe.js');
	wp_register_style('style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('font-awsome');
	wp_enqueue_style('bootstrap');
	wp_enqueue_style('style');
	wp_enqueue_script('jquery');
});

add_action('wp_foot', function(){
	wp_enqueue_script('bootstrap');
});

add_action('admin_enqueue_scripts', function(){
	wp_register_style('cmcc-admin', get_template_directory_uri() . '/admin/style.css');
	wp_register_script('cmcc-admin', get_template_directory_uri() . '/admin/script.js', array('jquery'));
	wp_enqueue_style('cmcc-admin');
	wp_enqueue_script('cmcc-admin');
});

add_action('admin_head-post-new.php', change_thumbnail_html);
add_action('admin_head-post.php', change_thumbnail_html);
function change_thumbnail_html( $content ) {
    if ('site' == $GLOBALS['post_type'])
      add_filter('admin_post_thumbnail_html',do_thumb);
}
function do_thumb($content){
	 return str_replace(__('Set featured image'), __('设置营业厅点位图'), $content);
}

add_filter('body_class', function($classes) {
	global $post;
	if (isset($post)) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
});
