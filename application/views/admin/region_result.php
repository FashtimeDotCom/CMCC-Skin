<?php $this->view('header'); ?>

<header>
	<h1>南区</h1>
</header>

<table class="table table-bordered summary">
	<thead>
		<tr>
			<th>营业厅名称</th>
			<td>物料</td>
			<td>画面</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $i<200; $i ++){ ?>
		<tr>
			<td><a href="<?=site_url()?>admin/outletresult">大木桥路营业厅 <span class="arrow">&raquo;</a></a></td>
			<td></td>
			<td></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php $this->view('footer'); ?>
