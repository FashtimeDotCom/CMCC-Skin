<?php
/*
 * 微信API响应页面，用来处理来自微信的请求
 */
$wx = new WeixinAPI();

$wx->verify();

$wx->onmessage('event', function($message){
	global $wx;
	if($message['EVENT'] === 'CLICK' && $message['EVENTKEY'] === 'error_report'){
		$text = "一.本页作为营业厅报障快速通道，用以解决营业厅内需要应急处理的故障信息。
营业厅在陈述故障的原因时，请在陈述信息前括号标明故障类别。\n1.【土建工程类】\n2.【设备类】\n3.【道具类】\n4.【POP类】\n例：\n【土建工程类】天花板漏水，8月4日。";
		$wx->reply_message($text, $message);
	}
});