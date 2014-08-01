<?php
/**
 *  访问这个页面，会删除现有微信自定义菜单，并重建
 */
$wx = new WeixinAPI();

$wx->remove_menu();

$data = array(
	'button'=>array(
		array(
			'name'=>'通知',
			'sub_button'=>array(
				array(
					'name'=>'换装发布',
					'type'=>'view',
					'url'=>$wx->generate_oauth_url(site_url() . '/site_decoration/')
				),
				array(
					'name'=>'物料发布',
					'type'=>'view',
					'url'=>$wx->generate_oauth_url(site_url() . '/site_decoration/')
				),
			)
		),
		array(
			'name'=>'签收',
			'sub_button'=>array(
				array(
					'type'=>'view',
					'name'=>'器架签收',
					'url'=>$wx->generate_oauth_url(site_url() . '/site_decoration/?action=recept-confirmation&step=frame'),
				),
				array(
					'type'=>'view',
					'name'=>'画面签收',
					'url'=>$wx->generate_oauth_url(site_url() . '/site_decoration/?action=recept-confirmation&step=picture'),
				),
				array(
					'type'=>'view',
					'name'=>'上传',
					'url'=>$wx->generate_oauth_url(site_url() . '/site_decoration/?action=result-upload')
				),
			)
		),
		array(
			'name'=>'汇总',
			'sub_button'=>array(
				array(
					'name'=>'汇总',
					'type'=>'view',
					'url'=>$wx->generate_oauth_url(site_url() . '/decoration/?action=result'),
				),
				array(
					'name'=>'报障',
					'type'=>'click',
					'key'=>'error_report'
				),
			),
		),
	)
);

$wx->create_menu($data);

header('Content-Type: application/json');
echo json_encode($wx->get_menu());