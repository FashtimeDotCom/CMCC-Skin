<?php get_header(); ?>
<header>
	<h1>
		管理引导
	</h1>
</header>

<table class="table table-bordered detail summary">
	<tbody>
		<tr>
			<td><a href="<?=site_url()?>/decoration/?tag=画面&action=requirement">换装发布</a></td>
		</tr>
		<tr>
			<td><a href="<?=site_url()?>/decoration/?tag=器架&action=requirement">物料下发</a></td>
		</tr>
		<tr>
			<td><a href="<?=site_url()?>/decoration/?action=result">汇总</a></td>
		</tr>
	</tbody>
</table>
<?php get_footer(); ?>
