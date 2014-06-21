<?php
$site_id = get_post_meta(get_the_ID(), 'site_id', true);
$decoration_id = get_post_meta(get_the_ID(), 'decoration', true);
$frames = json_decode(get_post_meta(get_the_ID(), 'frames', true));
$frame_types = json_decode(get_option('frame_types'));

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

if($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['result_upload'])){
	
	if(isset($_POST['frame_received'])){
		if(is_array($_POST['frame_received'])){
			foreach($_POST['frame_received'] as $name => $received){
				$received = json_decode($received);
				$frames->$name->received = $received;
				$received ? $unreceived['frames'] -- : $unreceived['frames'] ++;
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'frames_received', json_decode($_POST['frame_received']));
		}
	}
	
	if(isset($_POST['picture_received'])){
		if(is_array($_POST['picture_received'])){
			foreach($_POST['picture_received'] as $frame_name => $received){
				$received = json_decode($received);
				$frames->$frame_name->pictures_received = $received;
				foreach($frames->$frame_name->pictures as &$picture){
					$picture->received = $received;
					$received ? $unreceived['pictures'] -- : $unreceived['pictures'] ++;
				}
			}
			update_post_meta(get_the_ID(), 'frames', json_encode($frames, JSON_UNESCAPED_UNICODE));
		}
		else{
			update_post_meta(get_the_ID(), 'pictures_received', json_decode($_POST['picture_received']));
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($unreceived);
	
	exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['result_upload'])){
	
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';
	
	$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));
	!$result_photos && $result_photos = new stdClass();
	
	foreach($_FILES as $index => $file){
		$attachment_id = media_handle_upload($index, 0);
		if(is_integer($attachment_id)){
			$result_photos->$index = $attachment_id;
		}
	}
	
	update_post_meta(get_the_ID(), 'result_photos', json_encode($result_photos, JSON_UNESCAPED_UNICODE));
	
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	exit;
}

get_header();
?>

<?php if(!isset($_GET['result_upload'])){ ?>
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
			<td><?=get_the_date('Y-m-d')?></td>
		</tr>
		<tr>
			<th>签收须知</th>
			<td colspan="3" class="text-left">确认所收物料及数量，确认完毕后，在该物料右侧确认栏中点击以勾选。所有物料全部确认勾选后，才能点击确认签收。</td>
		</tr>
	</tbody>
</table>

<table id="receipt-detail" class="table table-bordered frames"<?php if(get_post_meta(get_the_ID(), 'frames_received', true)){ ?> style="display:none"<?php } ?>>
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
			<th colspan="6" class="text-center"><button type="button" id="finish" class="frames-received btn btn-success<?php if($unreceived['frames']){ ?> disabled<?php } ?>">签收完成</button></th>
		</tr>
	</tfoot>
</table>

<table id="receipt-detail" class="table table-bordered pictures"<?php if(!get_post_meta(get_the_ID(), 'frames_received', true)){ ?> style="display:none"<?php } ?>>
	<thead>
		<tr>
			<td>物料名称</td>
			<td>画面尺寸（WxL/mm）</td>
			<td>数量</td>
			<td>画面材质</td>
			<td>确认（√）</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($frames as $name => $frame){ ?>
		<tr>
			<td class="frame-type"><?=$name?></td>
			<td><?=$frame_types->$name->size?></td>
			<td class="dropdown"><?=$frame->quantity?><span class="caret"></span></td>
			<td><?=$frame_types->$name->material?></td>
			<td class="check"><span class="fa fa-check checkmark"<?php if(!$frame->pictures_received){ ?> style="display:none"<?php } ?>></span></td>
		</tr>
		<tr class="expanded">
			<td colspan="5">
				<table class="table table-bordered summary detail fixed-layout">
					<tbody>
						<?php
						$picture_groups = array();
						for($group = 0; $group < ceil( count($frame->pictures) / 4 ); $group ++){
							$picture_groups[] = array_slice($frame->pictures, $group * 4, 4);
						}
						?>
						<?php foreach($picture_groups as $pictures){ ?>
						<tr>
							<?php foreach($pictures as $picture){ ?>
							<th class="active">位置：<?=$picture->position?></th>
							<?php } ?>
							<?php for($i = 4 - count($pictures); $i>0; $i--){ // 补完一行 ?>
							<th></th>
							<?php } ?>
						</tr>
						<tr>
							<?php foreach($pictures as $picture){ ?>
							<?php
							$decoration_pictures = json_decode(get_post_meta($decoration_id, 'pictures', true));
							$position = $picture->position;
							?>
							<td class="active"><a href="<?=wp_get_attachment_url($decoration_pictures->$position)?>"><?=wp_get_attachment_image($decoration_pictures->$position)?></a></td>
							<?php } ?>
							<?php for($i = 4 - count($pictures); $i>0; $i--){ // 补完一行 ?>
							<td></td>
							<?php } ?>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="5" class="text-center"><button type="button" class="pictures-received btn btn-success<?php if($unreceived['pictures']){ ?> disabled<?php } ?>">签收完成</button></th>
		</tr>
	</tfoot>
</table>

<script type="text/javascript">
(function($){
$(function(){
	
	$('td.check').on('click', function(){
		$(this).children('.checkmark').toggle();
		var postData = {frame_received: {}, picture_received: {}};
		var frameType = $(this).siblings('.frame-type').text();
		var postScope = $(this).closest('table').hasClass('frames') ? 'frame_received' : 'picture_received';
		frameType && (postData[postScope][frameType] = $(this).children('.checkmark').is(':visible'));
		$.post(window.location.href, postData, function(unreceived){
			if(unreceived.frames === 0){
				$('table.frames :button.frames-received').removeClass('disabled');
			}else{
				$('table.frames :button.frames-received').addClass('disabled');
			}
			
			if(unreceived.pictures === 0){
				$('table.pictures :button.pictures-received').removeClass('disabled');
			}else{
				$('table.pictures :button.pictures-received').addClass('disabled');
			}
		});
	});

	$('td.dropdown').on('click', function(){
		var expand = $(this).parent('tr').next('tr.expanded');
		if(expand.is(':hidden')){
			expand.show();
		}else{
			expand.hide();
		}
	});
	
	$(':button.frames-received').on('click', function(){
		$.post(window.location.href, {frame_received: true}, function(){
			$('table.frames').hide().next('table.pictures').show();
		})
	});

	$(':button.pictures-received').on('click', function(){
		$.post(window.location.href, {picture_received: true}, function(){
			window.location.search = 'result_upload';
		})
	});

});
})(jQuery);
</script>
<?php }else{
$result_positions = json_decode(get_option('result_upload_positions'));
$result_photos = json_decode(get_post_meta(get_the_ID(), 'result_photos', true));
?>
<div class="result-upload">
	<form method="post" enctype="multipart/form-data">
		<?php foreach($result_positions as $slug => $name){ ?>
		<div class="row">
			<div class="col-xs-12">
				<h2><?=$name?></h2>
				<?php if(isset($result_photos->$slug)){ ?>
				<?=wp_get_attachment_image($result_photos->$slug, 'large')?>
				<?php }else{ ?>
				<img class="preview" />
				<input type="file" name="<?=$slug?>">
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-success">上传</button>
		</div>
	</form>
</div>
<script type="text/javascript">
jQuery(function($){
	
	$('.result-upload form input[type="file"]').change(function(){
		
		var input = $(this);
		
		if (this.files && this.files[0]) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				input.siblings('img.preview').attr('src', e.target.result);
			}

			reader.readAsDataURL(this.files[0]);
		}
	});
});
</script>
<?php } ?>
<?php get_footer(); ?>
