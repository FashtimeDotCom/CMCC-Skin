<?php
/*
 * 获得当前用户所在营业厅的营业厅换装，并列表
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
$site_id = get_user_meta($user_id, 'site');
$site_decorations = get_posts(array('post_type'=>'site_decoration', 'posts_per_page'=>-1, 'meta_key'=>'site_id', 'meta_value'=>$site_id));
get_header(); ?>

<header>
	<h1>所有换装</h1>
</header>

<table class="table table-bordered detail summary">
	<tbody>
		<?php foreach($site_decorations as $site_decoration){ ?>
		<tr>
			<td><a href="<?=get_the_permalink(get_post_meta($site_decoration->ID, 'decoration', true))?>"><?=$site_decoration->post_title?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php get_footer(); ?>
