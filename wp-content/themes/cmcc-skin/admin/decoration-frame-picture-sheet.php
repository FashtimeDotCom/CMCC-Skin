<input type="hidden" id="sheets" name="sheets" value="<?=esc_attr(get_post_meta($post->ID, 'sheets', true))?>" />
<ul class="sheet-list clearfix"<?php if(!$sheets){ ?> style="display:none"<?php } ?>>
	<?php foreach($sheets as $sheet_id => $status){ ?>
	<li id="<?=$sheet_id?>">
		<p class="hide-if-no-js sheet-file"><?=get_post($sheet_id)->post_title?></p>
		<p class="hide-if-no-js sheet-actions">
			<a href="<?=wp_get_attachment_url($sheet_id)?>" class="download">下载</a>
			<?php if($status === 'imported'){ ?>
			<label>已导入</label>
			<?php }else{ ?>
			<a href="#" class="remove">移除</a>
			<?php } ?>
		</p>
	</li>
	<?php } ?>
	<li style="display:none" id="">
		<p class="hide-if-no-js sheet-file"></p>
		<p class="hide-if-no-js sheet-actions">
			<a href="#" class="download">下载</a>
			<a href="#" class="remove">移除</a>
		</p>
	</li>
</ul>
<p class="hide-if-no-js add-sheet"><a href="#" title="上传营业厅物料画面表格">上传表格</a></p>
<?php if(!empty($site_decorations)){ ?>
<ul class="site-frames">
	<?php foreach($site_decorations as $site_decoration){ ?>
	<li class="site-name">
		<h3><?=get_post(get_post_meta($site_decoration->ID, 'site_id', true))->post_title?>
			<?php if(get_post_meta($site_decoration->ID, 'frames_received', true)){ ?><span class="dashicons dashicons-pressthis"></span><?php } ?>
			<?php if(get_post_meta($site_decoration->ID, 'pictures_received', true)){ ?><span class="dashicons dashicons-format-image"></span><?php } ?>
			<?php if(get_post_meta($site_decoration->ID, 'reviewed', true)){ ?><span class="dashicons dashicons-yes"></span><?php } ?>
		</h3>
	</li>
	<?php	foreach(json_decode(get_post_meta($site_decoration->ID, 'frames', true)) as $frame_name => $frame){ ?>
	<li class="frame<?php if($frame->received){ ?> received<?php } ?>"><?=$frame_name?>：
		<?php foreach($frame->pictures as $picture){ ?>
		<span class="picture<?php if($picture->received){ ?> received<?php } ?>"><?=$picture->position?></span>
		<?php } ?>
	</li>
	<?php	} ?>
	<?php } ?>
</ul>
<?php } ?>
