<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data" autocomplete='off'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">QC Deadstok</a></li>
            <li><a data-toggle="tab" href="#menu1">QC Deadstok Modifikasi</a></li>
        </ul>
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br>
                <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">Sales Order</th>
                            <th class="text-center">No IPP</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center no-sort">Option</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div id="menu1" class="tab-pane fade"><br>
                <table class="table table-sm table-bordered table-striped" id="my-grid3" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">Sales Order</th>
                            <th class="text-center">No IPP</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center no-sort">Option</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

 <!-- modal -->
 <div class="modal fade" id="ModalView2">
    <div class="modal-dialog"  style='width:75%; '>
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
<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({
			width: '150px'
		})
		DataTables2();
		DataTables3();

        $(document).on('click', '.check_real', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>QC Deadstok</b>");
            $("#view2").load(base_url + active_controller + '/modal_qc_deadstok/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
            $("#ModalView2").modal();
        });

        $(document).on('click', '.check_real_modif', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>QC Deadstok</b>");
            $("#view2").load(base_url + active_controller + '/modal_qc_deadstok_modif/'+$(this).data('kode'));
            $("#ModalView2").modal();
        });

		$(document).on('click', '#sendCheck', function(e){
            e.preventDefault();
            // alert('Development');
            // return false;
            swal({
                    title: "Are you sure?",
                    text: "Pastikan semua data sudah benar!",
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
                        var formData 	=new FormData($('#form_proses')[0]);
                        var baseurl=base_url + active_controller +'/process_qc_deadstok';
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
                                            timer	: 7000
                                        });
                                    window.location.href = base_url + active_controller
                                }
                                else if(data.status == 2){
                                    swal({
                                        title	: "Save Failed!",
                                        text	: data.pesan,
                                        type	: "warning",
                                        timer	: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled',false);
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
                                $('#updateCheck').prop('disabled',false);
                            }
                        });
                    } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    $('#updateCheck').prop('disabled',false);
                    return false;
                    }
            });
        });

        $(document).on('click', '#rejectCheck', function(e){
            e.preventDefault();
            // alert('Development');
            // return false;
            swal({
                    title: "Are you sure?",
                    text: "Reject, Booking Deadstok akan dihapus !",
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
                        var formData 	=new FormData($('#form_proses')[0]);
                        var baseurl=base_url + active_controller +'/process_reject_qc_deadstok';
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
                                            timer	: 7000
                                        });
                                    window.location.href = base_url + active_controller
                                }
                                else if(data.status == 2){
                                    swal({
                                        title	: "Save Failed!",
                                        text	: data.pesan,
                                        type	: "warning",
                                        timer	: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled',false);
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
                                $('#updateCheck').prop('disabled',false);
                            }
                        });
                    } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    $('#updateCheck').prop('disabled',false);
                    return false;
                    }
            });
        });

        //MODIF
        $(document).on('click', '#sendCheck_modif', function(e){
            e.preventDefault();
            // alert('Development');
            // return false;
            swal({
                    title: "Are you sure?",
                    text: "Pastikan semua data sudah benar!",
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
                        var formData 	=new FormData($('#form_proses')[0]);
                        var baseurl=base_url + active_controller +'/process_qc_deadstok_modif';
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
                                            timer	: 7000
                                        });
                                    window.location.href = base_url + active_controller
                                }
                                else if(data.status == 2){
                                    swal({
                                        title	: "Save Failed!",
                                        text	: data.pesan,
                                        type	: "warning",
                                        timer	: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled',false);
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
                                $('#updateCheck').prop('disabled',false);
                            }
                        });
                    } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    $('#updateCheck').prop('disabled',false);
                    return false;
                    }
            });
        });

        $(document).on('click', '#rejectCheck_modif', function(e){
            e.preventDefault();
            // alert('Development');
            // return false;
            swal({
                    title: "Are you sure?",
                    text: "Reject, Booking Deadstok akan dihapus !",
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
                        var formData 	=new FormData($('#form_proses')[0]);
                        var baseurl=base_url + active_controller +'/process_reject_qc_deadstok_modif';
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
                                            timer	: 7000
                                        });
                                    window.location.href = base_url + active_controller
                                }
                                else if(data.status == 2){
                                    swal({
                                        title	: "Save Failed!",
                                        text	: data.pesan,
                                        type	: "warning",
                                        timer	: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled',false);
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
                                $('#updateCheck').prop('disabled',false);
                            }
                        });
                    } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    $('#updateCheck').prop('disabled',false);
                    return false;
                    }
            });
        });
		
	});

	function DataTables2(status=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_qc_deadstok',
				type: "post",
				data: function(d){
					d.status = status
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}

    function DataTables3(status=null){
		var dataTable = $('#my-grid3').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_qc_deadstok_modif',
				type: "post",
				data: function(d){
					d.status = status
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
