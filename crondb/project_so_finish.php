<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

//echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
$sqlTrunc = "TRUNCATE TABLE group_so_cost_project_finish_fast_table";
$koneksi->query($sqlTrunc);

$sqlHeader      = "SELECT
						a.no_ipp AS no_ipp,
						a.estimasi AS estimasi,
						a.rev AS rev,
						a.order_type AS order_type,
						a.nm_customer AS nm_customer,
						a.sts_ipp AS sts_ipp,
						IF(a.est_mat IS NULL, 0, a.est_mat) AS est_mat,
						IF(a.est_mat IS NULL, 0, a.est_harga) AS est_harga,
						IF(a.est_mat IS NULL, 0, a.real_material) AS real_material,
						IF(a.est_mat IS NULL, 0, a.real_harga) AS real_harga,
						IF(a.est_mat IS NULL, 0, a.persenx) AS persenx,
						'system' AS create_by,
						'".date('Y-m-d H:i:s')."' AS create_date
					FROM
						group_so_cost_project_finish_fast a
					WHERE a.no_ipp NOT IN 
						('IPP19032L',
						'IPP19046L',
						'IPP19060E',
						'IPP19061E',
						'IPP19062L',
						'IPP19063L',
						'IPP19087E',
						'IPP19090E',
						'IPP19157L',
						'IPP19192L',
						'IPP19247L',
						'IPP19260E',
						'IPP19263E',
						'IPP19266E',
						'IPP19271E',
						'IPP19275E',
						'IPP19279L',
						'IPP19292E',
						'IPP19310L',
						'IPP19333L',
						'IPP19341E',
						'IPP19363L',
						'IPP19382L',
						'IPP19399E',
						'IPP19401E',
						'IPP19410E',
						'IPP19415E',
						'IPP19421L',
						'IPP19439E')";
$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;


foreach($restHeader AS $val=>$valx){
	$sqlInsertDet = "INSERT INTO 
						group_so_cost_project_finish_fast_table 
						( 
							no_ipp, 
							estimasi, 
							rev, 
							order_type, 
							nm_customer, 
							sts_ipp, 
							est_mat, 
							est_harga, 
							real_material, 
							real_harga, 
							persenx, 
							create_by, 
							create_date
						)
						VALUE
						(
							'".$valx['no_ipp']."',
							'".$valx['estimasi']."',
							'".$valx['rev']."',
							'".$valx['order_type']."',
							'".$valx['nm_customer']."',
							'".$valx['sts_ipp']."',
							'".$valx['est_mat']."',
							'".$valx['est_harga']."',
							'".$valx['real_material']."',
							'".$valx['real_harga']."',
							'".$valx['persenx']."',
							'".$valx['create_by']."',
							'".$valx['create_date']."'
						)
                        ";
        // echo $sqlInsertDet."<br>";
        $koneksi->query($sqlInsertDet);
}


// $sqlInsertHead = "
                    // INSERT INTO 
						// group_so_cost_project_finish_fast_table 
						// ( 
							// no_ipp, 
							// estimasi, 
							// rev, 
							// order_type, 
							// nm_customer, 
							// sts_ipp, 
							// est_mat, 
							// est_harga, 
							// real_material, 
							// real_harga, 
							// persenx, 
							// create_by, 
							// create_date
						// ) 
						// SELECT
							// a.no_ipp,
							// a.estimasi,
							// a.rev,
							// a.order_type,
							// a.nm_customer,
							// a.sts_ipp,
							// IF(a.est_mat IS NULL, 0, a.est_mat) AS est_mat,
							// IF(a.est_mat IS NULL, 0, a.est_harga) AS est_harga,
							// IF(a.est_mat IS NULL, 0, a.real_material) AS real_material,
							// IF(a.est_mat IS NULL, 0, a.real_harga) AS real_harga,
							// IF(a.est_mat IS NULL, 0, a.persenx) AS persenx,
							// 'system',
							// '".date('Y-m-d H:i:s')."'
						// FROM
							// group_so_cost_project_finish_fast a";
// $koneksi->query($sqlInsertHead);


?>