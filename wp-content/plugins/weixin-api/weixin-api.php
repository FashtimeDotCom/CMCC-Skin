<?php
/**
 * Plugin Name: Weixin API
 * Plugin URI: 
 * Description: 
 * Version: 0.1
 * Author: Uice Lu
 * Author URI: https://cecilia.uice.lu/
 * License: 
 */
class WeixinAPI {
	
	private $partner_id;
	private $partner_key;
	private $pay_sign_key;
	private $app_id;
	private $app_secret;
	private $token;
	
	function __construct() {
		$this->partner_id = get_option('wx_partner_id');
		$this->partner_key = get_option('wx_partner_key');
		$this->pay_sign_key = get_option('wx_pay_sign_key');
		$this->app_id = get_option('wx_app_id');
		$this->app_secret = get_option('wx_app_secret');
		$this->token = get_option('wx_token');
	}
	
	/*
	 * 验证来源为微信
	 */
	function verify(){
		$sign = array(
			$this->token,
			$_GET['timestamp'],
			$_GET['nonce']
		);

		sort($sign, SORT_STRING);

		if(sha1(implode($sign)) !== $_GET['signature']){
			exit('Signature verification failed.');
		}
	}
	
	function get_access_token(){
		
		//TODO 似乎缓存access_token有点问题
		$stored = json_decode(get_option('wx_access_token'));
		
		if($stored && $stored->expires_at > time()){
			return $stored->token;
		}
		
		$query_args = array(
			'grant_type'=>'client_credential',
			'appid'=>$this->app_id,
			'secret'=>$this->app_secret
		);
		
		$return = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?' . http_build_query($query_args)));
		
		if($return->access_token){
			update_option('wx_access_token', json_encode(array('token'=>$return->access_token, 'expires_at'=>time() + $return->expires_in - 60)));
			return $return->access_token;
		}
		
		return false;
		
	}
	
	function get_user_info($openid, $lang = 'zh_CN'){
		
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?';
		
		$query_vars = array(
			'access_token'=>$this->get_access_token(),
			'openid'=>$openid,
			'lang'=>$lang
		);
		
		$url .= http_build_query($query_vars);
		
		$user_info = json_decode(file_get_contents($url));
		
		return $user_info;
		
	}
	
	/**
	 * 生成授权地址
	 * @param type $redirect_uri
	 * @param type $scope
	 * @return string
	 */
	function oauth_redirect($redirect_uri = null, $state = '', $scope = 'snsapi_base', $redirect = true){
		
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
		
		$query_args = array(
			'appid'=>$this->app_id,
			'redirect_uri'=>is_null($redirect_uri) ? site_url() : $redirect_uri,
			'response_type'=>'code',
			'scope'=>$scope,
			'state'=>$state
		);
		
		$url .= http_build_query($query_args) . '#wechat_redirect';
		
		if($redirect){
			header('Location: ' . $url);
			exit;
		}
		
		return $url;
	}
	
	function get_oauth_token($code){
		
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

		$query_args = array(
			'appid'=>$this->app_id,
			'secret'=>$this->app_secret,
			'code'=>$code,
			'grant_type'=>'authorization_code'
		);

		$auth_result = json_decode(file_get_contents($url . http_build_query($query_args)));

		if(!isset($auth_result->openid)){
			echo 'Authentication failed. ';
			echo json_encode($auth_result);
			exit;
		}
		
		$auth_result->expires_at = $auth_result->expires_in + time();
		
		update_option('wx_oauth_token_' . $auth_result->openid, json_encode($auth_result));
		
		return $auth_result;
	}
	
	function refresh_oauth_token($refresh_token){
		
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
		
		$query_args = array(
			'appid'=>$this->app_id,
			'grant_type'=>'refresh_token',
			'refresh_token'=>$refresh_token,
		);
		
		$url .= http_build_query($query_args);
		
		$auth_result = json_decode(file_get_contents($url));
		
		return $auth_result;
	}
	
	/*
	 * OAuth方式获得用户信息
	 */
	function oauth_get_user_info($openid, $lang = 'zh_CN'){
		
		$url = 'https://api.weixin.qq.com/sns/userinfo?';
		
		$auth_info = json_decode(get_option('wx_oauth_token_' . $openid));
		
		if(!$auth_info){
			$auth_info = $this->get_oauth_token($_GET['code']);
		}
		elseif($auth_info->expires_at <= time()){
			$auth_info = $this->refresh_oauth_token($auth_info->refresh_token);
		}
		
		$query_vars = array(
			'access_token'=>$auth_info->access_token,
			'openid'=>$openid,
			'lang'=>$lang
		);
		
		$url .= http_build_query($query_vars);
		
		$user_info = json_decode(file_get_contents($url));
		
		return $user_info;
	}
	
	function generate_js_pay_args($notify_url, $order_id, $total_price, $order_name, $attach = ' '){
		
		$package_data = array(
			'bank_type'=>'WX',
			'body'=>$order_name,
			'attach'=>$attach,
			'partner'=>$this->partner_id,
			'out_trade_no'=>$order_id,
			'total_fee'=>(string)(int) ($total_price * 100),
			'fee_type'=>'1',
			'notify_url'=>$notify_url,
			'spbill_create_ip'=>$_SERVER['REMOTE_ADDR'],
			'input_charset'=>'UTF-8'
		);

		ksort($package_data, SORT_STRING);

		$string1 = urldecode(http_build_query($package_data));
		$stringSignTemp = $string1 . '&key=' . get_option('wx_partner_key');
		$signValue = strtoupper(md5($stringSignTemp));
		$string2 = http_build_query($package_data, null, null, PHP_QUERY_RFC3986);
		$package = $string1 . '&sign=' . $signValue;

		$nonce_str = (string) rand(1E15, 1E16-1);
		$timestamp = time();

		$pay_sign_data = array(
			'appid'=>get_option('wx_app_id'),
			'timestamp'=>$timestamp,
			'noncestr'=>$nonce_str,
			'package'=>$package,
			'appkey'=>get_option('wx_pay_sign_key')
		);

		ksort($pay_sign_data, SORT_STRING);
		$string1 = urldecode(http_build_query($pay_sign_data));
		$pay_sign = sha1($string1);

		$pay_request_args = array(
			'appId'=>(string) get_option('wx_app_id'),
			'timeStamp'=>(string) $timestamp,
			'nonceStr'=>(string) $nonce_str,
			'package'=>$package,
			'signType'=>'SHA1',
			'paySign'=>$pay_sign,
		);
		
		return $pay_request_args;
	}
	
	function generate_js_edit_address_args(){
		
		$args = array(
			'appId'=>(string) $this->app_id,
			'scope'=>'jsapi_address',
			'signType'=>'sha1',
			'addrSign'=>'',
			'timeStamp'=>(string) time(),
			'nonceStr'=>(string) rand(1E15, 1E16-1)
		);
		
		$sign_args = array(
			'appid'=>$this->app_id,
			'url'=>"http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
			'timestamp'=>$args['timeStamp'],
			'noncestr'=>$args['nonceStr'],
			'accesstoken'=>$this->get_oauth_token($_GET['code'])->access_token
		);

		ksort($sign_args, SORT_STRING);
		$string1 = urldecode(http_build_query($sign_args));
		
		$args['addrSign'] = sha1($string1);

		return $args;
		
	}
	
	function generate_qr_code($scene_id, $action_info = array(), $action_name = 'QR_SCENE', $expires_in = '1800'){
		// TODO scene_id 应该要可以自动生成
		// TODO 过期scene应该要回收
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->get_access_token();
		
		$action_info['scene']['scene_id'] = $scene_id;
		
		$post_data = array(
			'expire_seconds'=>$expires_in,
			'action_name'=>$action_name,
			'action_info'=>$action_info,
		);
		
		$ch = curl_init($url);
		
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($post_data)
		));
		
		$response = json_decode(curl_exec($ch));
		
		if(!property_exists($response, 'ticket')){
			return $response;
		}
		
		$qrcode = array(
			'url'=>'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($response->ticket),
			'expires_at'=>time() + $response->expire_seconds,
			'action_info'=>$action_info,
			'ticket'=>$response->ticket
		);
		
		update_option('wx_qrscene_' . $scene_id, json_encode($qrcode));
		
		return $qrcode;
		
	}
	
	function remove_menu(){
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $this->get_access_token();
		return json_decode(file_get_contents($url));
	}
	
	function create_menu($data){
		
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->get_access_token();
		
		$ch = curl_init($url);
		
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json'
			),
			CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE)
		));
		
		$response = json_decode(curl_exec($ch));
		
		return $response;
		
	}
	
}
