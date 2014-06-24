<?php
/** 
 * 微信API响应页面，用来处理来自微信的请求
 */
$wx = new WeixinAPI();
		
// 验证请求来自微信
$wx->verify();

if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
	xml_parse_into_struct(xml_parser_create(), $GLOBALS["HTTP_RAW_POST_DATA"], $post);

	$post=array_column($post,'value','tag');

	if(!is_array($post)){
		exit('XML parse error.');
	}
}
