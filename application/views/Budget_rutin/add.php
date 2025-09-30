<?php
$this->load->view('include/side_menu');
$department 	= (!empty($header))?$header[0]->department:'';
$costcenter 	= (!empty($header))?$header[0]->costcenter:'';
$id_gudang 	= (!empty($header))?$header[0]->id_gudang:'';
$tanda 			= (!empty($code))?'Update':'Insert';
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
	<input type="hidden" name="code_budget" value="<?=$code;?>">
    <input type="hidden" name="tanda" value="<?=$tanda;?>">
<div class="box box-primary" style='margin-right: 17px;'>
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Department <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<select name='department' id='department' class='form-control input-md'>
					<option value='0'>Select An Department</option>
					<?php
						foreach(get_list_dept() AS $val => $valx){
							$dept = ($valx['id'] == $department)?'selected':'';
							echo "<option value='".$valx['id']."' ".$dept.">".$valx['nm_dept']."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Cost Center</b></label>
			<div class='col-sm-4'>
				<select name='costcenter' id='costcenter' class='form-control input-md'>
					<option value='0'>Select An Cost Center</option>
					<?php
						foreach(get_list_costcenter() AS $val => $valx){
							$cc = ($valx['id_costcenter'] == $costcenter)?'selected':'';
							echo "<option value='".$valx['id_costcenter']."' ".$cc.">".strtoupper($valx['nm_costcenter'])."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Warehouse Project</b></label>
			<div class='col-sm-4'>
				<select name='id_gudang_project' id='id_gudang_project' class='form-control input-md'>
					<option value='0'>Select An Warehouse Project</option>
					<?php
						foreach($warehouse_project AS $val => $valx){
							$cc = ($valx['id'] == $id_gudang)?'selected':'';
							$nm_gudang = (!empty($valx['nm_customer']))?'Gudang Project '.$valx['nm_customer']:$valx['nm_gudang'];
							echo "<option value='".$valx['id']."' ".$cc.">".strtoupper($nm_gudang)."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<br>
		<?php
		foreach(get_list_jenis_rutin() AS $valH => $valxHeader){
			$detail 		= $this->db->query("SELECT * FROM budget_rutin_detail WHERE code_budget='".$code."' AND jenis_barang='".$valxHeader['id']."' ")->result_array();
			$jenis_barang2	= $this->db->query("SELECT a.*, b.price_from_supplier FROM con_nonmat_new a LEFT JOIN accessories b ON a.code_group=b.id_material WHERE a.category_awal='".$valxHeader['id']."' ORDER BY a.material_name ASC ")->result_array();
			echo "<h4><b>".strtoupper($valxHeader['category'])."</b></h4>";
			?>
			<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class='text-center' style='width: 5%;'>#</th>
						<th class='text-center' style='width: 30%;'>Nama Barang</th>
						<th class='text-center'>Spesifikasi</th>
						<th class='text-center' style='width: 10%;'>Kebutuhan 1 Bulan</th>
						<th class='text-center' style='width: 10%;'>Price From Supplier</th>
						<th class='text-center' style='width: 10%;'>Total Budget</th>
						<th class='text-center' style='width: 10%;'>Satuan</th>
						<th class='text-center' style='width: 5%;'>#</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						$SUM_QTY=0;
						$SUM_BUDGET=0;
						if(!empty($detail)){
							foreach($detail AS $val => $valx){ $id++;
								$spec = get_name('con_nonmat_new', 'spec', 'code_group', $valx['id_barang']);
			
								echo "<tr class='header_".$valxHeader['id'].$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>";
										echo "<select name='detail[".$valxHeader['id'].$id."][id_barang]' data-no='".$valxHeader['id'].$id."' data-id_barang='".$valxHeader['id']."' class='chosen_select form-control input-sm getSpec'>";
										echo "<option value='0'>Select Barang</option>";
										foreach($jenis_barang2 AS $val2 => $valx2){
											$price_sup = (!empty($valx2['price_from_supplier']))?$valx2['price_from_supplier']:0;

											$dex = ($valx['id_barang'] == $valx2['code_group'])?'selected':'';
										  echo "<option value='".$valx2['code_group']."' data-price_sup='".$price_sup."' ".$dex.">".strtoupper($valx2['code_group'].' - '.$valx2['material_name'])."</option>";
										}
										echo "</select>";
									echo "</td>";
									echo "<td align='left'>";
										echo "<input type='hidden' name='detail[".$valxHeader['id'].$id."][jenis_barang]' class='form-control input-md' value='".$valxHeader['id']."'>";
										echo "<input name='detail[".$valxHeader['id'].$id."][spesifikasi]' id='spec_".$valxHeader['id'].$id."' class='form-control input-md' readonly placeholder='Spesifikasi' value='".strtoupper($spec)."'>";
									echo "</td>";
									echo "<td align='left'>";
										echo "<input name='detail[".$valxHeader['id'].$id."][kebutuhan_month]' class='form-control text-center input-md maskM kebutuhan_month' value='".number_format($valx['kebutuhan_month'])."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''>";
									echo "</td>";
									$price_from_supplier = $valx['price_from_supplier'];
									$total_budget = $price_from_supplier * $valx['kebutuhan_month'];
									echo "<td align='left'>";
										echo "<input name='detail[".$valxHeader['id'].$id."][price_from_supplier]' class='form-control text-right input-md autoNumeric2 price_from_supplier' readonly value='".$price_from_supplier."'>";
									echo "</td>";
									echo "<td align='right' style='vertical-align:middle;' class='cal_tot_budget'>".number_format($total_budget,2)."</td>";
									echo "<td align='left'>";
										echo "<select name='detail[".$valxHeader['id'].$id."][satuan]' data-no='".$valxHeader['id'].$id."' id='satuan_".$valxHeader['id'].$id."' class='chosen_select form-control input-sm'>";
										echo "<option value='0'>Select Satuan</option>";
										foreach($satuan AS $val2 => $valx2){
											$dex = ($valx['satuan'] == $valx2['id_satuan'])?'selected':'';
											echo "<option value='".$valx2['id_satuan']."' ".$dex.">".strtoupper($valx2['kode_satuan'])."</option>";
										}
										echo "</select>";
									echo "</td>";
									echo "<td align='center'>";
										echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delPart' title='Delete Part'><i class='fa fa-close'></i></button>";
									echo "</td>";
								echo "</tr>";

								$SUM_QTY += $valx['kebutuhan_month'];
								$SUM_BUDGET += $total_budget;
							}
						}
					?>
					<tr id='add_<?=$valxHeader['id'].$id;?>' class='<?=$id;?>'>
						<td align='center'></td>
						<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' data-id_barang='<?=$valxHeader['id'];?>' class='btn btn-sm btn-success addPart' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>
						<td align='center' colspan='6'></td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<th align='center'></th>
						<th align='center'>TOTAL BUDGET</th>
						<th align='center'></th>
						<th class='text-right'><?=number_format($SUM_QTY);?></th>
						<th align='center'></th>
						<th class='text-right'><?=number_format($SUM_BUDGET,2);?></th>
						<th align='center' colspan='2'></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<?php
		}
		?>
		
		<br>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Back','id'=>'back')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 5px 5px 0px;','value'=>'Create','content'=>'Save','id'=>'save')).' ';
		?>
	</div>
 </div>
  <!-- /.box -->

</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.chosen_select').chosen();
		$(".autoNumeric2").autoNumeric('init', {mDec: '0', aPad: false});
	});
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller+'/index_rutin';
	});
	
	$(document).on('click', '.addPart', function(){
		var jenis_barang		= $(this).data('id_barang');
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];
		
		var class_id = parseInt($(this).parent().parent().attr('class')) + 1;

		$.ajax({
			url: base_url + active_controller+'/get_add/'+id+'/'+jenis_barang+'/'+class_id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskM').maskMoney();
				$(".autoNumeric2").autoNumeric('init', {mDec: '0', aPad: false});
				swal.close();
			},
			error: function() {
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('change', '.getSpec', function(){
		var no 				= $(this).data('no');
		var jenis_barang	= $(this).val();
		var price_sup 		= $(this).find(':selected').data('price_sup');
		var item_sat 		= $('#satuan_'+no);

		var HTML = $(this).parent().parent()
		var getPSub = HTML.find('.price_from_supplier')
		getPSub.val(number_format(price_sup))

		if(jenis_barang == '0'){
			swal({
				title	: "Error Message!",
				text	: 'Nama Barang empty, select first ...',
				type	: "warning"
			});
			return false;
		}
		
		loading_spinner();
		$.ajax({
			url: base_url + active_controller+'/get_spec/'+jenis_barang,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#spec_"+no).val(data.spec);
				$(item_sat).html(data.option).trigger("chosen:updated");
				swal.close();
			},
			error: function() {
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});

	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent().attr('class');
		$("."+get_id).remove();
	});
	
	$(document).on('keyup', '.kebutuhan_month', function(){
		var qty			= getNum($(this).val().split(",").join(""))
		var HTML 		= $(this).parent().parent()
		var price_sup 	= getNum(HTML.find('.price_from_supplier').val().split(",").join(""))
		var budget 		= HTML.find('.cal_tot_budget')
		// console.log(qty)
		// console.log(price_sup)
		budget.text(number_format(price_sup*qty,2))
	});
	
	$(document).on('click', '#save', function(e){
		e.preventDefault();
		$('#save').prop('disabled',true);
		
		var department		= $('#department').val();
		
		if(department == '0'){
			swal({
				title	: "Error Message!",
				text	: 'Department empty, select first ...',
				type	: "warning"
			});

			$('#save').prop('disabled',false);
			return false;
		}
		
		swal({ 
			title: "Are you sure?",
			text: "You will save be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/add_rutin',
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false, 
					contentType	: false,				
					success		: function(data){								
						if(data.status == 1){											
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/index_rutin';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
						$('#save').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save').prop('disabled',false);
			return false;
			}
		});
	});

</script>
