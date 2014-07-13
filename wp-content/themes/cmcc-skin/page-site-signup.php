<?php

$wx = new WeixinAPI();

$auth_info = $wx->get_oauth_info();

if(isset($_POST['signup'])){
	
	$sites = get_posts(array('post_type'=>'site','name'=>$_POST['site_name']));
	empty($sites) && exit('Site name invalid.');
	
	$site_id = $sites[0]->ID;
	
	$user_id = wp_create_user($_POST['username'], $auth_info->openid);
	
	add_user_meta($user_id, 'wx_openid', $auth_info->openid, true);
	add_user_meta($user_id, 'phone', $_POST['phone']);
	add_user_meta($user_id, 'site', $site_id);
	
	wp_set_current_user($user_id);
	wp_set_auth_cookie($user_id);
	
	headers_sent() && exit('Redirect failed, headers already sent.');
	header('Location: ' . urldecode($_GET['forward_to']) . '&access_token=' . $_GET['access_token']);
	exit;
}

$sites = get_posts(array('post_type'=>'site', 'posts_per_page'=>-1));
$region_sites = array();
foreach($sites as $site){
	$region = get_post_meta($site->ID, 'region', true);
	if(empty($region_sites[$region])){
		$region_sites[$region] = array();
	}
	$region_sites[$region][] = $site->post_title;
}

get_header();
?>

<header>
	<h1><img src="<?=get_template_directory_uri()?>/img/title.png"></h1>
</header>

<div class="input-form">
	<form id="site-signup-form" method="post" class="form-horizontal">
		<div class="form-group">
			<label for="region" class="col-xs-4 control-label">区域</label>
			<div class="col-xs-8">
				<select id="region" name="region" class="form-control">
					<?php foreach(json_decode(get_option('regions')) as $region){ ?>
					<option value="<?=$region?>"><?=$region?></option>
					<?php } ?>
				</select>
				<!--<input id="region" name="region" type="text" class="form-control" />-->
			</div>
		</div>
		<div class="form-group">
			<label for="site" class="col-xs-4 control-label">营业厅</label>
			<div class="col-xs-8 ">
				<select id="site" name="site_name" class="form-control">
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="manager" class="col-xs-4 control-label">负责人</label>
			<div class="col-xs-8">
				<input id="manager" name="username" type="text" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label for="phone" class="col-xs-4 control-label">手机</label>
			<div class="col-xs-8">
				<input id="phone" name="phone" type="text" class="form-control" />
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" name="signup" class="btn btn-default">注册新用户</button>
		</div>
	</form>
</div>

<script type="text/javascript">
jQuery(function($){
	
		var regionSites = <?=json_encode($region_sites, JSON_UNESCAPED_UNICODE)?>;
	
	$('#region').on('change', function(){
		var sites = regionSites[$(this).val()];
		$('#site').empty();
		for(var i = 0; i < sites.length; i ++){
			$('#site').append($('<option/>', {value: sites[i], text: sites[i]}));
		}
	}).trigger('change');
	
	$('#site-signup-form').on('submit', function(){
		if($(this).find(':input[name="username"]').val() === ''){
			alert('请填写负责人名称');
			return false;
		}
		
		var phone = $(this).find(':input[name="phone"]').val();
		if(phone.length !== 11 || phone.substr(0, 1) !== '1'){
			alert('请填写正确的手机号');
			return false;
		}
		
	});
	
});
</script>

<?php get_footer(); ?>
