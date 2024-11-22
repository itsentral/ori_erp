<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';
include_once 'function_connect_tanki.php';

//echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();

$db2 				= new database_ORI_TANKI();
$koneksi_tanki 		= $db2->connect();
//echo $db1;exit;
$dateC = date('Y-m-d');
$date = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));
// $date = '2024-09-01';
$sqlHeader      = "SELECT a.*, b.id_milik, b.no_spk, b.product_code, b.id_product AS nm_tanki FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' and a.id_product='tanki' ";


$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;
// echo "<pre>";
// print_r($restHeader);
// exit;

//!!!==================================DIAKTIFKAN DITUKAR
$dateNext = $date;
// $dateNext = date('Y-m-d',strtotime($restHeader[0]['status_date']));

$kurs=1;
$sqlkurs="select * from ms_kurs where tanggal <='".$dateNext."' and mata_uang='USD' order by tanggal desc limit 1";
$dtkurs	= $koneksi->query($sqlkurs);
if(!empty($dtkurs)) {
	$getkurs	= $dtkurs->fetch_array(MYSQLI_ASSOC);
	$kurs=$getkurs['kurs'];
}

if(!empty($restHeader)){
    // $sqlDel1 = "DELETE FROM laporan_per_bulan WHERE `date`='".$date."' ";
    $sqlDel2 = "DELETE FROM laporan_per_hari WHERE `date`='".$date."' and id_product='tanki' ";
    // $koneksi->query($sqlDel1);
    $koneksi->query($sqlDel2);


    $ArrDay = array();
    $Sum_est_mat        = 0;
    $Sum_est_harga      = 0;
    $Sum_real_mat       = 0;
    $Sum_real_harga     = 0;
    $Sum_real_harga_rp  = 0;
    $Sum_direct_labour  = 0;
    $Sum_in_labour      = 0;
    $Sum_machine        = 0;
    $Sum_mould_mandrill = 0;
    $Sum_consumable     = 0;
    $Sum_foh_consumable = 0;
    $Sum_foh_depresiasi = 0;
    $Sum_by_gaji        = 0;
    $Sum_by_non_pro     = 0;
    $Sum_by_rutin       = 0;
	$Sum_man_hours      = 0;
    foreach($restHeader AS $val=>$valx){

        $sqlEstMaterial = "SELECT SUM(berat) AS est_berat, SUM(berat*price) AS est_price FROM est_material_tanki WHERE id_det='".$valx['id_milik']."' GROUP BY id_det";
        $QEstMat	    = $koneksi->query($sqlEstMaterial);
        $restEstMat	    = $QEstMat->fetch_array(MYSQLI_ASSOC);

        $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;

        $est_material_bef          = (!empty($restEstMat['est_berat']))?$restEstMat['est_berat']:0;
        $est_harga_bef             = (!empty($restEstMat['est_price']))?$restEstMat['est_price']:0;

        $est_material           = $est_material_bef * $jumTot;
        $est_harga              = $est_harga_bef * $jumTot;

        $sqlBy 		= " SELECT
                            a.dia_lebar AS diameter,
                            a.panjang AS diameter2,
                            a.t_dsg AS pressure,
                            a.t_est AS liner,
                            $est_harga AS sum_price,
                            a.man_hours AS man_hours,
                            (a.man_hours * a.pe_direct_labour) AS direct_labour,
                            (a.man_hours * a.pe_indirect_labour) AS indirect_labour,
                            (a.t_time * a.pe_machine) AS machine,
                            0 AS mould_mandrill,
                            ($est_material * a.pe_consumable) AS consumable,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_foh_consumable / 100 ) AS foh_consumable,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_foh_depresiasi / 100 ) AS foh_depresiasi,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
                            (
                                    (((a.man_hours * a.pe_direct_labour))+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
                        FROM
                                bq_detail_detail a
                        WHERE a.id='".$valx['id_milik']."' LIMIT 1";
        // echo $sqlBy."<br>";
        $Q_By		= $koneksi_tanki->query($sqlBy);
        $restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
        
        $sqlBan         = " SELECT 
                                SUM(a.material_terpakai) AS real_material, 
                                SUM(a.material_terpakai*b.price) AS real_harga 
                            FROM 
                                production_real_detail a
                                INNER JOIN est_material_tanki b ON a.id_detail=b.id
                            WHERE a.id_production_detail='".$valx['id_production_detail']."' 
                            GROUP BY a.id_production_detail";
        $Q_ByBan	    = $koneksi->query($sqlBan);
        $restBan	    = $Q_ByBan->fetch_array(MYSQLI_ASSOC);
        
        $real_material          = (!empty($restBan['real_material']))?$restBan['real_material']:0;
        $real_harga             = (!empty($restBan['real_harga']))?$restBan['real_harga']:0;
        $real_harga_rp          = $real_harga * $kurs;

        $Sum_est_mat        += $est_material;
        $Sum_est_harga      += $est_harga;
        $Sum_real_mat       += $real_material;
        $Sum_real_harga     += $real_harga;
        $Sum_real_harga_rp  += $real_harga_rp;
        $Sum_direct_labour  += $restBy['direct_labour'] * $jumTot;
        $Sum_in_labour      += $restBy['indirect_labour'] * $jumTot;
        $Sum_machine        += $restBy['machine'] * $jumTot;
        $Sum_mould_mandrill += $restBy['mould_mandrill'] * $jumTot;
        $Sum_consumable     += $restBy['consumable'] * $jumTot;
        $Sum_foh_consumable += $restBy['foh_consumable'] * $jumTot;
        $Sum_foh_depresiasi += $restBy['foh_depresiasi'] * $jumTot;
        $Sum_by_gaji        += $restBy['biaya_gaji_non_produksi'] * $jumTot;
        $Sum_by_non_pro     += $restBy['biaya_non_produksi'] * $jumTot;
        $Sum_by_rutin       += $restBy['biaya_rutin_bulanan'] * $jumTot;
		$Sum_man_hours       += $restBy['man_hours'] * $jumTot;
        
        $sqlInsertDet = "INSERT INTO laporan_per_hari
                            (id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
                            qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
                            real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
                            consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,no_spk,no_so)
                            VALUE
                            ('".$valx['id_produksi']."','".$valx['nm_tanki']."','".$valx['id_product']."',
                            '".$restBy['diameter']."','".$restBy['diameter2']."','0',
                            '0','".$valx['status_date']."','".$valx['product_ke']."',
                            '".$valx['qty_akhir']."','".$valx['qty']."','".date('Y-m-d',strtotime($valx['status_date']))."','".$valx['id_production_detail']."',
                            '".$valx['id_milik']."','".$est_material."','".$est_harga."',
                            '".$real_material."','".$real_harga."','".$restBy['direct_labour'] * $jumTot."',
                            '".$restBy['indirect_labour'] * $jumTot."','".$restBy['machine'] * $jumTot."',
                            '".$restBy['mould_mandrill'] * $jumTot."','".$restBy['consumable'] * $jumTot."',
                            '".$restBy['foh_consumable'] * $jumTot."','".$restBy['foh_depresiasi'] * $jumTot."',
                            '".$restBy['biaya_gaji_non_produksi'] * $jumTot."','".$restBy['biaya_non_produksi'] * $jumTot."',
                            '".$restBy['biaya_rutin_bulanan'] * $jumTot."','system','".date('Y-m-d H:i:s')."','".$restBy['man_hours'] * $jumTot."','".$real_harga_rp."','".$kurs."','".$valx['no_spk']."','".substr($valx['product_code'],0,9)."')
                        ";
        // echo $sqlInsertDet."<br>";
        // exit;
        $koneksi->query($sqlInsertDet);
    }
    // exit;
    $ArrDayMonth = array(
        'date' => $date,
        'est_material' => $Sum_est_mat,
        'est_harga' => $Sum_est_harga,
        'real_material' => $Sum_real_mat,
        'real_harga' => $Sum_real_harga,
        'real_harga_rp' => $Sum_real_harga_rp,
        'kurs' => $kurs,
        'direct_labour' => $Sum_direct_labour,
        'indirect_labour' => $Sum_in_labour,
        'machine' => $Sum_machine,
        'mould_mandrill' => $Sum_mould_mandrill,
        'consumable' => $Sum_consumable,
        'foh_consumable' => $Sum_foh_consumable,
        'foh_depresiasi' => $Sum_foh_depresiasi,
        'biaya_gaji_non_produksi' => $Sum_by_gaji,
        'biaya_non_produksi' => $Sum_by_non_pro,
        'biaya_rutin_bulanan' => $Sum_by_rutin,
        'insert_by' => 'system',
        'insert_date' => date('Y-m-d H:i:s')
    );
    $sqlInsertHead = "INSERT INTO laporan_per_bulan
                            (`date`,est_material,est_harga,real_material,real_harga,
                            direct_labour,indirect_labour,machine,mould_mandrill,consumable,
                            foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs)
                            VALUE
                            ('". $date."','".$Sum_est_mat."','".$Sum_est_harga."','".$Sum_real_mat."',
                            '".$Sum_real_harga."','".$Sum_direct_labour."','".$Sum_in_labour."',
                            '".$Sum_machine."','".$Sum_mould_mandrill."','".$Sum_consumable."',
                            '".$Sum_foh_consumable."','".$Sum_foh_depresiasi."','".$Sum_by_gaji."',
                            '".$Sum_by_non_pro."','".$Sum_by_rutin."','system','".date('Y-m-d H:i:s')."','".$Sum_man_hours."','".$Sum_real_harga_rp."','".$kurs."')
                        ";
        // echo $sqlInsertHead."<br>";
    // $koneksi->query($sqlInsertHead);

        // $sqlInsertStatus = "INSERT INTO laporan_status 
        //                         (`date`,`status`,insert_by, insert_date)
        //                         VALUE
        //                         ('".$date."','SUCCESS','system','".date('Y-m-d H:i:s')."')   
        //                     ";
        // $koneksi->query($sqlInsertStatus);
        echo "Success Insert Data";
}
else{
    echo "No Data Insert Data";
}


//WIP
$sqlHeader      = "SELECT a.*, b.id_milik, b.no_spk, b.product_code, b.id_product AS nm_tanki FROM history_pro_header_cron_wip a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' and a.id_product='tanki' ";
$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;

$dateNext = $date;

$kurs=1;
$sqlkurs="select * from ms_kurs where tanggal <='".$dateNext."' and mata_uang='USD' order by tanggal desc limit 1";
$dtkurs	= $koneksi->query($sqlkurs);
if(!empty($dtkurs)) {
	$getkurs	= $dtkurs->fetch_array(MYSQLI_ASSOC);
	$kurs=$getkurs['kurs'];
}

if(!empty($restHeader)){
    // $sqlDel1 = "DELETE FROM laporan_per_bulan WHERE `date`='".$date."' ";
    $sqlDel2 = "DELETE FROM laporan_wip_per_hari WHERE `date`='".$date."' and id_product='tanki' ";
    // $koneksi->query($sqlDel1);
    $koneksi->query($sqlDel2);


    $ArrDay = array();
    $Sum_est_mat        = 0;
    $Sum_est_harga      = 0;
    $Sum_real_mat       = 0;
    $Sum_real_harga     = 0;
    $Sum_real_harga_rp  = 0;
    $Sum_direct_labour  = 0;
    $Sum_in_labour      = 0;
    $Sum_machine        = 0;
    $Sum_mould_mandrill = 0;
    $Sum_consumable     = 0;
    $Sum_foh_consumable = 0;
    $Sum_foh_depresiasi = 0;
    $Sum_by_gaji        = 0;
    $Sum_by_non_pro     = 0;
    $Sum_by_rutin       = 0;
	$Sum_man_hours      = 0;
    foreach($restHeader AS $val=>$valx){

        $sqlEstMaterial = "SELECT SUM(berat) AS est_berat, SUM(berat*price) AS est_price FROM est_material_tanki WHERE id_det='".$valx['id_milik']."' GROUP BY id_det";
        $QEstMat	    = $koneksi->query($sqlEstMaterial);
        $restEstMat	    = $QEstMat->fetch_array(MYSQLI_ASSOC);

        $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;

        $est_material_bef          = (!empty($restEstMat['est_berat']))?$restEstMat['est_berat']:0;
        $est_harga_bef             = (!empty($restEstMat['est_price']))?$restEstMat['est_price']:0;

        $est_material           = $est_material_bef * $jumTot;
        $est_harga              = $est_harga_bef * $jumTot;

        $sqlBy 		= " SELECT
                            a.dia_lebar AS diameter,
                            a.panjang AS diameter2,
                            a.t_dsg AS pressure,
                            a.t_est AS liner,
                            $est_harga AS sum_price,
                            a.man_hours AS man_hours,
                            (a.man_hours * a.pe_direct_labour) AS direct_labour,
                            (a.man_hours * a.pe_indirect_labour) AS indirect_labour,
                            (a.t_time * a.pe_machine) AS machine,
                            0 AS mould_mandrill,
                            ($est_material * a.pe_consumable) AS consumable,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_foh_consumable / 100 ) AS foh_consumable,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_foh_depresiasi / 100 ) AS foh_depresiasi,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_gaji_non_produksi / 100 ) AS biaya_gaji_non_produksi,
                            (
                                    ((a.man_hours * a.pe_direct_labour)+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_non_produksi / 100 ) AS biaya_non_produksi,
                            (
                                    (((a.man_hours * a.pe_direct_labour))+(a.man_hours * a.pe_indirect_labour)+(a.t_time * a.pe_machine)+($est_material * a.pe_consumable))+ $est_harga 
                            ) * ( a.pe_biaya_rutin_bulanan / 100 ) AS biaya_rutin_bulanan 
                        FROM
                                bq_detail_detail a
                        WHERE a.id='".$valx['id_milik']."' LIMIT 1";
        // echo $sqlBy."<br>";
        $Q_By		= $koneksi_tanki->query($sqlBy);
        $restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
        
        $sqlBan         = " SELECT 
                                SUM(a.material_terpakai) AS real_material, 
                                SUM(a.material_terpakai*b.price) AS real_harga 
                            FROM 
                                production_real_detail a
                                INNER JOIN est_material_tanki b ON a.id_detail=b.id
                            WHERE a.id_production_detail='".$valx['id_production_detail']."' 
                            GROUP BY a.id_production_detail";
        $Q_ByBan	    = $koneksi->query($sqlBan);
        $restBan	    = $Q_ByBan->fetch_array(MYSQLI_ASSOC);
        
        $real_material          = (!empty($restBan['real_material']))?$restBan['real_material']:0;
        $real_harga             = (!empty($restBan['real_harga']))?$restBan['real_harga']:0;
        $real_harga_rp          = $real_harga * $kurs;

        $Sum_est_mat        += $est_material;
        $Sum_est_harga      += $est_harga;
        $Sum_real_mat       += $real_material;
        $Sum_real_harga     += $real_harga;
        $Sum_real_harga_rp  += $real_harga_rp;
        $Sum_direct_labour  += $restBy['direct_labour'] * $jumTot;
        $Sum_in_labour      += $restBy['indirect_labour'] * $jumTot;
        $Sum_machine        += $restBy['machine'] * $jumTot;
        $Sum_mould_mandrill += $restBy['mould_mandrill'] * $jumTot;
        $Sum_consumable     += $restBy['consumable'] * $jumTot;
        $Sum_foh_consumable += $restBy['foh_consumable'] * $jumTot;
        $Sum_foh_depresiasi += $restBy['foh_depresiasi'] * $jumTot;
        $Sum_by_gaji        += $restBy['biaya_gaji_non_produksi'] * $jumTot;
        $Sum_by_non_pro     += $restBy['biaya_non_produksi'] * $jumTot;
        $Sum_by_rutin       += $restBy['biaya_rutin_bulanan'] * $jumTot;
		$Sum_man_hours       += $restBy['man_hours'] * $jumTot;
        
        $sqlInsertDet = "INSERT INTO laporan_wip_per_hari
                            (id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
                            qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
                            real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
                            consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs,no_spk,no_so)
                            VALUE
                            ('".$valx['id_produksi']."','".$valx['nm_tanki']."','".$valx['id_product']."',
                            '".$restBy['diameter']."','".$restBy['diameter2']."','0',
                            '0','".$valx['status_date']."','".$valx['product_ke']."',
                            '".$valx['qty_akhir']."','".$valx['qty']."','".date('Y-m-d',strtotime($valx['status_date']))."','".$valx['id_production_detail']."',
                            '".$valx['id_milik']."','".$est_material."','".$est_harga."',
                            '".$real_material."','".$real_harga."','".$restBy['direct_labour'] * $jumTot."',
                            '".$restBy['indirect_labour'] * $jumTot."','".$restBy['machine'] * $jumTot."',
                            '".$restBy['mould_mandrill'] * $jumTot."','".$restBy['consumable'] * $jumTot."',
                            '".$restBy['foh_consumable'] * $jumTot."','".$restBy['foh_depresiasi'] * $jumTot."',
                            '".$restBy['biaya_gaji_non_produksi'] * $jumTot."','".$restBy['biaya_non_produksi'] * $jumTot."',
                            '".$restBy['biaya_rutin_bulanan'] * $jumTot."','system','".date('Y-m-d H:i:s')."','".$restBy['man_hours'] * $jumTot."','".$real_harga_rp."','".$kurs."','".$valx['no_spk']."','".substr($valx['product_code'],0,9)."')
                        ";
        // echo $sqlInsertDet."<br>";
        // exit;
        $koneksi->query($sqlInsertDet);
    }
    // exit;
    $ArrDayMonth = array(
        'date' => $date,
        'est_material' => $Sum_est_mat,
        'est_harga' => $Sum_est_harga,
        'real_material' => $Sum_real_mat,
        'real_harga' => $Sum_real_harga,
        'real_harga_rp' => $Sum_real_harga_rp,
        'kurs' => $kurs,
        'direct_labour' => $Sum_direct_labour,
        'indirect_labour' => $Sum_in_labour,
        'machine' => $Sum_machine,
        'mould_mandrill' => $Sum_mould_mandrill,
        'consumable' => $Sum_consumable,
        'foh_consumable' => $Sum_foh_consumable,
        'foh_depresiasi' => $Sum_foh_depresiasi,
        'biaya_gaji_non_produksi' => $Sum_by_gaji,
        'biaya_non_produksi' => $Sum_by_non_pro,
        'biaya_rutin_bulanan' => $Sum_by_rutin,
        'insert_by' => 'system',
        'insert_date' => date('Y-m-d H:i:s')
    );
    $sqlInsertHead = "INSERT INTO laporan_wip_per_bulan
                            (`date`,est_material,est_harga,real_material,real_harga,
                            direct_labour,indirect_labour,machine,mould_mandrill,consumable,
                            foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs)
                            VALUE
                            ('". $date."','".$Sum_est_mat."','".$Sum_est_harga."','".$Sum_real_mat."',
                            '".$Sum_real_harga."','".$Sum_direct_labour."','".$Sum_in_labour."',
                            '".$Sum_machine."','".$Sum_mould_mandrill."','".$Sum_consumable."',
                            '".$Sum_foh_consumable."','".$Sum_foh_depresiasi."','".$Sum_by_gaji."',
                            '".$Sum_by_non_pro."','".$Sum_by_rutin."','system','".date('Y-m-d H:i:s')."','".$Sum_man_hours."','".$Sum_real_harga_rp."','".$kurs."')
                        ";
        // echo $sqlInsertHead."<br>";
    // $koneksi->query($sqlInsertHead);
        echo "Success Insert Data";
}
else{
    echo "No Data Insert Data";
}

?>