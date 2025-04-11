<?php
$this->load->view('include/side_menu'); 
?>  
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
			<?php
				 if($akses_menu['create']=='1'){
			?>
			  <button type='button' class="btn btn-sm btn-success" id='addDefault'>
				<i class="fa fa-plus"></i> Add Default
			  </button>
			  <?php
				}
			  ?>
			</div>
			<br><br>
			<div class="box-tool pull-right">
				<label>Search : </label>
				<select id='series' name='series' class='form-control input-sm' style='min-width:200px;'>
					<option value='0'>All Standart Default</option>
					<?php
						foreach($listseries AS $val => $valx){
							echo "<option value='".$valx['nm_default']."'>".strtoupper($valx['nm_default'])."</option>";
						}
					?>
				</select>
				<?php
				if(empty($this->uri->segment(3))){
				?>
				<select id='komponenc' name='komponenc' class='form-control input-sm' style='min-width:200px;'>
					<option value='0'>All Component</option>
					<?php
						foreach($listkomponen AS $val => $valx){
							echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
						}
					?>
				</select> 
				<?php
				}
				?>
				<input type='hidden' name='group' id='group' value='<?= $this->uri->segment(3);?>'>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<table id="my-grid" width='100%' class="table table-bordered table-striped">
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">No.</th>
						<th class="text-center">Standart</th>
						<th class="text-center">Product</th>
						<th class="text-center">Dim 1</th>
						<th class="text-center">Dim 2 </th>
						<th class="text-center">Waste</th>
						<th class="text-center">Max Tolerance</th>
						<th class="text-center">Min Tolerance</th>
						<th class="text-center no-sort">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<!-- /.box-body -->
	 </div> 
  <!-- /.box -->

  <!-- modal -->
	<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
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
	
	<div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:30%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
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
<style>
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
</style>
<script>
	$(document).ready(function(){
		$(".chosen-select").chosen();
		
		$(document).ready(function(){
			var group = $('#group').val();
			var series = $('#series').val();
			// var komponen = "";
			// if(group != ""){
				var komponen = $('#komponenc').val();
			// } 
			DataTables(series, group, komponen); 
		});
		
		$(document).on('change','#series', function(e){
			e.preventDefault();
			var series = $('#series').val();
			var group = $('#group').val();
			// var komponen = "";
			// if(group != ""){
				var komponen = $('#komponenc').val();
			// }
			DataTables(series, group, komponen);
		});
		
		$(document).on('change','#komponenc', function(e){
			e.preventDefault();
			var series = $('#series').val();
			var group = $('#group').val();
			var komponen = $('#komponenc').val();
			DataTables(series, group, komponen);
		});
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		$('#printSPK').click(function(e){
			e.preventDefault();
			var id_product	= $(this).data('id_product');
			
			var Links		= base_url +'index.php/'+ active_controller+'/printSPK/'+id_product;
			window.open(Links,'_blank');
		});
		
		$(document).on('click', '#detDefault', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL DEFAULT</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailDefault/'+$(this).data('id'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#editDefault', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>EDIT DEFAULT</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalEditDetailDefault/'+$(this).data('id'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#addDefault', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>ADD DEFAULT</b>");
			$("#view").load(base_url +'index.php/'+ active_controller+'/modalAddDefault/');
			$("#ModalView").modal();
		});
		$(document).on('click', '#addP', function(e){
			e.preventDefault();
			$("#head_title2").html("<b>ADD STANDART DEFAULT</b>");
			$("#view2").load(base_url +'index.php/'+ active_controller+'/modalAddP/');
			$("#ModalView2").modal();
		});
		
		$(document).on('click', '#addPSave', function(){
			var standart_code			= $('#standart_codex').val();
			
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Default Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/addPSave',
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
									  timer	: 5000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller+'/'+data_url;
								$("#ModalView2").modal('hide');
								$("#head_title").html("<b>ADD DEFAULT</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalAddDefault/');
								$("#ModalView").modal();
								
								
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000,
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
							  timer				: 5000,
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
		
		$(document).on('click', '#addDefaultSave', function(){
			var komponen		= $('#komponen').val();
			var standart_code	= $('#standart_code').val();
			var diameter		= $('#diameter').val();
			var diameter2		= $('#diameter2').val();
			var max				= $('#max').val();
			var min				= $('#min').val();
			var plastic_film	= $('#plastic_film').val();
			var waste			= $('#waste').val();
			var overlap			= $('#overlap').val();
			
			if(komponen == '' || komponen == null || komponen == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Product Namex is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(standart_code == '' || standart_code == null || standart_code == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Default Name is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(diameter == '' || diameter == null || diameter == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(diameter2 == '' || diameter2 == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Diameter 2 is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(plastic_film == '' || plastic_film == null || plastic_film == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plastic Faktor is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(waste == '' || waste == null || waste == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Waste is Empty, please input first ...',
				  type	: "warning"
				});
				$('#addDefaultSave').prop('disabled',false);
				return false;	
			}
			if(komponen == 'elbow mould' || komponen == 'elbow mitter'){
				if(overlap == '' || overlap == null){
					swal({
					  title	: "Error Message!",
					  text	: 'Overlap is Empty, please input first ...',
					  type	: "warning"
					});
					$('#addDefaultSave').prop('disabled',false);
					return false;	
				}
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
						url			: base_url+'index.php/'+active_controller+'/addDefaultSave',
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
									  timer	: 5000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller+'/default_master';
								// $("#ModalView2").modal('hide');
								// $("#head_title").html("<b>ADD DEFAULT</b>");
								// $("#view").load(base_url +'index.php/'+ active_controller+'/modalAddDefault/');
								// $("#ModalView").modal();
								
								
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000,
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
							  timer				: 5000,
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
		
		
		$(document).on('click', '#editDefaultSave', function(){
			var max				= $('#max').val();
			var min				= $('#min').val();
			var plastic_film	= $('#plastic_film').val();
			var waste			= $('#waste').val();
			var overlap			= $('#overlap').val();
			
			if(plastic_film == '' || plastic_film == null || plastic_film == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Plastic Faktor is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;	
			}
			if(waste == '' || waste == null || waste == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Waste is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
				return false;	
			}
			if(overlap == '' || overlap == null){
				swal({
				  title	: "Error Message!",
				  text	: 'Overlap is Empty, please input first ...',
				  type	: "warning"
				});
				$('#editDefaultSave').prop('disabled',false);
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
						url			: base_url+'index.php/'+active_controller+'/editDefaultSave',
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
									  timer	: 5000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller+'/default_master';	
								
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 5000,
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
							  timer				: 5000,
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
	
	function DataTables(series = null, group = null, komponen = null){
		// alert(series);
		// alert(group);
		// alert(komponen);
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url +'index.php/'+active_controller+'/getDataJSONDefault',
				type: "post",
				data: function(d){
					d.series 	= series,
					d.group 	= group,
					d.komponen 	= komponen
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
</script>
