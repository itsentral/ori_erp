<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-right">
                <?php if($akses_menu['create']=='1'){ ?>
                <a href="<?php echo site_url('serah_terima_asset/add') ?>" class="btn btn-md btn-success" id='btn-add'>
                    <i class="fa fa-plus"></i> Add Form
                </a>
                <?php } ?>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="tbl_serah_terima" class="table table-bordered table-striped" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center" width='5%'>No</th>
                            <th class="text-center" width='10%'>Form No.</th>
                            <th class="text-center">Receiver</th>
                            <th class="text-center">Location</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Assets</th>
                            <th class="text-center" width='10%'>Created Date</th>
                            <th class="text-center no-sort" width='15%'>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<?php $this->load->view('include/footer'); ?>

<script>
$(function(){
    swal.close();

    $('#tbl_serah_terima').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "ajax": {
            url: base_url + 'serah_terima_asset/data_side',
            type: "POST"
        },
        "columns": [
            {"orderable": false},
            null,
            null,
            null,
            null,
            {"orderable": false},
            null,
            {"orderable": false}
        ],
        "order": [[1, 'desc']],
        "fixedHeader": {
            "header": true
        }
    });

    // Delete
    $(document).on('click', '.btn-delete', function(){
        var id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "Data serah terima asset akan dihapus!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if(isConfirm){
                $.ajax({
                    url: base_url + 'serah_terima_asset/delete/' + id,
                    type: "POST",
                    dataType: "json",
                    success: function(data){
                        if(data.status == 1){
                            swal("Deleted!", data.pesan, "success");
                            $('#tbl_serah_terima').DataTable().ajax.reload();
                        } else {
                            swal("Failed!", data.pesan, "error");
                        }
                    },
                    error: function(){
                        swal("Error!", "Connection error. Please try again.", "error");
                    }
                });
            } else {
                swal("Cancelled", "Data is safe :)", "error");
            }
        });
    });
});
</script>
