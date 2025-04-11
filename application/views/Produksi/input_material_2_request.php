
<?php 
//EDIT INPUT MATERIAL
$GET_STOCK_MAT = get_warehouseStockMaterial();
$nomor2 = 0;
if($hist_produksi != '0'){
    if(!empty($get_liner_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='8' align='left'>LINER</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'LINER THIKNESS / CB';
                    foreach ($get_liner_utama as $key => $value) { $nomor++; $nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='<?=$LAYER;?>'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$id_category;?>' data-id_material='<?=$material;?>' data-layer='<?=$LAYER;?>'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_liner[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_liner[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_liner[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_liner[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_liner[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_liner[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>

                <?php
                    if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                    }
                ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
    <?php if(!empty($get_joint_utama)){ ?>
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead>
                <tr class='bg-purple'><th colspan='8' align='left'>RESIN AND ADD</th></tr>
                <tr class='bg-blue'>
                    <th class='text-center' width='5%'>#</th>
                    <th class='text-center' width='17%'>Kategori</th>
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $nomorX = 0;
                    $LAYER = 'RESIN AND ADD';
                    foreach ($get_joint_utama as $key => $value) { $nomor++; $nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }
                        $CATEGORY = $value['nm_category'];
                        $HIDDEN = '';
                        if($nomor == '1'){
                            $CATEGORY = 'RESIN';
                        }
                        if($nomor != '1' AND $id_category == 'TYP-0001'){
                            $HIDDEN = 'hidden';
                        }
                        else{
                            $nomorX++;
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr <?=$HIDDEN;?>>
                            <td class='text-center'><?=$nomorX;?></td>
                            <td><?=$CATEGORY;?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='<?=$LAYER;?>'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$id_category;?>' data-id_material='<?=$material;?>' data-layer='<?=$LAYER;?>'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_joint[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_joint[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='detail_joint[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_joint[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_joint[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_joint[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_joint[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>

                <?php
                    if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
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
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'STRUKTUR NECK 1';
                    foreach ($get_str_n1_utama as $key => $value) { $nomor++;$nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='STRUKTUR NECK 1'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$value['id_category'];?>' data-id_material='<?=$material;?>' data-layer='STRUKTUR NECK 1'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_strn1[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_strn1[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
								<input type='hidden' name='detail_strn1[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_strn1[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_strn1[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_strn1[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_strn1[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
                <?php
                if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
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
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'STRUKTUR NECK 2';
                    foreach ($get_str_n2_utama as $key => $value) { $nomor++;$nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='STRUKTUR NECK 2'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$value['id_category'];?>' data-id_material='<?=$material;?>' data-layer='STRUKTUR NECK 2'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_strn2[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_strn2[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
								<input type='hidden' name='detail_strn2[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_strn2[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_strn2[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_strn2[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>  
                                <input type='text' name='detail_strn2[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
                <?php
                if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
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
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'STRUKTUR THICKNESS';
                    foreach ($get_structure_utama as $key => $value) { $nomor++;$nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='STRUKTUR THICKNESS'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$value['id_category'];?>' data-id_material='<?=$material;?>' data-layer='STRUKTUR THICKNESS'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_str[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_str[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
								<input type='hidden' name='detail_str[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_str[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_str[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_str[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_str[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
                <?php
                if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
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
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'EXTERNAL LAYER THICKNESS';
                    foreach ($get_external_utama as $key => $value) { $nomor++;$nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='EXTERNAL LAYER THICKNESS'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$value['id_category'];?>' data-id_material='<?=$material;?>' data-layer='EXTERNAL LAYER THICKNESS'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_ext[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_ext[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
								<input type='hidden' name='detail_ext[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_ext[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_ext[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_ext[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_ext[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
                <?php
                if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
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
                    <th class='text-center'>Material</th>
                    <th class='text-center' width='8%'>Stock (kg)</th>
                    <th class='text-center' width='12%'>Kebutuhan (kg)</th>
                    <th class='text-center' width='20%'>Aktual Material</th>
                    <th class='text-center' width='8%'>Persen (%)</th>
                    <th class='text-center' width='8%'>Aktual (kg)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $nomor = 0;
                    $LAYER = 'TOPCOAT';
                    foreach ($get_topcoat_utama as $key => $value) { $nomor++;$nomor2++;
                        $material       = $value['id_material_req'];
                        $id_category    = $ArrGetCategory[$material]['id_category'];
                        $nm_category    = $ArrGetCategory[$material]['nm_category'];
                        $list_material  = $get_material_by_category[$id_category];

                        $changeMixing   = ($nomor > 1)?'changeMixing':'';
  
                        $material_sel   =  $value['id_material'];
                        $WEIGHT_SUDAH   = (!empty($value['check_qty_oke']))?floatval($value['check_qty_oke']):0;
                        $WEIGHT_EST     = (!empty($value['qty_oke']))?floatval($value['qty_oke']):0;
                        $COLOR = 'text-orange';
                        if($WEIGHT_SUDAH > $WEIGHT_EST){
                            $COLOR = 'text-red';
                        }
                        if($WEIGHT_SUDAH == $WEIGHT_EST){
                            $COLOR = 'text-green';
                        }

                        $UNIQ_STOCK = $material_sel.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$value['nm_category'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnyaNew' title='Tambahkan Material Baru' data-layer='TOPCOAT'><i class='fa fa-plus text-green'></i></span></td>
                            <td><?=$value['nm_material'];?>&nbsp;&nbsp;&nbsp;<span style='cursor:pointer; display:none;' class='addMatLainnya' title='Tambahkan Material' data-id_category='<?=$value['id_category'];?>' data-id_material='<?=$material;?>' data-layer='TOPCOAT'><i class='fa fa-plus text-green'></i></span></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='<?=$COLOR;?> budget_over_value_input'><?=$WEIGHT_SUDAH;?></span> / <span class='text-blue budget_over_value_est'><?=number_format($WEIGHT_EST,4);?></span></td>
                            <td>
                                <select name='detail_topcoat[<?=$nomor;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material_sel;?>' data-mat='<?=$material_sel;?>'>
                                    <option value="MTL-1903000">NONE MATERIAL</option>
                                    <?php
                                        foreach($list_material AS $valMat => $valxMat){
                                            // if($valxMat['id_material'] == $material_sel){
                                                $sel = ($valxMat['id_material'] == $material_sel)?'selected':'';
                                                echo "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
                                            // }
                                        }
                                    ?>
                                </select>
                                <span class='text-red budget_over text-bold'>Budget Over !!!</span>
                                <span class='text-purple budget_stock text-bold'>Stock Over, stock otomatis disesuaikan !!!</span>
                            </td>
                            <td>
                                <input type='text' name='detail_topcoat[<?=$nomor;?>][persen]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$nomor;?>' autocomplete='off'>
                            </td>
                            <td>
								<input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_material]' value='<?=$material;?>'>
								<input type='hidden' name='detail_topcoat[<?=$nomor;?>][layer]' value='<?=$LAYER;?>'>
								<input type='hidden' name='detail_topcoat[<?=$nomor;?>][id_key]' value='<?=$nomor2;?>'>
                                <input type='hidden' name='detail_topcoat[<?=$nomor;?>][kebutuhan]' value='<?=$WEIGHT_EST;?>'>
                                <input type='text' name='detail_topcoat[<?=$nomor;?>][terpakai]' data-nomor='<?=$nomor;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$nomor;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                ?>
                <?php
                if(!empty($ArrMaterialAdd[$LAYER])){
                    foreach ($ArrMaterialAdd[$LAYER] as $key => $value) {$nomor++;
                        $uniq_number    = '1001'.$value['id'];
                        $id_category    = $ArrGetCategory[$value['actual_type']]['id_category'];
                        $nm_category    = $ArrGetCategory[$value['actual_type']]['nm_category'];
                        $nm_material    = $ArrGetCategory[$value['actual_type']]['nm_material'];

                        $list_material  = $get_material_by_category[$id_category];
                        $material       = $value['actual_type'];
                        $changeMixing   = '';

                        $BERAT_REQ = (!empty($value['terpakai']))?floatval($value['terpakai']):0;

                        $UNIQ_STOCK = $material.'-'.$id_gudang_from;
                        $STOCK = (!empty($GET_STOCK_MAT[$UNIQ_STOCK]))?$GET_STOCK_MAT[$UNIQ_STOCK]:0;
                        ?>
                        <tr>
                            <td class='text-center'><?=$nomor;?></td>
                            <td><?=$nm_category;?></td>
                            <td><?=$nm_material;?></td>
                            <td class='text-right stockMaterial'><?=number_format($STOCK,4);?></td>
                            <td class='text-right text-bold'><span class='text-green'><?=$BERAT_REQ;?></span></td>
                            <td>
                                <select name='edit_add[<?=$uniq_number;?>][actual_type]' class='form-control chosen_select id_material mat_<?=$material;?>' data-mat='<?=$material;?>' disabled>
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
                                <input type='text' name='edit_add[<?=$uniq_number;?>][persen]' data-nomor='<?=$uniq_number;?>' value='<?=$value['persen']?>' class='form-control input-sm text-center autoNumeric3 changePersen clPersen<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                            <td>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][id]' value='<?=$value['id'];?>'>
                                <input type='hidden' name='edit_add[<?=$uniq_number;?>][actual_type2]' value='<?=$material;?>'>
                                <input type='text' name='edit_add[<?=$uniq_number;?>][terpakai]' data-nomor='<?=$uniq_number;?>' class='form-control input-sm text-center changeOB autoNumeric3 <?=$changeMixing;?> weightResin<?=$uniq_number;?>' autocomplete='off'>
                            </td>
                        </tr>
                    <?php 
                    }
                }
                ?>
            </tbody>
        </table>
        <br>
    <?php }
} ?>