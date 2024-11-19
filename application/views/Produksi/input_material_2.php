
<?php 
$GET_STOCK_MAT = get_warehouseStockMaterial();
echo $hist_produksi;
$id_key = 0;
if($hist_produksi == '0'){
if(!empty($get_liner_utama)){ ?>
    <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
        <thead>
            <tr class='bg-purple'><th colspan='9' align='left'>LINER</th></tr>
            <tr class='bg-blue'>
                <th class='text-center' width='3%'>#</th>
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                
                foreach ($get_liner_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'LINER THIKNESS / CB','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'LINER THIKNESS / CB','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_liner[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                            <input type='text' name='detail_liner[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
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
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n1_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 1','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 1','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_strn1[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                            <input type='text' name='detail_strn1[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
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
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n2_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 2','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 2','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_strn2[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>  
                            <input type='text' name='detail_strn2[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
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
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_structure_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR THICKNESS','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR THICKNESS','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_str[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                            <input type='text' name='detail_str[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
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
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_external_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'EXTERNAL LAYER THICKNESS','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'EXTERNAL LAYER THICKNESS','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_ext[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                            <input type='text' name='detail_ext[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
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
                <th class='text-center' width='13%'>Kategori</th>
                <th class='text-center' width='25%'>Material</th>
                <th class='text-center' width='8%'>Estimasi (kg)</th>
                <th class='text-center' width='8%'>WIP (kg)</th>
                <th class='text-center' width='8%'>Waiting (kg)</th>
                <th class='text-center' width='8%'>Weight (kg)</th>
                <th class='text-center'>Aktual Material</th>
                <th class='text-center' width='8%'>Actual (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_topcoat_utama as $key => $value) { $nomor++; $id_key++;
                    $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                    $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'TOPCOAT','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                    $terpakai = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                    $material   = (!empty($get_edit))?$get_edit[0]->id_material:$value['id_material'];

                    $get_edit2 = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total')->where_in('id_spk',$id_spk)->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'TOPCOAT','spk'=>'2','kode_spk'=>$kode_spk))->result();
                    $terpakai2 = (!empty($get_edit2))?$get_edit2[0]->aktual_total:0;
                    ?>
                    <tr>
                        <td class='text-center'><?=$nomor;?></td>
                        <td><?=$value['nm_category'];?></td>
                        <td><?=$value['nm_material'];?></td>
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'],3);?></td>
                        <td class='text-right text-bold text-green'><?=number_format($terpakai2,3);?></td>
                        <td class='text-right text-bold text-red'><?=number_format($value['berat_all'] - $terpakai2,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat'],4);?></td>
                        <td>
                            <select name='detail_topcoat[<?=$nomor;?>][actual_type]' class='form-control chosen_select'>
                                <option value="MTL-1903000">NONE MATERIAL</option>
                                <?php
                                    foreach($list_material AS $valMat => $valxMat){
                                        $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                        echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                        <td>
                            <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                            <input type='text' name='detail_topcoat[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                    </tr>
                <?php 
                }
            ?>
        </tbody>
    </table>
    <br>
<?php }} ?>

<?php 
//EDIT INPUT MATERIAL
$id_key = 0;
if($hist_produksi != '0'){
    if(!empty($get_liner_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='8' align='left'>LINER</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                   
                    foreach ($get_liner_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        // $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'LINER THIKNESS / CB','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $SQL1       = "SELECT id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen FROM production_spk_input WHERE id_material='".$value['id_material']."' AND kode_spk='".$kode_spk."' AND status_date='".$hist_produksi."' AND spk = '2' AND (layer = 'LINER THIKNESS / CB' OR layer = 'RESIN AND ADD') GROUP BY id_material ";
                        $get_edit   = $this->db->query($SQL1)->result();
                        $terpakai2  = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai   = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_liner[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_liner[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_liner[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
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
                <tr class='bg-purple'><th colspan='8' align='left'>STRUCTURE NECK 1</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n1_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 1','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $terpakai2 = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_strn1[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_strn1[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_strn1[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
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
                <tr class='bg-purple'><th colspan='8' align='left'>STRUCTURE NECK 2</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_str_n2_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR NECK 2','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $terpakai2 = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_strn2[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_strn2[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>  
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_strn2[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
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
                <tr class='bg-purple'><th colspan='8' align='left'>STRUCTURE</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_structure_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'STRUKTUR THICKNESS','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $terpakai2 = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_str[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_str[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_str[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
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
                <tr class='bg-purple'><th colspan='8' align='left'>EXTERNAL</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_external_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'EXTERNAL LAYER THICKNESS','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $terpakai2 = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_ext[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_ext[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_ext[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
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
                <tr class='bg-purple'><th colspan='8' align='left'>TOP COAT</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center' width='25%'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    foreach ($get_topcoat_utama as $key => $value) { $nomor++; $id_key++;
                        $list_material = $this->db->get_where('raw_materials', array('id_category'=>$value['id_category'],'delete'=>'N'))->result_array();
                        $get_edit = $this->db->group_by('id_material')->limit(1)->select('id_material, id_material_aktual, SUM(aktual_total) AS aktual_total, persen')->get_where('production_spk_input',array('id_material'=>$value['id_material'],'layer'=>'TOPCOAT','spk'=>'2','kode_spk'=>$kode_spk,'status_date'=>$hist_produksi))->result();
                        $terpakai2 = (!empty($get_edit))?$get_edit[0]->aktual_total:'';
                        $terpakai = ($terpakai2 > 0)?$terpakai2:'';
                        $material   = (!empty($get_edit[0]->id_material_aktual))?$get_edit[0]->id_material_aktual:$value['id_material'];
                        $KEY        = $kode_trans.'-'.$id_key;
                        $persen     = (!empty($get_percent[$KEY]['persen']) AND $get_percent[$KEY]['persen'] > 0)?$get_percent[$KEY]['persen']:'';

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?></td>
                            <td><?=$value['nm_material'];?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right'><?=number_format($value['berat'],4);?></td>
                            <td>
                                <select name='detail_topcoat[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            $sel = ($valxMat['id_material'] == $material)?'selected':'';
                                            echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                        }
                                    ?>
                                </select>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_topcoat[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off' value='<?=$persen;?>'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat'];?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_material]' value='<?=$value['id_material'];?>'>
                                <input type='text' name='detail_topcoat[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center changeOB autoNumeric3 weightResin<?=$nomor;?>' autocomplete='off' value='<?=$terpakai;?>'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
            </tbody>
        </table>
        <br>
<?php }
} ?>