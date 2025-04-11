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
			echo "<td class='header_style_company' colspan='3'>".$data_iden[0]->nama_resmi."</td>";
			echo "<td class='header_style_company bold color_req' colspan='3'>PURCHASE ORDER</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat' colspan='3'>".strtoupper($data_iden[0]->alamat_baris1)."</td>";
			echo "<td class='header_style_alamat' width='15%'>PO No.</td>";
			echo "<td class='header_style_alamat' width='1%'>:</td>";
			echo "<td class='header_style_alamat'>".$no_po."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat' colspan='3'>".strtoupper($data_iden[0]->alamat_baris2)."</td>";
			echo "<td class='header_style_alamat'>Order Date</td>";
            echo "<td class='header_style_alamat'>:</td>";
			echo "<td class='header_style_alamat'>".(($data_header[0]->approval1_date!="0000-00-00" && $data_header[0]->approval1_date != null)?date('d F Y',strtotime($data_header[0]->approval1_date)):"")."</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td class='header_style_alamat' colspan='3'>".strtoupper($data_iden[0]->alamat_baris3)."</td>";
			echo "<td class='header_style_alamat'>PIC</td>";
            echo "<td class='header_style_alamat'>:</td>";
			echo "<td class='header_style_alamat'>".ucfirst(strtolower(get_name('users','nm_lengkap','username',$data_header[0]->updated_by)))."</td>";
		echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat' width='15%'>NPWP</td>";
            echo "<td class='header_style_alamat' width='1%'>:</td>";
			echo "<td class='header_style_alamat' width='34%'>".strtoupper($data_header[0]->npwp)."</td>";
			echo "<td class='header_style_alamat'>Date Required</td>";
            echo "<td class='header_style_alamat'>:</td>";
			echo "<td class='header_style_alamat'>".date('d F Y',strtotime($data_header[0]->tgl_dibutuhkan))."</td>";
		echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'>Phone No</td>";
            echo "<td class='header_style_alamat'>:</td>";
			echo "<td class='header_style_alamat'>".strtoupper($data_header[0]->phone)."</td>";
			echo "<td class='header_style_alamat'>Term Of Payment</td>";
            echo "<td class='header_style_alamat'>:</td>";
			echo "<td class='header_style_alamat'>".strtoupper(strtolower($data_header[0]->top))."</td>";
		echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'>Remarks</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(strtolower($data_header[0]->remarks))."</td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'>To</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier))."</td>";
            echo "<td class='header_style_alamat' rowspan='3'>Ship To</td>";
            echo "<td class='header_style_alamat' rowspan='3'>:</td>";
            echo "<td class='header_style_alamat' rowspan='3'>".$data_iden[0]->nama_resmi."<br>".strtoupper($data_iden[0]->alamat_baris1).", ".strtoupper($data_iden[0]->alamat_baris2).", ".strtoupper($data_iden[0]->alamat_baris3)."</td>";
        echo "</tr>";
        // echo "<tr>";
        //     echo "<td class='header_style_alamat'>Attn.</td>";
        //     echo "<td class='header_style_alamat'>:</td>";
        //     echo "<td class='header_style_alamat'></td>";
        // echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'>Address</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(get_name('supplier','alamat','id_supplier',$data_header[0]->id_supplier))."</td>";
        echo "</tr>";
		echo "<tr>";
            echo "<td class='header_style_alamat'>Information</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(get_name('supplier','keterangan','id_supplier',$data_header[0]->id_supplier))."</td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'>Phone No. / Contact</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(get_name('supplier','telpon','id_supplier',$data_header[0]->id_supplier))." / ".ucwords(strtolower(get_name('supplier','cp','id_supplier',$data_header[0]->id_supplier)))."</td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
        echo "</tr>";
        echo "<tr>";
            echo "<td class='header_style_alamat'>Fax No / Email</td>";
            echo "<td class='header_style_alamat'>:</td>";
            echo "<td class='header_style_alamat'>".strtoupper(get_name('supplier','fax','id_supplier',$data_header[0]->id_supplier))." / ".strtolower(get_name('supplier','email','id_supplier',$data_header[0]->id_supplier))."</td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
            echo "<td class='header_style_alamat'></td>";
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
				echo "<th style='text-align: left'>DESCRIPTION</th>";
				echo "<th style='text-align: right' width='12%'>QTY</th>";
				echo "<th style='text-align: center' width='12%'>UOM</th>";
				echo "<th style='text-align: right' width='15%'>UNIT PRICE</th>";
				echo "<th style='text-align: right' width='17%'>AMOUNT</th>";
			echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
			$no = 0;
			$SUM = 0;
			foreach($rest_detail AS $val => $valx2){	
				$no++;
				$SUM += $valx2['price_ref_sup'] * $valx2['qty_purchase'];

				$get_satuan = $this->db->select('satuan')->get_where('tran_material_rfq_detail', array('no_po'=>$no_po,'id_material'=>$valx2['id_material']))->result();
				$satuan_id = (!empty($get_satuan))?$get_satuan[0]->satuan:0;
				
				$nm_material = get_name('raw_materials','nm_material','id_material',$valx2['id_material']);
				$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$satuan_id);
				if($valx2['category'] == 'acc'){
					$nm_material = get_name_acc($valx2['id_material']);
				}
				
				echo "<tr>";
					echo "<td>".$no."</td>";
					echo "<td>".strtoupper($nm_material)."</td>";
					echo "<td align='right'>".number_format($valx2['qty_purchase'])."</td>";
					echo "<td align='center'>".strtoupper($satuan)."</td>";
					echo "<td align='right'>".number_format($valx2['net_price'],2)."</td>";
					echo "<td align='right'>".number_format($valx2['total_price'],2)."</td>";
				echo "</tr>";
			}
			$max = 2;
			$sisa = $max - $no;
			for($a=1; $a<=$sisa; $a++){
				echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
					echo "<td></td>";
				echo "</tr>";
			}
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>TOTAL</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_po,2)."</b></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>DISCOUNT (".number_format($data_header[0]->discount,2)." %)</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_po * $data_header[0]->discount / 100,2)."</b></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>NET PRICE</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->net_price,2)."</b></td>";
			echo "</tr>";
            echo "<tr>";
				echo "<td align='right' colspan='5'><b>TAX (".number_format($data_header[0]->tax,1)." %)</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->net_price * $data_header[0]->tax / 100,2)."</b></td>";
			echo "</tr>";
/*
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>DELIVERY COST</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->delivery_cost,2)."</b></td>";
			echo "</tr>";
*/
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>GRAND TOTAL</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_price,2)."</b></td>";
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
				echo "<td><p class='bold'>Amount in Words :<br><u>".ucwords(numberTowords($data_header[0]->total_price))." ".$satuan."</u></p></td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	echo "<p>Please Confirm this Purchase Order by Email To : purchasing@ori.co.id</p>";
	echo "<table class='gridtable4' width='100%' border='0' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td width='25%'></td>";
				echo "<td width='25%'></td>";
				echo "<td width='25%'></td>";
				echo "<td align='right' width='25%'>VENDOR CONFIRMATION<br>Sign and Stamp,</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td height='30px;'>&nbsp;</td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
				echo "<td></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td>Buyer</td>";
				echo "<td>Manager</td>";
				echo "<td>Director</td>";
				echo "<td></td>";
			echo "</tr>";
            echo "<tr>";
				echo "<td>".strtoupper($data_header[0]->buyer)."</td>";
				echo "<td></td>";
				echo "<td></td>";
                echo "<td align='right'>".strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier))."</td>";
            echo "</tr>";
		echo "</tbody>";
	echo "</table><br>";
	echo "<pagebreak />";
    echo "<table class='gridtableX' width='100%' border='0'>";
		echo "<tbody>";
            echo "<tr>";
                echo "<td colspan='2' style='font-size:10px;font-weight:bold;text-align:center;'>
                    KAMI TIDAK MENERIMA KOMISI/BINGKISAN DALAM BENTUK APA PUN,<br>
                    APABILA ADA PERMINTAAN DARI KARYAWAN KAMI ATAU<br>
                    ADA SARAN & KRITIK UNTUK KAMI, DAPAT MENGHUBUNGI KAMI<br>
                    DI NOMOR 08111 - 466 - 097
                </td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td colspan='2'>Terms & Condition</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td width='4%'>1.</td>";
                echo "<td>Please state our above PO No. on your Delivery Order, Invoice and all correspondence as reference.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>2.</td>";
                echo "<td>Please submit your invoice complete with Beneficiary Name, Bank Account No., SWIFT CODE No.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>3.</td>";
                echo "<td>Our company reserves the right to cancel this PO in case of:</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- The goods supplied is not in confirmity with the spesifications, mentioned in this PO.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Delay in delivery more than 1 (one) week without any acceptable reasons. </td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>4.</td>";
                echo "<td>VENDOR shall responsible to replace free of charge all the good, which might result to defective due to faults in material.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>5.</td>";
                echo "<td>Seller must acknowledge acceptance of this Purchase Order by returning to Buyer a signed Purchase Order within 2 (two) working days.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>6.</td>";
                echo "<td>Acceptable this Purchase Order is Subject to the buyer's standard terms & conditions.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>7.</td>";
                echo "<td>Starting from April, 2013 VAT form must follow the new tax regulation : PER-24/PJ/2012.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>8.</td>";
                echo "<td>Invoice Submission every 02nd & 18th of each month, and maximum 3 (three) months after we release Purchase Order.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>9.</td>";
                echo "<td>Description item on invoice and delivery order similarly with PO.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>10.</td>";
                echo "<td>Invoicing partial not allowed.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>11.</td>";
                echo "<td>Acceptance handover report should be attached with the invoice (Berita Acara Serah Terima Hasil Jasa).</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>12.</td>";
                echo "<td>VENDOR must comply with all provisions of the occupational safety and health quality management system.</td>";
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
		font-size: 11px;
        vertical-align: top !important;
	}
	
	.header_style2{
		background-color: #0049a8;
		color: white;
		font-size: 11px;
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
		font-size:12 px;
		color:#333333;
		border: 1px solid #dddddd;
		border-collapse: collapse;
	}
	table.gridtable th {
		padding: 6px;
		background-color: #0049a8;
		color: white;
		border-color: #0049a8;
		border-style: solid;
		border-width: 1px;
	}
	table.gridtable th.head {
		padding: 6px;
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
		padding: 6px;
	}
	table.gridtable td.cols {
		padding: 6px;
	}


	table.gridtable2 {
		font-family: Arial, Helvetica, sans-serif;
		font-size:12px;
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
		font-size:12px;
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
		font-size:12px;
		color:#333333;
	}
	table.gridtable4 td {
		padding: 3px;
		border-color: #dddddd;
	}
	table.gridtable4 td.cols {
		padding: 3px;
	}

    table.gridtableX {
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border: 1px solid #dddddd;
		border-collapse: collapse;
	}
	table.gridtableX td {
		padding: 4px;
	}
	table.gridtableX td.cols {
		padding: 4px;
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
