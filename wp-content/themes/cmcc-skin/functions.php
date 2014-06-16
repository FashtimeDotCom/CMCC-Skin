<?php
add_action('init', function(){
	show_admin_bar(false);
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

add_filter('body_class', function($classes) {
	global $post;
	if (isset($post)) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
});
