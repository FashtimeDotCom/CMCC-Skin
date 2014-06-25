<?php
/*
 * Sub Template in Single "site_decoration"
 */
$result_positions = json_decode(get_option('result_upload_positions'));
$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));

if(isset($_GET['reviewed']) && $_GET['reviewed']){
	update_post_meta(get_the_ID(), 'reviewed', true);
	header('Location: ' . get_the_permalink($decoration_id) . '?action=region-result&region=' . get_post_meta(get_the_ID(), 'site_region', true));
}

?>

<header>
	<h1><?=get_post($site_id)->post_title?></h1>
</header>

<table class="table table-bordered summary">
	<thead>
		<tr>
			<th colspan="2"><?=get_post($site_id)->post_title?>点位图</th>
		</tr>
		<tr>
			<th colspan="2" style="background:#FFF"><a href="<?=wp_get_attachment_url(get_post_meta($site_id, '_thumbnail_id', true))?>"><?=get_the_post_thumbnail($site_id, 'large')?></a></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>厅负责人</th>
			<td><?=get_post_meta($site_id, 'manager', true)?></td>
		</tr>
		<tr>
			<th>厅负责人方式</th>
			<td><?=get_post_meta($site_id, 'manager_phone', true)?></td>
		</tr>
		<tr>
			<th>营业厅电话</th>
			<td><?=get_post_meta($site_id, 'phone', true)?></td>
		</tr>
		<tr>
			<th>地址</th>
			<td><?=get_post_meta($site_id, 'address', true)?></td>
		</tr>
	</tbody>
</table>

<header style="margin-top: 5px;">
	<h1><?=get_post($site_id)->post_title?>换装情况</h1>
</header>
<div class="result-upload">
	<?php foreach($result_positions as $slug => $name){ ?>
	<div class="row">
		<div class="col-xs-12">
			<h2><?=$name?></h2>
			<a href="<?=wp_get_attachment_url($result_photos->$slug)?>"><?=wp_get_attachment_image($result_photos->$slug, 'large')?></a>
		</div>
	</div>
	<?php } ?>
	<div class="form-actions">
		<?php if(current_user_can('review_site_result')){ ?>
		<button onclick="window.location.search += '&reviewed=1'" class="btn btn-success fa fa-check"> 审核通过</button>
		<?php } ?>
	</div>
</div>
