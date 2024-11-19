<?php
$id_bq = $this->uri->segment(3); 
$Imp	= explode('-', $id_bq);
$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT a.*, b.sum_mat, b.est_harga FROM bq_detail_header a INNER JOIN estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' ORDER BY a.id ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader; 
// echo "<pre>";
// print_r($row);
// echo "</pre>";
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
				<th class="text-center" style='vertical-align:middle;' width='4%'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='19%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Weight /Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost /Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Weight Total</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				$No = 0;
				foreach($qBQdetailRest AS $val => $valx){
					$No++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$SumQty	= $valx['sum_mat'] * $valx['qty'];
					$Sum += $SumQty;
					
					$SumQtyX	= $valx['est_harga'] * $valx['qty'];
					$SumX += $SumQtyX;
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					echo "<tr>";
						echo "<td align='center'>".$No."</span></td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".spec_bq($valx['id'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($valx['sum_mat'], 2)." Kg</span></td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($valx['est_harga'], 2)."</span></td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($SumQty, 2)." Kg</span></td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($SumQtyX, 2)."</span></td>";
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-center" colspan='7' style='vertical-align:middle;'>Total</th>
				<th class="text-right" style='padding-right:20px;'>
					<?= number_format($Sum, 2);?> Kg
					<input type='hidden' name='total_kg' value='<?= number_format($Sum, 2);?>'>
				</th>
				<th class="text-right" style='padding-right:20px;'>
					<?= number_format($SumX, 2);?>
					<input type='hidden' name='total_cost' value='<?= number_format($SumX, 2);?>'>
				</th>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>
<script>
	$(document).ready(function(){
		swal.close();
	});
	$(document).on('click', '#detailDT', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailDT/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#MatDetail', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
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