<?php
/*
 * Sub Template in Single "decoration"
 */

$region = $_GET['region'];
$site_decorations = get_posts(array('post_type'=>'site_decoration', 'meta_query'=>array(array('key'=>'decoration', 'value'=>get_the_ID()), array('key'=>'site_region', 'value'=>$region)), 'posts_per_page'=>-1));
?>
<header>
	<h1><?=$region?></h1>
</header>

<table class="table table-bordered summary">
	<thead>
		<tr>
			<th>营业厅名称</th>
			<td>物料</td>
			<td>画面</td>
			<td>审核</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($site_decorations as $site_decoration){ ?>
		<tr>
			<td>
				<a href="<?=get_the_permalink($site_decoration->ID)?>?action=result">
					<?=get_post(get_post_meta($site_decoration->ID, 'site_id', true))->post_title?>
					<span class="arrow">&raquo;</span>
				</a>
			</td>
			<td><?php if(get_post_meta($site_decoration->ID, 'frames_received', true)){ ?><span class="fa fa-check"></span><?php } ?></td>
			<td><?php if(get_post_meta($site_decoration->ID, 'pictures_received', true)){ ?><span class="fa fa-check"></span><?php } ?></td>
			<td><?php if(get_post_meta($site_decoration->ID, 'reviewed', true)){ ?><span class="fa fa-check"></span><?php } ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
