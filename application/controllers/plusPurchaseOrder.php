<?php
set_time_limit(0);
ob_start();

$Successno			=0;
$ErrorInfo			=0;
$sroot 				= $_SERVER['DOCUMENT_ROOT'];


function print_pemilihan_rfq($Nama_APP, $no_rfq, $koneksi, $printby){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot."/application/libraries/MPDF57/mpdf.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$sql_d	= "SELECT a.* FROM tran_material_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' GROUP BY a.id_material ORDER BY a.id DESC";
	$rest_d	= mysqli_query($conn, $sql_d);

	$sql_sup	= "SELECT a.* FROM tran_material_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' GROUP BY a.id_supplier ORDER BY a.id_supplier ASC";
	$rest_sup	= mysqli_query($conn, $sql_sup);
	$rest_sup2	= mysqli_query($conn, $sql_sup);
	
	$num_sup = mysqli_num_rows($rest_sup);
	?>

	<table class="gridtable2" border='1' width='100%' cellpadding='2'>
		<tr>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>HASIL PEMILIHAN SUPPLIER <?= $no_rfq; ?></h2></b></td>
		</tr>
	</table>
	<br>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th class='mid' rowspan='2'>MATERIAL NAME</th>
				<th class='mid' width='5%' rowspan='2'>PRICE REF</th>
				<?php
				$wid = 60 / $num_sup;
				$wind2 = $wid/3;
				while($supplier = mysqli_fetch_array($rest_sup)){
					echo "<th class='mid' width='".$wid."%' colspan='3'>".$supplier['nm_supplier']."</th>";
				}
				?>
				<th class='mid' width='12%' rowspan='2'>HASIL PILIH</th>
			</tr>
			<tr>
				<?php
				while($supplier2 = mysqli_fetch_array($rest_sup2)){
					echo "<th class='mid' width='".$wind2."%'>Price</th>";
					echo "<th class='mid' width='".$wind2."%'>MOQ</th>";
					echo "<th class='mid' width='".$wind2."%'>Lead Time</th>";
				} 
				?>
			</tr>
		</thead>
		<tbody>
			<?php
            while($result = mysqli_fetch_array($rest_d)){
				$sql2 		= "SELECT id_supplier, nm_supplier FROM tran_material_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' AND a.id_material='".$result['id_material']."' AND a.status='SETUJU' LIMIT 1";
				$rest2		= mysqli_query($conn, $sql2);
				$data_sup	= mysqli_fetch_array($rest2);
				
				$nm_material = $result['nm_material'];
				$satuan = 'KG';
				if($result['category'] == 'acc'){
					$nm_material = get_name_acc($result['id_material']);
					$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$result['idmaterial']);
					if(empty($result['idmaterial'])){
						$nm_material = $result['nm_material'];
					}
				}
				
                echo "<tr>";
					echo "<td class='mid' >".strtoupper($nm_material)."</td>";
					echo "<td align='right' class='mid'>".number_format($result['price_ref'],2)."</td>";
					$rest_sup3	= mysqli_query($conn, $sql_sup);
					while($supplier3 = mysqli_fetch_array($rest_sup3)){
						$sql_d 		= "SELECT a.* FROM tran_material_rfq_detail a WHERE a.no_rfq='".$no_rfq."' AND a.deleted='N' AND a.id_material='".$result['id_material']."' AND a.id_supplier='".$supplier3['id_supplier']."'";
						$rest2T		= mysqli_query($conn, $sql_d);
						$data_supT	= mysqli_fetch_array($rest2T);
						
						echo "<td align='right' class='mid' width='".$wind2."%'>".number_format($data_supT['price_ref_sup'],2)."</td>";
						echo "<td align='right' class='mid' width='".$wind2."%'>".number_format($data_supT['moq'],2)."</td>";
						echo "<td align='center' class='mid' width='".$wind2."%'>".number_format($data_supT['lead_time'])."</td>";
					}
					echo "<td align='left' >".$data_sup['nm_supplier']."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	
	<style type="text/css">
		@page {
			margin-top: 1cm;
			margin-left: 0.5cm;
			margin-right: 0.5cm;
			margin-bottom: 1cm;
		}
		.mid{
			vertical-align: middle !important;
		}
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:9px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable th.head {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}

		table.gridtable2 {
			font-family: verdana,arial,sans-serif;
			font-size:10px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable2 th {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable2 th.head {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable2 td {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable2 td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
	</style>


	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($no_rfq);
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output('hasil_pemilihan_supplier_'.$no_rfq.'.pdf' ,'I');
}

function print_incoming_material($Nama_APP, $kode_trans, $koneksi, $printby, $check){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot."/application/libraries/MPDF57/mpdf.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$sql_d 		= "SELECT * FROM warehouse_adjustment_detail WHERE kode_trans='".$kode_trans."' ";
	$rest_d		= mysqli_query($conn, $sql_d);
	
	$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
	$result_header		= mysqli_query($conn, $sql_header);
	$rest_data 				= mysqli_fetch_array($result_header);
	?>

	<table class="gridtable2" border='1' width='100%' cellpadding='2'>
		<tr>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>PERMINTAAN PENGECEKAN MATERIAL</h2></b></td>
		</tr>
	</table>
	<br>
	<br>
	<table class="gridtable2" width="100%" border='0'>
		<thead>
			<tr>
				<td class="mid" width='15%'>No PO</td>
				<td class="mid" width='2%'>:</td>
				<td class="mid"><?=$rest_data['no_ipp'];?></td>
			</tr>
			<tr>
				<td class="mid">No Transaksi</td>
				<td class="mid">:</td>
				<td class="mid"><?= $kode_trans;?></td>
			</tr>
			<tr>
				<td class="mid">Tanggal Penerimaan</td>
				<td class="mid">:</td>
				<td class="mid"><?= date('d F Y', strtotime($rest_data['created_date']));?></td>
			</tr>
		</thead>
	</table><br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th class="mid" width='5%'>No</th>
				<th class="mid" style='vertical-align:middle;'>Nama Barang</th>
				<th class="mid" width='10%'>Qty Order</th>
                <th class="mid" width='10%'>Qty Diterima</th>
                <th class="mid" width='10%'>Qty Kurang</th>
				<th class="mid" width='15%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			$No=0;
            while($valx = mysqli_fetch_array($rest_d)){ $No++;
			
				$qty_oke 		= number_format($valx['qty_oke'],2);
				$qty_rusak 		= number_format($valx['qty_rusak'],2);
				$keterangan 	= (!empty($valx['keterangan']))?ucfirst($valx['keterangan']):'-';
				$qty_kurang 	= number_format($valx['qty_order'] - $valx['qty_oke'],2);
				if($check == 'check' AND $rest_data['checked'] == 'Y'){
					$qty_oke 		= number_format($valx['check_qty_oke'],2);
					$qty_rusak 		= number_format($valx['check_qty_rusak'],2);
					$keterangan 	= (!empty($valx['check_keterangan']))?ucfirst($valx['check_keterangan']):'-';
					$qty_kurang 	= number_format($valx['qty_order'] - $valx['check_qty_oke'],2);
				}
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['qty_order'],2)."</td>";
					echo "<td align='right'>".$qty_oke."</td>";
					echo "<td align='right'>".$qty_kurang."</td>";
					echo "<td>".$keterangan."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table><br><br><br>
	<table class="gridtable2" width='100%' border='0' cellpadding='2'>
		<tr>
			<td width='75%'></td>
			<td>TTD</td>
			<td></td>
		</tr>
		<tr>
			<td height='45px'></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>Supervisor Gudang</td>
			<td></td>
		</tr>
	</table>
	
	<style type="text/css">
		@page {
			margin-top: 1cm;
			margin-left: 0.5cm;
			margin-right: 0.5cm;
			margin-bottom: 1cm;
		}
		.mid{
			vertical-align: middle !important;
		}
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:9px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable th.head {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}

		table.gridtable2 {
			font-family: verdana,arial,sans-serif;
			font-size:10px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable2 th {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable2 th.head {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable2 td {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable2 td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
	</style>


	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($kode_trans."/".date('ymdhis', strtotime($dated))); 
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output('incoming material '.$kode_trans.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');
}

function print_request_material($Nama_APP, $kode_trans, $koneksi, $printby, $check,$ArrGetSO,$ArrGetSPK,$ArrGetIPP){

	$KONN = array(
		'user' => $koneksi['hostuser'],
		'pass' => $koneksi['hostpass'],
		'db'   => $koneksi['hostdb'],
		'host' => $koneksi['hostname']
	);

	$conn = mysqli_connect($KONN['host'],$KONN['user'],$KONN['pass']);
	mysqli_select_db($conn, $KONN['db']);

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	// include $sroot."/application/libraries/MPDF57/mpdf.php";
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$sql_d 		= "SELECT * FROM warehouse_adjustment_detail WHERE kode_trans='".$kode_trans."' ";
	$rest_d		= mysqli_query($conn, $sql_d);
	
	$sql_header 		= "SELECT * FROM warehouse_adjustment WHERE kode_trans='".$kode_trans."' ";
	$result_header		= mysqli_query($conn, $sql_header);
	$rest_data 			= mysqli_fetch_array($result_header);
	$NO_SPK 			= $rest_data['no_spk'];
	$TGL_PLANNING_ 		= $rest_data['tanggal'];
	
	$NO_SO = "";
	if($rest_data['no_ipp'] != 'resin mixing'){
	$NO_SO = $ArrGetSO['BQ-'.$rest_data['no_ipp']];
	}
	
	if($rest_data['no_ipp'] == 'resin mixing'){
		$SQL 			= "SELECT id_milik FROM production_spk_parsial WHERE kode_spk='".$rest_data['kode_spk']."' AND  created_date='".$rest_data['created_date']."' AND  spk='1' ";
		$REST_SQL		= mysqli_query($conn, $SQL);
		// echo '<pre>';
		// print_r($get_detail_spk2); exit;
		$ArrNo_SPK = [];
		$ArrNo_SO = [];
		while($value = mysqli_fetch_array($REST_SQL)){
			$ArrNo_SPK[] 	= $ArrGetSPK[$value['id_milik']];
			$ArrNo_SO[] 	= $ArrGetSO[$ArrGetIPP[$value['id_milik']]];
		}

		$NO_SO = implode(', ',array_unique($ArrNo_SO));
		$NO_SPK = implode(', ',array_unique($ArrNo_SPK));
	}
	$id_milik = get_name('production_detail','id_milik','no_spk',$NO_SPK);
	$TGL_PLANNING = (!empty($TGL_PLANNING_))?date('d-M-Y',strtotime($TGL_PLANNING_)):'';
	$QTY_SPK = (!empty($rest_data['qty_spk']))?'( Qty: '.number_format($rest_data['qty_spk']).')':'';
	?>

	<table class="gridtable2" border='1' width='100%' cellpadding='2'>
		<tr>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>SURAT PERMINTAAN MATERIAL</h2></b></td>
		</tr>
	</table>
	<br>
	<br>
	<table class="gridtable2" width="100%" border='0'>
		<thead>
			<tr>
				<td class="mid" width='15%'>Dari Gudang</td>
				<td class="mid" width='2%'>:</td>
				<td class="mid" width='25%'><?= get_name('warehouse', 'nm_gudang', 'id', $rest_data['id_gudang_dari']);?></td>
				<td class="mid" width='15%'>Ke Gudang</td>
				<td class="mid" width='2%'>:</td>
				<td class="mid" width='41%'><?= get_name('warehouse', 'nm_gudang', 'id', $rest_data['id_gudang_ke']);?></td>
			</tr>
			<tr>
				<td class="mid">No Transaksi</td>
				<td class="mid">:</td>
				<td class="mid"><?= $kode_trans;?></td>
				<td class="mid">No SO / Project</td>
				<td class="mid">:</td>
				<td class="mid"><?= $NO_SO;?> / <?=strtoupper(get_name('production','project','no_ipp',$rest_data['no_ipp']));?></td>
			</tr>
			<tr>
				<td class="mid">Tanggal Request</td>
				<td class="mid">:</td>
				<td class="mid"><?= date('d F Y', strtotime($rest_data['created_date']));?></td>
				<td class="mid">No SPK</td>
				<td class="mid">:</td>
				<td class="mid"><?= $NO_SPK.$QTY_SPK;?></td>
			</tr>
			<tr>
				<td class="mid">Product / Spec</td>
				<td class="mid">:</td>
				<td class="mid"><?=strtoupper(get_name('so_detail_header','id_category','id',$id_milik)).' / '.strtoupper(spec_bq2($id_milik));?></td>
				<td class="mid">Tgl Planning</td>
				<td class="mid">:</td>
				<td class="mid"><?= $TGL_PLANNING;?></td>
			</tr>
		</thead>
	</table><br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th class="mid" width='4%'>No</th>
				<th class="mid" style='vertical-align:middle;'>Material Name</th>
				<th class="mid" style='vertical-align:middle;'>Category</th>
                <th class="mid" width='8%'>Est (Kg)</th>
                <th class="mid" width='8%'>Sisa Request (Kg)</th>
                <th class="mid" width='8%'>Total Request (Kg)</th>
                <th class="mid" width='8%'>Request (Kg)</th>
                <th class="mid" width='8%'>Aktual (Kg)</th>
				<th class="mid" width='15%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			$No=0;
            while($valx = mysqli_fetch_array($rest_d)){ $No++;
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td align='right'>".number_format($valx['qty_est'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_sisa'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_total_req'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_oke'],4)."</td>";
					echo "<td align='right'>".number_format($valx['check_qty_oke'],4)."</td>";
					echo "<td>".strtoupper($valx['keterangan'])."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table><br><br><br>
	<table class="gridtable2" width='100%' border='0' cellpadding='2'>
		<tr>
			<td width='65%'></td>
			<td>Disiapkan,</td>
			<td></td>
			<td>Penerima,</td>
			<td></td>
		</tr>
		<tr>
			<td height='45px'></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td>_________________</td>
			<td></td>
			<td>_________________</td>
			<td></td>
		</tr>
	</table>
	
	<style type="text/css">
		@page {
			margin-top: 1cm;
			margin-left: 0.5cm;
			margin-right: 0.5cm;
			margin-bottom: 1cm;
		}
		.mid{
			vertical-align: middle !important;
		}
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable th {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable th.head {
			border-width: 1px;
			padding: 8px;
			border-style: solid;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable td {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: solid;
			border-color: #666666;
			background-color: #ffffff;
		}

		table.gridtable2 {
			font-family: verdana,arial,sans-serif;
			font-size:14;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable2 th {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable2 th.head {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable2 td {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable2 td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
	</style>


	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today." / ".$kode_trans."</i></p>";
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($kode_trans."/".date('ymdhis', strtotime($dated))); 
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output('request material '.$kode_trans.'/'.date('ymdhis', strtotime($dated)).'.pdf' ,'I');
}


?>
