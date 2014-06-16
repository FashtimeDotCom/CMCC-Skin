<table class="form-table">
	<tbody>
		<tr>
			<th><label for="region">区域：</label></th>
			<td>
				<select name="region" style="width:25em">
					<?php foreach($regions as $region){ ?>
					<option value="<?=$region?>"<?=selected(get_post_meta($post->ID, 'region', true))?>><?=$region?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="manager">厅负责人：</label></th>
			<td><input type="text" id="manager" name="manager" value="<?=esc_attr(get_post_meta($post->ID, 'manager', true))?>" class="regular-text code" /></td>
		</tr>
		<tr>
			<th><label for="manager-phone">厅负责人联系方式：</label></th>
			<td><input type="text" id="manager-phone" name="manager_phone" value="<?=esc_attr(get_post_meta($post->ID, 'manager_phone', true))?>" class="regular-text code" /></td>
		</tr>
		<tr>
			<th><label for="phone">电话：</label></th>
			<td><input type="text" id="phone" name="phone" value="<?=esc_attr(get_post_meta($post->ID, 'phone', true))?>" class="regular-text code" /></td>
		</tr>
		<tr>
			<th><label for="address">地址：</label></th>
			<td><input type="text" id="address" name="address" value="<?=esc_attr(get_post_meta($post->ID, 'address', true))?>" class="regular-text code" /></td>
		</tr>
	</tbody>
</table>