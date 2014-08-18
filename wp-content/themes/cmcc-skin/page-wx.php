<?php
/*
 * 微信API响应页面，用来处理来自微信的请求
 */
$wx = new WeixinAPI();

$wx->verify();

$wx->onmessage('event', function($message){
	global $wx;
	if($message['EVENT'] === 'CLICK' && $message['EVENTKEY'] === 'error_report'){
		$text = "本功能只接受营业厅传播类物料报障，其他报障请播1号通\n报障格式：\n营业厅-联系人-联系电话\n报障内容\n报障时间\n例：\n上海南站-XX-13916026852\n上墙灯箱不亮\n2014-08-16";
		$wx->reply_message($text, $message);
	}
});