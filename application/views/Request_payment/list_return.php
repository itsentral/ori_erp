<?php
$this->load->view('include/side_menu');
?>
<div id="alert_edit" class="alert alert-success alert-dismissable" style="padding: 15px; display: none;"></div>
<div class="box">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>		
	</div>
	<div class="box-body">
		<div class="table-responsive  col-md-12">
		<table id="mytabledata" class="table table-bordered">
		<thead>
		<tr>
			<th width="5">#</th>
			<th>No Dokumen</th>
			<th>Request By</th>
			<th>Tanggal</th>
			<th>Keperluan</th>
			<th>Tipe</th>
			<th>Nilai Pengembalian</th>
			<th>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($row)){
			$numb=0; 
			foreach($row AS $record){ 
			  if($record->jumlah <=0){
				$numb++;
			?>
		<tr>
		    <td><?= $numb; ?></td>
			<td><?= $record->no_doc ?></td>
			<td><?= $record->nama ?></td>
			<td><?= $record->tgl_doc ?></td>
			<td><?= $record->keperluan ?></td>
			<td><?= $record->tipe ?></td>
			<td><?= number_format($record->jumlah) ?></td>
			<td>
			<?php
			if($record->tipe=='expense'){?>
				<?php if($akses_menu['create']=='1'){ ?>
					<a href="javascript:edit(<?=$record->ids?>)"><i class="fa fa-check fa-2x"></i></a>
			<?php } 
			}?>
			</td>
		</tr>
		<?php
				}
			}
		}  ?>
		</tbody>
		</table>
		</div>
	</div>
	<!-- /.box-body -->
</div>
	<div class="modal fade" id="Mymodal" >
		<div class="modal-dialog" style="width:100%">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">View Expense</h4>
				</div>
				<div class="modal-body" id="listexpense">
				</div>
			</div>
		</div>
	</div>
<?php $this->load->view('include/footer'); ?>
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script type="text/javascript">
	$("#mytabledata").DataTable({
	"paging": false,
	"ordering": false,
	"info": false,
	});
	$(".divide").divide();
	function edit (id){
		$("#listexpense").load(base_url +'expense/review/'+id);
		$("#Mymodal").modal();
	};
</script>
