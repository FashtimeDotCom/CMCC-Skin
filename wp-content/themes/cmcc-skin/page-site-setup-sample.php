<?php get_header(); ?>

<header>
	<h1>器架 / 画面安装规范</h1>
</header>

<?php if(empty($_GET['ID'])){ $examples = get_posts(array('posts_per_page'=>-1)); ?>
<table class="table table-bordered detail summary">
	<tbody>
		<?php foreach($examples as $example){ ?>
		<tr>
			<td><a href="<?=site_url()?>/site-setup-sample/?ID=<?=$example->ID?>"><?=$example->post_title?></a></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php } else{ $example = get_post($_GET['ID']); ?>
<h2><?=$example->post_title?></h2>
<?=$example->post_content?>
<?php } ?>
<?php get_footer(); ?>
