<?php
$this->load->view('include/side_menu_dashboard'); 
?>

<!-- Content Header -->
<section class="content-header">
    <h1>
        <?=$title;?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pembelian</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table class="table no-margin table-hover table-striped table-bordered">
                    <thead class='bg-blue'>
                        <tr>
                            <th>PROCESS</th>
                            <th width='15%' class='text-right'>MATERIALS</th>
                            <th width='15%' class='text-right'>STOK</th>
                            <th width='15%' class='text-right'>DEPARTMENT</th>
                            <th width='15%' class='text-right'>ASSETS</th>
                        </tr>
                    </thead>
                    <tbody id='body_d'></tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- modal -->
<div class="modal fade" id="ModalView">
    <div class="modal-dialog" style='width:40%; '>
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
<!-- modal -->
<style>
    .detialOutAppPR, .detialPRApp, .detialCompSup, .detailOutCompApp, .detailOutPrsPO, .detailWaitAppPO, .detailReleasePO, .detailIncomingPO{
        cursor: pointer;
    }
</style>
<?php $this->load->view('include/footer_dashboard'); ?>
<script>
	$(document).ready(function(){
		getDashboard()
	});

    $(document).on('click', '.detialOutAppPR', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detialOutAppPR/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detialPRApp', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detialPRApp/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detialCompSup', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detialCompSup/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detailOutCompApp', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detailOutCompApp/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detailOutPrsPO', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detailOutPrsPO/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detailWaitAppPO', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detailWaitAppPO/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detailReleasePO', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detailReleasePO/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    $(document).on('click', '.detailIncomingPO', function(e) {
        e.preventDefault();
        loading_spinner();
        $("#head_title").html("<b>Detail PR</b>");
        $("#view").load(base_url + active_controller + '/detailIncomingPO/' + $(this).data('type'));
        $("#ModalView").modal();
    });

    const getDashboard = () => {
        return new Promise((resolve, reject) => {
        $.ajax({
            url			: base_url + active_controller+'/getPurchase',
            type		: "POST",
            dataType	: 'json',
            beforeSend  : function(){
                loading_spinner()
            },
            success		: function(data){
                if(data.status == 1){
                    $('#body_d').html(data.dashboard);
                    swal.close()
                }
                else{
                    alert(data.pesan)
                    swal.close()
                }
            },
            error: function(error) {
                alert('Error server program !!!')
                swal.close()
            }
        });
        })
    }
</script>
