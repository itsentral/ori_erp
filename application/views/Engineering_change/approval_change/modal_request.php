
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
	<input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
	<input type="hidden" name='no_ipp' id='no_ipp' value='<?= $no_ipp;?>'>
    <table width='80%'>
        <tr>
            <td width='20%'>No IPP</td>
            <td width='1%'>:</td>
            <td><?=$no_ipp;?></td>
        </tr>
        <tr>
            <td>No SO</td>
            <td>:</td>
            <td><?=$no_so;?></td>
        </tr>
        <tr>
            <td>No SPK</td>
            <td>:</td>
            <td><?=$no_spk;?></td>
        </tr>
        <tr>
            <td>File Eng. Change</td>
            <td>:</td>
            <td>
            <?php if(!empty($upload_spk)){ ?>
            <a href='<?=base_url('assets/file/produksi/'.$upload_spk);?>' target='_blank' title='Download' data-role='qtip'>Download</a>
            <?php } ?>	
            </td>
        </tr>
        <tr>
            <td>Keterangan</td>
            <td>:</td>
            <td><?=$keterangan;?></td>
        </tr>
    </table>
    <br>
	<?php
    if(!empty($get_liner_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='9' align='left'>LINER</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='3%'>#</th>
                    <th class='text-center' width='18%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_liner_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_liner[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_liner[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n1_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_strn1[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_strn1[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n2_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_strn2[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_strn2[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_structure_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_str[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_str[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_external_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_ext[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_ext[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                    <th class='text-center'>Change Material</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $id_category = $getMaterial[0]['id_category'];
                        $nm_category = $getMaterial[0]['nm_category'];
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$id_category,'delete'=>'N'))->result_array();
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right text-bold text-black'><?=number_format($value['berat'],3);?>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][layer]' value='<?=$value['layer'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_update]' value='<?=$value['ket_req_pro'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][table_update]' value='<?=$value['check_keterangan'];?>'>
                            </td>
                            <td>
                            <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id]' value='<?=$value['id'];?>'>
                            <select name='detail_topcoat[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $value['id_material'])?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
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
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin: 10px 0px 5px 0px;','value'=>'Save','content'=>'Change Material','id'=>'request_material'));
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
        $('.chosen_select').chosen();
    });
</script>