<?php
/*
 * 微信API响应页面，用来处理来自微信的请求
 */
$wx = new WeixinAPI();

$wx->verify();

$wx->onmessage('event', function($message){
	global $wx;
	if($message['EVENT'] === 'CLICK' && $message['EVENTKEY'] === 'error_report'){
		$text = "本页将作为营业厅报修通道\n报修将根据以下四种类别进行报修\n一、土建工程类\n二、设备类\n三、道具类\n四、POP类";
		$wx->reply_message($text, $message);
	}
});