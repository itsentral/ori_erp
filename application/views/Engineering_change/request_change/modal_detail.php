
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
	<input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
	<input type="hidden" name='no_spk' id='no_spk' value='<?= $no_spk;?>'>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_liner_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n1_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n2_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_structure_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_external_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
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
                    <th class='text-center' width='15%'>Kategori</th>
                    <th class='text-center' width='35%'>Material</th>
                    <th class='text-center'>Change Material</th>
                    <th class='text-center' width='10%'>Estimasi (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_topcoat_utama as $key => $value) { $nomor++;
                        $getMaterial = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material']))->result_array();
                        $nm_category = $getMaterial[0]['nm_category'];
                        $getMaterialC = $this->db->get_where('raw_materials',array('id_material'=>$value['id_material_change']))->result_array();
                        $nm_material = $getMaterialC[0]['nm_material'];
                        $color = ($value['id_material'] == $value['id_material_change'])?'':'text-danger text-bold';
                        if($value['id_material'] == $value['id_material_change']){
                            $nm_material = "";
                        }
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='<?=$color;?>'><?=$nm_material;?></td>
                            <td class='text-right'><?=number_format($value['berat'],3);?></td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
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