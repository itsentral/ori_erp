	<?php
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	// $mpdf=new mPDF('utf-8','A4');
	$mpdf=new mPDF('utf-8','A4-L');

	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');

	$qHeader	= "SELECT a.* FROM production a WHERE a.no_ipp='".$no_ipp."' ";
	$dHeader	= $this->db->query($qHeader)->result_array();

	$qHeaderShi		= "SELECT a.*, b.country_name FROM production_delivery a INNER JOIN country b ON a.country_code=b.country_code WHERE a.no_ipp='".$no_ipp."' ";
	$dHeaderShip	= $this->db->query($qHeaderShi)->result_array();

	$qHeaderDet		= "SELECT a.* FROM production_req_sp a WHERE a.no_ipp='".$no_ipp."' ";
	$dResultDet		= $this->db->query($qHeaderDet)->result_array();
	$dResultDet2	= $this->db->query($qHeaderDet)->result_array();
	?>

	<table class="gridtable2" border='1' width='100%' cellpadding='2'>
		<tr>
			<td align='center'><b>PT  ORI POLYTEC COMPOSITE</b></td>
		</tr>
		<tr>
			<td align='center'><b><h2>IDENTIFIKASI PERMINTAAN PELANGGAN</h2></b></td>
		</tr>
	</table>
	<br>
	<table class="gridtable2" border='0' width='100%' >
		<tr>
			<td width='20%'>IPP Number</td>
			<td width='1%'>:</td>
			<td width='29%'><?= $no_ipp; ?></td>
			<td width='20%'>IPP Date</td>
			<td width='1%'>:</td>
			<td width='29%'><?= date('d F Y', strtotime($dHeader[0]['created_date'])); ?></td>
		</tr>
		<tr>
			<td>Customer Name</td>
			<td>:</td>
			<td><?= strtoupper($dHeader[0]['nm_customer']); ?></td>
			<td>Revision To</td>
			<td>:</td>
			<td><?= $dHeader[0]['ref_ke'];?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= strtoupper($dHeader[0]['project']); ?></td>
			<td style='vertical-align:top;'>Revision By</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= ($dHeader[0]['ref_ke'] == '0')?ucfirst(strtolower($dHeader[0]['created_by'])):ucfirst(strtolower($dHeader[0]['modified_by']));?></td>
		</tr>
		<tr>
			<td>Max Tolerance</td>
			<td>:</td>
			<td><?= floatval($dHeader[0]['max_tol']); ?></td>
			<td>Min Tolerance</td>
			<td>:</td>
			<td><?= floatval($dHeader[0]['min_tol']); ?></td>
		</tr>
		<tr>
			<td>Validity & Guarantee</td>
			<td>:</td>
			<td><?= (!empty($dHeader[0]['validity']))?strtoupper($dHeader[0]['validity']):'-';?></td>
			<td>Payment Term</td>
			<td>:</td>
			<td><?= (!empty($dHeader[0]['payment']))?ucwords(strtolower($dHeader[0]['payment'])):'-';?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Referensi Customer/Project</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader[0]['ref_cust']))?strtoupper($dHeader[0]['ref_cust']):'-';?></td>
			<td style='vertical-align:top;'>Special Requirements From Customer</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader[0]['syarat_cust']))?strtoupper($dHeader[0]['syarat_cust']):'-';?></td>
		</tr>
		<tr>
			<td style='vertical-align:top;'>Note</td>
			<td style='vertical-align:top;'>:</td>
			<td style='vertical-align:top;'><?= (!empty($dHeader[0]['note']))?strtoupper($dHeader[0]['note']):'-';?></td>
			<td style='vertical-align:top;'></td>
			<td style='vertical-align:top;'></td>
			<td style='vertical-align:top;'></td>
		</tr>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='14'>SPECIFICATION LIST</th>
			</tr>
			<tr>
				<th width='5%'>No</th>
				<th width='7%'>Product</th>
				<th width='7%'>Resin Type</th>
				<th width='7%'>Liner</th>
				<th width='7%'>Preaseure</th>
				<th width='9%'>Stifness</th>
				<th width='10%'>Aplication</th>
				<th width='8%'>Vacum_Rate</th>
				<th width='8%'>Life Time</th>
				<th width='7%'>Reference Standard</th>
				<th width='7%'>Conductive</th>
				<th width='7%'>Fire Retardant</th>
				<th width='7%'>Color</th>
				<th width='7%'>Abrasive</th>
				<th width='7%'>Product Supply</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				foreach($dResultDet AS $val => $data){
					$no++;
					$std_asme	= ($data['std_asme']=='Y')?'ASME , ':'';
					$std_ansi	= ($data['std_ansi']=='Y')?'ANSI , ':'';
					$std_astm	= ($data['std_astm']=='Y')?'ASTM , ':'';
					$std_awwa	= ($data['std_awwa']=='Y')?'AWWA , ':'';
					$std_bsi	= ($data['std_bsi']=='Y')?'BSI , ':'';
					$std_jis	= ($data['std_jis']=='Y')?'JIS , ':'';
					$std_sni	= ($data['std_sni']=='Y')?'SNI , ':'';
					$std_din	= ($data['std_din']=='Y')?'DIN , ':'';
					$std_fff	= ($data['std_fff']=='Y')?'FLAT FACE FLANGE , ':'';
					$std_rf		= ($data['std_rf']=='Y')?'RAISED FLANGE , ':'';
					$etc_1		= ($data['std_etc']=='Y' AND $data['etc_1'] != '')?$data['etc_1']."/":'';
					$etc_2		= ($data['std_etc']=='Y' AND $data['etc_2'] != '')?$data['etc_2']."/":'';
					$etc_3		= ($data['std_etc']=='Y' AND $data['etc_3'] != '')?$data['etc_3']."/":'';
					$etc_4		= ($data['std_etc']=='Y' AND $data['etc_4'] != '')?$data['etc_4']."/":'';

					?>
					<tr>
						<td align='center'><?= $no;?></td>
						<td align='center'><?= $data['product'];?></td>
						<td align='center'><?= $data['type_resin'];?></td>
						<td align='center'><?= $data['liner_thick'];?></td>
						<td align='center'><?= $data['pressure'];?> Bar</td>
						<td align='center'><?= $data['stifness'];?> Pa</td>
						<td align='center'><?= $data['aplikasi'];?></td>
						<td align='center'><?= $data['vacum_rate'];?></td>
						<td align='center'><?= $data['time_life'];?> Year</td>
						<td align='center'><?= $std_asme.$std_ansi.$std_astm.$std_awwa.$std_bsi.$std_jis.$std_sni.$etc_1.$etc_2.$etc_3.$etc_4; ?></td>

						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<td align='left' width='60%'><?= ($data['konduksi_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['konduksi_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<td align='left' width='60%'><?= ($data['tahan_api_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<td align='left'><?= ($data['tahan_api_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left' width='30%'>Liner</td>
									<td align='left' width='10%'>:</td>
									<?php
									$colorLiner = ($data['color_liner'] == '')?'-':strtoupper($data['color_liner']);
									?>
									<td align='left' width='60%'><?= ($data['color'] == 'N')?'-':$colorLiner;?></td>
								</tr>
								<tr>
									<td align='left'>Str</td>
									<td align='left'>:</td>
									<?php
									$colorStructure = ($data['color_structure'] == '')?'-':strtoupper($data['color_structure']);
									?>
									<td align='left'><?= ($data['color'] == 'N')?'-':$colorStructure;?></td>
								</tr>
								<tr>
									<td align='left'>Eks</td>
									<td align='left'>:</td>
									<?php
									$colorExt = ($data['color_external'] == '')?'-':strtoupper($data['color_external']);
									?>
									<td align='left'><?= ($data['color'] == 'N')?'-':$colorExt;?></td>
								</tr>
								<tr>
									<td align='left'>Tc</td>
									<td align='left'>:</td>
									<?php
									$colorTopcoat = ($data['color_topcoat'] == '')?'-':strtoupper($data['color_topcoat']);
									?>
									<td align='left'><?= ($data['color'] == 'N')?'-':$colorTopcoat;?></td>
								</tr>
							</table>
						</td>
						<td align='center'><?= ($data['abrasi'] == 'Y')?'<b>YES</b>':'NO';?></td>
						<td align='center'><?= $data['product_supply'];?></td>
					</tr>
					<?php
				}
			?>
		</tbody>
	</table>
	<br>
	<table class="gridtable" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<th align='left' colspan='7'>SPECIFICATION LIST</th>
			</tr>

			<tr>
				<th width='5%'>No</th>
				<th width='7%'>Product</th>
				<th width='20%'>Document</th>
				<th width='20%'>Certificate</th>
				<th width='20%'>Testing</th>
				<th width='18%'>Add Request</th>
				<th width='10%'>Note</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no2=0;
				foreach($dResultDet2 AS $val => $data2){
					$no2++;
				?>
					<tr>
						<td align='center'><?= $no2;?></td>
						<td align='center'><?= $data2['product'];?></td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left mid' width='30%'>Document 1</td>
									<td align='left mid' width='5%'>:</td>
									<?php
									$document_1 = ($data2['document_1'] == '')?'-':strtoupper($data2['document_1']);
									?>
									<td align='left mid' width='65%'><?= ($data2['document'] == 'N')?'-':$document_1;?></td>
								</tr>
								<tr>
									<td align='left mid'>Document 2</td>
									<td align='left mid'>:</td>
									<?php
									$document_2 = ($data2['document_2'] == '')?'-':strtoupper($data2['document_2']);
									?>
									<td align='left mid' width='65%'><?= ($data2['document'] == 'N')?'-':$document_2;?></td>
								</tr>
								<tr>
									<td align='left mid'>Document 3</td>
									<td align='left mid'>:</td>
									<?php
									$document_3 = ($data2['document_3'] == '')?'-':strtoupper($data2['document_3']);
									?>
									<td align='left mid' width='65%'><?= ($data2['document'] == 'N')?'-':$document_3;?></td>
								</tr>
								<tr>
									<td align='left mid'>Document 4</td>
									<td align='left mid'>:</td>
									<?php
									$document_4 = ($data2['document_4'] == '')?'-':strtoupper($data2['document_4']);
									?>
									<td align='left mid' width='65%'><?= ($data2['document'] == 'N')?'-':$document_4;?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left mid' width='30%'>Certificate 1</td>
									<td align='left mid' width='5%'>:</td>
									<td align='left mid' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_1'] == '')?'-':strtoupper($data2['sertifikat_1']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Certificate 2</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_2'] == '')?'-':strtoupper($data2['sertifikat_2']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Certificate 3</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_3'] == '')?'-':strtoupper($data2['sertifikat_3']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Certificate 4</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='65%'><?= ($data2['sertifikat'] == 'N')?'-':($data2['sertifikat_4'] == '')?'-':strtoupper($data2['sertifikat_4']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left mid' width='21%'>Testing 1</td>
									<td align='left mid' width='5%'>:</td>
									<td align='left mid' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_1'] == '')?'-':strtoupper($data2['test_1']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Testing 2</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_2'] == '')?'-':strtoupper($data2['test_2']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Testing 3</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_3'] == '')?'-':strtoupper($data2['test_3']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Testing 4</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='69%'><?= ($data2['test'] == 'N')?'-':($data2['test_4'] == '')?'-':strtoupper($data2['test_4']);?></td>
								</tr>
							</table>
						</td>
						<td align='center'>
							<table class="gridtable3" width='100%' border='0' cellpadding='0' cellspacing='0'>
								<tr>
									<td align='left mid' width='45%'>Top Coated Color</td>
									<td align='left mid' width='5%'>:</td>
									<td align='left mid' width='50%'><?= ($data2['ck_minat_warna_tc'] == 'N')?'-':($data2['ck_minat_warna_tc'] == '')?'-':strtoupper($data2['minat_warna_tc']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Pigmented Color</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='50%'><?= ($data2['ck_minat_warna_pigment'] == 'N')?'-':($data2['ck_minat_warna_pigment'] == '')?'-':strtoupper($data2['minat_warna_pigment']);?></td>
								</tr>
								<tr>
									<td align='left mid'>Resin Request</td>
									<td align='left mid'>:</td>
									<td align='left mid' width='50%'><?= (empty($data2['resin_req_cust']))?'-':ucfirst(strtolower($data2['resin_req_cust']));?></td>
								</tr>
								<tr>
									<td align='left mid'>&nbsp;</td>
									<td align='left mid'></td>
									<td align='left mid' width='50%'></td>
								</tr>
							</table>
						</td>
						<td><?= strtoupper($data2['note']);?></td>
					</tr>
				<?php
				}
			?>
		</tbody>
	</table>
	<br>
	<table class="gridtable2" width='100%' border='1' cellpadding='2'>
		<thead align='center'>
			<tr>
				<td align='left' colspan='6'><b>SHIPPING DETAIL</b></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td  width='20%'>Country</td>
				<td  width='1%'>:</td>
				<td  width='29%'><?= strtoupper($dHeaderShip[0]['country_name']); ?></td>
				<td  width='20%'>Delivery Date</td>
				<td  width='1%'>:</td>
				<td  width='29%'><?= date('d F Y', strtotime($dHeaderShip[0]['date_delivery'])); ?></td>
			</tr>
			<tr>
				<td  width='24%'>Address</td>
				<td  width='1%'>:</td>
				<td  width='25%' colspan='4'><?= strtoupper($dHeaderShip[0]['address_delivery']); ?></td>
			</tr>
			<tr>
				<td>Shipping Method</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip[0]['metode_delivery']); ?></td>
				<td>Handling Equipment</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip[0]['alat_berat']); ?></td>
			</tr>
			<tr>
				<td>Packing</td>
				<td>:</td>
				<td><?= $dHeaderShip[0]['packing']; ?></td>
				<td>Instalation</td>
				<td>:</td>
				<td><?= strtoupper($dHeaderShip[0]['isntalasi_by']); ?></td>
			</tr>
			<tr>
				<td>Validity & Guarantee</td>
				<td>:</td>
				<td><?= $dHeaderShip[0]['garansi']; ?> Year</td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tbody>
	</table>
	
	<style type="text/css">
		@page {
			margin-top: 1cm;
			margin-left: 1.5cm;
			margin-right: 1cm;
			margin-bottom: 1cm;
		}
		.mid{
			vertical-align: middle;
		}
		.font{
			font-family: verdana,arial,sans-serif;
			font-size:14px;
		}
		.fontheader{
			font-family: verdana,arial,sans-serif;
			font-size:13px;
			color:#333333;
			border-width: 1px;
			border-color: #666666;
			border-collapse: collapse;
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

		table.gridtable3 {
			font-family: verdana,arial,sans-serif;
			font-size:9px;
			color:#333333;
			border-width: 0px;
			border-color: #666666;
			border-collapse: collapse;
		}
		table.gridtable3 th {
			border-width: 1px;
			padding: 8px;
			border-style: none;
			border-color: #666666;
			background-color: #f2f2f2;
		}
		table.gridtable3 th.head {
			border-width: 1px;
			padding: 8px;
			border-style: none;
			border-color: #666666;
			background-color: #7f7f7f;
			color: #ffffff;
		}
		table.gridtable3 td {
			border-width: 1px;
			padding: 3px;
			border-style: none;
			border-color: #666666;
			background-color: #ffffff;
		}
		table.gridtable3 td.cols {
			border-width: 1px;
			padding: 3px;
			border-style: none;
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
		p {
			margin: 0 0 0 0;
		}
	</style>


	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	$refX	=  $dHeader[0]['ref_ke'];
	$html = ob_get_contents();
	// exit;
	ob_end_clean();
	// $mpdf->SetWatermarkText('ORI Group');
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle($no_ipp);
	$mpdf->AddPage();
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output($no_ipp.' REVISI KE '.$refX.'.pdf' ,'I');