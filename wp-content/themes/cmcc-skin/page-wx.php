<?php
/*
 * 微信API响应页面，用来处理来自微信的请求
 */
$wx = new WeixinAPI();

$wx->verify();

$wx->onmessage('event', function($message){
	global $wx;
	if($message['EVENT'] === 'CLICK' && $message['EVENTKEY'] === 'error_report'){
		$text = "本页将作为营业厅报修通道，用以向上级申报厅内现有故障。\n营业厅请根据故障类别点击下列相应的报障通道进行申报\n一、土建工程类电路、装修等故障）\n二、设备类（电子、影印设备等）\n三、道具类（器架、台卡等）\n四、POP类（海报、吊旗等）";
		$wx->reply_message($text, $message);
	}
});