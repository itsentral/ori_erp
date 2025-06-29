<?php
$Imp	= explode('-', $id_bq);

$checkSO 	= "	SELECT * FROM production WHERE no_ipp = '".$Imp[1]."' AND `status`='WAITING EST PRICE PROJECT' ";
$restChkSO	= $this->db->query($checkSO)->num_rows();
if($restChkSO < 1){
	?>
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			IPP ini tidak dapat dilakukan approve, please update data.<br>
		</p>
	</div>
	<?php
}
else{
?>

<div class="box-body">
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			<span style='color:green;'><b>MOHON CEK TERLEBIH DAHULU BERAT DAN COST SATUAN, <span style='color:red;'>UNTUK MENIMINALISIR KESALAHAN</span></b></span><br>
		</p>
	</div>
	<div class="form-group row">
		<div class='col-sm-4 '>
		   <label class='label-control'>Approve Action</label>
		   <select name='status' id='status' class='form-control input-md'>
				<option value='0'>Select Action</option>
				<!--<option value='Y'>APPROVE</option>-->
				<option value='N'>REVISI TO ENGGENERING (BQ/MTO)</option>
				<option value='X'>REVISI TO ENGGENERING (EST)</option>
			</select>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-8 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
		
	</div>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'approvedQ')).' ';
	?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Weight/Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost/Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Material Est (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Material Cost</th>
				<th class='text-center' style='vertical-align:middle;' width='8%'>Process Cost</th>
				<th class='text-center' style='vertical-align:middle;' width='8%'>COGS</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$SUM_BERAT = 0;
				$SUM_PRICE = 0;
				$SUM_PROCESS = 0;
				$SUM_COGS = 0;
				$No = 0;
				if(!empty($result)){
					foreach($result AS $val => $valx){
						$No++;
						$spaces = "";
						$bgwarna = 'bg-blue';

						$ID 						= $valx['id'];
						$id_category 				= $valx['id_category'];
						$id_milik 					= $valx['id'];
						$length 					= $valx['length'];
						$id_product 				= $valx['id_product'];
						$qty 						= $valx['qty'];
						$man_power					= $valx['man_power'];
						$man_hours					= $valx['man_hours'];
						$id_mesin					= $valx['id_mesin'];
						$total_time					= $valx['total_time'];

						$SUMMARY = getEstimasi_Product($id_milik,$id_category);
						
						$TotalBerat	= (!empty($SUMMARY['est_mat']))?$SUMMARY['est_mat'] * $qty:0;
						$SUM_BERAT 	+= $TotalBerat;
						
						$TotalPrice	= (!empty($SUMMARY['est_price']))?$SUMMARY['est_price'] * $qty:0;
						$SUM_PRICE += $TotalPrice;

						$direct_labour 				= $man_hours * $valx['pe_direct_labour'] * $qty;
						$indirect_labour 			= $man_hours * $valx['pe_indirect_labour'] * $qty;
						$machine 					= $total_time * $valx['pe_machine'] * $qty;
						$mould_mandrill 			= $valx['pe_mould_mandrill'] * $qty;
						$consumable 				= $TotalBerat * $valx['pe_consumable'];

						$cost_process 				= $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable;

						$foh_consumable 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_consumable']/100);
						$foh_depresiasi 			= ($cost_process + $TotalPrice) * ($valx['pe_foh_depresiasi']/100);
						$biaya_gaji_non_produksi 	= ($cost_process + $TotalPrice) * ($valx['pe_biaya_gaji_non_produksi']/100);
						$biaya_non_produksi 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_non_produksi']/100);
						$biaya_rutin_bulanan 		= ($cost_process + $TotalPrice) * ($valx['pe_biaya_rutin_bulanan']/100);

						$TotalCost		= $direct_labour + $indirect_labour + $machine + $mould_mandrill + $consumable + $foh_consumable + $foh_depresiasi + $biaya_gaji_non_produksi + $biaya_non_produksi + $biaya_rutin_bulanan;
						$SUM_PROCESS 	+= $TotalCost;
						
						$COGS 			= $TotalCost + $TotalPrice;
						$SUM_COGS 		+= $COGS;
						
						if($id_category == 'pipe' OR $id_category == 'pipe slongsong'){
							$lengthX = (floatval($length));
						}
						else{
							$lengthX = (floatval($length));
						}
						
						echo "<tr>";
							echo "<td align='center'>".$No."</td>";
							echo "<td align='left'>".$spaces."".strtoupper($id_category)."</td>";
							echo "<td align='left'>".$spaces."".spec_bq($id_milik)."</td>";
							echo "<td align='center'><span class='badge ".$bgwarna."'>".$qty."</span></td>";
							echo "<td align='left'>".$id_product."</span></td>";
							echo "<td align='right'>".number_format($TotalBerat/$qty, 3)."</span></td>";
							echo "<td align='right'>".number_format($TotalPrice/$qty, 2)."</span></td>";
							echo "<td align='right'>".number_format($TotalBerat, 3)."</span></td>";
							echo "<td align='right'>".number_format($TotalPrice, 2)."</span></td>";
							echo "<td align='right'>".number_format($TotalCost, 2)."</td>";
							echo "<td align='right'>".number_format($COGS, 2)."</span></td>";					
						echo "</tr>";
					}
				}
				else{
					echo "<tr>";
						echo "<td colspan='11'>Tidak ada product yang ditampilkan</td>";
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-center" colspan='7' style='vertical-align:middle;'>Total</th>
				<th class="text-right"><?= number_format($SUM_BERAT, 3);?></th>
				<th class="text-right"><?= number_format($SUM_PRICE, 2);?></th>
				<?php
				echo "<th class='text-right'>".number_format($SUM_PROCESS, 2)."</th>";
				echo "<th class='text-right'>".number_format($SUM_COGS, 2)."</th>";
				?>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>
<script>
	$(document).ready(function(){
		swal.close();
	});
	
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N' || $(this).val() == 'X'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
		
		$(document).on('click', '#approvedQ', function(){
			var bF				= $('#id_bq').val();
			var status 			= $('#status').val();
			var approve_reason 	= $('#approve_reason').val();
			
			if(status == '0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Action approve belum dipilih ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			if(status == 'N' && approve_reason == ''){
				swal({
				  title	: "Error Message!",
				  text	: 'Alasan reject masih kosong ...',
				  type	: "warning"
				});
				$('#approvedQ').prop('disabled',false);
				return false;
			}
			
			swal({
			  title: "Are you sure?",
			  text: "You will not be able to process again this data!",
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
						url			: base_url+active_controller+'/AppCost/'+bF,
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
								window.location.href = base_url + active_controller+'/project';
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
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
		
	});

</script>