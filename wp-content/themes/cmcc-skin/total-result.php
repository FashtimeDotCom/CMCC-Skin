<?php
/*
 * Sub Template in Single "decoration"
 */
$regions = json_decode(get_option('regions'));
$site_decorations = get_posts(array('post_type'=>'site_decoration', 'posts_per_page'=>-1, 'meta_key'=>'decoration', 'meta_value'=>get_the_ID()));
$region_result = array(
//	'<region_name>'=>array('total'=>0, 'received'=>0, 'reviewed'=>0);
);
foreach($site_decorations as $site_decoration){
	$region = get_post_meta($site_decoration->ID, 'site_region', true);
	$frames_received = get_post_meta($site_decoration->ID, 'frames_received', true);
	$pictures_received = get_post_meta($site_decoration->ID, 'pictures_received', true);
	$reviewed = get_post_meta($site_decoration->ID, 'reviewed', true);
	
	empty($region_result[$region]) && $region_result[$region] = array('total'=>0, 'received'=>0, 'reviewed'=>0);
	
	$region_result[$region]['total'] ++;
	
	if($frames_received && $pictures_received){
		$region_result[$region]['received'] ++;
	}
	
	if($reviewed){
		$region_result[$region]['reviewed'] ++;
	}
	
}
?>	

<header>
	<h1>签收换装完成情况</h1>
</header>

<table class="table table-bordered summary">
	<thead>
		<tr>
			<th>区域名称</th>
			<td>签收情况</td>
			<td>换装情况</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($regions as $region){ ?>
		<tr>
			<td><a href="<?php the_permalink(); ?>?action=region-result&region=<?=$region?>"><?=$region?> <span class="arrow">&raquo;</span></a></td>
			<td><?=$region_result[$region]['received']?> / <?=$region_result[$region]['total']?></td>
			<td><?=$region_result[$region]['reviewed']?> / <?=$region_result[$region]['total']?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
