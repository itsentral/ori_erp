<?php
$this->load->view('include/side_menu');
// print_r($get_liner_utama);
$gudang_sel = get_name('production_spk','gudang1','kode_spk',$kode_spk);
$FLAG = get_name('production_spk','spk1','kode_spk',$kode_spk);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"></div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <input type="hidden" name="hist_produksi" id="hist_produksi" value='0'>
        <p>Produk yang diproduksi: </p>
        <div id='input-product'>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead>
                    <tr class='bg-blue'>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='10%'>No SO</th>
                        <th class="text-center" width='10%'>No SPK</th>
                        <th class="text-center">Product</th>
                        <th class="text-center" width='15%'>Spec</th>
                        <th class="text-center" width='10%'>Qty SPK</th>
                        <th class="text-center" width='10%'>Sudah Input</th>
                        <th class="text-center" width='10%'>Qty Balance</th>
                        <th class="text-center" width='10%'>Qty Produksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if(!empty($get_detail_spk)){
                            foreach($get_detail_spk AS $key => $value){
                                $key++;
                                $EXPLODE = explode('-',$value['product_code']);
                                $NO_SPK = $value['no_spk'];;
                                $SPEC = spec_bq2($value['id_milik']);
                                $KET = '';

                                $readonly = '';
                                $qty_auto = '';
                                $labelAlert = '';
                                if($value['id_product'] == 'tanki'){
                                    $NO_SPK = $value['no_spk'];
                                    $SPEC = (!empty($GET_SPEC_TANK[$value['id_milik']]))?$GET_SPEC_TANK[$value['id_milik']]:'';
                                    $KET = 'TANKI - ';
                                }
                                if($value['id_product'] == 'deadstok'){
                                    $tanda_deadstok = $value['product_code_cut'];
                                    $HeaderDeadstok = $this->db
                                                        ->select('a.id, b.no_so, b.no_ipp, b.no_spk, a.proses, b.product_name, b.product_spec, COUNT(a.id) AS qty, b.id_milik')
                                                        ->group_by('a.kode')
                                                        ->join('deadstok b','a.id_deadstok=b.id','left')
                                                        ->get_where('deadstok_modif a',array('kode'=>$tanda_deadstok))
                                                        ->result_array();
                                    $SPEC = (!empty($HeaderDeadstok[0]['product_spec']))?$HeaderDeadstok[0]['product_spec']:'';
                                    $readonly = 'readonly';
                                    $qty_auto = $value['qty'] - $value['qty_input'];
                                    $labelAlert = "<span class='text-danger text-bold'>Deadstok Tidak Bisa Parsial !!!</span>";
                                }
                                echo "<tr>";
                                    echo "<td align='center'>".$key."</td>";
                                    echo "<td align='center'>".$EXPLODE[0]."</td>";
                                    echo "<td align='center'>".$NO_SPK."</td>";
                                    echo "<td>".strtoupper($KET.$value['product'])."</td>";
                                    echo "<td>".$SPEC."</td>";
                                    echo "<td class='text-center text-blue text-bold'>".number_format($value['qty'])."</td>";
                                    echo "<td class='text-center text-green text-bold'>".number_format($value['qty_input'])."</td>";
                                    echo "<td class='text-center text-red text-bold sisa_spk'>".number_format($value['qty'] - $value['qty_input'])."</td>";
                                    echo "<td align='center'>
                                            <input type='hidden' name='detail_input[$key][id]' class='form-control text-center' value='".$value['id']."'>
                                            <input type='hidden' name='detail_input[$key][id_milik]' class='form-control text-center' value='".$value['id_milik']."'>
                                            <input type='hidden' name='detail_input[$key][qty_all]' class='form-control text-center' value='".$value['qty']."'>
                                            <input type='text' name='detail_input[$key][qty]' class='form-control text-center autoNumeric0 qty_spk' value='".$qty_auto."' ".$readonly.">
                                            </td>";
                                echo "</tr>";
                            }
                        }
                        else{
                            echo "<tr>";
                                echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <div class='text-right'><?=$labelAlert;?></div>
        </div>
        <br>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Request Warehouse</label>
            </div>
            <div class='col-sm-3 '>
                <select name="id_gudang_from" id="id_gudang_from" class='form-control chosen_select'>
                    <option value="0">Pilih Warehouse</option>
                <?php
                foreach ($warehouse2 as $key => $value) {
                    echo "<option value='".$value['id']."'>".$value['nm_gudang']."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-2'>
                <label class='label-control'>Upload Enginnering Change</label>
            </div>
            <div class='col-sm-3'>
                <input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload Enginnering Change'>
                <span id='download_eng_change'></span>
            </div>
            <div class='col-sm-2 text-right'>
                <button type='button' class='btn btn-md btn-success' id='show_material'>Tampilkan SPK Material</button>
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>To Warehouse Produksi</label>
            </div>
            <div class='col-sm-3 '>
                <select name="id_gudang" id="id_gudang" class='form-control chosen_select'>
                    <option value="0">Pilih Warehouse</option>
                <?php
                foreach ($warehouse as $key => $value) {
                    echo "<option value='".$value['id']."'>".$value['nm_gudang']."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-7 text-right'>
                <button type='button' id='back' class='btn btn-md btn-danger' style='float:right; margin-left:10px;'>Back</button>
                <button type='button' id='update_real_spk1' class='btn btn-md btn-primary' style='float:right;'>Request</button>
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Planning Produksi</label>
            </div>
            <div class='col-sm-3 '>
                <input type="text" name='date_produksi' id='date_produksi' class='form-control text-center' readonly data-role="datepicker_lost">
            </div>
            <div class='col-sm-7 text-right'></div>
        </div>
        <input type="hidden" name='kode_spk' value='<?=$kode_spk;?>'>
		<input type="hidden" name='tanda_mixing' value='1'>

        <div id='input-material'><div>
	</div>
	<!-- /.box-body -->
    <div class="box-footer">
        <!-- <?php if($FLAG == 'Y'){ ?>
            <button type='button' id='update_to_costing_spk1' class='btn btn-md btn-primary' style='float:right; margin-left: 5px;'>Update & Release To Costing</button>
        <?php } ?> -->
    </div>
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style type="text/css">
    td{
        vertical-align  :middle !important;
    }
    #date_produksi{
        cursor: pointer;
    }
</style>
<script>
    $(document).ready(function(){
        $(".autoNumeric3").autoNumeric('init', {mDec: '3', aPad: false});
        $(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false});
        $('#update_real_spk1').hide();
    });

    $(document).on('click', '#back', function(e){
        window.location.href = base_url + active_controller + '/index_loose/request';
    });

    $(document).on('keyup','.qty_spk', function(){
        var qty 	= getNum($(this).val().split(",").join(""));
        var qty_max = getNum($(this).parent().parent().find('.sisa_spk').html().split(",").join(""));
        if(qty > qty_max){
            $(this).val(qty_max);
        }
        $('#input-material').html('');
        $('#update_real_spk1').hide();
    });

    $(document).on('click', '#update_real_spk1', function(e){
        e.preventDefault();
        $(this).prop('disabled',true);
        let id_gudang_from = $('#id_gudang_from').val()
        let id_gudang = $('#id_gudang').val();
        let date_produksi = $('#date_produksi').val();
        if(id_gudang_from == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'SubGudang belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }

        if(id_gudang == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'Gudang Produksi belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }

        if(date_produksi == ''){
            swal({
                title	: "Notification Message !",
                text	: 'Tanggal Produksi belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }
        

        // return false;
        $('#update_real_spk1').prop('disabled',false);

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
                    var formData 	=new FormData($('#form_proses_bro')[0]);
                    var baseurl=base_url + active_controller +'/<?=$link_submit;?>';
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
                                window.location.href = base_url + active_controller+"/request_material/"+data.kode_spk;
                            }
                            else if(data.status == 2){
                                swal({
                                    title	: "Save Failed!",
                                    text	: data.pesan,
                                    type	: "warning",
                                    timer	: 3000
                                });
                            }
                            $('#update_real_spk1').prop('disabled',false);
                        },
                        error: function() {

                            swal({
                                title				: "Error Message !",
                                text				: 'An Error Occured During Process. Please try again..',
                                type				: "warning",
                                timer				: 3000
                            });
                            $('#update_real_spk1').prop('disabled',false);
                        }
                    });
                } else {
                swal("Cancelled", "Data can be process again :)", "error");
                $('#update_real_spk1').prop('disabled',false);
                return false;
                }
        });
    });

    $(document).on('click', '#show_material', function(e){
        e.preventDefault();

        loading_spinner();
        var formData 	=new FormData($('#form_proses_bro')[0]);
        var baseurl=base_url + active_controller +'/show_material_input_request';
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
                    $('#input-material').html(data.data_html);
                    if(data.data_html != ''){
                        $('#update_real_spk1').show();
                    }
                    else{
                        $('#update_real_spk1').hide();
                    }
                    $(".autoNumeric3").autoNumeric('init', {mDec: '3', aPad: false});
                    $('.chosen_select').chosen();
                    swal.close();
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
    });

    $(document).on('change', '#hist_produksi', function(e){
        e.preventDefault();

        loading_spinner();
        var formData 	=new FormData($('#form_proses_bro')[0]);
        var baseurl=base_url + active_controller +'/show_product_input';
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
                    $('#input-product').html(data.data_html);
                    $('#date_produksi').val(data.tanggal_produksi)
                    $('#id_gudang').val(data.id_gudang).trigger('chosen:updated')
                    $('#update_real_spk1').hide();
                    $('#input-material').html('');
                    $(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false})
                    $('.chosen_select').chosen()
                    swal.close();
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
    });
</script>