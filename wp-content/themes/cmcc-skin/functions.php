<?php
add_action('init', function(){
	
	show_admin_bar(false);
	
	add_theme_support('post-thumbnails');
	
	register_post_type('site', array(
		'labels'=>array(
			'name'=>'营业厅',
			'all_items'=>'所有营业厅',
			'add_new'=>'添加',
			'add_new_item'=>'添加营业厅',
			'edit_item'=>'编辑营业厅',
			'new_item'=>'新营业厅',
			'view_item'=>'查看营业厅',
			'search_items'=>'搜索营业厅',
			'not_found'=>'未找到营业厅'
		),
		'public'=>true,
		'supports'=>array('title','thumbnail'),
		'has_archive'=>true,
		'register_meta_box_cb'=>function($post){
			add_meta_box('properties', '营业厅信息', function($post){
				$regions = json_decode(get_option('regions', '[]'));
				require get_template_directory() . '/admin/site-info-meta-box.php';
			}, 'site', 'normal');
			remove_meta_box( 'postimagediv', 'site', 'side');
			add_meta_box('postimagediv', __('营业厅点位图'), 'post_thumbnail_meta_box', 'site', 'side');
		},
		'menu_icon'=>'dashicons-admin-home'
	));
	
	register_post_type('decoration', array(
		'labels'=>array(
			'name'=>'换装',
			'all_items'=>'所有换装',
			'add_new'=>'发布',
			'add_new_item'=>'发布换装',
			'edit_item'=>'编辑换装',
			'new_item'=>'新换装',
			'view_item'=>'查看换装',
			'search_items'=>'搜索换装',
			'not_found'=>'未找到换装'
		),
		'public'=>true,
		'supports'=>array('title','thumbnail'),
		'menu_icon'=>'dashicons-art',
		'register_meta_box_cb'=>function($post){
			add_meta_box('pictures', '画面', function($post){
				$pictures = json_decode(get_post_meta($post->ID, 'pictures', true), JSON_OBJECT_AS_ARRAY);
				!$pictures && $pictures = array();
				require get_template_directory() . '/admin/decoration-picture.php';
			}, 'decoration', 'normal');
			
			add_meta_box('frame-picture-sheet', '各营业厅器架画面对应表', function($post){
				$sheets = json_decode(get_post_meta($post->ID, 'sheets', true), JSON_OBJECT_AS_ARRAY);
				!$sheets && $sheets = array();
				require get_template_directory() . '/admin/decoration-frame-picture-sheet.php';
			}, 'decoration', 'normal');
		}
	));
	
	register_post_type('site_decoration', array(
		'label'=>'营业厅换装',
	));
	
	isset($_POST['sheets']) && add_action('save_post', import_site_decoration_sheet);
	add_action('save_post', update_metas);
	
	function update_metas($post_id){

		if($_POST['post_type'] === 'site'){
			$metas = array(
				'region',
				'site_map',
				'manager',
				'manager_phone',
				'phone',
				'address',
			);
		}
		elseif($_POST['post_type'] === 'decoration'){
			$metas = array(
				'pictures',
				'sheets'
			);
		}
		else{
			$metas = array();
		}

		foreach($metas as $field){
			if(isset($_POST[$field])){
				update_post_meta($post_id, $field, $_POST[$field]);
			}
		}
		
	}
	
	function import_site_decoration_sheet($post_id){
		
		remove_action( 'save_post', 'import_site_decoration_sheet', 10 );
		
		$sheets = json_decode(stripslashes($_POST['sheets']));

		if($sheets !== false){
			foreach($sheets as $sheet_id => &$status){

				// 合并后的数据更新到营业厅换装中
				if($status === 'queued'){
					// import excel, create site_decorations
					$path = get_attached_file($sheet_id);

					$excel = PHPExcel_IOFactory::load($path);
					$sheet = $excel->getSheet();

					$site_name = $sheet->getTitle();

					$site_query_result = get_posts(array('post_type'=>'site', 'name'=>$site_name));

					if(empty($site_query_result)){
						exit('系统中没有' . $site_name . '，请先添加这个营业厅');
					}

					$site_id = $site_query_result[0]->ID;

					$highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
					$highestRow = $sheet->getHighestRow();

					$header = array(); // 表头，列号和列名对照关系

					for($column = 0; $column <= $highestColumn; $column++){
						$header[$column] = $sheet->getCellByColumnAndRow($column, 1)->getValue();
					}

					if(array_diff(array('器架名称', '画面位置'), $header)){
						exit('表格必须包含“器架名称”和画面位置2列');
						continue;
					}

					$table = array();

					for($row = 2; $row <= $highestRow; $row++){

						$row_data = array();

						for($column = 0; $column <= $highestColumn; $column++){
							$value = $sheet->getCellByColumnAndRow($column, $row)->getValue();
							// TODO 检查画面和位置是否合法
							$row_data[$header[$column]] = $value;
						}

						$table[] = $row_data;

					}

					$site_decoration_query_result = get_posts(array('post_type'=>'site_decoration', 'meta_key'=>'site', 'meta_value'=>$site_id));

					// 如果没有这个营业厅的换装数据，先创建
					// TODO decoration的pictures和sheets meta数据会被一同保存到site_decoration中，目前不影响使用
					if(empty($site_decoration_query_result)){
						$site_decoration_id = wp_insert_post(array(
							'post_type'=>'site_decoration',
							'post_title'=>$site_name . ' - ' . get_post($post_id)->post_title,
							'post_status'=>'published',
						));
					}
					else{
						$site_decoration_id = $site_decoration_query_result[0]->ID;
					}

					// 拼出器架和画面数据
					$frames = array();
					foreach($table as $row){
						!isset($frames[$row['器架名称']]) && $frames[$row['器架名称']] = array('quantity'=>0, 'received'=>false, 'pictures'=>array());
						$frames[$row['器架名称']]['quantity'] ++;
						$frames[$row['器架名称']]['pictures'][] = array('position'=>$row['画面位置'], 'received'=>false);
					}

					update_post_meta($site_decoration_id, 'frames', json_encode($frames));
					add_post_meta($site_decoration_id, 'site_id', $site_id);
					add_post_meta($site_decoration_id, 'decoration', $post_id);
					add_post_meta($site_decoration_id, 'frames_received', false);
					add_post_meta($site_decoration_id, 'pictures_received', false);
					add_post_meta($site_decoration_id, 'reviewed', false);
					
					$status = 'imported';
				}
			}
		}

		$_POST['sheets'] = json_encode($sheets);
		
		add_action( 'save_post', 'import_site_decoration_sheet', 10 );
		
	}
});

add_action('wp_enqueue_scripts', function(){
	wp_register_style('bootstrap', 'http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css');
	wp_register_style('font-awesome', '//libs.baidu.com/fontawesome/4.0.3/css/font-awesome.min.css');
	wp_register_script('bootstrap', 'http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js');
	wp_register_script('swipe', get_template_directory_uri() . '/js/swipe.js');
	wp_register_style('style', get_template_directory_uri() . '/style.css');
	wp_enqueue_style('font-awsome');
	wp_enqueue_style('bootstrap');
	wp_enqueue_style('style');
	wp_enqueue_script('jquery');
});

add_action('wp_foot', function(){
	wp_enqueue_script('bootstrap');
});

add_action('admin_enqueue_scripts', function(){
	wp_register_style('cmcc-admin', get_template_directory_uri() . '/admin/style.css');
	wp_register_script('cmcc-admin', get_template_directory_uri() . '/admin/script.js', array('jquery'));
	wp_enqueue_style('cmcc-admin');
	wp_enqueue_script('cmcc-admin');
});

add_action('admin_head-post-new.php', change_thumbnail_html);
add_action('admin_head-post.php', change_thumbnail_html);
function change_thumbnail_html( $content ) {
    if ('site' == $GLOBALS['post_type'])
      add_filter('admin_post_thumbnail_html',do_thumb);
}
function do_thumb($content){
	 return str_replace(__('Set featured image'), __('设置营业厅点位图'), $content);
}

add_filter('body_class', function($classes) {
	global $post;
	if (isset($post)) {
		$classes[] = $post->post_type . '-' . $post->post_name;
	}
	return $classes;
});