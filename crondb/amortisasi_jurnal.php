<?php 
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 			= new database_ORI();
$koneksi 		= $db1->connect();
define('DBACC', 'gl');

//cek if end of month
$tanggal_exe=date("Y-m-t");
if(date("Y-m-d")!=$tanggal_exe) die("Bukan akhir bulan");

$bulan=date("m");
$tahun=date("Y");

$sqlHeader	= "select * from amortisasi_generate WHERE bulan='".$bulan."' and tahun='".$tahun."' and kd_asset in (select kd_asset from amortisasi where status=1)";
$Q_Awal	= $koneksi->query($sqlHeader);

//echo $sqlHeader."<hr>";

$ArrJurnal = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$ArrJurnal[] = $row;

if(!empty($ArrJurnal)){
	$det_Jurnaltes1=array();
	$jenis_jurnal = 'AMORTISASI';
	$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
	$payment_date=date("Y-m-d");
	foreach($ArrJurnal AS $val => $valx){
		$dtrow	= $koneksi->query("select a.*,b.coa as cat_coa from amortisasi a left join amortisasi_category b on a.category=b.id  WHERE kd_asset='".$valx["kd_asset"]."'");

//		echo "select a.*,b.coa as cat_coa from amortisasi a left join amortisasi_category b on a.category=b.id  WHERE kd_asset='".$valx["kd_asset"]."'<hr>";

		if(!empty($dtrow)) {
			$result	= $dtrow->fetch_array(MYSQLI_ASSOC);
		}

		$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, no_reff, jenis_jurnal, nocust)
		VALUE 
		('".$nomor_jurnal."','".$payment_date."','JV','".$result['coa']."','Amortisasi ".$result['nm_asset'].",".$tahun."-".$bulan."','".$result['kd_asset']."','".$result['value']."','0','".$result['kd_asset']."','".$jenis_jurnal."','')";
		$koneksi->query($sqlinsert);

//		echo $sqlinsert.'<hr>';

		$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, no_reff, jenis_jurnal, nocust)
		VALUE 
		('".$nomor_jurnal."','".$payment_date."','JV','".$result['cat_coa']."','Amortisasi ".$result['nm_asset'].",".$tahun."-".$bulan."','".$result['kd_asset']."','0','".$result['value']."','".$result['kd_asset']."','".$jenis_jurnal."','')";
		$koneksi->query($sqlinsert);

//		echo $sqlinsert.'<hr>';

		$koneksi->query("update amortisasi_generate set flag='Y' WHERE kd_asset='".$valx["kd_asset"]."' and nomor='".$valx["nomor"]."'");

//		echo "update amortisasi_generate set flag='Y' WHERE kd_asset='".$valx["kd_asset"]."' and nomor='".$valx["nomor"]."'"."<hr>";

	}

	$nocab	= 'A';
	$Cabang	= '101';
	$bulan_Proses	= date('Y',strtotime($payment_date));
	$Urut			= 1;
	$Pros_Cab		= $koneksi->query("SELECT subcab,nomorJC FROM ".DBACC.".pastibisa_tb_cabang WHERE nocab='".$Cabang."' limit 1");
	$det_Cab		= $Pros_Cab->fetch_array(MYSQLI_ASSOC); 
	if($det_Cab){
		$nocab		= $det_Cab['subcab'];
		$Urut		= intval($det_Cab['nomorJC']) + 1;
	}
	$Format			= $Cabang.'-'.$nocab.'JV'.date('y',strtotime($payment_date));
	$Nomor_JV		= $Format.str_pad($Urut, 5, "0", STR_PAD_LEFT);
	$koneksi->query("UPDATE ".DBACC.".pastibisa_tb_cabang SET nomorJC=(nomorJC + 1),lastupdate='".date("Y-m-d")."' WHERE nocab='".$Cabang."'");


	$Bln	= substr($payment_date,5,2);
	$Thn	= substr($payment_date,0,4);
	$Q_Detail = $koneksi->query("select * from jurnaltras where jenis_jurnal='".$jenis_jurnal."' and stspos='0' and nomor='".$nomor_jurnal."'");
	$DtJurnal = array();
	while($rowjurnal  = $Q_Detail->fetch_array(MYSQLI_ASSOC))
	$DtJurnal[] = $rowjurnal;
       $total = 0;
	foreach($DtJurnal AS $keys => $vals){
		
		$total += $vals["kredit"];
		$sqlinsert="insert into ".DBACC.".jurnal (nomor, tipe, tanggal, no_reff, no_perkiraan, keterangan, debet, kredit )
		VALUE 
		('".$Nomor_JV."','JV','".$payment_date."','".$vals["no_request"]."','".$vals["no_perkiraan"]."','".$vals["keterangan"]."','".$vals["debet"]."','".$vals["kredit"]."')";
		$koneksi->query($sqlinsert);
	}

	$sqlinsert2="insert into ".DBACC.".javh (nomor, tgl, jml, kdcab, jenis, keterangan, bulan, tahun,memo, user_id, ho_valid )
	VALUE 
	('".$Nomor_JV."','".$payment_date."','".$total."','101','JV','Amortisasi ".$Bln." - ".$Thn."','".$Bln."','".$Thn."','memo','system','')";
	$koneksi->query($sqlinsert2);
	
	$koneksi->query("update jurnaltras set stspos='1'  WHERE jenis_jurnal='".$jenis_jurnal."' and nomor='".$nomor_jurnal."'");


}

?>