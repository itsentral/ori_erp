<?php

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php"; 
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');
	
	$data_iden	= $this->db->get('identitas')->result();
	$data_header	= $this->db->get_where('tran_material_po_header',array('no_po'=>$no_po))->result();
	
	$rest_detail	= $this->db->get_where('tran_material_po_detail',array('no_po'=>$no_po))->result_array();
	if($data_header[0]->status != 'DELETED'){
		$rest_detail	= $this->db->get_where('tran_material_po_detail',array('no_po'=>$no_po,'deleted'=>'N'))->result_array();
	}
	
	echo "<table class='gridtable2' border='0' width='100%' cellpadding='2'>";
		echo "<tr>";
			echo "<td class='header_style_company' width='65%'>".$data_iden[0]->nama_resmi."</td>";
			echo "<td class='header_style_company bold color_req' colspan='2'>PURCHASE ORDER</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]->alamat_baris1)."</td>";
			echo "<td class='header_style_alamat' width='18%'>Order Nomor</td>";
			echo "<td class='header_style_alamat'>:&nbsp;&nbsp;&nbsp;".$no_po."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]->alamat_baris2)."</td>";
			echo "<td class='header_style_alamat'>Order Date</td>";
			echo "<td class='header_style_alamat'>:&nbsp;&nbsp;&nbsp;".date('d F Y')."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat'>".strtoupper($data_iden[0]->alamat_baris3)."</td>";
			echo "<td class='header_style_alamat'>&nbsp;</td>";
			echo "<td class='header_style_alamat'></td>";
		echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<table border='0' width='100%' cellpadding='0'>";
		echo "<tr>";
			echo "<td width='44%' style='vertical-align:top;'>";
				echo "<table class='default' border='0' width='100%' cellpadding='2'>";
					echo "<tr>";
						echo "<td class='header_style2 bold'>VENDOR</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper(get_name('supplier','alamat','id_supplier',$data_header[0]->id_supplier))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>PHONE ".strtoupper(get_name('supplier','telpon','id_supplier',$data_header[0]->id_supplier))." ,CONTACT ".strtoupper(get_name('supplier','cp','id_supplier',$data_header[0]->id_supplier))."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>EMAIL ".strtoupper(get_name('supplier','email','id_supplier',$data_header[0]->id_supplier))."</td>";
					echo "</tr>";
				echo "</table>";
			echo "</td>";
			echo "<td width='6%'></td>";
			echo "<td width='44%'>";
				echo "<table class='default' width='100%' cellpadding='2'>";
					echo "<tr>";
						echo "<td class='header_style2 bold'>SHIP AND INVOICE TO</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".$data_iden[0]->nama_resmi."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper($data_iden[0]->alamat_baris1)."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper($data_iden[0]->alamat_baris2)."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>".strtoupper($data_iden[0]->alamat_baris3)."</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td>PHONE ".strtoupper($data_iden[0]->no_telp)."</td>";
					echo "</tr>";
				echo "</table>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<table class='gridtable3' width='50%' border='1' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td width='40%'>INCOTERMS</td>";
				echo "<td>".strtoupper($data_header[0]->incoterms)."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>REQUEST DATE</td>";
				echo "<td>".date('d F Y', strtotime($data_header[0]->request_date))."</td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	echo "<br>";
	echo "<table class='gridtable' width='100%' border='0' cellpadding='2'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th style='text-align: left' width='5%'>NO</th>";
				echo "<th style='text-align: left' width='18%'>ITEM CODE</th>";
				echo "<th style='text-align: left'>DESCRIPTION</th>";
				echo "<th style='text-align: right' width='12%'>QUANTITY</th>";
				echo "<th style='text-align: left' width='8%'>UOM</th>";
				echo "<th style='text-align: right' width='12%' colspan='2'>UNIT PRICE</th>";
				echo "<th style='text-align: right' width='12%' colspan='2'>AMOUNT</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			$no = 0;
			$SUM = 0;
			foreach($rest_detail AS $val => $valx2){	
				$no++;
				$SUM += $valx2['price_ref_sup'] * $valx2['qty_purchase'];
				
				$nm_material = get_name('raw_materials','nm_material','id_material',$valx2['id_material']);
				$satuan = get_name('raw_materials','cost_satuan','id_material',$valx2['id_material']);
				if($valx2['category'] == 'acc'){
					$nm_material = get_name_acc($valx2['id_material']);
					$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$valx2['idmaterial']);
				}
				
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td>".strtoupper(get_name('raw_materials','idmaterial','id_material',$valx2['id_material']))."</td>";
					echo "<td>".strtoupper($nm_material)."</td>";
					echo "<td align='right'>".number_format($valx2['qty_purchase'])."</td>";
					echo "<td>".strtoupper($satuan)."</td>";
					echo "<td align='right'>".$data_header[0]->mata_uang."</td>";
					echo "<td align='right'>".number_format($valx2['price_ref_sup'],2)."</td>";
					echo "<td align='right'>".$data_header[0]->mata_uang."</td>";
					echo "<td align='right'>".number_format($valx2['price_ref_sup'] * $valx2['qty_purchase'],2)."</td>";
				echo "</tr>";
			}
			$max = 8;
			$sisa = $max - $no;
			for($a=1; $a<=$sisa; $a++){
				echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			}
			echo "<tr>";
				echo "<td colspan='6'></td>";
				echo "<td align='right'><b>SUBTOTAL</b></td>";
				echo "<td align='right'>".$data_header[0]->mata_uang."</td>";
				echo "<td align='right'><b>".number_format($SUM,2)."</b></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='6'></td>";
				echo "<td align='right'><b>TAX</b></td>";
				echo "<td align='right'>".$data_header[0]->mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->tax/100 * $SUM,2)."</b></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td colspan='6'></td>";
				echo "<td align='right'><b>TOTAL</b></td>";
				echo "<td align='right'>".$data_header[0]->mata_uang."</td>";
				echo "<td align='right'><b>".number_format($SUM - ($data_header[0]->tax/100 * $SUM),2)."</b></td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	// echo "<p class='bold'>Amount in Words</p>"; 
$satuan = "Rupiah";
if($data_header[0]->mata_uang == 'USD'){
	$satuan = "Dollars";
}

	echo "<table class='gridtable3' width='100%' border='1' cellpadding='2' style='margin-top:10px;'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td><p class='bold'>Amount in Words :<br><u>".ucwords(numberTowords($SUM - ($data_header[0]->tax/100 * $SUM)))." ".$satuan."</u></p></td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	// echo "<br>";
	echo "<p class='bold'>TERM AND CONDITIONS</p>"; 
	echo "<table class='gridtable3' width='50%' border='1' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td width='40%'>TERM OF PAYMENT</td>";
				echo "<td>".strtoupper($data_header[0]->top)."</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>REMARKS</td>";
				echo "<td>".strtoupper($data_header[0]->remarks)."</td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	echo "<br>";
	echo "<p>Please Confirm this Purchase Order by Email To : purchasing@ori.co.id</p>";
	echo "<table class='gridtable4' width='100%' border='0' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td width='25%'></td>";
				echo "<td width='25%'></td>";
				echo "<td align='right' width='50%'>VENDOR CONFIRMATION<br>Sign and Stamp,</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td height='30px;'>&nbsp;</td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Buyer</td>";
				echo "<td>Manager</td>";
				echo "<td align='right'>".strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier))."</td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	?>
	<style type="text/css">
	@page {
		margin-top: 0.4 cm;
		margin-left: 0.4 cm;
		margin-right: 0.4 cm;
		margin-bottom: 0.4 cm;
		margin-footer: 0 cm
	}
	
	.bold{
		font-weight: bold;
	}
	
	.color_req{
		color: #0049a8;
	}
	
	.header_style_company{
		padding: 15px;
		color: black;
		font-size: 20px;
	}
	
	.header_style_alamat{
		padding: 10px;
		color: black;
		font-size: 10px;
	}
	
	.header_style2{
		background-color: #0049a8;
		color: white;
		font-size: 10px;
		padding: 8px;
	}
	
	
	
	table.default {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
		padding: 0px;
	}
	
	p{
		font-family: Arial, Helvetica, sans-serif;
		font-size:10px;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
	}
	.font{
		font-family: verdana,arial,sans-serif;
		font-size:14px;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border: 1px solid #dddddd;
		border-collapse: collapse;
	}
	table.gridtable th {
		padding: 8px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable th.head {
		padding: 8px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable tr:nth-child(even) {
		background-color: #f2f2f2;
	}
	table.gridtable td {
		padding: 8px;
	}
	table.gridtable td.cols {
		padding: 8px;
	}


	table.gridtable2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size:9px;
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

	#hrnew {
		border: 0;
		border-bottom: 1px dashed #ccc;
		background: #999;
	}
	
	table.gridtable3 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
		border-width: 1px;
		border-color: #dddddd;
		border-collapse: collapse;
	}
	table.gridtable3 td {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #dddddd;
	}
	table.gridtable3 td.cols {
		border-width: 1px;
		padding: 6px;
		border-style: solid;
		border-color: #dddddd;
	}
	
	table.gridtable4 {
		font-family: verdana,arial,sans-serif;
		font-size:9px;
		color:#333333;
	}
	table.gridtable4 td {
		padding: 3px;
		border-color: #dddddd;
	}
	table.gridtable4 td.cols {
		padding: 3px;
	}
	</style>


	<?php

	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($no_po);
	$mpdf->AddPage();
	$mpdf->WriteHTML($html);
	$mpdf->Output($no_po.' - '.strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier)).' '.date('dmyhis').'.pdf' ,'I');


?>
