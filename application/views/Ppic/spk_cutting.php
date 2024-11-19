<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
            <button type='button' class='btn btn-md btn-success' id='cutting' style='float:right;'><i class='fa fa-scissors'></i> Cutting</button><br><br>
			<select name='no_ipp' id='no_ipp' class='form-control input-sm chosen-select' style='width:150px; float:right;'>
				<option value='0'>ALL IPP</option>
				<?php
				foreach($data_ipp as $val => $valx)
				{
					echo "<option value='".$valx['id_bq']."'>".str_replace('BQ--','',$valx['id_bq'])."</option>";
				}
				?>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">IPP</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">DN1</th>
						<th class="text-center th">Length</th>
						<th class="text-center th">Thick</th>
						<th class="text-center th no-sort">Product Code</th>
						<th class="text-center th no-sort">No SPK</th>
						<th class="text-center th no-sort">Cutting Plan</th>
						<th class="text-center th no-sort">Date</th>
						<th class="text-center th no-sort">Qty</th>
						<th class="text-center th no-sort">Cut</th>
						<th class="text-center th no-sort">Option</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<!-- modal -->
<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:50%; '>
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
		let no_ipp = $('#no_ipp').val();
		DataTables(no_ipp);

		$(document).on('change','#no_ipp', function(){
			let no_ipp = $('#no_ipp').val();
			DataTables(no_ipp);
		});

		$(document).on('click', '.edit_spk', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>SET SPK PRODUKSI</b>");
			$.ajax({
				type:'POST',
				url: base_url + active_controller+'/edit_spk_cutting/'+$(this).data('id'),
				success:function(data){
					$("#ModalView").modal();
					$("#view").html(data);

				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Timed Out ...',
					type				: "warning",
					timer				: 5000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

        $(document).on('click', '#cutting', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist product minimal 1',
					type	: "warning"
				});
				return false;
			}
			// return false;
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
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/cutting_multiple',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
                                window.location.href = base_url + active_controller+'/cutting/'+data.id_list;
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
									type	: "warning",
									timer	: 3000
								});
							}
						},
						error: function() {
							swal({
							title		: "Error Message !",
							text		: 'An Error Occured During Process. Please try again..',						
							type		: "warning",								  
							timer		: 3000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		//LOCK APLIT
		$(document).on('click', '.lock_split', function(e){
			e.preventDefault();
			let id = $(this).data('id');
			swal({
			  title: "Are you sure?",
			  text: "Tidak dapat di cutting ulang !!!",
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
					var baseurl		= base_url + active_controller +'/lock_split/'+id;
					$.ajax({
						url			: baseurl,
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
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 3000
							});
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			  }
			});
		});

		
		$(document).on('click','.copy_eksclude', function(){
		
			// console.log(nomor)
			var Rows	= "<tr>";
				Rows	+= "<td>";
				Rows	+= "<input type='text' class='form-control input-sm text-left' name='detail[]' placeholder='Tahapan'></td>";
				Rows	+= "<td align='left'>";
				Rows	+= "<button type='button' class='btn btn-sm btn-success copy_eksclude' title='Add'><i class='fa fa-plus'></i></button>";
				Rows	+= "&nbsp;<button type='button' class='btn btn-sm btn-danger delete_test' style='margin-left:5px;' title='Delete'><i class='fa fa-trash'></i></button>";
				Rows	+= "</td>";
				Rows	+= "</tr>";
			// alert(Rows);
			$(this).parent().parent().after(Rows);
		});

		$(document).on('click','.delete_test', function(){
			$(this).parent().parent().remove();
		});

		$(document).on('click', '#save_spk', function(e){
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
					var baseurl		= base_url + active_controller +'/edit_spk_cutting';
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
							$('#save_spk').prop('disabled',false);
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 3000
							});
							$('#save_spk').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#save_spk').prop('disabled',false);
				return false;
			  }
			});
		});
		

	});
		
	function DataTables(no_ipp=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"lengthChange": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url +active_controller+'/server_side_spk_cutting',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp
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
