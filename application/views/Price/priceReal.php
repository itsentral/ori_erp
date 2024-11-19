<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>IPP Number</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['no_ipp']);
						echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_produksi']);
					?>				
				</div>
				<label class='label-control col-sm-2'><b>Machine</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Machine Name', 'readonly'=>'readonly'), $row[0]['nm_mesin']);
					?>
				</div>
			</div>
			
			<a href="<?php echo site_url('price/project') ?>" class="btn btn-md btn-danger" style='float:right; width: 100px; margin-bottom: 5px;'>Back</a>
			<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table'>
					<tr class='bg-blue'>
						<th class="text-center" class="no-sort" width="75px">No</th>
						<th class="text-center" style='width: 250px;'>Product Delivery</th>
						<th class="text-center" style='width: 250px;'>Product Type</th>
						<th class="text-center">Product Name</th>
						<th class="text-center">Product To</th>
						<th class="text-center" style='width: 150px;'>Detail Price</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$a=0;
						foreach($rowD AS $val => $valx){
							$a++;
							?>
							<tr>
								<td align='center'><?= $a; ?></td>
								<td align='center'><?= strtoupper($valx['id_delivery']);?></td>
								<td><?= strtoupper($valx['id_category']);?></td>
								<td><?= $valx['id_product'];?></td>
								<td align='center'><span class='badge bg-blue'><?= $valx['product_ke'];?></span></td>
								<td align='center'>
								<?php
								if($valx['upload_real'] == 'Y' OR $valx['upload_real2'] == 'Y'){
									echo "&nbsp;<button type='button' class='btn btn-sm btn-warning' id='MatDetail' title='Detail Price ".$valx['nm_product']." ke (".$valx['product_ke'].")' data-id_product='".$valx['id_product']."' data-id_produksi='".$valx['id_produksi']."' data-id_milik='".$valx['id_milik']."' data-id_producktion='".$valx['id']."'><i class='fa fa-eye'></i></button>";	
									echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printPriceperMat/'.$valx['id_produksi'].'/'.$valx['id_product'].'/'.$valx['product_ke'].'/'.$valx['id_delivery'].'/'.$valx['id'].'/'.$valx['id_milik'])."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
									// echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/perbandinganExcell/'.$valx['id_produksi'].'/'.$valx['id_product'].'/'.$valx['product_ke'].'/'.$valx['id_delivery'].'/'.$valx['id'])."' class='btn btn-sm btn-success' target='_blank' title='Print' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
									echo "&nbsp;<button type='button' class='btn btn-sm btn-success btn_download' title='Dwonload Excel' data-ke='".$valx['product_ke']."' data-nm_product='".$valx['nm_product']."' data-id_product='".$valx['id_product']."' data-id_delivery='".$valx['id_delivery']."' data-id_produksi='".$valx['id_produksi']."' data-id_producktion='".$valx['id']."' data-id_milik='".$valx['id_milik']."'><i class='fa fa-file-excel-o'></i></button>";
								}
								else{
									echo "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='Data belum di update'><i class='fa fa-close'></i></button>";	
								}
								?>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
			
		</div>

		<div class='box-footer'>
			<?php
			// echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
			<a href="<?php echo site_url('price/project') ?>" class="btn btn-sm btn-danger">Back</a>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
   <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->	
</form>

<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<style type="text/css">
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	#kdcab_chosen{
		width: 100% !important;
	}
	#province_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		
		$('.btn_download').click(function(e){
			e.preventDefault();
			var id_product		= $(this).data('id_product');
			var id_produksi		= $(this).data('id_produksi');
			var id_delivery		= $(this).data('id_delivery');
			var id_producktion	= $(this).data('id_producktion');
			var ke				= $(this).data('ke');
			var nm_product		= $(this).data('nm_product');
			var id_milik		= $(this).data('id_milik');
			
			var Links		= base_url +'index.php/'+ active_controller+'/ExcelPerbandingan/'+id_product+'/'+id_produksi+'/'+id_delivery+'/'+id_producktion+'/'+ke+'/'+id_product+'/'+id_milik;
			window.open(Links,'_blank');
		});
		
		$('#real_start_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$('#real_end_produksi').datepicker({
			format : 'yyyy-mm-dd',
			startDate: 'now'
		});
		
		$(document).on('click', '#MatDetail', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailPriceDetail/'+$(this).data('id_product')+'/'+$(this).data('id_producktion')+'/'+$(this).data('id_milik'));
			$("#ModalView").modal();
		});
	}); 
</script>
