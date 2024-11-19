<?php

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php"; 
	$mpdf=new mPDF('utf-8','A4');
	$mpdf->defaultheaderline=0;
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');
	
	$data_iden	= $this->db->get('identitas')->result();
	$data_header	= $this->db->get_where('tran_material_po_header',array('no_po'=>$no_po))->result();
	
	$rest_detail	= $this->db->get_where('tran_material_po_detail',array('no_po'=>$no_po))->result_array();
	if($data_header[0]->status != 'DELETED'){
		$rest_detail	= $this->db->get_where('tran_material_po_detail',array('no_po'=>$no_po,'deleted'=>'N'))->result_array();
	}

	$mata_uang = (!empty($data_header[0]->mata_uang))?$data_header[0]->mata_uang:'';

	$HTML_HEADER = "<table class='gridtable2' border='0' width='100%' cellpadding='2'>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_company' colspan='3' style='padding-left:50px;'>".$data_iden[0]->nama_resmi."</td>";
			$HTML_HEADER .= "<td class='header_style_company bold' colspan='3'>PURCHASE ORDER</td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris1)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat' width='15%'>PO No.</td>";
			$HTML_HEADER .= "<td class='header_style_alamat' width='1%'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>".$no_po."</td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris2)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>Order Date</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>".(($data_header[0]->approval1_date!="0000-00-00" && $data_header[0]->approval1_date != null)?date('d F Y',strtotime($data_header[0]->approval1_date)):"")."</td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris3)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>PIC</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>".ucfirst(strtolower(get_name('users','nm_lengkap','username',$data_header[0]->updated_by)))."</td>";
		$HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>Date Required</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>".date('d F Y',strtotime($data_header[0]->tgl_dibutuhkan))."</td>";
		$HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat' width='15%'>NPWP</td>";
            $HTML_HEADER .= "<td class='header_style_alamat' width='1%'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat' width='38%'>".strtoupper($data_header[0]->npwp)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>Term Of Payment</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(strtolower($data_header[0]->top))."</td>";
		$HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Phone No</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper($data_header[0]->phone)."</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Remarks</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(strtolower($data_header[0]->remarks))."</td>";
        $HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>To</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier))."</td>";
            $HTML_HEADER .= "<td class='header_style_alamat' rowspan='3'>Ship To</td>";
            $HTML_HEADER .= "<td class='header_style_alamat' rowspan='3'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat' rowspan='3'>".$data_iden[0]->nama_resmi."<br>".strtoupper($data_iden[0]->alamat_baris1).", ".strtoupper($data_iden[0]->alamat_baris2).", ".strtoupper($data_iden[0]->alamat_baris3)."</td>";
        $HTML_HEADER .= "</tr>";
        // $HTML_HEADER .= "<tr>";
        //     $HTML_HEADER .= "<td class='header_style_alamat'>Attn.</td>";
        //     $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
        //     $HTML_HEADER .= "<td class='header_style_alamat'></td>";
        // $HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Address</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(get_name('supplier','alamat','id_supplier',$data_header[0]->id_supplier))."</td>";
        $HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Information</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(get_name('supplier','keterangan','id_supplier',$data_header[0]->id_supplier))."</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
        $HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Phone No. / Contact</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(get_name('supplier','telpon','id_supplier',$data_header[0]->id_supplier))." / ".ucwords(strtolower(get_name('supplier','cp','id_supplier',$data_header[0]->id_supplier)))."</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
        $HTML_HEADER .= "</tr>";
        $HTML_HEADER .= "<tr>";
            $HTML_HEADER .= "<td class='header_style_alamat'>Fax No / Email</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>:</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'>".strtoupper(get_name('supplier','fax','id_supplier',$data_header[0]->id_supplier))." / ".strtolower(get_name('supplier','email','id_supplier',$data_header[0]->id_supplier))."</td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
        $HTML_HEADER .= "</tr>";
	$HTML_HEADER .= "</table>";
	// echo "<br>";
	$HTML_HEADER .= "<table class='gridtable2' width='100%' border='0' cellpadding='2' style='margin-top:5px;margin-bottom:5px;'>";
		$HTML_HEADER .= "<tbody>";
			$HTML_HEADER .= "<tr>";
				$HTML_HEADER .= "<td width='15%'>INCOTERMS</td>";
				$HTML_HEADER .= "<td width='1%'>:</td>";
				$HTML_HEADER .= "<td>".strtoupper($data_header[0]->incoterms)."</td>";
			$HTML_HEADER .= "</tr>";
			// $HTML_HEADER .= "<tr>";
			// 	$HTML_HEADER .= "<td>REQUEST DATE</td>";
			// 	$HTML_HEADER .= "<td>:</td>";
			// 	$HTML_HEADER .= "<td>".date('d F Y', strtotime($data_header[0]->request_date))."</td>";
			// $HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "</tbody>";
	$HTML_HEADER .= "</table>";
	// echo "<br>";
	echo "<table class='gridtable' width='100%' border='1' cellpadding='0' cellspacing='0' style='margin-top:10px;'>";
		echo "<thead>";
			echo "<tr>";
				echo "<th style='text-align: center' width='5%'>NO</th>";
				echo "<th style='text-align: left'>DESCRIPTION</th>";
				echo "<th style='text-align: right' width='10%'>QTY</th>";
				echo "<th style='text-align: center' width='10%'>UOM</th>";
				echo "<th style='text-align: right' width='15%'>UNIT PRICE</th>";
				echo "<th style='text-align: right' width='19%' colspan='2'>AMOUNT</th>";
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
					echo "<td align='center'>".$no."</td>";
					echo "<td>".strtoupper($nm_material)."</td>";
					echo "<td align='right'>".number_format($valx2['qty_purchase'])."</td>";
					echo "<td align='center'>".strtoupper($satuan)."</td>";
					echo "<td align='right'>".number_format($valx2['net_price'],2)."</td>";
					echo "<td align='center' width='5%' style='border-right: hidden;'>".$mata_uang."</td>";
					echo "<td align='right' width='14%'>".number_format($valx2['total_price'],2)."</td>";
				echo "</tr>";
			}
			$max = 2;
			$sisa = $max - $no;
			// for($a=1; $a<=$sisa; $a++){
			// 	echo "<tr>";
			// 		echo "<td>&nbsp;</td>";
			// 		echo "<td></td>";
			// 		echo "<td></td>";
			// 		echo "<td></td>";
			// 		echo "<td></td>";
			// 		echo "<td></td>";
			// 	echo "</tr>";
			// }
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>TOTAL</b></td>";
				echo "<td align='center' style='border-right: hidden; font-weight:bold;'>".$mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_po,2)."</b></td>";
			echo "</tr>";
            if($data_header[0]->discount > 0){
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>DISCOUNT (".number_format($data_header[0]->discount,2)." %)</b></td>";
				echo "<td align='center' style='border-right: hidden; font-weight:bold;'>".$mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_po * $data_header[0]->discount / 100,2)."</b></td>";
			echo "</tr>";
            }
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>NET PRICE</b></td>";
				echo "<td align='center' style='border-right: hidden; font-weight:bold;'>".$mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->net_price,2)."</b></td>";
			echo "</tr>";
			if($data_header[0]->tax > 0){
            echo "<tr>";
				echo "<td align='right' colspan='5'><b>TAX (".number_format($data_header[0]->tax,1)." %)</b></td>";
				echo "<td align='center' style='border-right: hidden; font-weight:bold;'>".$mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->net_price * $data_header[0]->tax / 100,2)."</b></td>";
			echo "</tr>";
			}
/*
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>DELIVERY COST</b></td>";
				echo "<td align='right'><b>".number_format($data_header[0]->delivery_cost,2)."</b></td>";
			echo "</tr>";
*/
			echo "<tr>";
				echo "<td align='right' colspan='5'><b>GRAND TOTAL</b></td>";
				echo "<td align='center' style='border-right: hidden; font-weight:bold;'>".$mata_uang."</td>";
				echo "<td align='right'><b>".number_format($data_header[0]->total_price,2)."</b></td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	// echo "<p class='bold'>Amount in Words</p>"; 
$satuan = "Rupiah";
if($data_header[0]->mata_uang == 'USD'){
	$satuan = "Dollars";
}

if(!empty($data_header[0]->amount_words)){
	$satuan = $data_header[0]->amount_words;
}

	echo "<table class='gridtable2' width='100%' border='1' cellpadding='2' style='margin-top:5px;'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td><p class='bold'>Amount in Words :<br><u>".ucwords(numberTowords($data_header[0]->total_price))." ".$satuan."</u></p></td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td><p>Please Confirm this Purchase Order by Email To : purchasing@ori.co.id</p></td>";
			echo "</tr>";
		echo "</tbody>";
	echo "</table>";
	// echo "<p>Please Confirm this Purchase Order by Email To : purchasing@ori.co.id</p>";
	echo "<table class='gridtable4' width='100%' border='0' cellpadding='2'>";
		echo "<tbody>";
			echo "<tr>";
				echo "<td width='25%'></td>";
				echo "<td width='25%'></td>";
				echo "<td width='25%'></td>";
				echo "<td align='right' width='25%'>VENDOR CONFIRMATION<br>Sign and Stamp,</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td height='20px;'>&nbsp;</td>";
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
	// echo "<pagebreak />";
	echo "<hr>";
    echo "<table class='gridtable5' width='100%' border='0'>";
		echo "<tbody>";
            echo "<tr>";
                echo "<td colspan='2' style='font-size:8px;font-weight:bold;text-align:center;'>
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
		.bold{
			font-weight: bold;
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
		p{
			font-family: verdana,arial,sans-serif;
			font-size:10px;
		}
		
		table.gridtable {
			font-family: verdana,arial,sans-serif;
			font-size:11 px;
			border-collapse: collapse;
		}
		table.gridtable th {
			padding: 3px;
		}
		table.gridtable th.head {
			padding: 3px;
		}
		table.gridtable td {
			padding: 3px;
		}
		table.gridtable td.cols {
			padding: 3px;
		}

		table.gridtable2 {
			font-family: verdana,arial,sans-serif;
			font-size:11px;
			color:#000000;
			border-collapse: collapse;
		}
		table.gridtable2 th {
			padding: 1px;
		}
		table.gridtable2 th.head {
			padding: 1px;
		}
		table.gridtable2 td {
			border-width: 1px;
			padding: 1px;
		}
		table.gridtable2 td.cols {
			padding: 1px;
		}
		
		table.gridtable4 {
			font-family: verdana,arial,sans-serif;
			font-size:12px;
			color:#000000;
		}
		table.gridtable4 td {
			padding: 1px;
			border-color: #dddddd;
		}
		table.gridtable4 td.cols {
			padding: 1px;
		}

		table.gridtable5 {
			font-family: verdana,arial,sans-serif;
			font-size:8px;
			color:#000000;
		}
		table.gridtable5 td {
			padding: 1px;
			border-color: #dddddd;
		}
		table.gridtable5 td.cols {
			padding: 1px;
		}
	</style>

	<?php
	$html = ob_get_contents();
	// exit;
	ob_end_clean();

	$mpdf->SetWatermarkImage(
			$sroot.'/assets/images/ori_logo2.png',
			1,
			[21,30],
			[0, 0]);
	$mpdf->showWatermarkImage = true;

	$mpdf->SetHeader($HTML_HEADER);
	$mpdf->SetTitle($no_po);
	$mpdf->AddPageByArray([
		'margin-left' => 10,
		'margin-right' => 10,
		'margin-top' => 95,
		'margin-bottom' => 5,
		'default-header-line' => 5,
	]);
	$mpdf->WriteHTML($html);
	$mpdf->Output($no_po.' - '.strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier)).'.pdf' ,'I');


?>
