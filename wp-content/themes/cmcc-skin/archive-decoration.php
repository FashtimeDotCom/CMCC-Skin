<?php
/*
 * 获得所有换装，并列表
 * 目前此页面仅用于结果统计，故会检查是否具有审核者权限
 */
$wx = new WeixinAPI();

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

wp_set_current_user($user_id);

if($_GET['action'] === 'result' && !current_user_can('review_site_result')){
	echo '你没有权限查看换装结果';
	exit;
}

get_header();
?>
<header>
	<h1>结果统计</h1>
</header>

<table class="table table-bordered detail summary">
	<tbody>
		<?php while(have_posts()): the_post(); ?>
		<tr>
			<td><a href="<?php the_permalink(); ?>?action=total-result"><?php the_title(); ?></a></td>
		</tr>
		<?php endwhile; ?>
	</tbody>
</table>
<?php get_footer(); ?>