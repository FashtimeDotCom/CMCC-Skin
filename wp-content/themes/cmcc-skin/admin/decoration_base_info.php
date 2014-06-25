<table class="form-table">
	<tbody>
		<tr>
			<th><label for="requirement">要求：</label></th>
			<td><input type="text" id="requirement" name="requirement" value="<?=esc_attr(get_post_meta($post->ID, 'requirement', true))?>" class="regular-text code" /></td>
		</tr>
		<tr>
			<th><label for="date">时间：</label></th>
			<td><input type="text" id="date" name="date" value="<?=esc_attr(get_post_meta($post->ID, 'date', true))?>" class="regular-text code" /></td>
		</tr>
		<tr>
			<th><label for="instruction">方法：</label></th>
			<td><input type="text" id="instruction" name="instruction" value="<?=esc_attr(get_post_meta($post->ID, 'instruction', true))?>" class="regular-text code" /></td>
		</tr>
	</tbody>
</table>