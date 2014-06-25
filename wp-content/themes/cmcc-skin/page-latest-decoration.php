<?php
/*
 * 自动跳转到与用户相关的最新一个换装，并转发请求的参数到该换装的永久地址
 */
$wx = new WeixinAPI();

$auth_info = $wx->get_oauth_info();
$users = get_users(array('meta_key'=>'wx_openid','meta_value'=>$auth_info->openid));

if(!$users){
	$query_args = array(
		'access_token'=>$auth_info->access_token,
		'forward_to'=>urlencode_deep(current_url())
	);
	header('Location: ' . site_url() . '/site-signup/?' . build_query($query_args));
	exit;
}

$user_id = $users[0]->ID;
$site_id = get_user_meta($user_id, 'site');
$site_decorations = get_posts(array('post_type'=>'site_decoration', 'posts_per_page'=>1, 'meta_key'=>'site_id', 'meta_value'=>$site_id));

if(empty($site_decorations)){
	exit('没有正在进行的换装');
}

$site_decoration_id = $site_decorations[0]->ID;

$decoration_id = get_post_meta($site_decoration_id, 'decoration', true);

header('Location: ' . get_permalink($decoration_id));
exit;
