
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
	<input type="hidden" name='no_ipp' id='no_ipp' value='<?= $no_ipp;?>'>
	<input type="hidden" name='no_spk' id='no_spk' value='<?= $no_spk;?>'>
	<input type="hidden" name='id_milik' id='id_milik' value='<?= $id_milik;?>'>
    <table width='80%'>
        <tr>
            <td width='20%'>No IPP</td>
            <td width='1%'>:</td>
            <td><?=$no_ipp;?></td>
        </tr>
        <tr>
            <td>No SO</td>
            <td>:</td>
            <td><?=$id_milik;?></td>
        </tr>
        <tr>
            <td>No SPK</td>
            <td>:</td>
            <td><?=$no_spk;?></td>
        </tr>
    </table>
    <div class='form-group row'>		 	 
        <label class='label-control col-sm-2'><b>Upload Eng. Change <span class='text-danger'>*</span></b></label>
        <div class='col-sm-4 text-right'>             
            <input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload Eng. Change'>
            <?php if(!empty($upload_spk)){ ?>
            <a href='<?=base_url('assets/file/produksi/'.$upload_spk);?>' target='_blank' title='Download' data-role='qtip'>Download</a>
            <?php } ?>	
        </div>
    </div>
    <div class='form-group row'>		 	 
        <label class='label-control col-sm-2'><b>Keterangan <span class='text-danger'>*</span></b></label>
        <div class='col-sm-4'>             
            <textarea name="ket_request" id="ket_request"  class='form-control input-md' rows='2' placeholder='Keterangan Request'></textarea>
        </div>
    </div>
    <br>
	<?php
    if(!empty($get_liner_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>LINER</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_liner_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_str_n1_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>STRUCTURE NECK 1</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n1_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_str_n2_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>STRUCTURE NECK 2</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n2_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_structure_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>STRUCTURE</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_structure_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_external_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>EXTERNAL</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_external_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_topcoat_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>TOP COAT</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='15%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id]' value='<?=$value['id_detail'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][table_update]' value='<?=$value['table_update'];?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 10px 0px 5px 0px;','value'=>'Save','content'=>'Process Request','id'=>'request_material'));
	?>
	</table>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
</style> 
<script>
	$(document).ready(function(){
        swal.close();
    });
</script>