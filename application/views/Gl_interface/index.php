<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-exchange"></i> Daftar Transaksi GL Interface</h3>
    </div>
    <div class="box-body">

                <!-- Filter -->
                <div class="row" style="margin-bottom:10px;">
                    <div class="col-sm-2">
                        <select id="filter_status" class="form-control input-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="pending">PENDING</option>
                            <option value="posted">POSTED</option>
                            <option value="error">ERROR</option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <select id="filter_jenis" class="form-control input-sm">
                            <option value="">-- Semua Jenis --</option>
                            <?php foreach($jenis_list as $j): ?>
                            <option value="<?= $j['jenis_transaksi'] ?>"><?= strtoupper($j['jenis_transaksi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input type="date" id="filter_tgl_from" class="form-control input-sm" placeholder="Dari Tanggal">
                    </div>
                    <div class="col-sm-2">
                        <input type="date" id="filter_tgl_to" class="form-control input-sm" placeholder="Sampai Tanggal">
                    </div>
                    <div class="col-sm-2">
                        <button id="btn_filter" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filter</button>
                        <button id="btn_reset" class="btn btn-sm btn-default"><i class="fa fa-times"></i> Reset</button>
                    </div>
                    <div class="col-sm-2 text-right">
                        <button id="btn_retry_bulk" class="btn btn-sm btn-warning">
                            <i class="fa fa-refresh"></i> Retry Semua Error
                        </button>
                    </div>
                </div>

                <!-- Summary badges -->
                <div class="row" style="margin-bottom:10px;" id="summary_row"></div>

                <!-- Table -->
                <table id="tbl_gl" class="table table-striped table-bordered table-hover table-condensed" width="100%">
                    <thead>
                        <tr class="bg-blue">
                            <th class="text-center" width="3%">#</th>
                            <th class="text-center" width="8%">Tanggal</th>
                            <th class="text-center" width="12%">Nomor JV</th>
                            <th class="text-center" width="10%">Jenis</th>
                            <th class="text-center">Keterangan</th>
                            <th class="text-center" width="10%">Jumlah</th>
                            <th class="text-center" width="5%">Detail</th>
                            <th class="text-center" width="8%">Status</th>
                            <th class="text-center" width="10%">Posted At</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

<!-- Modal Detail -->
<div class="modal fade" id="ModalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-blue">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="modal_title_detail">Detail Jurnal</h4>
            </div>
            <div class="modal-body" id="modal_body_detail">
                <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var base_url    = '<?= site_url() ?>';
var active_ctrl = 'gl_interface';

var table = $('#tbl_gl').DataTable({
    processing  : true,
    serverSide  : true,
    ajax        : {
        url  : base_url + active_ctrl + '/server_side',
        type : 'POST',
        data : function(d){
            d.status   = $('#filter_status').val();
            d.jenis    = $('#filter_jenis').val();
            d.tgl_from = $('#filter_tgl_from').val();
            d.tgl_to   = $('#filter_tgl_to').val();
        }
    },
    columns     : [
        {orderable:false},{orderable:true},{orderable:true},
        {orderable:true},{orderable:false},{orderable:false},
        {orderable:false},{orderable:true},{orderable:true},{orderable:false}
    ],
    order       : [[0,'desc']],
    pageLength  : 25,
    language    : { processing: "<i class='fa fa-spinner fa-spin'></i> Loading..." }
});

// Filter
$('#btn_filter').on('click', function(){ table.ajax.reload(); });
$('#btn_reset').on('click', function(){
    $('#filter_status, #filter_jenis').val('');
    $('#filter_tgl_from, #filter_tgl_to').val('');
    table.ajax.reload();
});

// Detail
$(document).on('click', '.btn-detail', function(){
    var id = $(this).data('id');
    $('#modal_title_detail').html('<b>Detail Jurnal GL Interface #' + id + '</b>');
    $('#modal_body_detail').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    $('#ModalDetail').modal('show');
    $.ajax({
        url     : base_url + active_ctrl + '/modal_detail/' + id,
        type    : 'POST',
        success : function(data){ $('#modal_body_detail').html(data); }
    });
});

// Retry single
$(document).on('click', '.btn-retry', function(){
    var id  = $(this).data('id');
    var btn = $(this);
    if(!confirm('Retry posting transaksi #' + id + '?')) return;
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
    $.ajax({
        url     : base_url + active_ctrl + '/retry/' + id,
        type    : 'POST',
        success : function(res){
            var r = JSON.parse(res);
            if(r.status == 1){
                swal('Berhasil', r.pesan, 'success');
            } else {
                swal('Gagal', r.pesan, 'error');
            }
            table.ajax.reload(null, false);
        },
        error   : function(){ swal('Error', 'Terjadi kesalahan koneksi.', 'error'); },
        complete: function(){ btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Retry'); }
    });
});

// Retry bulk
$('#btn_retry_bulk').on('click', function(){
    var jenis = $('#filter_jenis').val();
    if(!confirm('Retry semua transaksi ERROR' + (jenis ? ' jenis: '+jenis : '') + '?')) return;
    $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    var self = this;
    $.ajax({
        url     : base_url + active_ctrl + '/retry_bulk',
        type    : 'POST',
        data    : { jenis: jenis },
        success : function(res){
            var r = JSON.parse(res);
            swal('Selesai', r.pesan, 'info');
            table.ajax.reload(null, false);
        },
        complete: function(){ $(self).prop('disabled', false).html('<i class="fa fa-refresh"></i> Retry Semua Error'); }
    });
});
</script>
