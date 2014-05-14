<?php $this->view('header'); ?>

<header class="recept">
	<h1>中国移动上海公司营业厅画面签收单</h1>
</header>

<table id="receipt-summary" class="table table-bordered summary">
	<tbody>
		<tr>
			<th style="width: 20%">区域</th>
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
			<th>物料名称</th>
			<th>画面尺寸（WxL/mm）</th>
			<th>数量</th>
			<th>画面材质</th>
			<th>确认（√）</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>悬挂灯箱（小）</td>
			<td>585x835</td>
			<td>2<span class="caret"></span></td>
			<td>灯片</td>
			<td class="check"></td>
		</tr>
		<tr class="expanded">
			<td colspan="5">
				<table class="table table-bordered summary detail">
					<tbody>
						<tr>
							<th class="active">画面编号：A1</th>
							<th class="active">画面编号：A2</th>
							<th>画面编号：B1</th>
							<th>画面编号：B2</th>
							<th>画面编号：B3</th>
							<th>画面编号：B4</th>
						</tr>
						<tr>
							<td class="active"></td>
							<td class="active"></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th>画面编号：A1</th>
							<th>画面编号：A2</th>
							<th>画面编号：B1</th>
							<th>画面编号：B2</th>
							<th>画面编号：B3</th>
							<th>画面编号：B4</th>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th>画面编号：A1</th>
							<th>画面编号：A2</th>
							<th>画面编号：B1</th>
							<th>画面编号：B2</th>
							<th>画面编号：B3</th>
							<th>画面编号：B4</th>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th>画面编号：A1</th>
							<th>画面编号：A2</th>
							<th>画面编号：B1</th>
							<th>画面编号：B2</th>
							<th>画面编号：B3</th>
							<th>画面编号：B4</th>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<?php for($i = 0; $i < 10; $i++){ ?>
		<tr>
			<td>落地灯箱（大）</td>
			<td>775x1175</td>
			<td>2<span class="caret"></span></td>
			<td>灯片</td>
			<td class="check"></td>
		</tr>
		<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th colspan="5" class="text-center"><button type="button" id="finish" class="btn btn-success">签收完成</button></th>
		</tr>
	</tfoot>
</table>

<?php $this->view('footer'); ?>
