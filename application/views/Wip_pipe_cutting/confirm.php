
<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">

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
							echo "<td align='right'>".number_format($length)."</td>";
							echo "<td align='left'>".$spool_drawing."</td>";
						echo "</tr>";
						
						if($count > 1){
							$nox = 0;
							for($a=2; $a<=$count; $a++){ $nox++;
								echo "<tr>";
								echo "<td align='right'>".number_format($each[$nox]['length_split'])."</td>";
								echo "<td align='left'>".$each[$nox]['spool_drawing']."</td>";
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
        <input type="hidden" name='id' value='<?=$valx['id'];?>'>
		<a href="<?php echo site_url($this->uri->segment(1)) ?>" style='margin-top:5px; float:right;' class="btn btn-md btn-danger">Back</a>
		<?php if(!empty($detail)){ ?>
		<button type='button' id='save_split' class='btn btn-success' style='margin-top:5px; margin-right:5px; float:right;'>Closing</button>	
		<?php } ?>		
	</div>
</div>
</form>
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
					var formData  	= new FormData($('#form_proses')[0]);
					var baseurl		= base_url + active_controller +'/confirm';
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
								window.location.href = base_url + active_controller;
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
</script>