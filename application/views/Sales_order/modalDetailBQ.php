<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM so_bf_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT a.*, b.sum_mat, b.est_harga FROM so_bf_detail_header a INNER JOIN estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' ORDER BY a.id_delivery ASC, a.sub_delivery ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
$NumBaris		= $this->db->query($qBQdetailHeader)->num_rows();
?>
<div class="box-body">
	<div class="form-group row">
		<?php
		echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
		echo form_input(array('type'=>'hidden','id'=>'numR'),$NumBaris);
		?>
	</div>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:150px; float:right; margin: 0px 0px 5px 0px;','value'=>'Go Final Drawing','content'=>'Go Final Drawing','id'=>'approvedQ')).' ';
	?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='6%' class='no-sort'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='17%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='28%'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Cost</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Option</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				$no = 0;
				foreach($qBQdetailRest AS $val => $valx){ $no++;
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
					$dist = '';
					if($valx['so_sts'] == 'Y'){
						$dist = 'disabled';
					}
					echo "<tr>";
						echo "<td align='right' class='so_style_list'><center>".$no."<input type='hidden' id='est_harga_".$no."' value='".$valx['est_harga']."'></center></td>";
						echo "<td align='left' class='so_style_list'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							if($valx['id_category'] == 'pipe'){$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);}
							else{$dim = "belum di set";} 
						echo "<td align='left' class='so_style_list'>".$spaces."".$dim."</td>";
						echo "<td align='center' class='so_style_list'>
								<input type='text' name='UpQtySo[".$no."][qty]' class='numberOnly chQty' style='text-align: center; width:100%;' data-no='".$no."' value='".$valx['qty']."'>
								<input type='hidden' name='UpQtySo[".$no."][id]' style='text-align: center; width:100%;' value='".$valx['id']."'>
								</td>";
						echo "<td align='left' class='so_style_list'>".$valx['id_product']."</span></td>";
						echo "<td align='right' class='so_style_list'><div id='sumAk_".$no."'>".number_format($SumQtyX, 2)."</div></span></td>";
						echo "<td align='center' class='so_style_list'><button type='button' data-id='".$valx['id']."' data-id_bq_header='".$valx['id_bq_header']."' data-id_milik='".$valx['id_milik']."' class='btn btn-sm btn-danger del_so' title='Delete From SO / Back To Quotation' data-role='qtip'><i class='fa fa-trash'></i></button></span></td>";
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-center" colspan='5' style='vertical-align:middle;'>Total</th>
				<th class="text-right"><div id='sumSO'><?= number_format($SumX, 2);?></div></th>
				<th class="text-right" style='padding-right:20px;'></th>
			</tr>
		</tbody>
	</table>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','style'=>'min-width:150px; float:right; margin: 5px 0px 0px 0px;','value'=>'Save qty SO','content'=>'Save Qty SO','id'=>'saveQtySO')).' ';
	?>
</div>
<style>
	.so_style_list{
		vertical-align:middle;
		padding-left:20px;
	}
</style>

<script>
	swal.close();
	
	$(".numberOnly").on("keypress keyup blur",function (event) {    
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});
	
	$(document).on('keyup','.chQty', function(){
		var nomor = $(this).data('no');
		var numR = $('#numR').val();
		var qty = parseFloat($(this).val());
		var perPro = parseFloat($('#est_harga_'+nomor).val());
		var sumPro = getNum(qty * perPro);
		$('#sumAk_'+nomor).html(sumPro.toFixed(2));
		var a;
		var TotalSum = 0;
		for(a=1;a<=numR;a++){
			var getT = getNum($('#sumAk_'+a).html());
			TotalSum += getT;
		}
		$('#sumSO').html(TotalSum.toFixed(2));
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
	
	$("#chk_all").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
	
	$(document).ready(function(){
		$('#HideReject').hide();
		$(document).on('change', '#status', function(){
			if($(this).val() == 'N'){
				$('#HideReject').show();
			}
			else{
				$('#HideReject').hide();
			}
		});
		
		$(document).on('click', '#approvedQ', function(){
			
			var bF				= $('#id_bq').val();
			
			swal({
			  title: "Are you sure?",
			  text: "Jika Sudah Final Drawing Data Tidak Bisa Di Kembalikan Ke Proses Sebelumnya",
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
						url			: base_url+'index.php/'+active_controller+'/AppCost/'+bF,
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
								window.location.href = base_url + active_controller;
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
	
	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}

</script>