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
			$HTML_HEADER .= "<td class='header_style_company bold color_req' colspan='3'></td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris1)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat' width='15%'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat' width='1%'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris2)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
		$HTML_HEADER .= "</tr>";
		$HTML_HEADER .= "<tr>";
			$HTML_HEADER .= "<td class='header_style_alamat' colspan='3' style='padding-left:50px;'>".strtoupper($data_iden[0]->alamat_baris3)."</td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
            $HTML_HEADER .= "<td class='header_style_alamat'></td>";
			$HTML_HEADER .= "<td class='header_style_alamat'></td>";
		$HTML_HEADER .= "</tr>";
	$HTML_HEADER .= "</table>";
    echo "<table class='gridtable5' width='100%' border='0'>";
		echo "<tbody>";
            echo "<tr>";
                echo "<td colspan='2'>Terms & Condition</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td width='4%'>1.</td>";
                echo "<td>VENDOR must acknowledge acceptance of this Purchase Order by returning to Buyer a signed Purchase Order within 2 (two) working days.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>2.</td>";
                echo "<td>Acceptable this Purchase Order is Subject to the buyer's standard terms & condition.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>3.</td>";
                echo "<td>Please state our above Purchase Order No. on your Delivery Order, Invoice and all correspondence as a reference.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>4.</td>";
                echo "<td>Our company reserves the right to cancel this Purchase Order in case of:</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- The goods supplied is not in conformity with the specifications, mentioned in this Purchase Order; and</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Delay in delivery more than 1 (one) week without any acceptable reasons.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>5.</td>";
                echo "<td>In the event of a delay in delivery from the specified time, the VENDOR shall be subject to a penalty of 0.1% (zero point one percent) per day, up to a maximum of 5% (five percent) of the Purchase Order price.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>6.</td>";
                echo "<td>In the event of any non-conformity, damage, and/or defect in the products delivered to the Buyer, the VENDOR shall be obligated to: </td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- be responsible for replacing the goods with new ones at no additional cost; and</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- compensate the Buyer for any resulting losses.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>7.</td>";
                echo "<td>Invoices shall be submitted on the 2nd and 18th of each month and no later than three (3) months after the issuance of the Purchase Order.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>8.</td>";
                echo "<td>Please submit your invoice complete with Beneficiary Name, Bank Account No., SWIFT CODE No.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>9.</td>";
                echo "<td>The description item on the invoice and delivery order will be similar to those on the Purchase Order.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>Acceptance handover report should be attached with the invoice (Berita Acara Serah Terima Hasil Jasa)</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>The delivery order for PO assets will require:</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Guarantee</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Manual Book</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Certificate</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>The delivery order for PO raw material will attach the following data: </td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Certificate of Analysis (“COA”)</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Material Safety Data Sheet (“MSDS”)</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Tax Deducted at Source (“TDS”)</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Certificate</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>The delivery order for PO material import will require the following data:</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Bills of Lading (“BL”)</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Packing Lists (“PL”)</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Invoice</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Form Facility</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Insurance</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- COA</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- MSDS</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- TDS</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Certificate</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>10.</td>";
                echo "<td>Acceptance handover report should be attached with invoice. For  services Purchase Order, the following data also will be attached:</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Guarantee</td>";
            echo "</tr>";
                echo "<tr>";
                echo "<td></td>";
                echo "<td>- Manual Book</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td></td>";
                echo "<td>- Certificate</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>11.</td>";
                echo "<td>The Vendor shall comply with all applicable regulations related to its business activities, including but not limited to Value Added Tax (VAT) form in accordance with the latest tax regulation, specifically the Regulation of Minister of the Finance Number 81 of 2024 on Taxation Provisions for the Implementation of the Core Administration System. The Vendor shall also comply with Regulation of the Government Number 50 of 2012 on The Implementation of Occupational Safety and Health Management System, as well as any other prevailing laws and regulations.</td>";
            echo "</tr>";
            echo "<tr>";
                echo "<td>12.</td>";
                echo "<td>The Vendor shall comply with all applicable laws and regulations in Indonesia related to the procurement of goods and services.</td>";
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
			font-size:10px;
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
		'margin-top' => 45,
		'margin-bottom' => 5,
		'default-header-line' => 5,
	]);
	$mpdf->WriteHTML($html);
	$mpdf->Output('(T&C) '.$no_po.' - '.strtoupper(get_name('supplier','nm_supplier','id_supplier',$data_header[0]->id_supplier)).'.pdf' ,'I');


?>
