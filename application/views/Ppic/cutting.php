
<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<br><br>
		<div class="box-tool pull-left">
		</div>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class='text-center vam' width='4%'>No</th>
					<th class='text-center vam' width='12%'>Product</th>
					<th class='text-center vam' width='18%'>Id Product</th>
					<th class='text-center vam' width='9%'>Diameter</th>
					<th class='text-center vam' width='9%'>Thickness</th>
					<th class='text-center vam' width='9%'>Length</th>
					<th class='text-center vam' width='12%'>Sum Length Split</th>
					<th class='text-center vam' width='12%'>Length Split</th>
					<th class='text-center vam' width='12%'>Spool Number</th>
					<th class='text-center vam' width='5%'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no = 0;
				if(!empty($detail)){
					foreach($detail AS $val => $valx){
						$no++;
						$count 	= $this->db->query("SELECT * FROM so_cutting_detail WHERE id_bq = '".$valx['id_bq']."' AND id_header='".$valx['id']."'")->num_rows();
						$each 	= $this->db->query("SELECT * FROM so_cutting_detail WHERE id_bq = '".$valx['id_bq']."' AND id_header='".$valx['id']."'")->result_array();
						$sum 	= $this->db->query("SELECT SUM(length_split) AS total FROM so_cutting_detail WHERE id_bq = '".$valx['id_bq']."' AND id_header='".$valx['id']."'")->result();
						
						$length 	= (!empty($each))?$each[0]['length_split']:0;
						$spool_drawing 	= (!empty($each))?$each[0]['spool_drawing']:'';
						$count 		= (!empty($count))?$count:'1';
						
						// $disabled = ($valx['sts_plan'] == 'Y')?'disabled':'';
						$disabled = '';
						
						echo "<tr class='baris_".$no."'>";
							echo "<td rowspan='".$count."' align='center'>".$no."
									<input type='hidden' name='detail[".$no."][id_bq]' value='".$valx['id_bq']."' ".$disabled.">
									<input type='hidden' name='detail[".$no."][id_milik]' value='".$valx['id_milik']."' ".$disabled.">
									<input type='hidden' name='detail[".$no."][id_header]' value='".$valx['id']."' ".$disabled.">
									<input type='hidden' name='detail[".$no."][id_category]' value='".$valx['id_category']."' ".$disabled.">
									<input type='hidden' name='detail[".$no."][diameter]' value='".$valx['diameter_1']."' ".$disabled.">
									<input type='hidden' name='detail[".$no."][length]' value='".$valx['length']."' ".$disabled.">
									
									</td>";
							echo "<td rowspan='".$count."' align='center'>".strtoupper($valx['id_category'])."</td>";
							echo "<td rowspan='".$count."' align='left'>".$valx['id_product']."</td>";
							echo "<td rowspan='".$count."' align='center'>".number_format($valx['diameter_1'])."</td>";
							echo "<td rowspan='".$count."' align='center'>".number_format($valx['thickness'],2)."</td>";
							echo "<td rowspan='".$count."' align='center'><div id='qty_del_".$no."'>".number_format($valx['length'])."</div></td>";
							echo "<td rowspan='".$count."' align='left'><div id='tot_qty_del_".$no."'>Jumlah &nbsp;: ".number_format($sum[0]->total)."<br>Balance : ".number_format($valx['length'] - $sum[0]->total)."</div></td>";
							echo "<td align='center'><input type='text' name='detail[".$no."][detail][0][length]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center maskMoney qty_".$no." qty_deliv' ".$disabled." value='".number_format($length)."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='center'><input type='text' name='detail[".$no."][detail][0][spool]' data-no='".$no."' data-no2='1' class='form-control input-sm text-left' ".$disabled." value='".$spool_drawing."'></td>";
							echo "<td align='center'><button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$no."' ".$disabled."><i class='fa fa-plus'></i></button></td>";
						echo "</tr>";
						
						if($count > 1){
							$nox = 0;
							for($a=2; $a<=$count; $a++){ $nox++;
								echo "<tr>";
								echo "<td align='left'><input type='text' name='detail[".$no."][detail][".$nox."][length]' data-no='".$no."' data-no2='1' class='form-control input-sm text-center maskMoney qty_".$no." qty_deliv' ".$disabled." value='".number_format($each[$nox]['length_split'])."' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
								echo "<td align='left'><input type='text' name='detail[".$no."][detail][".$nox."][spool]' data-no='".$no."' data-no2='1' class='form-control input-sm text-left' ".$disabled." value='".$each[$nox]['spool_drawing']."'></td>";
									echo "<td align='center'>";
										echo "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='".$no."' ".$disabled."><i class='fa fa-trash'></i></button>";
									echo "</td>";
								echo "</tr>";
							}
						}
					}
				}else{
					echo "<tr>";
						echo "<td colspan='10'>Tidak ada product pipa</td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
		<br>
		<a href="<?php echo site_url('ppic/spk_cutting') ?>" style='margin-top:5px; float:right;' class="btn btn-md btn-danger">Back</a>
		<?php if(!empty($detail)){ ?>
		<button type='button' id='save_split' class='btn btn-success' style='margin-top:5px; margin-right:5px; float:right;'>Save</button>	
		<?php } ?>		
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor:pointer;
	}
	.vam{
		vertical-align:middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		
		$(document).on('click','.plus', function(){
			var no 		= $(this).data('id');
			// alert($(this).parent().parent().find("td:nth-child(1)").attr('rowspan'));return false;
			var kolom	= parseFloat($(this).parent().parent().find("td:nth-child(1)").attr('rowspan')) + 1;
			
			$(this).parent().parent().find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7)").attr('rowspan', kolom);
			
			var Rows	= "<tr>";
				Rows	+= "<td align='center'><input type='text' name='detail["+no+"][detail]["+kolom+"][length]' data-no='"+no+"' data-no2='"+kolom+"' class='form-control input-sm text-center maskMoney qty_"+no+" qty_deliv' placeholder='0' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
				Rows	+= "<td align='center'><input type='text' name='detail["+no+"][detail]["+kolom+"][spool]' data-no='"+no+"' data-no2='"+kolom+"' class='form-control input-sm text-left'></td>";
				Rows	+= "<td align='center'>";
				Rows	+= "<button type='button' class='btn btn-sm btn-danger delete' title='Delete' data-id='"+no+"'><i class='fa fa-trash'></i></button>";
				Rows	+= "</td>";
				Rows	+= "</tr>";
			// alert(Rows);
			$(this).parent().parent().after(Rows);
			
			$('.maskMoney').maskMoney();
		});
		
		$(document).on('click','.delete', function(){
			var no 		= $(this).data('id');
			var kolom	= parseFloat($(".baris_"+no).find("td:nth-child(1)").attr('rowspan')) - 1;
			$(".baris_"+no).find("td:nth-child(1), td:nth-child(2), td:nth-child(3), td:nth-child(4), td:nth-child(5), td:nth-child(6), td:nth-child(7)").attr('rowspan', kolom);
			$(this).parent().parent().remove();
			sum_qty_delivery(no);
		});
		
		$(document).on('keyup','.qty_deliv', function(){
			var no 	= $(this).data('no');
			sum_qty_delivery(no);
		});
		
		$(document).on('click', '#save_split', function(e){
			e.preventDefault();
			
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
					var baseurl		= base_url + active_controller +'/cutting';
					$.ajax({
						url			: baseurl,
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
									timer	: 3000
									});
								window.location.href = base_url + active_controller +'/spk_cutting';
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 3000
								});
							}
							$('#save_split').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 3000
							});
							$('#save_split').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_split').prop('disabled',false);
				return false;
			  }
			});
		});
	});
	
	function sum_qty_delivery(no = null){
		var SUM = 0;
		var qty = getNum($('#qty_del_'+no).html().split(",").join(""));
		
		// console.log('.qty_'+no);
		$('.qty_'+no).each(function(){
			var valuex = Number($(this).val().split(",").join(""));
			SUM += valuex;
		});
        var balance = qty - SUM;
		$('#tot_qty_del_'+no).html("Jumlah &nbsp;: "+number_format(SUM)+"<br>Balance : "+number_format(balance));
		
		if(SUM > qty){
			$(".baris_"+no).find("td:nth-child(7)").attr('style','background-color:red;');
			$('#save_split').hide();
		}
		else if(SUM == qty){
			$(".baris_"+no).find("td:nth-child(7)").attr('style','background-color:#00ff00;');
			$('#save_split').show();
		}else{
			$(".baris_"+no).find("td:nth-child(7)").attr('style','background-color:transparant;');
			$('#save_split').show();
		}
	}
</script>