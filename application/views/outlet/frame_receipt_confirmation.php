<?php $this->view('header'); ?>

<header class="recept">
	<h1>中国移动上海公司营业厅物料签收单</h1>
</header>

<table id="receipt-summary" class="table table-bordered summary">
	<tbody>
		<tr>
			<th>区域</th>
			<td>南区</td>
			<th>厅经理确认</th>
			<td>南区</td>
		</tr>
		<tr>
			<th>厅名</th>
			<td>上海南站营业厅</td>
			<th>日期</th>
			<td>5.20</td>
		</tr>
		<tr>
			<th>签收须知</th>
			<td colspan="3">确认所收物料及数量，确认完毕后，在该物料右侧确认栏中点击以勾选。所有物料全部确认勾选后，才能点击确认签收。</td>
		</tr>
	</tbody>
</table>

<table id="receipt-detail" class="table table-bordered">
	<thead>
		<tr>
			<td>序号</td>
			<td>物料名称</td>
			<td>图例</td>
			<td>数量</td>
			<td>确认（√）</td>
			<td>备注</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i = 0; $i < 17; $i++){ ?>
		<tr>
			<td><?=$i + 1?></td>
			<td>悬挂灯箱（小）</td>
			<td><img src="/img/换装发布表格1-1.jpg" class="sample-picture"></td>
			<td><?=floor(rand(0, 1000) / 100)?></td>
			<td class="check"></td>
			<td></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="6" class="text-center"><button type="button" id="finish" class="btn btn-success">签收完成</button></th>
		</tr>
	</tfoot>
</table>

<?php $this->view('footer'); ?>
