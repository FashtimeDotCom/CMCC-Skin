<input type="hidden" id="sheets" name="sheets" value="<?=esc_attr(get_post_meta($post->ID, 'sheets', true))?>" />
<ul class="sheet-list clearfix"<?php if(!$sheets){ ?> style="display:none"<?php } ?>>
	<?php foreach($sheets as $sheet_id => $status){ ?>
	<li id="<?=$sheet_id?>">
		<p class="hide-if-no-js sheet-file"><?=get_post_meta($sheet_id, '_wp_attached_file', true)?></p>
		<p class="hide-if-no-js sheet-actions">
			<a href="<?=wp_get_attachment_url($sheet_id)?>" class="download">下载</a>
			<label>已导入</label>
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
