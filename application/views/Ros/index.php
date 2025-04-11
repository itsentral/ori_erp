<?php
$this->load->view('include/side_menu');
?>
<style>
.list-group{
    max-height: 300px;
    margin-bottom: 10px;
    overflow:scroll;
    -webkit-overflow-scrolling: touch;
}
.chosen-container{
	width: 100% !important;
	text-align : left !important;
}
</style>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool">
		<?php
			if($akses_menu['create']=='1'){
		?><div class="row">
			<div class="col-md-6">
					<button class="btn btn-success" type="button" id="ros_material" onclick="showhide(1)">
						<i class="fa fa-plus">&nbsp;</i> New ROS Material
					</button>
					<div id="ros_1" class="hidden">
					<select id="ros_c1" name="ros_material" onchange="add_new(1)">
						<option value="">Pilih PO Material</option>
						<?php
						foreach($no_po AS $val => $valx){
							echo "<option value='".$valx['no_po']."'>".strtoupper($valx['no_po'])." - ".$valx['nm_supplier']."</option>";
						}
					?>
					</select>
					</div>
			</div>
			<div class="col-md-6">
					<button class="btn btn-info" type="button" id="ros_non_material" onclick="showhide(2)">
						<i class="fa fa-plus">&nbsp;</i> New ROS Non Material
					</button>
					<div id="ros_2" class="hidden">
					<select id="ros_c2" name="ros_non_material" onchange="add_new(2)">
						<option value="">Pilih PO Non Material</option>
						<?php
						foreach($no_po_nm AS $val => $valx){
							echo "<option value='".$valx['no_po']."'>".strtoupper($valx['no_po'])." - ".$valx['nm_supplier']."</option>";
						}
					?>
					</select>
					</div>
			</div>
		  <?php
			}
		  ?>			
		</div>		
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Nomor ROS</th>
					<th class="text-center">No PO</th>
					<th class="text-center">Supplier</th>
					<th class="text-center">Tgl ROS</th>
					<th class="text-center">Status</th>
					<th class="text-center">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
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
<?php $this->load->view('include/footer'); ?>
<script>
function showhide(id){
	$("#ros_"+id).removeClass("hidden");
	ids=1;
	if(id==1) ids=2;
	$("#ros_"+ids).addClass("hidden");
}
function add_new(id){
	var idpo=$("#ros_c"+id).chosen().val();
	window.location.href = base_url + active_controller + '/add_ros/' + idpo +"/"+id;
}
$(document).ready(function() {
    DataTables();
});

$(document).on('click', '.viewed', function(e) {
    window.location.href = base_url + active_controller + '/detail_ros/' + $(this).data('ros') + '/' + $(this).data('tipetrans');
});

$(document).on('click', '.edited', function(e) {
    window.location.href = base_url + active_controller + '/edit_ros/' + $(this).data('ros') + '/' + $(this).data('tipetrans');
});

function DataTables() {
    var dataTable = $('#my-grid').DataTable({
        "serverSide": true,
        "stateSave": true,
        "autoWidth": false,
        "processing": true,
        "destroy": true,
        "responsive": true,
        "aaSorting": [
            [1, "desc"]
        ],
        "columnDefs": [{
            "targets": 'no-sort',
            "orderable": false,
        }],
        "sPaginationType": "simple_numbers",
        "iDisplayLength": 10,
        "aLengthMenu": [
            [10, 20, 50, 100, 150],
            [10, 20, 50, 100, 150]
        ],
        "ajax": {
            url: base_url + active_controller + '/server_side_ros',
            type: "post",
            cache: false,
            error: function() {
                $(".my-grid-error").html("");
                $("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
                $("#my-grid_processing").css("display", "none");
            }
        }
    });
}

$(document).on('click', '.deleted', function() {
    var idros = $(this).data('ros');

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
                    url: base_url + active_controller + '/delete_ros/' + idros + '/' + $(this).data('tipetrans'),
                    type: "POST",
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 1) {
                            swal({
                                title: "Delete Success!",
                                text: data.pesan,
                                type: "success",
                                timer: 5000
                            });
                            window.location.href = base_url + active_controller;
                        } else if (data.status == 0) {
                            swal({
                                title: "Delete Failed!",
                                text: data.pesan,
                                type: "warning",
                                timer: 5000
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title: "Error Message !",
                            text: 'An Error Occured During Process. Please try again..',
                            type: "warning",
                            timer: 5000
                        });
                    }
                });
            } else {
                swal("Cancelled", "Data can be process again :)", "error");
                return false;
            }
        });
});

$(document).on('click', '.updated', function() {
    var idros = $(this).data('ros');

    swal({
            title: "Are you sure?",
            text: "Update this data ?",
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
                    url: base_url + active_controller + '/update_ros/' + idros + '/' + $(this).data('tipetrans'),
                    type: "POST",
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status == 1) {
                            swal({
                                title: "Update Success!",
                                text: data.pesan,
                                type: "success",
                                timer: 5000
                            });
                            window.location.href = base_url + active_controller;
                        } else if (data.status == 0) {
                            swal({
                                title: "Update Failed!",
                                text: data.pesan,
                                type: "warning",
                                timer: 5000
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title: "Error Message !",
                            text: 'An Error Occured During Process. Please try again..',
                            type: "warning",
                            timer: 5000
                        });
                    }
                });
            } else {
                swal("Cancelled", "Data can be process again :)", "error");
                return false;
            }
        });
});
</script>
