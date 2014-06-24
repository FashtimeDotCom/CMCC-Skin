<?php
/**
 *  访问这个页面，会删除现有微信自定义菜单，并重建
 */
$wx = new WeixinAPI();

$wx->remove_menu();

$data = array(
	'button'=>array(
		array(
			'name'=>'换装',
			'type'=>'view',
			'url'=>'http://cmcc.uice.lu/latest-decoration/'
		),
		array(
			'name'=>'签收',
			'sub_button'=>array(
				array(
					'type'=>'view',
					'name'=>'签收',
					'url'=>$wx->oauth_redirect('http://cmcc.uice.lu/my-latest-decoration/?action=reception', '', 'snsapi_base', false),
				),
				array(
					'type'=>'view',
					'name'=>'上传',
					'url'=>$wx->oauth_redirect('http://cmcc.uice.lu/my-latest-decoration/?action=upload', '', 'snsapi_base', false)
				),
			)
		),
		array(
			'name'=>'管理',
			'sub_button'=>array(
				array(
					'type'=>'view',
					'name'=>'片区',
					'url'=>$wx->oauth_redirect('http://cmcc.uice.lu/latest-decoration?action=region-result', '', 'snsapi_base', false),
				),
				array(
					'type'=>'view',
					'name'=>'总览',
					'url'=>$wx->oauth_redirect('http://cmcc.uice.lu/latest-decoration?action=total-result', '', 'snsapi_base', false),
				),
			)
		),
	)
);

var_export($wx->create_menu($data));