<?php
$site_id = get_post_meta(get_the_ID(), 'site_id', true);
$decoration_id = get_post_meta(get_the_ID(), 'decoration', true);
$frames = json_decode(get_post_meta(get_the_ID(), 'frames', true));
$unreceived = array('frames'=>0, 'pictures'=>0);
foreach($frames as $name => $frame){
	if(!$frame->received){
		$unreceived['frames']++;
	}
	foreach($frame->pictures as $picture){
		if(!$picture->received){
			$unreceived['pictures']++;
		}
	}
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	
	if(isset($_POST['frame_received'])){
		foreach($_POST['frame_received'] as $name => $received){
			$received = json_decode($received);
			$frames->$name->received = $received;
			$received ? $unreceived['frames'] -- : $unreceived['frames'] ++;
		}
		update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
	}
	
	header('Content-Type: application/json');
	echo json_encode($unreceived);
	
	exit;
}

get_header();
?>

<header class="recept">
	<h1>中国移动上海公司营业厅物料签收单</h1>
</header>

<table id="receipt-summary" class="table table-bordered summary">
	<tbody>
		<tr>
			<th>区域</th>
			<td><?=get_post_meta($site_id, 'region', true)?></td>
			<th>厅经理确认</th>
			<td><?=get_post_meta($site_id, 'manager', true)?></td>
		</tr>
		<tr>
			<th>厅名</th>
			<td><?=get_post($site_id)->post_title?></td>
			<th>日期</th>
			<td><?=get_the_date('Y-m-d')?></td><!--TODO 什么日期-->
		</tr>
		<tr>
			<th>签收须知</th>
			<td colspan="3" class="text-left">确认所收物料及数量，确认完毕后，在该物料右侧确认栏中点击以勾选。所有物料全部确认勾选后，才能点击确认签收。</td>
		</tr>
	</tbody>
</table>

<table id="receipt-detail" class="table table-bordered">
	<thead>
		<tr>
			<td>序号</td>
			<td>物料名称</td>
			<td>图例</td>
			<td>数量</td>
			<td>确认（√）</td>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0; ?>
		<?php foreach($frames as $name => $frame){ ?>
		<?php $i++; ?>
		<tr>
			<td><?=$i?></td>
			<td class="frame-type"><?=$name?></td>
			<td><img src="<?=get_template_directory_uri()?>/img/<?=urlencode($name)?>.jpg" class="sample-picture"></td>
			<td><?=$frame->quantity?></td>
			<td class="check"><span class="fa fa-check checkmark"<?php if(!$frame->received){ ?> style="display:none"<?php } ?>></span></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="6" class="text-center"><a id="finish" href="<?=site_url()?>/site-picture-receipt-confirmation/" class="btn btn-success<?php if($unreceived['frames']){ ?> disabled<?php } ?>">签收完成</a></th>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
(function($){
	$(function(){
		$('td.check').on('click', function(){
			$(this).children('.checkmark').toggle();
			var frameType = $(this).siblings('.frame-type').text();
			var postData = {frame_received: {}, picture_received: {}};
			postData.frame_received[frameType] = $(this).children('.checkmark').is(':visible');
			$.post(window.location.href, postData, function(unreceived){
				if(unreceived.frames === 0){
					$('a#finish').removeClass('disabled');
				}else{
					$('a#finish').addClass('disabled');
				}
			});
		});
	});
})(jQuery);
</script>

<?php get_footer(); ?>
