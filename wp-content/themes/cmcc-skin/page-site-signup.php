<?php get_header(); ?>

<header>
	<h1><img src="<?=get_template_directory_uri()?>/img/title.png"></h1>
</header>

<div class="input-form">
	<form method="post" class="form-horizontal">
		<div class="form-group">
			<label for="region" class="col-xs-4 control-label">区域</label>
			<div class="col-xs-8">
				<select id="region" name="region" class="form-control">
					<option>宝山区</option>
					<option>浦东新区</option>
					<option>松江区</option>
					<option>普陀区</option>
					<option>黄埔区</option>
				</select>
				<!--<input id="region" name="region" type="text" class="form-control" />-->
			</div>
		</div>
		<div class="form-group">
			<label for="site" class="col-xs-4 control-label">营业厅</label>
			<div class="col-xs-8 ">
				<input id="site" name="site" type="text" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label for="manager" class="col-xs-4 control-label">负责人</label>
			<div class="col-xs-8">
				<input id="manager" name="manager" type="text" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label for="phone" class="col-xs-4 control-label">手机</label>
			<div class="col-xs-8">
				<input id="phone" name="phone" type="text" class="form-control" />
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" name="signup" class="btn btn-default">注册新用户</button>
		</div>
	</form>
</div>

<?php get_footer(); ?>
