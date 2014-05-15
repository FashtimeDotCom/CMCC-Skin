<?php $this->view('header'); ?>

<header>
	<h1>2014移动营业厅晚春换装</h1>
</header>

<section id="pictures" class="swipe">
	<div class="swipe-wrap">
		<div class="row" style="margin:0">
			<?php for($i = 0; $i < 6; $i++){ ?>
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
				<a href="<?=site_url()?>img/换装发布表格1-<?=$i+1?>.jpg"><img src="<?=site_url()?>img/换装发布表格1-<?=$i+1?>.jpg"></a>
			</div>
			<?php } ?>
		</div>
		<div class="row" style="margin:0">
			<?php for($i = 0; $i < 6; $i++){ ?>
			<div class="col-xs-4">
				<a href="<?=site_url()?>img/换装发布表格1-<?=$i+1?>.jpg"><img src="<?=site_url()?>img/换装发布表格1-<?=$i+6?>.jpg"></a>
			</div>
			<?php } ?>
		</div>
	</div>
</section>

<section id="requirement">
	<table class="table table-bordered summary">
		<tr>
			<th>
				换装要求
			</th>
			<td>
				B1 B6 B7
			</td>
		</tr>
		<tr>
			<th>
				换装时间
			</th>
			<td>
				4月21日完成（市区18号完成）
			</td>
		</tr>
		<tr>
			<th>
				换装方法
			</th>
			<td>
				对照换装画面示意图上的编号，如“A1”厅平面点位图中“A1”。
			</td>
		</tr>
		<tr>
			<th>
				换装指导示范
			</th>
			<td>
				<a href="<?=site_url()?>outlet/setupsample">点击查看 &raquo;</a>
			</td>
		</tr>
	</table>
</section>

<script type="text/javascript" src="<?=site_url()?>js/swipe.js"></script>
<script type="text/javascript">
	Swipe(document.getElementById('pictures'));
</script>

<?php $this->view('footer'); ?>
