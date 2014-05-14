<?php $this->view('header'); ?>

<header>
	<h1>签收换装完成情况</h1>
</header>

<table class="table table-bordered summary">
	<thead>
		<tr>
			<th>区域名称</th>
			<td>签收情况</td>
			<td>换装情况</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $i<200; $i ++){ ?>
		<tr>
			<td>南区 >></td>
			<td class="check"></td>
			<td class="check"></td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php $this->view('footer'); ?>
