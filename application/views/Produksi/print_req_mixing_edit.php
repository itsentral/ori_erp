<?php
$this->load->view('include/side_menu');
$NO_IPP = $get_detail_spk2[0]['no_ipp'];

$CUSTOMER = strtoupper(get_name('production','nm_customer','no_ipp',$NO_IPP));
$PROJECT = strtoupper(get_name('production','project','no_ipp',$NO_IPP));
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<input type="hidden" name='id' id='id' value='<?=$id;?>'>
<input type="hidden" name='kode_trans' id='kode_trans' value='<?=$kode_trans;?>'>
<input type="hidden" name='hist_produksi' id='hist_produksi' value='<?=$hist_produksi;?>'>
<input type="hidden" name='print_ke' id='print_ke' value='<?=$print_ke;?>'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"></div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class='table' width='100%'>
            <tbody>
                <?php
                echo "<tr>";
                    echo "<td width='20%'>No Transaksi</td>";
                    echo "<td width='2%'>:</td>";
                    echo "<td width='28%'>".$kode_trans."</td>";
                    echo "<td width='20%'</td>";
                    echo "<td width='2%'></td>";
                    echo "<td width='28%'><button type='button' id='print_berat_custom' class='btn btn-md btn-success' style='float:right; margin-left: 5px;'>Print SPK</button></td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Tgl Planning</td>";
                    echo "<td>:</td>";
                    echo "<td>".date('d F Y', strtotime($get_detail_spk2[0]['tanggal_produksi']))."</td>";
                    echo "<td><b>Tgl Request Berikutnya</b></td>";
                    echo "<td>:</td>";
                    echo "<td><input type='text' id='tgl_plan' name='tgl_plan' class='form-control' data-role='datepicker_lost'  readonly></td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Customer</td>";
                    echo "<td>:</td>";
                    echo "<td>".$CUSTOMER."</td>";
                    echo "<td>Costcenter</td>";
                    echo "<td>:</td>";
                    echo "<td>";
                        echo "<select name='costcenter' class='form-control'>";
                            foreach ($costcenter as $key => $value) {
                                $selected = ($gudang_to == $value['id'])?'selected':'';
                                echo "<option value='".$value['id']."' $selected>".strtoupper($value['nm_gudang'])."</option>";
                            }
                        echo "</select>";
                    echo "</td>";
                echo "</tr>";
                echo "<tr>";
                    echo "<td>Project</td>";
                    echo "<td>:</td>";
                    echo "<td colspan='4'>".$PROJECT."</td>";
                echo "</tr>";
                ?>
            </tbody>
        </table>
        <br>
        <p>Produk yang akan diproduksi:</p>
        <div id='input-product'>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead>
                    <tr class='bg-blue'>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center">No SO</th>
                        <th class="text-center">Product</th>
                        <th class="text-center">No SPK</th>
                        <th class="text-center" width='20%'>Spec</th>
                        <th class="text-center" width='10%'>Qty</th>
                        <th class="text-center" width='10%'>Qty Produksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if(!empty($get_detail_spk2)){
                        foreach($get_detail_spk2 AS $key => $value){
                            $key++;
                            $EXPLODE = explode('-',$value['product_code']);
                            $SPEC = spec_bq2($value['id_milik']);
                            $KET = '';

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
                            }
                            echo "<tr>";
                                echo "<td align='center'>".$key."</td>";
                                echo "<td align='center'>".strtoupper($EXPLODE[0])."</td>";
                                echo "<td>".strtoupper($KET.$value['product'])."</td>";
                                echo "<td align='center'>".$value['no_spk']."</td>";
                                echo "<td>".$SPEC."</td>";
                                echo "<td align='center'>".number_format($value['qty'])."</td>";
                                echo "<td align='center'>".number_format($value['qty_parsial'])."</td>";
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
        </div>
        <p>Kebutuhan material:</p>
        <?php 
        $nomor = 0;
        if(!empty($get_liner_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>LINER THIKNESS / CB</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        
                        foreach ($get_liner_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold kebutuhan'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='LINER THIKNESS / CB'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <?php if(!empty($get_str_n1_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>STRUCTURE NECK 1</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($get_str_n1_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='STRUCTURE NECK 1'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <?php if(!empty($get_str_n2_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>STRUCTURE NECK 2</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($get_str_n2_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='STRUCTURE NECK 2'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <?php if(!empty($get_structure_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>STRUKTUR THICKNESS</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($get_structure_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='STRUKTUR THICKNESS'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <?php if(!empty($get_external_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>EXTERNAL THICKNESS</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($get_external_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='EXTERNAL THICKNESS'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        <?php if(!empty($get_topcoat_utama)){ ?>
            <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
                <thead class='bg-blue'>
                    <tr><th colspan='6' align='left'>TOPCOAT</th></tr>
                    <tr>
                        <th class="text-center" width='5%'>#</th>
                        <th class="text-center" width='17%'>Kategori</th>
                        <th class="text-center">Material</th>
                        <th class="text-center" width='12%'>Kebutuhan (kg)</th>
                        <th class="text-center" width='12%'>Request (kg)</th>
                        <th class="text-center" width='15%'>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                            $TotReq = (!empty($ArrSearch[$nomor]))?$ArrSearch[$nomor]:0;
                            $readonly = ($TotReq >= $value['berat'])?'readonly':'';
                            echo "<tr>";
                                echo "<td align='center'>".$nomor."</td>";
                                echo "<td>".$value['nm_category']."</td>";
                                echo "<td>".$value['nm_material']."</td>";
                                echo "<td class='text-right text-bold'><span class='text-blue'>".floatval($TotReq)."</span> / <span class='text-green'>".number_format($value['berat'],4)."</span></td>";
                                echo "<td>
                                        <input type='hidden' name='edit_request[".$nomor."][category]' value='".$value['nm_category']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][id_material]' value='".$value['id_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][material]' value='".$value['nm_material']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][total_req]' value='".$TotReq."'>
                                        <input type='hidden' name='edit_request[".$nomor."][estimasi]' value='".$value['berat']."'>
                                        <input type='hidden' name='edit_request[".$nomor."][layer]' value='TOPCOAT'>
                                        <input type='text' name='edit_request[".$nomor."][request]' class='form-control input-sm text-center autoNumeric3 blockRequest' autocomplete='off' $readonly>
                                        <span class='text-red text-bold labelMaxRequest'></span>
                                        </td>";
                                echo "<td><input type='text' name='edit_request[".$nomor."][keterangan]' class='form-control input-sm text-left' autocomplete='off' $readonly></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
            <br>
        <?php } ?>
        
	</div>
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style type="text/css">
    td{
        vertical-align  :middle !important;
    }
    #tgl_plan{
        cursor: pointer;
    }
    .labelMaxRequest{
        font-size: smaller
    }
</style>
<script>
    $(document).ready(function(){
        $(".labelMaxRequest").hide()
        $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
        $(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false});
    });

    $(document).on('click', '#print_berat_custom', function(e){
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
                    var baseurl=base_url + active_controller +'/print_req_mixing_save';
                    var formData 	=new FormData($('#form_proses_bro')[0]);
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
                                window.location.href = base_url + active_controller+"/print_req_mixing_new/"+data.kode_uniq;
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


    $(document).on('keyup', '.blockRequest', function(e){
        let berat = getNum($(this).val().split(',').join(''))
        let kebutuhan = $(this).parent().parent().find('.kebutuhan').text()
        let SPLIT = kebutuhan.split(' / ')
        let sudahRequest = getNum(SPLIT[0].split(',').join(''))
        let estimasi = getNum(SPLIT[1].split(',').join(''))
        let maxRequest = estimasi - sudahRequest

        if(maxRequest < berat){
            $(this).val(number_format(maxRequest,4))
            $(this).parent().parent().find('.labelMaxRequest').text(`Maksimal request: ${number_format(maxRequest,4)}`)
            $(this).parent().parent().find('.labelMaxRequest').show()
        }
    });
</script>