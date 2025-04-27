<?php

	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php"; 
	$mpdf=new mPDF('utf-8','A4');
	$mpdf->defaultheaderline=0;

	set_time_limit(0);
	ini_set('memory_limit','1024M');
	
	$data_iden	= $this->db->get('identitas')->result();
	$data_header	= $this->db->get_where('tran_po_header',array('no_po'=>$no_po))->result();
	
	$rest_detail	= $this->db
							->select('a.*, b.kode_satuan AS unit, c.spec')
							->join('raw_pieces b','a.satuan=b.id_satuan','left')
							->join('tran_rfq_detail c','a.no_po=c.no_po AND a.id_barang=c.id_barang','left')
							->get_where('tran_po_detail a',array('a.no_po'=>$no_po))->result_array();
	if($data_header[0]->status != 'DELETED'){
		$rest_detail	= $this->db
							->select('a.*, b.kode_satuan AS unit, c.spec')
							->join('raw_pieces b','a.satuan=b.id_satuan','left')
							->join('tran_rfq_detail c','a.no_po=c.no_po AND a.id_barang=c.id_barang','left')
							->get_where('tran_po_detail a',array('a.no_po'=>$no_po,'a.deleted'=>'N'))->result_array();
	}

	$mata_uang = (!empty($data_header[0]->mata_uang))?$data_header[0]->mata_uang:'';
	
	$HTML_HEADER = "<table class='gridtable2' border='0' width='100%' cellpadding='2'>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_company' colspan='3' style='padding-left:50px;'>".$data_iden[0]->nama_resmi."</td>";
			$HTML_HEADER .= "<td class='header_style_company bold color_req' colspan='3'>PURCHASE ORDER</td>";
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
	// $HTML_HEADER .= "<br>";
	$HTML_HEADER .= "<table class='gridtable2' width='100%' border='0' cellpadding='2' style='margin-top:5px;margin-bottom:5px;'>";
		$HTML_HEADER .= "<tbody>";
			$HTML_HEADER .= "<tr>";
                $HTML_HEADER .= "<td width='15%'>INCOTERMS</td>";
                $HTML_HEADER .= "<td width='1%'>:</td>";
				$HTML_HEADER .= "<td>".strtoupper($data_header[0]->incoterms)."</td>";
			$HTML_HEADER .= "</tr>";
			// $HTML_HEADER .= "<tr>";
            //     $HTML_HEADER .= "<td>REQUEST DATE</td>";
            //     $HTML_HEADER .= "<td>:</td>";
			// 	$REQ_DATE = (!empty($data_header[0]->request_date))?$data_header[0]->request_date:$data_header[0]->tgl_dibutuhkan;
			// 	$HTML_HEADER .= "<td>".date('d F Y', strtotime($REQ_DATE))."</td>";
			// $HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "</tbody>";
	$HTML_HEADER .= "</table>";
	// echo "<br>";
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
	$mpdf->Output('(T&C) '.$no_po.' - '.strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier)).'.pdf' ,'I');


?>
