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
		'forward_to'=>current_url()
	);
	header('Location: ' . site_url() . '/site-signup/?' . build_query($query_args));
	exit;
}else{
	$user_id = $users[0]->ID;
}
