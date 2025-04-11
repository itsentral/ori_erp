<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
            <button type='button' id='upload' class='btn btn-sm btn-primary' style='float:right;'>Upload Deadstok</button>
            <a href="<?php echo site_url('deadstok/download_excel') ?>" class="btn btn-sm btn-success" style='float:right; margin-right:5px;'>Download Data</a>
            <a href="<?php echo site_url('deadstok/download_template') ?>" class="btn btn-sm btn-warning" style='float:right; margin-right:5px;'>Download Template</a>
            <a href="<?php echo site_url('deadstok/spk_print') ?>" class="btn btn-sm btn-info" style='float:right; margin-right:5px;'>SPK On Progress</a>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">ID</th>
						<th class="text-center th">Category</th>
						<th class="text-center th">No Barang</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">Type</th>
						<th class="text-center th">Spec</th>
						<th class="text-center th">Resin</th>
						<th class="text-center th">Length</th>
						<th class="text-center th">Qty</th>
						<th class="text-center th">Dibooking</th>
						<th class="text-center th">Option</th>
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
		DataTables();
	});

    $(document).on('click', '#upload', function(e){
        e.preventDefault();
        $("#head_title").html("<b>UPLOAD TEMPLATE</b>");
        $("#view").load(base_url + active_controller+'/modalUpload');
        $("#ModalView").modal();
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
                var formData  	= new FormData($('#form_proses')[0]);
                var baseurl		= base_url + active_controller +'/upload_deadstok';
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

    $(document).on('click', '.delete', function(){
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
					url			: base_url+active_controller+'/delete/'+bF,
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
								  timer	: 3000
								});
								DataTables(); 
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
		
	function DataTables(){
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150, 500, 750, 1000], [10, 20, 50, 100, 150, 500, 750, 1000]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON',
				type: "post",
				data: function(d){
					// d.kode_partner = $('#kode_partner').val()
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
