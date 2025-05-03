
<?php 
if($hist_produksi == '0'){
    $hist_produksi = null;
if(!empty($get_liner_utama)){ ?>
    <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
        <thead>
            <tr class='bg-purple'><th colspan='9' align='left'>LINER</th></tr>
            <tr class='bg-blue'>
                <th class='text-center' width='3%'>#</th>
                <th class='text-center' width='18%'>Kategori</th>
                <th class='text-center'>Material</th>
                <th class='text-center' width='15%'>Estimasi (kg)</th>
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_liner_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>
                            <input type='text' name='detail_liner[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_liner[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
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
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n1_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>
                            <input type='text' name='detail_strn1[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_strn1[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
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
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_str_n2_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>  
                            <input type='text' name='detail_strn2[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_strn2[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
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
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_structure_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>
                            <input type='text' name='detail_str[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_str[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
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
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_external_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>
                            <input type='text' name='detail_ext[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_ext[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
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
                <th class='text-center' width='15%'>Weight Request (kg)</th>
                <th class='text-center' hidden>Aktual Material</th>
                <th class='text-center' hidden width='8%'>Actual (kg)</th>
                <th class='text-center' hidden width='15%'>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $nomor = 0;
                foreach ($get_topcoat_utama as $key => $value) { $nomor++;
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
                        <td class='text-right text-bold text-blue'><?=number_format($value['berat_all'] / $qty_est_tanki,3);?></td>
                        <td class='text-right text-bold text-black'><?=number_format($value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty,3);?></td>
                        <td hidden>
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
                        <td hidden>
                            <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$value['berat_all'] / $qty_est_tanki  / $qty_est_tanki * $qty;?>'>
                            <input type='text' name='detail_topcoat[<?=$nomor;?>][terpakai]' class='form-control input-sm text-center autoNumeric3' autocomplete='off' value='<?=$terpakai;?>'>
                        </td>
                        <td hidden>
                            <input type='text' name='detail_topcoat[<?=$nomor;?>][keterangan]' class='form-control input-sm text-left' autocomplete='off' >
                        </td>
                    </tr>
                <?php 
                }
            ?>
        </tbody>
    </table>
    <br>
<?php }} ?>