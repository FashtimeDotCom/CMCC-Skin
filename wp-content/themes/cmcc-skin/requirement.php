<?php
/*
 * Sub Template in Single "decoration"
 */
$pictures = json_decode(get_post_meta(get_the_ID(), 'pictures', true), JSON_OBJECT_AS_ARRAY);
$picture_groups = array();
for($group = 0; $group < ceil( count($pictures) / 6 ); $group ++){
	$picture_groups[] = array_slice($pictures, $group * 6, 6);
}
?>
<header>
	<h1>2014移动营业厅晚春换装</h1>
</header>

<section id="pictures" class="swipe">
	<div class="swipe-wrap">
		<?php foreach ($picture_groups as $picture_group){ ?>
		<div class="row" style="margin:0">
			<?php foreach($picture_group as $position => $picture_id){ ?>
			<div class="col-xs-4">
	<!--			<ul class="list-unstyled">
					<li>
						<span class="picture-description-title">画面编号：</span>
						<span class="picture-description">B2</span>
					</li>
					<li>
						<span class="picture-description-title">所在区域：</span>
						<span class="picture-description">X</span>
					</li>
					<li>
						<span class="picture-description-title">画面内容：</span>
						<span class="picture-description highlight">热门软件下载</span>
					</li>
				</ul>-->
				<a href="<?=wp_get_attachment_url($picture_id)?>"><?=wp_get_attachment_image($picture_id, 'decoration-picture')?></a>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
	</div>
</section>

<section id="requirement">
	<table class="table table-bordered summary">
		<tr>
			<th>
				换装要求
			</th>
			<td>
				<?=get_post_meta(get_the_ID(), 'requirement', true)?>
			</td>
		</tr>
		<tr>
			<th>
				换装时间
			</th>
			<td>
				<?=get_post_meta(get_the_ID(), 'date', true)?>
			</td>
		</tr>
		<tr>
			<th>
				换装方法
			</th>
			<td>
				<?=get_post_meta(get_the_ID(), 'instruction', true)?>
			</td>
		</tr>
		<tr>
			<th>
				换装指导示范
			</th>
			<td>
				<a href="<?=site_url()?>/site-setup-sample/">点击查看 &raquo;</a>
			</td>
		</tr>
	</table>
</section>

<script type="text/javascript" src="<?=get_template_directory_uri()?>/js/swipe.js"></script>
<script type="text/javascript">
	Swipe(document.getElementById('pictures'), {
		continuous: false
	});
</script>
