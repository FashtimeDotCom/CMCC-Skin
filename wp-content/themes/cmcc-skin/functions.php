<?php
add_action('init', function(){
	
	show_admin_bar(false);
	
	add_theme_support('post-thumbnails');
	
	add_image_size('decoration-picture', 150, 351, true);
	
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
		
			add_meta_box('base_info', '基本信息', function($post){
				require get_template_directory() . '/admin/decoration_base_info.php';
			}, 'decoration', 'normal');
		
			add_meta_box('pictures', '画面', function($post){
				$pictures = json_decode(get_post_meta($post->ID, 'pictures', true), JSON_OBJECT_AS_ARRAY);
				!$pictures && $pictures = array();
				require get_template_directory() . '/admin/decoration-picture.php';
			}, 'decoration', 'normal');
			
			add_meta_box('frame-picture-sheet', '各营业厅器架画面对应表', function($post){
				$sheets = json_decode(get_post_meta($post->ID, 'sheets', true), JSON_OBJECT_AS_ARRAY);
				!$sheets && $sheets = array();
				// TODO 已导入的文件如果从美体裤删除，会在这里显示一个空项
				$site_decorations = get_posts(array('post_type'=>'site_decoration', 'meta_key'=>'decoration', 'meta_value'=>$post->ID, 'posts_per_page'=>-1));
				require get_template_directory() . '/admin/decoration-frame-picture-sheet.php';
			}, 'decoration', 'normal');
		}
	));
	
	register_post_type('site_decoration', array(
		'label'=>'营业厅换装',
		'public'=>true,
		'show_ui'=>false
	));
	
	isset($_POST['sheets']) && add_action('save_post', 'import_site_decoration_sheet');
	add_action('save_post', 'update_metas');
	
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
				'requirement',
				'date',
				'instruction',
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

					$site_query_result = get_posts(array('post_type'=>'site', 'name'=>$site_name, 'posts_per_page'=>-1));

					if(empty($site_query_result)){
						add_user_meta(get_current_user_id(), '_admin_notice', 'error: 系统中没有' . $site_name . '，请先添加这个营业厅');
						continue;
					}

					$site_id = $site_query_result[0]->ID;

					$highestColumn = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());
					$highestRow = $sheet->getHighestRow();

					$header = array(); // 表头，列号和列名对照关系

					for($column = 0; $column <= $highestColumn; $column++){
						$header[$column] = $sheet->getCellByColumnAndRow($column, 1)->getValue();
					}

					if(array_diff(array('器架名称', '画面位置'), $header)){
						add_user_meta(get_current_user_id(), '_admin_notice', 'error: 表格必须包含“器架名称”和画面位置2列，请检查并修改后重新上传');
						wp_delete_post($sheet_id);
						unset($sheets[$sheet_id]);
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

					$site_decoration_query_result = get_posts(array('post_type'=>'site_decoration', 'meta_query'=>array(array('key'=>'site', 'value'=>$site_id),array('key'=>'decoration', 'value'=>$post_id)), 'posts_per_page'=>-1));

					// 如果没有这个营业厅的换装数据，先创建
					// TODO decoration的pictures和sheets meta数据会被一同保存到site_decoration中，目前不影响使用
					if(empty($site_decoration_query_result)){
						$site_decoration_id = wp_insert_post(array(
							'post_type'=>'site_decoration',
							'post_title'=>$site_name . ' - ' . get_post($post_id)->post_title,
							'post_status'=>'publish',
						));
					}
					else{
						$site_decoration_id = $site_decoration_query_result[0]->ID;
					}

					// 拼出器架和画面数据
					$frames = array();
					foreach($table as $row){
						!isset($frames[$row['器架名称']]) && $frames[$row['器架名称']] = array('quantity'=>0, 'received'=>false, 'pictures_received'=>false, 'pictures'=>array());
						$frames[$row['器架名称']]['quantity'] ++;
						$row['画面位置'] && $frames[$row['器架名称']]['pictures'][] = array('position'=>$row['画面位置'], 'received'=>false);
					}

					update_post_meta($site_decoration_id, 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
					add_post_meta($site_decoration_id, 'site_id', $site_id);
					add_post_meta($site_decoration_id, 'decoration', $post_id);
					add_post_meta($site_decoration_id, 'frames_received', false);
					add_post_meta($site_decoration_id, 'pictures_received', false);
					add_post_meta($site_decoration_id, 'reviewed', false);
					add_post_meta($site_decoration_id, 'site_region', get_post_meta($site_id, 'region', true));
					
					$status = 'imported';
					add_user_meta(get_current_user_id(), '_admin_notice', 'updated: 已导入文件 ' . get_post($sheet_id)->post_title . ' 中的数据');
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
	wp_register_style('mi', get_template_directory_uri() . '/css/mi.css');
	wp_register_script('bootstrap', 'http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js');
	wp_register_script('swipe', get_template_directory_uri() . '/js/swipe.js');
	wp_register_style('style', get_template_directory_uri() . '/style.css', array(), '2014-07-15');
	wp_enqueue_style('font-awesome');
	wp_enqueue_style('bootstrap');
	wp_enqueue_style('style');
	wp_enqueue_script('jquery');
	// TODO 小米微信中才需要载入的样式，还应加入微信MicroMessege UA 字串
	if(strpos($_SERVER['HTTP_USER_AGENT'], ' MI ') !== false){
		wp_enqueue_style('mi');
	}
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

add_action('admin_head-post-new.php', 'change_thumbnail_html');
add_action('admin_head-post.php', 'change_thumbnail_html');
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

add_action('admin_notices', function(){
	$notices = get_user_meta(get_current_user_id(), '_admin_notice');
	foreach($notices as $notice){
		$part = preg_split('/\: /', $notice);
		$type = $part[0];
		$message = $part[1];
?>
<div class="<?=$type?>">
	<p><?=$message?></p>
</div>
<?php
	}
	delete_user_meta(get_current_user_id(), '_admin_notice');
});

add_action('admin_init', function(){
	get_role('editor')->add_cap('review_site_result');
});

// TODO 这么做只是为了使用中文名称注册营业厅管理人员，存在一些安全性问题
add_filter('sanitize_user', function( $username, $raw_username, $strict ) {
	if( !$strict )
		return $username;
	return sanitize_user(stripslashes($raw_username), false);
}, 10, 3);

if(!function_exists('current_url')){
	function current_url(){
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}

// add columns to User panel list page
add_filter('manage_users_columns', function($column) {
	
	$column = array (
		'cb' => '<input type="checkbox" />',
		'username' => '用户名',
		'name' => '姓名',
		'email' => '电子邮件',
		'phone' => '手机',
		'role' => '角色',
		'posts' => '文章',
	);
    
    return $column;
	
});

// add the data
add_filter('manage_users_custom_column', function ($val, $column_name, $user_id){
    switch ($column_name) {
        case 'phone' :
            return get_user_meta($user_id, 'phone', true);
        default:
    }
    return;
}, 10, 3 );

/**
 * add extra field for user in admin panel
 */
add_filter('user_contactmethods', function($profile_fields) {

	$profile_fields=array(
		'phone'=>'手机',
	);

	return $profile_fields;
});