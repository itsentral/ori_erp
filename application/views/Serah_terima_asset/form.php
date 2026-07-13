<?php
$this->load->view('include/side_menu');

$id             = (!empty($data)) ? $data['id'] : '';
$form_no        = (!empty($data)) ? $data['form_no'] : '';
$sender         = (!empty($data)) ? $data['sender'] : '';
$receiver       = (!empty($data)) ? $data['receiver'] : '';
$lokasi         = (!empty($data)) ? $data['lokasi'] : '';
$departemen     = (!empty($data)) ? $data['departemen'] : '';
?>
<form action="#" method="POST" id="form_serah_terima" enctype="multipart/form-data">
    <input type="hidden" name="id" id="header_id" value="<?=$id;?>">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3>
        </div>
        <div class="box-body">
            <!-- Sender & Receiver Info -->
            <div class="form-group row">
                <label class="label-control col-sm-2"><b>Sender <span class="text-red">*</span></b></label>
                <div class="col-sm-4">
                    <?php echo form_input(array('id'=>'sender','name'=>'sender','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nama Pengirim'), $sender); ?>
                </div>
                <label class="label-control col-sm-2"><b>Lokasi <span class="text-red">*</span></b></label>
                <div class="col-sm-4">
                    <select name="lokasi" id="lokasi" class="form-control input-md chosen-select">
                        <option value="">Select Lokasi</option>
                        <?php
                            $arr_lokasi = array('OPC 1','OPC 2','OPC 3','Office');
                            foreach($arr_lokasi as $lok){
                                $sel = ($lokasi == $lok) ? 'selected' : '';
                                echo "<option value='".$lok."' ".$sel.">".$lok."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="label-control col-sm-2"><b>Receiver <span class="text-red">*</span></b></label>
                <div class="col-sm-4">
                    <?php echo form_input(array('id'=>'receiver','name'=>'receiver','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Nama Penerima'), $receiver); ?>
                </div>
                <label class="label-control col-sm-2"><b>Departemen <span class="text-red">*</span></b></label>
                <div class="col-sm-4">
                    <select name="departemen" id="departemen" class="form-control input-md chosen-select">
                        <option value="">Select Departemen</option>
                        <?php
                            foreach($list_dept as $dept){
                                if($dept['deleted'] == 'N'){
                                    $sel = ($departemen == $dept['id']) ? 'selected' : '';
                                    echo "<option value='".$dept['id']."' ".$sel.">".strtoupper($dept['nm_dept'])."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>

            <hr>

            <!-- Detail Asset Section -->
            <div class="row" style="margin-bottom:10px;">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-sm btn-success" id="btn-add-asset">
                        <i class="fa fa-plus"></i> Add Asset
                    </button>
                </div>
            </div>

            <div id="asset-container">
                <?php if(!empty($detail)): ?>
                    <?php foreach($detail as $idx => $det): ?>
                    <div class="panel panel-default asset-row" data-index="<?=$idx;?>">
                        <div class="panel-heading">
                            <strong>Asset #<?=($idx+1);?></strong>
                            <button type="button" class="btn btn-xs btn-danger pull-right btn-remove-asset"><i class="fa fa-times"></i> Remove</button>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group row">
                                <label class="label-control col-sm-2"><b>Asset</b></label>
                                <div class="col-sm-4">
                                    <select name="asset_id[]" class="form-control input-md chosen-select select-asset" data-index="<?=$idx;?>">
                                        <option value="">Select Asset</option>
                                        <?php foreach($list_asset as $ast): ?>
                                            <option value="<?=$ast['id'];?>" <?=($det['asset_id']==$ast['id'])?'selected':'';?>><?=$ast['nm_asset'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <label class="label-control col-sm-2"><b>Assets Code</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="assets_code[]" class="form-control input-md assets-code" value="<?=$det['assets_code'];?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="label-control col-sm-2"><b>New Assets Code</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="new_assets_code[]" class="form-control input-md" value="<?=$det['new_assets_code'];?>" placeholder="New Assets Code">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="label-control col-sm-2"><b>Model</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="model[]" class="form-control input-md" value="<?=$det['model'];?>" placeholder="Model">
                                </div>
                                <label class="label-control col-sm-2"><b>Merk</b></label>
                                <div class="col-sm-4">
                                    <input type="text" name="merk[]" class="form-control input-md" value="<?=$det['merk'];?>" placeholder="Merk">
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="box-footer">
                <?php
                    echo form_button(array('type'=>'button','style'=>'float:right; margin-left:5px; width:100px;','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Cancel','onClick'=>'javascript:back()'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'btn-save','style'=>'width:100px; float:right;'));
                ?>
            </div>
        </div>
    </div>
</form>

<!-- Template for new asset row -->
<template id="asset-row-template">
    <div class="panel panel-default asset-row" data-index="__INDEX__">
        <div class="panel-heading">
            <strong>Asset #__NUM__</strong>
            <button type="button" class="btn btn-xs btn-danger pull-right btn-remove-asset"><i class="fa fa-times"></i> Remove</button>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="form-group row">
                <label class="label-control col-sm-2"><b>Asset</b></label>
                <div class="col-sm-4">
                    <select name="asset_id[]" class="form-control input-md select-asset" data-index="__INDEX__">
                        <option value="">Select Asset</option>
                        <?php foreach($list_asset as $ast): ?>
                            <option value="<?=$ast['id'];?>"><?=$ast['nm_asset'];?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <label class="label-control col-sm-2"><b>Assets Code</b></label>
                <div class="col-sm-4">
                    <input type="text" name="assets_code[]" class="form-control input-md assets-code" readonly placeholder="Otomatis muncul">
                </div>
            </div>
            <div class="form-group row">
                <label class="label-control col-sm-2"><b>New Assets Code</b></label>
                <div class="col-sm-4">
                    <input type="text" name="new_assets_code[]" class="form-control input-md" placeholder="New Assets Code">
                </div>
            </div>
            <div class="form-group row">
                <label class="label-control col-sm-2"><b>Model</b></label>
                <div class="col-sm-4">
                    <input type="text" name="model[]" class="form-control input-md" placeholder="Model">
                </div>
                <label class="label-control col-sm-2"><b>Merk</b></label>
                <div class="col-sm-4">
                    <input type="text" name="merk[]" class="form-control input-md" placeholder="Merk">
                </div>
            </div>
        </div>
    </div>
</template>

<?php $this->load->view('include/footer'); ?>

<script>
$(function(){
    swal.close();
    $(".chosen-select").chosen();

    var assetIndex = <?= !empty($detail) ? count($detail) : 0; ?>;

    // Add Asset Row
    $('#btn-add-asset').on('click', function(){
        var template = $('#asset-row-template').html();
        template = template.replace(/__INDEX__/g, assetIndex);
        template = template.replace(/__NUM__/g, assetIndex + 1);
        $('#asset-container').append(template);
        
        // Reinitialize chosen for new selects
        $('#asset-container .asset-row[data-index="'+assetIndex+'"] select').chosen({
            allow_single_deselect: true,
            search_contains: true,
            no_results_text: 'No result found for : ',
            placeholder_text_single: 'Select an option'
        });
        
        assetIndex++;
        renumberAssets();
    });

    // Remove Asset Row
    $(document).on('click', '.btn-remove-asset', function(){
        $(this).closest('.asset-row').remove();
        renumberAssets();
    });

    // When selecting an asset, auto-fill the code
    $(document).on('change', '.select-asset', function(){
        var $row = $(this).closest('.asset-row');
        var asset_id = $(this).val();
        
        if(asset_id){
            $.ajax({
                url: base_url + 'serah_terima_asset/get_asset_info',
                type: 'POST',
                data: {id: asset_id},
                dataType: 'json',
                success: function(res){
                    if(res.status == 1){
                        $row.find('.assets-code').val(res.code_ori || res.kd_asset);
                    }
                }
            });
        } else {
            $row.find('.assets-code').val('');
        }
    });

    // Renumber asset panels
    function renumberAssets(){
        $('#asset-container .asset-row').each(function(i){
            $(this).find('.panel-heading strong').text('Asset #' + (i+1));
        });
    }

    // Save Form
    $('#btn-save').on('click', function(e){
        e.preventDefault();
        $(this).prop('disabled', true);

        var sender      = $('#sender').val();
        var receiver    = $('#receiver').val();
        var lokasi      = $('#lokasi').val();
        var departemen  = $('#departemen').val();
        var assetCount  = $('#asset-container .asset-row').length;

        if(!sender){
            swal({title: "Error!", text: "Sender belum diisi.", type: "warning"});
            $('#btn-save').prop('disabled', false);
            return false;
        }
        if(!receiver){
            swal({title: "Error!", text: "Receiver belum diisi.", type: "warning"});
            $('#btn-save').prop('disabled', false);
            return false;
        }
        if(!lokasi){
            swal({title: "Error!", text: "Lokasi belum dipilih.", type: "warning"});
            $('#btn-save').prop('disabled', false);
            return false;
        }
        if(!departemen){
            swal({title: "Error!", text: "Departemen belum dipilih.", type: "warning"});
            $('#btn-save').prop('disabled', false);
            return false;
        }
        if(assetCount < 1){
            swal({title: "Error!", text: "Minimal harus ada 1 asset.", type: "warning"});
            $('#btn-save').prop('disabled', false);
            return false;
        }

        swal({
            title: "Are you sure?",
            text: "Simpan data serah terima asset?",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-primary",
            confirmButtonText: "Yes, Save!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm){
            if(isConfirm){
                loading_spinner();
                var formData = new FormData($('#form_serah_terima')[0]);
                var url = base_url + active_controller;
                
                var headerId = $('#header_id').val();
                if(headerId){
                    url += '/edit/' + headerId;
                } else {
                    url += '/add';
                }

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    cache: false,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data){
                        if(data.status == 1){
                            swal({
                                title: "Success!",
                                text: data.pesan,
                                type: "success",
                                timer: 3000
                            });
                            window.location.href = base_url + active_controller;
                        } else {
                            swal({
                                title: "Failed!",
                                text: data.pesan,
                                type: "warning"
                            });
                            $('#btn-save').prop('disabled', false);
                        }
                    },
                    error: function(){
                        swal({
                            title: "Error!",
                            text: "Connection error. Please try again.",
                            type: "warning"
                        });
                        $('#btn-save').prop('disabled', false);
                    }
                });
            } else {
                swal("Cancelled", "Data tidak disimpan.", "error");
                $('#btn-save').prop('disabled', false);
            }
        });
    });
});
</script>
