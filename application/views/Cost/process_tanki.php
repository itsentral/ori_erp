<?php
$this->load->view('include/side_menu'); 
?>  
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">  
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br> 
		<div class="box-tool pull-left">
			<!-- <button type='button' id='update_cost' style='min-width:150px;' class="btn btn-sm btn-primary">
				Update Cost Process
			  </button><br><div style='color:red;'><b>Last Update by <?= ucwords(strtolower($get_by[0]['update_by']))." On <u>".date('d-m-Y H:i:s', strtotime($get_by[0]['update_on']));?></u></b></div>
			  <div id="spinnerx">
				<img src="<?php echo base_url('assets/img/tres_load.gif') ?>" > <span style='color:green; font-size:16px;'><b>Please Wait ...</b></span>
			</div> -->
		</div>
		<div class="box-tool pull-right">
		<!-- <?php
			if($akses_menu['create']=='1'){		?>
			  <a href="<?php echo site_url('cost/add_process') ?>" style='min-width:150px; margin-top:10px; float:right;' class="btn btn-sm btn-success" id='btn-add'>
				<i class="fa fa-plus"></i> Add Process Cost
			  </a>
		  <?php
			}
		  ?> -->

		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width ='7%'>No.</th>
					<th class="text-center" width ='16%'>Item Cost</th>
					<th class="text-center" width ='24%'>Standart Perhitungan</th>
					<th class="text-center" width ='15%'>Standart Rate (USD)</th>
					<th class="text-center" width ='10%'>Update By</th>
					<th class="text-center" width ='15%'>Update Time</th>
					<th class="text-center" width ='12%'>Option</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						if($datas->deleted == 'N'){
							$int++;
							$updated_by = (!empty($datas->updated_by))?ucwords(strtolower($datas->updated_by)):ucwords(strtolower($datas->created_by));
							$updated_date = (!empty($datas->updated_date))?$datas->updated_date:$datas->created_date;
							echo"<tr>";							
								echo"<td align='center'>$int</td>"; 
								echo"<td align='left'>".$datas->item_cost."</td>";
								echo"<td align='left'>".$datas->std_hitung."</td>";
								echo"<td align='center'>".number_format($datas->std_rate, 2)."</td>";
								echo"<td align='left'>".$updated_by."</td>";
								echo"<td align='center'>".date('d-m-Y H:i:s', strtotime($updated_date))."</td>";
								echo"<td align='center'>";
									echo "<a id='viewM' data-id='".$datas->id."' class='btn btn-sm btn-warning' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></a>";
									if($akses_menu['update']=='1'){
										echo"&nbsp;<a id='editM' data-id='".$datas->id."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
									}									
									// if($akses_menu['delete']=='1'){
										// echo"&nbsp;<button type='button' id='del_satuan' data-id='".$datas->id."' class='btn btn-sm btn-danger' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";
									// }
								echo"</td>";
							echo"</tr>";
						}
					}
			  }
			  ?>
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:85%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#spinnerx').hide();
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#viewM', function(e){
			var id 	= $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>DETAIL PROCESS COST</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail1/'+id+'/process_tanki');
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#editM', function(e){
			var id 	= $(this).data('id');
			e.preventDefault();
			$("#head_title").html("<b>DETAIL PROCESS COST</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalEdit1/'+id+'/process_tanki');
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#update_cost', function(){
			
			swal({
			  title: "Update Cost Process ?",
			  text: "Tunggu sampai 'Last Update by ' menunjukan nama user dan update jam sekarang. ",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Update!",
			  cancelButtonText: "Tidak, Batalkan!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					// loading_spinner();
					$('#spinnerx').show();
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/update_cost',
						type		: "POST",
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
								$('#spinnerx').hide();
								window.location.href = base_url + active_controller + '/process_tanki';
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
								$('#spinnerx').hide();
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
							$('#spinnerx').hide();
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
		$(document).on('click', '#del_satuan', function(){
			var bF	= $(this).data('id');
			// alert(bF);
			// return false;
			swal({
			  title: "Are you sure?",
			  text: "Delete this data ?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/hapus/'+bF+'/process_tanki',
						type		: "POST",
						data		: "id="+bF,
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
								window.location.href = base_url + active_controller + '/process_tanki';
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
