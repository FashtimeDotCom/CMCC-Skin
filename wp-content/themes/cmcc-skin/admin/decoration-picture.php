<input type="hidden" id="pictures" name="pictures" value="<?=esc_attr(get_post_meta($post->ID, 'pictures', true))?>" />
<ul class="picture-list clearfix">
	<?php foreach($pictures as $position => $attachment_id){ ?>
	<li id="<?=$position?>">
		<div class="picture-detail">
			<p class="title">位置：<?=$position?></p>
			<p class="hide-if-no-js set-picture">
				<a title="选择画面图片" href="#">
					<?=wp_get_attachment_image($attachment_id)?>
				</a>
			</p>
		</div>
		<p class="hide-if-no-js remove-picture">
			<a href="#">移除画面</a>
		</p>
	</li>
	<?php } ?>
	<li>
		<p class="hide-if-no-js add-picture"><a href="#">添加位置</a></p>
		<div class="picture-detail" style="display:none">
			<p class="title">位置：<input type="text" style="width:5em"></p>
			<p class="hide-if-no-js set-picture">
				<a title="选择画面图片" href="#">
					选择画面图片
				</a>
			</p>
		</div>
		<p class="hide-if-no-js remove-picture" style="display:none">
			<a href="#">移除画面</a>
		</p>
	</li>
	<li style="display:none">
		<p class="hide-if-no-js add-picture"><a href="#">添加位置</a></p>
		<div class="picture-detail" style="display:none">
			<p class="title">位置：<input type="text" style="width:5em"></p>
			<p class="hide-if-no-js set-picture">
				<a title="选择画面图片" href="#" class="set-picture">
					选择画面图片
				</a>
			</p>
		</div>
		<p class="hide-if-no-js remove-picture" style="display:none">
			<a href="#">移除画面</a>
		</p>
	</li>
</ul>