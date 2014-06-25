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
			'url'=>$wx->generate_oauth_url('http://cmcc.uice.lu/latest-decoration/')
		),
		array(
			'name'=>'签收',
			'sub_button'=>array(
				array(
					'type'=>'view',
					'name'=>'签收',
					'url'=>$wx->generate_oauth_url('http://cmcc.uice.lu/my-latest-decoration/?action=recept-confirmation'),
				),
				array(
					'type'=>'view',
					'name'=>'上传',
					'url'=>$wx->generate_oauth_url('http://cmcc.uice.lu/my-latest-decoration/?action=result-upload')
				),
			)
		),
		array(
			'name'=>'汇总',
			'type'=>'view',
			'url'=>$wx->generate_oauth_url('http://cmcc.uice.lu/latest-decoration?action=total-result'),
		),
	)
);

$wx->create_menu($data);

header('Content-Type: application/json');
echo json_encode($wx->get_menu());