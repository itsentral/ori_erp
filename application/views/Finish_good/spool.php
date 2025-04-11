<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title; ?></h3>
            <div class="box-tool pull-right">
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
                <thead>
                    <tr class='bg-blue'>
                        <th class="text-center">#</th>
                        <th class="text-center">Kode</th>
                        <th class="text-center">Kode Spool</th>
                        <th class="text-center">No Drawing</th>
                        <th class="text-center">IPP</th>
                        <th class="text-center">Kode Product</th>
                        <th class="text-center">By</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Option</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</form>
<!-- modal -->
<div class="modal fade" id="ModalView" style='overflow-y: auto;'>
    <div class="modal-dialog" style='width:70%; '>
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
$(document).ready(function() {
    $('.chosen-select').chosen({
        width: '150px'
    })
    DataTables2();

    $(document).on('click', '.lock_spool', function(e) {
        e.preventDefault();

        let spool = $(this).data('spool');


        swal({
                title: "Are you sure?",
                text: "Lock Spool, next process QC !!!",
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
                    var baseurl = base_url + active_controller + '/release_spool';
                    $.ajax({
                        url: baseurl,
                        type: "POST",
                        data: {
                            'spool': spool
                        },
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                swal({
                                    title: "Save Success!",
                                    text: data.pesan,
                                    type: "success",
                                    timer: 3000
                                });
                                window.location.href = base_url + active_controller +
                                    "/spool";
                            } else {
                                swal({
                                    title: "Save Failed!",
                                    text: data.pesan,
                                    type: "warning",
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {

                            swal({
                                title: "Error Message !",
                                text: 'An Error Occured During Process. Please try again..',
                                type: "warning",
                                timer: 3000
                            });
                        }
                    });
                } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    return false;
                }
            });
    });

    $(document).on('click', '.qr', function(e) {
        e.preventDefault();

        // var Links = base_url + active_controller + '/print_qrcode_spool/' + $(this).data(
        // 'id_pro_detail');
        // window.open(Links, '_blank');
        $("#head_title").html("<b>QR Code Spool</b>");
        $("#view").load(base_url + active_controller + '/modalCreateQRSpool/' + $(this).data(
            'id_pro_detail'));
        $("#ModalView").modal();
    });

    $(document).on('click', '#print_qrcode', function(e) {
        e.preventDefault();
        var id_pro_detail = $('#id_pro_detail').val();
        let logo = $('input[name="logo"]:checked').val()
        let size = $('input[name="size"]:checked').val()

        if (id_pro_detail) {
            var Links = base_url + active_controller + '/print_qrcode_spool/' + id_pro_detail + "/" +
                logo +
                "/" + size;
            window.open(Links, '_blank');
        } else {
            swal({
                title: "Warning!",
                text: "Mohon pilih produk terlebih dahulu!",
                type: "warning",
                timer: 3000
            });
        }
    });
});

function DataTables2(status = null) {
    var dataTable = $('#my-grid2').DataTable({
        "serverSide": true,
        "stateSave": true,
        "bAutoWidth": true,
        "destroy": true,
        "processing": true,
        "responsive": true,
        "fixedHeader": {
            "header": true,
            "footer": true
        },
        "aaSorting": [
            [1, "asc"]
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
            url: base_url + active_controller + '/server_side_spool',
            type: "post",
            data: function(d) {
                d.status = status
            },
            cache: false,
            error: function() {
                $(".my-grid2-error").html("");
                $("#my-grid2").append(
                    '<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>'
                );
                $("#my-grid2_processing").css("display", "none");
            }
        }
    });
}
</script>