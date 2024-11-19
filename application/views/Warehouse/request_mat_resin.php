<?php
$this->load->view('include/side_menu');
// print_r($get_liner_utama);
$gudang_sel = get_name('production_spk','gudang2','kode_spk',$kode_spk);
$FLAG = get_name('production_spk','spk2','kode_spk',$kode_spk);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"><?=$hist_produksi;?>/<?=$kode_trans;?>/<?=$kode_spk;?></div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <p>Produk yang diproduksi: </p>
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
                $xyz_no_spk = [];
                $xyz_no_so = [];
                $xyz_product = [];
                    if(!empty($get_detail_spk2)){
                        foreach($get_detail_spk2 AS $key => $value){
                            $key++;
                            $EXPLODE = explode('-',$value['product_code']);
                            $SPEC = spec_bq2($value['id_milik']);
                            if($value['id_product'] == 'deadstok'){
                                $SPEC = "";
                            }

                            $xyz_no_spk[] = $value['no_spk'];
                            $xyz_no_so[] = $EXPLODE[0];
                            $xyz_product[] = $value['product'];
                            echo "<tr>";
                                echo "<td align='center'>".$key."</td>";
                                echo "<td align='center'>".strtoupper($EXPLODE[0])."</td>";
                                echo "<td>".strtoupper($value['product'])."</td>";
                                echo "<td align='center'>".$value['no_spk']."</td>";
                                echo "<td>".$SPEC."</td>";
                                echo "<td class='text-center text-blue text-bold'>".number_format($value['qty'])."</td>";
                                echo "<td align='center'>
                                        <input type='hidden' name='detail_input[$key][id]' class='form-control text-center' value='".$value['id']."'>
                                        <input type='hidden' name='detail_input[$key][id_milik]' class='form-control text-center' value='".$value['id_milik']."'>
                                        <input type='hidden' name='detail_input[$key][qty_all]' class='form-control text-center' value='".$value['qty']."'>
                                        <input type='text' name='detail_input[$key][qty]' class='form-control text-center autoNumeric0 qty_spk' value='".$value['qty_parsial']."' readonly>
                                        </td>";
                            echo "</tr>";
                        }
                    }
                    else{
                        echo "<tr>";
                            echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
                        echo "</tr>";
                    }
                    $xyz_no_spk     = implode(', ', $xyz_no_spk);
                    $xyz_no_so      = implode(', ', $xyz_no_so);
                    $xyz_product    = implode(', ', $xyz_product);
                ?>
            </tbody>
        </table>
        <input type="hidden" name='xyz_no_spk' id='xyz_no_spk' value='<?=$xyz_no_spk;?>'>
        <input type="hidden" name='xyz_no_so' id='xyz_no_so' value='<?=$xyz_no_so;?>'>
        <input type="hidden" name='xyz_product' id='xyz_product' value='<?=$xyz_product;?>'>
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
                    $sel = ($gudang_from == $value['id'])?'selected':'';
                    echo "<option value='".$value['id']."' ".$sel.">".$value['nm_gudang']."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-2'>
                <label class='label-control'>Upload Enginnering Change</label>
            </div>
            <div class='col-sm-3'>
                <input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload Enginnering Change'>
                <?php
                if(!empty($file_eng_change)){
                    echo "<a href='".base_url('assets/file/produksi/').$file_eng_change."' target='_blank'>Download</a>";
                }
                else{
                    echo "";
                }
                ?>
            </div>
            <div class='col-sm-2 text-right'>
                
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
                    $sel = ($gudang_to == $value['id'])?'selected':'';
                    echo "<option value='".$value['id']."' ".$sel.">".$value['nm_gudang']."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-7 text-right'>
                <button type='button' id='back' class='btn btn-md btn-danger' style='float:right; margin-left:10px;'>Back</button>
                <button type='button' id='closing_parsial' class='btn btn-md btn-warning' style='float:right; margin-left:10px;'>Close</button>
                <button type='button' id='update_real_spk2' class='btn btn-md btn-primary' style='float:right;'>Process</button>
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Request Date</label>
            </div>
            <div class='col-sm-3 '>
                <select name="no_request" id="no_request" class='form-control chosen_select'>
                    <?php
                foreach ($no_request as $key => $value) {
                    echo "<option value='".$value['kode_uniq']."'>".date('d-M-Y',strtotime($value['tgl_planning']))." | [".strtoupper(get_name('warehouse','nm_gudang','id',$value['id_gudang']))."]</option>";
                }
                ?>
                <option value="0">TANPA REQUEST</option>
                </select>
            </div>
            <div class='col-sm-7 text-right'>
                
            </div>
        </div>
        <input type="hidden" name='kode_spk' value='<?=$kode_spk;?>'>
        <input type="hidden" name='hist_produksi' value='<?=$hist_produksi;?>'>
        <input type="hidden" name='kode_trans' value='<?=$kode_trans;?>'>
        <input type="hidden" name='id' value='<?=$this->uri->segment(3);?>'>
		<input type="hidden" name='tanda_mixing' value='2'>
		<input type="hidden" id='nomoradd' value='999'>
        <div id='input-material'><div>
	</div>
	<!-- /.box-body -->
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
        $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
        $(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false});

        $(document).on('click','.addMatLainnya',function(){
            loading_spinner();
            let id_category = $(this).data('id_category');
            let id_material = $(this).data('id_material');
            let layer       = $(this).data('layer');
            let nomoradd    = $('#nomoradd').val()

            let append_html = $(this).parent().parent().parent()
            let nomor_a     = $('#nomoradd')
            // console.log(append_html)

            var baseurl = base_url +'produksi/add_material_request';
            $.ajax({
                url			: baseurl,
                type		: "POST",
                data		: {
                    'layer' : layer,
                    'id_category' : id_category,
                    'nomoradd' : nomoradd,
                    'id_material' : id_material
                },
                cache		: false,
                dataType	: 'json',
                success		: function(data){
                    if(data.status == 1){
                        append_html.append(data.data_html);
                        nomor_a.val(data.nomoradd)
                        $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
                        $('.chosen_select').chosen();
                        $('.clPersen1').hide();
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

        $(document).on('click','.addMatLainnyaNew',function(){
            loading_spinner();
            let layer       = $(this).data('layer');
            let nomoradd    = $('#nomoradd').val()

            let append_html = $(this).parent().parent().parent()
            let nomor_a     = $('#nomoradd')
            // console.log(append_html)

            var baseurl = base_url +'produksi/add_material_request_add';
            $.ajax({
                url			: baseurl,
                type		: "POST",
                data		: {
                    'layer' : layer,
                    'nomoradd' : nomoradd
                },
                cache		: false,
                dataType	: 'json',
                success		: function(data){
                    if(data.status == 1){
                        append_html.append(data.data_html);
                        nomor_a.val(data.nomoradd)
                        $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
                        $('.chosen_select').chosen();
                        $('.clPersen1').hide();
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

        $(document).on('change','.change_category',function(){
            loading_spinner();
            let id_category    = $(this).val()

            let append_html = $(this).parent().parent().find('.list_material')
            let nomor_a     = $('#nomoradd')
            // console.log(append_html)

            if(id_category != '0'){
                var baseurl = base_url +'produksi/get_material';
                $.ajax({
                    url			: baseurl,
                    type		: "POST",
                    data		: {
                        'id_category' : id_category
                    },
                    cache		: false,
                    dataType	: 'json',
                    success		: function(data){
                        if(data.status == 1){
                            $(append_html).html(data.option).trigger("chosen:updated");
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
            }
            else{
                $(append_html).html('').trigger("chosen:updated");
            }
        });

        $(document).on('change', '.id_material', function(e){
            let materialData = $(this).data('mat');
            let materialVal = $(this).val()
            let id_gudang = $('#id_gudang_from').val()
            let PARENT = $(this).parent().parent().find('.stockMaterial')
            // console.log(PARENT.html())
            // $.when(
                $(this).removeAttr('data-mat')
                $(this).addClass('mat_'+materialVal).removeClass('mat_'+materialData)
                $(this).attr('data-mat', materialVal)
            // ).done(
            // )
            
            //change stock
            var baseurl=base_url +'produksi/get_stock_material';
            $.ajax({
                url			: baseurl,
                type		: "POST",
                data		: {'id_material':materialVal,'id_gudang':id_gudang},
                cache		: false,
                dataType	: 'json',
                success		: function(data){
                    if(data.status == 1){
                        PARENT.html(data.stock)
                    }
                    else{
                        console.log('Error !')
                    }
                },
                error: function() {
                    swal({
                        title	: "Error Message !",
                        text	: 'An Error Occured During Process. Please try again..',
                        type	: "warning",
                        timer	: 3000
                    });
                }
            });
            
        });

        $(document).on('keyup','.changePersen',function(){
            let nomor = $(this).data('nomor')
            let persen = getNum($(this).val().split(",").join(""))
            let valResin = getNum($(this).parent().parent().parent().find('.weightResin1').val().split(",").join(""))
            let hasil = persen / 100 * valResin
            $(this).parent().parent().parent().find('.weightResin'+nomor).val(number_format(hasil,4))
        });

        $(document).on('keyup','.changeMixing',function(){
            let nomor = $(this).data('nomor')
            let aktualMix = getNum($(this).val().split(",").join(""))
            let valResin = getNum($(this).parent().parent().parent().find('.weightResin1').val().split(",").join(""))
            let hasil = (aktualMix / valResin) * 100
            // console.log(hasil);
            $(this).parent().parent().parent().find('.clPersen'+nomor).val(number_format(hasil,2))
        });

        $(document).on('keyup','.weightResin1',function(){
            let nomor
            let valResin = getNum($(this).val().split(",").join(""))
            let persen
            let hasil
            $(this).parent().parent().parent().find('.changePersen').each(function(){
                nomor = $(this).data('nomor')
                if(nomor != '1'){
                    persen = getNum($(this).val().split(",").join(""))
                    if(persen != ''){
                        hasil = persen / 100 * valResin
                        $(this).parent().parent().parent().find('.weightResin'+nomor).val(number_format(hasil,4))
                    }
                }
            });
        });

        loading_spinner();
		var formData 	=new FormData($('#form_proses_bro')[0]);
		var baseurl=base_url +'produksi/show_material_input2_request';
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
				   $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
				   $('.chosen_select').chosen();
                   $('.clPersen1').hide();
                   $('.budget_over').hide()
                   $('.budget_stock').hide()
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
					title		: "Error Message !",
					text		: 'An Error Occured During Process. Please try again..',
					type		: "warning",
					timer		: 3000
				});
			}
		});

        $(document).on('change','#id_gudang_from',function(){
            loading_spinner();
            var formData 	=new FormData($('#form_proses_bro')[0]);
            var baseurl=base_url +'produksi/show_material_input2_request';
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
                    $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
                    $('.chosen_select').chosen();
                    $('.clPersen1').hide();
                    $('.budget_over').hide()
                    $('.budget_stock').hide()
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
                        title		: "Error Message !",
                        text		: 'An Error Occured During Process. Please try again..',
                        type		: "warning",
                        timer		: 3000
                    });
                }
            });
        });
    });

    $(document).on('click', '#back', function(e){
        window.location.href = base_url + active_controller + '/request_produksi/subgudang';
    });

    $(document).on('click', '#update_real_spk2', function(e){
        e.preventDefault();
        $(this).prop('disabled',true);

        let id_gudang = $('#id_gudang').val();
        let id_gudang_from = $('#id_gudang_from').val();
        if(id_gudang == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'Gudang Tujuan belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }

        if(id_gudang_from == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'Gudang Asal belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
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
                    var formData 	=new FormData($('#form_proses_bro')[0]);
                    var baseurl=base_url + active_controller +'/save_update_produksi_2_new';
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
                                window.location.href = base_url + active_controller+"/request_produksi/subgudang";
                            }
                            else if(data.status == 2){
                                swal({
                                    title	: "Save Failed!",
                                    text	: data.pesan,
                                    type	: "warning",
                                    timer	: 7000
                                });
                            }
                            $('#update_real_spk2').prop('disabled',false);
                        },
                        error: function() {

                            swal({
                                title	: "Error Message !",
                                text	: 'An Error Occured During Process. Please try again..',
                                type	: "warning",
                                timer	: 3000
                            });
                            $('#update_real_spk2').prop('disabled',false);
                        }
                    });
                } else {
                swal("Cancelled", "Data can be process again :)", "error");
                $('#update_real_spk2').prop('disabled',false);
                return false;
                }
        });
    });

    $(document).on('click', '#closing_parsial', function(e){
        e.preventDefault();
        $(this).prop('disabled',true);

        let id_gudang = $('#id_gudang').val();
        let id_gudang_from = $('#id_gudang_from').val();
        if(id_gudang == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'Gudang Tujuan belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }

        if(id_gudang_from == '0'){
            swal({
                title	: "Notification Message !",
                text	: 'Gudang Asal belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }
        

        // return false;
        // $('#closing_parsial').prop('disabled',false);

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
                    var baseurl=base_url + active_controller +'/save_update_produksi_2_new_close';
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
                                window.location.href = base_url + active_controller+"/request_produksi/subgudang";
                            }
                            else if(data.status == 2){
                                swal({
                                    title	: "Save Failed!",
                                    text	: data.pesan,
                                    type	: "warning",
                                    timer	: 3000
                                });
                            }
                            $('#closing_parsial').prop('disabled',false);
                        },
                        error: function() {

                            swal({
                                title	: "Error Message !",
                                text	: 'An Error Occured During Process. Please try again..',
                                type	: "warning",
                                timer	: 3000
                            });
                            $('#closing_parsial').prop('disabled',false);
                        }
                    });
                } else {
                swal("Cancelled", "Data can be process again :)", "error");
                $('#closing_parsial').prop('disabled',false);
                return false;
                }
        });
    });

    $(document).on('keyup', '.changeOB', function(e){
        let parentHTML = $(this).parent().parent()
        // let budget = parentHTML.find('.budget_over_value').html()
        // let splitHTML = budget.split(" / ")
        let beratInput = getNum(parentHTML.find('.budget_over_value_input').html().split(",").join(""))
        let beratEst    = getNum(parentHTML.find('.budget_over_value_est').html().split(",").join(""))
        let beratVal = getNum($(this).val().split(",").join(""))
        let beratTot = beratVal+beratInput

        // console.log(beratInput)
        // console.log(beratEst)
        // console.log(beratVal)
        // console.log(beratTot)
        
        if(beratTot > beratEst){
            parentHTML.find('.budget_over').show()
        }
        else{
            parentHTML.find('.budget_over').hide()
        }


       //BLOCK STOCK
        let beratStock = getNum(parentHTML.find('.stockMaterial').html().split(",").join(""))
        let idMaterial = parentHTML.find('.id_material').val()
        let stockSm
        let thisValue
        let statusBreak = false
        parentHTML.parent().parent().parent().find('.mat_'+idMaterial).each(function(){

            thisValue = $(this).parent().parent().find('.changeOB')
            stockSm = getNum(thisValue.val().split(",").join(""))

            if(beratStock > stockSm){
                beratStock = beratStock - stockSm
                parentHTML.find('.budget_stock').show()
            }
            else{
                statusBreak = true
                parentHTML.find('.budget_stock').hide()
                return false
            }
            beratStock = beratStock
        });

        if(statusBreak === true){
            parentHTML.find('.budget_stock').show() 
        }
        else{
            parentHTML.find('.budget_stock').hide()
        }
    });

    $(document).on('change', '.changeOB, .changePersen', function(e){
        let parentHTML = $(this).parent().parent()

        //BLOCK STOCK
        let beratStock = getNum(parentHTML.find('.stockMaterial').html().split(",").join(""))
        let idMaterial = parentHTML.find('.id_material').val()
        let stockSm
        let NewStock
        let thisValue
        parentHTML.parent().parent().parent().find('.mat_'+idMaterial).each(function(){

            thisValue = $(this).parent().parent().find('.changeOB')
            stockSm = getNum(thisValue.val().split(",").join(""))
            // console.log(stockSm)

            if(beratStock > stockSm){
                NewStock = beratStock - stockSm
                thisValue.val(number_format(stockSm,4))
                // console.log(beratStock)
                // console.log(NewStock)
            }
            else{
                NewStock = beratStock - beratStock
                thisValue.val(number_format(beratStock,4))
                if(beratStock < 0){
                    thisValue.val(0)
                }
                // console.log(beratStock)
                // console.log(NewStock)
            }

            beratStock = NewStock
            // console.log(`----------`)
        });

        // console.log(beratStock)
        // console.log(idMaterial)
    });

    $(document).on('change', '.id_material', function(e){
        let materialData = $(this).data('mat');
        let materialVal = $(this).val()

        // console.log(materialData)
        // console.log(materialVal)

        // $.when(
            $(this).removeAttr('data-mat')
            $(this).addClass('mat_'+materialVal).removeClass('mat_'+materialData)
            $(this).attr('data-mat', materialVal)
        // ).done(
        // )

        
    });

</script>