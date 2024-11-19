<?php
$this->load->view('include/side_menu'); 
?> 
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			if($akses_menu['create']=='1'){		?>
			  <a href="<?php echo site_url('supplier/add') ?>" class="btn btn-sm btn-success" id='btn-add'>
				<i class="fa fa-plus"></i> Add Supplier
			  </a>
			  <?php
			}
			if($akses_menu['download']=='1'){		?>
			  <input type='button' id='upload' class='btn btn-sm btn-warning' value='Upload/Download'>
		  <?php
			}
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-left">Supplier ID</th>
					<th class="text-left">Supplier Name</th>
					<th class="text-left">Country Name</th>
					<th class="text-center">Status</th>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody>
			  <?php 
			  if($row){
					$int	=0;
					foreach($row as $datas){
						$int++;
						$class	= 'bg-green';
						$status	= 'Active';
						if($datas->sts_aktif == 'nonaktif'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						echo"<tr>";							
							echo"<td align='center'>$int</td>";
							echo"<td align='left'>".$datas->id_supplier."</td>";
							echo"<td align='left'>".$datas->nm_supplier."</td>";
							echo"<td align='left'>".$datas->country_name."</td>";
							echo"<td align='center'><span class='badge $class'>$status</span></td>";
							echo"<td align='center'>";
								echo"<button type='button' data-id_supplier='".$datas->id_supplier."' data-nm_supplier='".$datas->nm_supplier."' class='btn btn-sm btn-warning detail' title='View Data' data-role='qtip'><i class='fa fa-eye'></i></button>";
								if($akses_menu['update']=='1'){
									echo"&nbsp;<a href='".site_url($this->uri->segment(1).'/edit/'.$datas->id_supplier)."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
								}									
								if($akses_menu['delete']=='1'){
									echo"&nbsp;<button type='button' data-id_supplier='".$datas->id_supplier."' class='btn btn-sm btn-danger delete_data' title='Delete Data' data-role='qtip'><i class='fa fa-trash'></i></button>";
								}
							echo"</td>";
						echo"</tr>";
					}
			  }
			  ?>
			</tbody>
		</table>
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
<script>
	$(document).ready(function(){
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$(document).on('click', '#upload', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DOWNLOAD TEMPLATE / UPLOAD TEMPLATE</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalUpload/');
			$("#ModalView").modal();
		});
		
		$(document).on('click', '.detail', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL SUPPLIER ["+$(this).data('nm_supplier')+"]</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetail/'+$(this).data('id_supplier'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '.delete_data', function(){
			var bF	= $(this).data('id_supplier');
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
						url			: base_url + active_controller+'/hapus/'+bF,
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
		
		$(document).on('click', '#uploadEx', function(){
			var excel_file = $('#excel_file').val();
			if(excel_file == '' || excel_file == null){
				swal({
				  title	: "Error Message!",
				  text	: 'File upload is Empty, please choose file first...',
				  type	: "warning"
				});
				// $('#simpan-bro').prop('disabled',false);
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
					var baseurl		= base_url + active_controller +'/importData';
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
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller;
							}
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000
								});
							}
							$('#uploadEx').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 5000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#uploadEx').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#uploadEx').prop('disabled',false);
				return false;
			  }
			});
		});
		
	});
	
</script>
