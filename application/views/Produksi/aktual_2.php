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
		<div class="box-tool pull-right"></div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Pilih Hist Produksi</label>
            </div>
            <div class='col-sm-7 '>
                <select name="hist_produksi" id="hist_produksi" class='form-control chosen_select'>
                    <option value="0">Pilih Input Produksi</option>
                <?php
                foreach ($hist_produksi as $key => $value) {
					$disabled 			= (!empty($value['closing_produksi_date']))?"disabled='disabled'":'';
					$label 				= (!empty($value['closing_produksi_date']))?" (CLOSING)":'';
                    $input_produksi 	= ($value['created_date'] == $value['updated_date'] OR $value['tanggal_produksi'] == null)?"":' (DONE)';
					
                    echo "<option value='".$value['created_date']."' ".$disabled.">TANGGAL PRODUKSI : ".date('d-M-Y', strtotime($value['tanggal_produksi']))." / INPUT DATE : ".date('d-M-Y H:i:s', strtotime($value['created_date'])).$input_produksi.$label."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-3 '>
                <button type='button' class='btn btn-md btn-success' id='show_material'>Tampilkan Input Material</button>
            </div>
        </div>
        <p>Produk yang diproduksi: </p>
        <div id='input-product'></div>
        <br>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Warehouse Produksi</label>
            </div>
            <div class='col-sm-3 '>
                <select name="id_gudang" id="id_gudang" class='form-control chosen_select'>
                    <option value="0">Pilih Warehouse Produksi</option>
                <?php
                foreach ($warehouse as $key => $value) {
                    $sel = ($gudang_sel == $value['id'])?'selected':'';
                    echo "<option value='".$value['id']."' ".$sel.">".$value['nm_gudang']."</option>";
                }
                ?>
                </select>
            </div>
            <div class='col-sm-3'>
                <label class='label-control'>Upload Enginnering Change</label>
            </div>
            <div class='col-sm-4'>
                <input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload Enginnering Change'>
                <span id='download_eng_change'></span>
            </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Tgl. Mulai Produksi</label>
            </div>
            <div class='col-sm-3 '>
                <input type="text" name='date_produksi_start' id='date_produksi_start' class='form-control text-center' readonly>
            </div>
            <div class='col-sm-7 text-right'> </div>
        </div>
        <div class="form-group row">
            <div class='col-sm-2 '>
                <label class='label-control'>Tgl. Selesai Produksi</label>
            </div>
            <div class='col-sm-3 '>
                <input type="text" name='date_produksi' id='date_produksi' class='form-control text-center' readonly data-role="datepicker">
            </div>
            <div class='col-sm-7 text-right'>
                <button type='button' id='back' class='btn btn-md btn-danger' style='float:right; margin-left:10px;'>Back</button>
                <button type='button' id='update_real_spk2' class='btn btn-md btn-primary' style='float:right;'>Update</button>
            </div>
        </div>
        <input type="hidden" name='kode_spk' value='<?=$kode_spk;?>'>
		<input type="hidden" name='tanda_mixing' value='2'>
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
        $('#update_real_spk2').hide();
		$('#show_material').hide();

        $(document).on('keyup','.changePersen',function(){
            let nomor = $(this).data('nomor')
            let persen = getNum($(this).val().split(",").join(""))
            let valResin = getNum($(this).parent().parent().parent().find('.weightResin1').val().split(",").join(""))
            let hasil = persen / 100 * valResin
            $(this).parent().parent().parent().find('.weightResin'+nomor).val(number_format(hasil,4))
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
                hasil = persen / 100 * valResin
                $(this).parent().parent().parent().find('.weightResin'+nomor).val(number_format(hasil,4))
                }
            });
        });
    });

    $(document).on('click', '#back', function(e){
        window.location.href = base_url + active_controller + '/index_loose/aktual';
    });

    $(document).on('keyup','.qty_spk', function(){
        var qty 	= getNum($(this).val().split(",").join(""));
        var qty_max = getNum($(this).parent().parent().find('.sisa_spk').html().split(",").join(""));
        if(qty > qty_max){
            $(this).val(qty_max);
        }
    });

    $(document).on('click', '#update_real_spk2', function(e){
        e.preventDefault();
        $(this).prop('disabled',true);

        let id_gudang = $('#id_gudang').val();
        let date_produksi = $('#date_produksi').val();
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
                text	: 'Tanggal selesai produksi belum dipilih',						
                type	: "warning"
            });
            $(this).prop('disabled',false);
            return false;
        }
        

        // return false;
        $('#update_real_spk2').prop('disabled',false);

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
                                window.location.href = base_url + active_controller+"/aktual_2/"+data.kode_spk;
                            }
                            else if(data.status == 2){
                                swal({
                                    title	: "Save Failed!",
                                    text	: data.pesan,
                                    type	: "warning",
                                    timer	: 3000
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

    $(document).on('click', '#show_material', function(e){
        e.preventDefault();

		loading_spinner();
		var formData 	=new FormData($('#form_proses_bro')[0]);
		var baseurl=base_url + active_controller +'/show_material_input2';
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
				   $('#update_real_spk2').show();
				   $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
				   $('.chosen_select').chosen();
                   $('.clPersen1').hide();
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
					title				: "Error Message !",
					text				: 'An Error Occured During Process. Please try again..',
					type				: "warning",
					timer				: 3000
				});
			}
		});
    });

    $(document).on('change', '#id_gudang', function(e){
        e.preventDefault();

		loading_spinner();
		var formData 	=new FormData($('#form_proses_bro')[0]);
		var baseurl=base_url + active_controller +'/show_material_input2';
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
				   $('#update_real_spk2').show();
				   $(".autoNumeric3").autoNumeric('init', {mDec: '4', aPad: false});
				   $('.chosen_select').chosen();
                   $('.clPersen1').hide();
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
					if(data.data_html == ''){
						$('#show_material').hide();
					}
					else{
						$('#show_material').show();
					}
                    $('#date_produksi').val(data.tanggal_produksi)
                    $('#date_produksi_start').val(data.tanggal_start)
                    $('#id_gudang').val(data.id_gudang).trigger('chosen:updated')
                    $('#update_real_spk2').hide();
                    $('#input-material').html('');
                    $(".autoNumeric0").autoNumeric('init', {mDec: '0', aPad: false})
                    $('.chosen_select').chosen()

                    let linkDownload = '';
                    if(data.upload_eng_change2 != ''){
                        linkDownload = `<a href='${data.upload_eng_change2}' target='_blank'>Download</a> `;
                    }

                    $('#download_eng_change').html(linkDownload)
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

    $(document).on('keyup', '.changeOB', function(e){
        let parentHTML = $(this).parent().parent()
        // let budget = parentHTML.find('.budget_over_value').html()
        // let splitHTML = budget.split(" / ")
        
        // let beratInput = getNum(parentHTML.find('.budget_over_value_input').html().split(",").join(""))
        // let beratEst    = getNum(parentHTML.find('.budget_over_value_est').html().split(",").join(""))
        // let beratVal = getNum($(this).val().split(",").join(""))
        // let beratTot = beratVal+beratInput
        
        // if(beratTot > beratEst){
        //     parentHTML.find('.budget_over').show()
        // }
        // else{
        //     parentHTML.find('.budget_over').hide()
        // }


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

    });

    $(document).on('change', '.id_material', function(e){
        let materialData = $(this).data('mat');
        let materialVal = $(this).val()
        let id_gudang = $('#id_gudang').val()
        let PARENT = $(this).parent().parent().find('.stockMaterial')
        // console.log(PARENT.html())
        // $.when(
            $(this).removeAttr('data-mat')
            $(this).addClass('mat_'+materialVal).removeClass('mat_'+materialData)
            $(this).attr('data-mat', materialVal)
        // ).done(
        // )
        
        //change stock
        var baseurl=base_url + active_controller +'/get_stock_material';
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
</script>