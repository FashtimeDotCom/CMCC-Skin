<?php
/*
 * 获得所有换装，并列表
 * 目前此页面仅用于结果统计，故会检查是否具有审核者权限
 */
$wx = new WeixinAPI();

if(!is_user_logged_in()){
	$auth_info = $wx->get_oauth_info();
	$users = get_users(array('meta_key'=>'wx_openid','meta_value'=>$auth_info->openid));

	if(!$users){
		$query_args = array(
			'access_token'=>$auth_info->access_token,
			'forward_to'=>urlencode_deep(current_url())
		);
		header('Location: ' . site_url() . '/site-signup/?' . build_query($query_args));
		exit;
	}
	
	$user_id = $users[0]->ID;

	wp_set_auth_cookie($user_id);
	wp_set_current_user($user_id);

}

if($_GET['action'] === 'result' && !current_user_can('view_total_result') && !current_user_can('view_region_result')){
	header('Location: ' . $wx->generate_oauth_url(site_url() . '/my-latest-decoration/?action=result'));
	exit;
}

get_header();
?>
<header>
	<h1>
		<?php if($_GET['action'] === 'requirement'){ ?>发布<?php }else{ ?>结果查看<?php } ?>
	</h1>
</header>

<table class="table table-bordered detail summary">
	<tbody>
		<?php while(have_posts()): the_post(); ?>
		<tr>
			<td><a href="<?php the_permalink(); ?>?action=<?php if(isset($_GET['action']) && $action !== 'result'){ ?><?=$_GET['action']?><?php }elseif(current_user_can('view_total_result')){ ?>total-result<?php }else{ ?>region-result&region=<?=get_user_meta(get_current_user_id(), 'region', true)?><?php } ?>"><?php the_title(); ?></a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>
<?php get_footer(); ?>
