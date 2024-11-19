<?php
$this->load->view('include/side_menu');
$status = $tanda;
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
    <input type="hidden" id='status' name='status' value='<?= $status; ?>'>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title; ?></h3>
            <div class="box-tool pull-right">
            <a href='<?=base_url('/qc/download_excel_qc/'.$status);?>' target='_blank' class="btn btn-md btn-success" id="btn_download" style="float:right;" title="Download"><i class="fa fa-file-excel-o"> &nbsp;Download</i></a>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
                <thead>
                    <tr class='bg-blue'>
                        <th class="text-center">#</th>
                        <th class="text-center">No SPK</th>
                        <th class="text-center">Product</th>
                        <th class="text-center no-sort">No SO</th>
                        <th class="text-center no-sort">Customer</th>
                        <th class="text-center no-sort">Project</th>
                        <th class="text-center no-sort">Spec</th>
                        <!-- <th class="text-center no-sort">Length</th>
                        <th class="text-center no-sort">Thick</th> -->
                        <!-- <th class="text-center no-sort">Product Code</th> -->
                        <th class="text-center no-sort">Qty</th>
                        <th class="text-center no-sort">Tgl Produksi</th>
                        <th class="text-center no-sort" width="100">Option</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->

    <!-- modal -->
    <div class="modal fade" id="ModalView2">
        <div class="modal-dialog" style='width:95%; '>
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
    $(document).ready(function() {
        $('.chosen-select').chosen({
            width: '150px'
        })

        let status = $('#status').val();
        DataTables2(status);

        // $(document).on('click', '.Perbandingan', function(e){ 
        //     e.preventDefault();
        //     loading_spinner();
        //     $("#head_title2").html("<b>HISTORY INPUT ["+$(this).data('id_produksi')+" / "+$(this).data('id_product')+"]</b>");
        //     $("#view2").load(base_url +'production/modalPerbandingan_tmp/'+$(this).data('id_product')+'/'+$(this).data('id_pro_detail')+'/'+$(this).data('id_produksi')+'/'+$(this).data('qty_awal')+'/'+$(this).data('qty_akhir')+'/'+$(this).data('id_milik'));
        //     $("#ModalView2").modal();
        // });

        $(document).on('click', '.check_real', function(e) {
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>Laporan Aktual Produksi</b>");
            $("#view2").load(base_url + active_controller + '/modalEditReal/' + $(this).data('kode_spk') + '/' + $(this).data('id_produksi') + '/' + $(this).data('id_milik') + '/' + $(this).data('id_pro_detail'));
            $("#ModalView2").modal();
        });

        $(document).on('click', '.detail', function(e) {
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>Progress QC</b>");
            $("#view2").load(base_url + active_controller + '/modalDetailQC/' + $(this).data('id'));
            $("#ModalView2").modal();
        });

        $(document).on('click', '.qr', function(e) {
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>QR Code Product</b>");
            $("#view2").load(base_url + active_controller + '/modalCreateQR/' + $(this).data('kode_spk') + '/' + $(this).data('id_produksi') + '/' + $(this).data('id_milik') + '/' + $(this).data('id_pro_detail'));
            $("#ModalView2").modal();
        });

           $(document).on('click', '#print_qrcode', function(e) {
            e.preventDefault();
            var idmilik = [];
            $('.chk_personal').each(function(i, obj) {
                if (this.checked) {
                    idmilik.push($(this).val());
                }
            });
            console.log(idmilik.length);
            if (idmilik.length > 0) {
                idmilik = idmilik.join("-")
                var Links = base_url + active_controller + '/print_qrcode/' + idmilik;
                window.open(Links, '_blank');
            } else {
                swal({
                    title: "Warning!",
                    text: "Mohon pilih produk terlebih dahulu!",
                    type: "warning",
                    timer: 5000
                });
            }
        });
        $(document).on('click', '#sendCheck', function(e) {
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
                        var formData = new FormData($('#form_proses_bro')[0]);
                        var baseurl = base_url + active_controller + '/real_send';
                        $.ajax({
                            url: baseurl,
                            type: "POST",
                            data: formData,
                            cache: false,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                if (data.status == 1) {
                                    swal({
                                        title: "Save Success!",
                                        text: data.pesan,
                                        type: "success",
                                        timer: 7000
                                    });
                                    // window.location.href = base_url + active_controller+"/quality/"+data.tanda;
                                    $("#head_title2").html("<b>Laporan Aktual Produksi</b>");
                                    $("#view2").load(base_url + active_controller + '/modalEditReal/' + data.kode_spk + '/' + data.id_produksi + '/' + data.id_milik + '/' + data.id_pro_detail);
                                    $("#ModalView2").modal();
                                } else if (data.status == 2) {
                                    swal({
                                        title: "Save Failed!",
                                        text: data.pesan,
                                        type: "warning",
                                        timer: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled', false);
                            },
                            error: function() {

                                swal({
                                    title: "Error Message !",
                                    text: 'An Error Occured During Process. Please try again..',
                                    type: "warning",
                                    timer: 7000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                });
                                $('#updateCheck').prop('disabled', false);
                            }
                        });
                    } else {
                        swal("Cancelled", "Data can be process again :)", "error");
                        $('#updateCheck').prop('disabled', false);
                        return false;
                    }
                });
        });

        $(document).on('click', '#sendCheckUpload', function(e) {
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
                        var formData = new FormData($('#form_proses_bro')[0]);
                        var baseurl = base_url + active_controller + '/real_send_upload_dokumen';
                        $.ajax({
                            url: baseurl,
                            type: "POST",
                            data: formData,
                            cache: false,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                if (data.status == 1) {
                                    swal({
                                        title: "Save Success!",
                                        text: data.pesan,
                                        type: "success",
                                        timer: 7000
                                    });
                                    // window.location.href = base_url + active_controller+"/quality/"+data.tanda;
                                    $("#head_title2").html("<b>Laporan Aktual Produksi</b>");
                                    $("#view2").load(base_url + active_controller + '/modalEditReal/' + data.kode_spk + '/' + data.id_produksi + '/' + data.id_milik + '/' + data.id_pro_detail);
                                    $("#ModalView2").modal();
                                } else if (data.status == 2) {
                                    swal({
                                        title: "Save Failed!",
                                        text: data.pesan,
                                        type: "warning",
                                        timer: 7000
                                    });
                                }
                                $('#updateCheck').prop('disabled', false);
                            },
                            error: function() {

                                swal({
                                    title: "Error Message !",
                                    text: 'An Error Occured During Process. Please try again..',
                                    type: "warning",
                                    timer: 7000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                });
                                $('#updateCheck').prop('disabled', false);
                            }
                        });
                    } else {
                        swal("Cancelled", "Data can be process again :)", "error");
                        $('#updateCheck').prop('disabled', false);
                        return false;
                    }
                });
        });

        $(document).on('click', '#sendCheckRelease', function(e) {
            e.preventDefault();
            // alert('Development');
            // return false;
            if ($('.chk_personal:checked').length == 0) {
                swal({
                    title: "Error Message!",
                    text: 'Checklist milimal satu terlebih dahulu',
                    type: "warning"
                });
                $('#sendCheckRelease').prop('disabled', false);
                return false;
            }

            let error = false
            let daycode
            let qc_pass
            let nomor
            $('.chk_personal:checked').each(function() {
                nomor = $(this).data('nomor')
                daycode = $('#daycode_' + nomor).val()
                qc_pass = $('#qc_pass_date_' + nomor).val()
                if (daycode == '' || qc_pass == '') {
                    error = true
                    return false;
                }
            })

            if (error === true) {
                swal({
                    title: "Error Message!",
                    text: 'Daycode/QC PassDate Wajib Di Isi !!!',
                    type: "warning"
                });
                $('#sendCheckRelease').prop('disabled', false);
                return false;
            }

            swal({
                    title: "Are you sure?",
                    text: "Release ke Finish Good !!!",
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
                        var formData = new FormData($('#form_proses_bro')[0]);
                        var baseurl = base_url + active_controller + '/real_send_release_fg';
                        $.ajax({
                            url: baseurl,
                            type: "POST",
                            data: formData,
                            cache: false,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                if (data.status == 1) {
                                    swal({
                                        title: "Save Success!",
                                        text: data.pesan,
                                        type: "success",
                                        timer: 3000
                                    });
                                    $("#head_title2").html("<b>Laporan Aktual Produksi</b>");
                                    $("#view2").load(base_url + active_controller + '/modalEditReal/' + data.kode_spk + '/' + data.id_produksi + '/' + data.id_milik + '/' + data.id_pro_detail);
                                    $("#ModalView2").modal();
                                } else if (data.status == 2) {
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

        $(document).on('click', '#uploadTemp', function(e) {
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
                        var formData = new FormData($('#form_proses_bro')[0]);
                        var baseurl = base_url + active_controller + '/real_before_send';
                        $.ajax({
                            url: baseurl,
                            type: "POST",
                            data: formData,
                            cache: false,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            success: function(data) {
                                if (data.status == 1) {
                                    swal({
                                        title: "Save Success!",
                                        text: data.pesan,
                                        type: "success",
                                        timer: 7000
                                    });
                                    $("#head_title2").html("<b>Laporan Aktual Produksi</b>");
                                    $("#view2").load(base_url + active_controller + '/modalEditReal/' + data.kode_spk + '/' + data.id_produksi + '/' + data.id_milik);
                                    $("#ModalView2").modal();
                                } else if (data.status == 2) {
                                    swal({
                                        title: "Save Failed!",
                                        text: data.pesan,
                                        type: "warning",
                                        timer: 7000
                                    });
                                }
                                $('#uploadTemp').prop('disabled', false);
                            },
                            error: function() {

                                swal({
                                    title: "Error Message !",
                                    text: 'An Error Occured During Process. Please try again..',
                                    type: "warning",
                                    timer: 7000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                });
                                $('#uploadTemp').prop('disabled', false);
                            }
                        });
                    } else {
                        swal("Cancelled", "Data can be process again :)", "error");
                        $('#uploadTemp').prop('disabled', false);
                        return false;
                    }
                });
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
                url: base_url + active_controller + '/server_side_qc',
                type: "post",
                data: function(d) {
                    d.status = status
                },
                cache: false,
                error: function() {
                    $(".my-grid2-error").html("");
                    $("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#my-grid2_processing").css("display", "none");
                }
            }
        });
    }
</script>