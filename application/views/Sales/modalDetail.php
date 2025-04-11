
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title">Request Customer</h3>
	</div>
	<div class="box-body">
		<table border='0' width='100%'  class="table table-striped table-hover">
			<tr>
				<td width='15%'><b>Customer Name</b></td>
				<td width='2%'>:</td>
				<td width='33%'><?= $RestRequest[0]->nm_customer;?></td>
				<td width='15%'><b>Validity & Guarantee</b></td>
				<td width='2%'>:</td>
				<td width='33%'><?= (!empty($RestRequest[0]->validity))?strtoupper($RestRequest[0]->validity):'-';?></td>
			</tr>
			<tr>
				<td><b>Preject</b></td>
				<td>:</td>
				<td><?= strtoupper($RestRequest[0]->project);?></td>
				<td><b>Payment Term</b></td>
				<td>:</td>
				<td><?= (!empty($RestRequest[0]->payment))?strtoupper($RestRequest[0]->payment):'-';?></td>
			</tr>
			<tr>
				<td><b>Tolerance</b></td>
				<td>:</td>
				<td>Max : <?= floatval($RestRequest[0]->max_tol);?> | Min : <?= floatval($RestRequest[0]->min_tol);?></td>
				<td><b>Referensi Customer/Project</b></td>
				<td>:</td>
				<td><?= (!empty($RestRequest[0]->ref_cust))?strtoupper($RestRequest[0]->ref_cust):'-';?></td>
			</tr>
			<tr>
				<td><b>Note</b></td>
				<td>:</td>
				<td><?= (!empty($RestRequest[0]->note))?strtoupper($RestRequest[0]->note):'-';?></td>
				<td><b>Special Requirements From Customer</b></td>
				<td>:</td>
				<td><?= (!empty($RestRequest[0]->syarat_cust))?strtoupper($RestRequest[0]->syarat_cust):'-';?></td>
			</tr>
			<?php
				if($RestRequest[0]->status == 'CANCELED'){
			?>
			<tr>
				<td width='15%'><b>Cancel Reason</b></td>
				<td width='5%'>:</td>
				<td width='30%' colspan='4'><?= (!empty($RestRequest[0]->status_reason))?strtoupper($RestRequest[0]->status_reason):'-';?></td>
			</tr>
			<?php } ?>
		</table>
		<br><br>
		<div class="box box-danger">
			<div class="box-header">
				<h3 class="box-title">Specification</h3>
			</div>
			<div class="box-body">
				<table border='0' width='100%' class="table table-striped table-bordered table-hover table-condensed">
					<thead id='head_table'>
						<tr class='bg-blue'>
							<th style='text-align:center; vertical-align: middle;' >No</th>
							<th style='text-align:center; vertical-align: middle;' >Product</th>
							<th style='text-align:center; vertical-align: middle;' >Resin Type</th>
							<th style='text-align:center; vertical-align: middle;' >Liner</th>
							<th style='text-align:center; vertical-align: middle;' >Preaseure </th>
							<th style='text-align:center; vertical-align: middle;' >Stifness</th>
							<th style='text-align:center; vertical-align: middle;' >Aplication </th>
							<th style='text-align:center; vertical-align: middle;' >Vacum_Rate</th>
							<th style='text-align:center; vertical-align: middle;' >Life Time</th>
							<th style='text-align:center; vertical-align: middle;' >Standard</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(!empty($RestReqCustDet)){
							$no=0;
							foreach($RestReqCustDet AS $val => $valx){
								$no++;
								$std_asme	= ($valx['std_asme']=='Y')?'ASME, ':'';
								$std_ansi	= ($valx['std_ansi']=='Y')?'ANSI, ':'';
								$std_astm	= ($valx['std_astm']=='Y')?'ASTM, ':'';
								$std_awwa	= ($valx['std_awwa']=='Y')?'AWWA, ':'';
								$std_bsi	= ($valx['std_bsi']=='Y')?'BSI, ':'';
								$std_jis	= ($valx['std_jis']=='Y')?'JIS, ':'';
								$std_sni	= ($valx['std_sni']=='Y')?'SNI, ':'';
								$etc_1		= ($valx['std_etc']=='Y' AND $valx['etc_1'] != '')?strtoupper($valx['etc_1']).",":'';
								$etc_2		= ($valx['std_etc']=='Y' AND $valx['etc_2'] != '')?strtoupper($valx['etc_2']).",":'';
								$etc_3		= ($valx['std_etc']=='Y' AND $valx['etc_3'] != '')?strtoupper($valx['etc_3']).",":'';
								$etc_4		= ($valx['std_etc']=='Y' AND $valx['etc_4'] != '')?strtoupper($valx['etc_4']).",":'';
								?>
								<tr align='center'>
									<td><?= $no;?></td>
									<td><?= $valx['product'];?></td>
									<td><?= $valx['type_resin'];?></td>
									<td><?= $valx['liner_thick'];?></td>
									<td><?= $valx['pressure'];?> Bar</td>
									<td><?= $valx['stifness'];?> Pa</td>
									<td><?= $valx['aplikasi'];?></td>
									<td><?= $valx['vacum_rate'];?></td>
									<td><?= $valx['time_life'];?> Year</td>
									<td><?= $std_asme.$std_ansi.$std_astm.$std_awwa.$std_bsi.$std_jis.$std_sni.$etc_1.$etc_2.$etc_3.$etc_4; ?></td>
								</tr>
								<?php
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='10'>Data not found ...</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
				<br>
				<table border='0' width='100%' class="table table-striped table-bordered table-hover table-condensed">
					<thead id='head_table'>
						<tr class='bg-blue'>
							<th style='text-align:center; vertical-align: middle;' rowspan='2' width='5%'>No</th>
							<th style='text-align:center; vertical-align: middle;' rowspan='2' width='15%'>Product</th>
							<th style='text-align:center; vertical-align: middle;' colspan='4'>Conductive</th>
							<th style='text-align:center; vertical-align: middle;' colspan='4'>Fire Retardant</th>
							<th style='text-align:center; vertical-align: middle;' colspan='4'>Color</th>
							<th style='text-align:center; vertical-align: middle;' rowspan='2' width='12%'>Abrasive</th>
						</tr>
						<tr class='bg-blue'>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Liner</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Str</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Ext </th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>TC</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Liner</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Str</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Ext </th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>TC</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Liner</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Str</th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>Ext </th>
							<th style='text-align:center; vertical-align: middle;' width='6%'>TC</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(!empty($RestReqCustDet)){
							$no=0;
							foreach($RestReqCustDet AS $val => $valx){
								$no++;
								?>
								<tr>
									<td align='center'><?= $no;?></td>
									<td align='center'><?= $valx['product'];?></td>
									<td align='center'><?= ($valx['konduksi_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['konduksi_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['konduksi_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['konduksi_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['tahan_api_liner'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['tahan_api_structure'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['tahan_api_eksternal'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['tahan_api_topcoat'] == 'Y')?'<b>YES</b>':'NO';?></td>
									<td align='center'><?= ($valx['color'] == 'N')?'-':($valx['color_liner'] == '')?'-':strtoupper($valx['color_liner']);?></td>
									<td align='center'><?= ($valx['color'] == 'N')?'-':($valx['color_structure'] == '')?'-':strtoupper($valx['color_structure']);?></td>
									<td align='center'><?= ($valx['color'] == 'N')?'-':($valx['color_external'] == '')?'-':strtoupper($valx['color_external']);?></td>
									<td align='center'><?= ($valx['color'] == 'N')?'-':($valx['color_topcoat'] == '')?'-':strtoupper($valx['color_topcoat']);?></td>
									<td align='center'><?= ($valx['abrasi'] == 'Y')?'<b>YES</b>':'NO';?></td>
								</tr>
								<?php
							}
						}
						else{
							echo "<tr>";
								echo "<td colspan='10'>Data not found ...</td>";
							echo "</tr>";
						}
						?>
					</tbody>
				</table>
				<br>
				<table border='0' width='100%' class="table table-striped table-bordered table-hover table-condensed">
					<thead id='head_table'>
						<tr class='bg-blue'>
							<th style='text-align:center; vertical-align: middle;' width='5%'>No</th>
							<th style='text-align:center; vertical-align: middle;' width='8%'>Product</th>
							<th style='text-align:center; vertical-align: middle;' width='20%'>Document </th>
							<th style='text-align:center; vertical-align: middle;' width='20%'>Certificate</th>
							<th style='text-align:center; vertical-align: middle;' width='20%'>Testing</th>
							<th style='text-align:center; vertical-align: middle;' width='17%'>Add Request</th>
							<th style='text-align:center; vertical-align: middle;' width='10%'>Note</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no=0;
						foreach($RestReqCustDet AS $val => $valx){
							$no++;
							?>
							<tr>
								<td style='text-align:center; vertical-align: middle;' align='center' rowspan='4'><?= $no;?></td>
								<td style='text-align:center; vertical-align: middle;' align='center' rowspan='4'><?= $valx['product'];?></td>
								<td align='left'>Document 1 : <?= ($valx['document'] == 'N')?'-':($valx['document_1'] == '')?'-':ucwords(strtolower($valx['document_1']));?></td>
								<td align='left'>Certificate 1 : <?= ($valx['sertifikat'] == 'N')?'-':($valx['sertifikat_1'] == '')?'-':ucwords(strtolower($valx['sertifikat_1']));?></td>
								<td align='left'>Testing 1 : <?= ($valx['test'] == 'N')?'-':($valx['test_1'] == '')?'-':ucwords(strtolower($valx['test_1']));?></td>
								<td align='left'>Top Coated Color : <?= ($valx['ck_minat_warna_tc'] == 'N')?'-':($valx['ck_minat_warna_tc'] == '')?'-':ucwords(strtolower($valx['minat_warna_tc']));?></td>
								<td style='text-align:left; vertical-align: middle;' align='left' rowspan='4'><?= (!(empty($valx['note'])))?ucfirst(strtolower($valx['note'])):'-';?></td>
							</tr>
							<tr>
								<td align='left'>Document 2 : <?= ($valx['document'] == 'N')?'-':($valx['document_2'] == '')?'-':ucwords(strtolower($valx['document_2']));?></td>
								<td align='left'>Certificate 2 : <?= ($valx['sertifikat'] == 'N')?'-':($valx['sertifikat_2'] == '')?'-':ucwords(strtolower($valx['sertifikat_2']));?></td>
								<td align='left'>Testing 2 : <?= ($valx['test'] == 'N')?'-':($valx['test_2'] == '')?'-':ucwords(strtolower($valx['test_2']));?></td>
								<td align='left'>Pigmented Color : <?= ($valx['ck_minat_warna_pigment'] == 'N')?'-':($valx['ck_minat_warna_pigment'] == '')?'-':ucwords(strtolower($valx['minat_warna_pigment']));?></td>
							</tr>
							<tr>
								<td align='left'>Document 3 : <?= ($valx['document'] == 'N')?'-':($valx['document_3'] == '')?'-':ucwords(strtolower($valx['document_3']));?></td>
								<td align='left'>Certificate 3 : <?= ($valx['sertifikat'] == 'N')?'-':($valx['sertifikat_3'] == '')?'-':ucwords(strtolower($valx['sertifikat_3']));?></td>
								<td align='left'>Testing 3 : <?= ($valx['test'] == 'N')?'-':($valx['test_3'] == '')?'-':ucwords(strtolower($valx['test_3']));?></td>
								<td align='left'>Resin Request : <?= (empty($valx['resin_req_cust']))?'-':ucwords(strtolower($valx['resin_req_cust']));?></td>
							</tr>
							<tr>
								<td align='left'>Document 4 : <?= ($valx['document'] == 'N')?'-':($valx['document_4'] == '')?'-':ucwords(strtolower($valx['document_4']));?></td>
								<td align='left'>Certificate 4 : <?= ($valx['sertifikat'] == 'N')?'-':($valx['sertifikat_4'] == '')?'-':ucwords(strtolower($valx['sertifikat_4']));?></td>
								<td align='left'>Testing 4 : <?= ($valx['test'] == 'N')?'-':($valx['test_4'] == '')?'-':ucwords(strtolower($valx['test_4']));?></td>
								<td align='left'></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="box box-warning">
	<div class="box-header">
		<h3 class="box-title">Shipping</h3>
	</div>
	<div class="box-body">
		<table border='0' width='100%' class="table table-striped table-hover">
			<tr>
				<td width='15%'><b>Country Name</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->country_name;?></td>
				<td width='15%'></td>
				<td width='5%'></td>
				<td width='30%'></td>
			</tr>
			<tr>
				<td width='15%'><b>Delivery Date</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= date('d F Y', strtotime($RestShipping[0]->date_delivery));?></td>
				<td width='15%'></td>
				<td width='5%'></td>
				<td width='30%'></td>
			</tr>
			<tr>
				<td width='15%'><b>Address</b></td>
				<td width='5%'>:</td>
				<td width='30%' colspan='4'><?= strtoupper($RestShipping[0]->address_delivery);?></td>
			</tr>
			<tr>
				<td width='15%'><b>Shipping Method</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->metode_delivery;?></td>
				<td width='15%'><b>Handling Equipment</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->alat_berat;?></td>
			</tr>
			<tr>
				<td width='15%'><b>Packing</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->packing;?></td>
				<td width='15%'><b>Instalation</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->isntalasi_by;?></td>
			</tr>
			<tr>
				<td width='15%'><b>Validity & Guarantee</b></td>
				<td width='5%'>:</td>
				<td width='30%'><?= $RestShipping[0]->garansi;?> Year</td>
				<td width='15%'></td>
				<td width='5%'></td>
				<td width='30%'></td>
			</tr>
		</table>
	</div>
</div>

<script>
	$(document).ready(function(){
		swal.close();
		var standard_spec 	= $('#standard_spec').val();
		var document 		= $('#document').val();
		var color 			= $('#color').val();
		var test 			= $('#test').val();
		var sertifikat 		= $('#sertifikat').val();
		var abrasi 			= $('#abrasi').val();
		var konduksi 		= $('#konduksi').val();
		var tahan_api 		= $('#tahan_api').val();
		
		if(standard_spec != 'S-NON-01'){
			$('#StandardHide').hide();
		}
		if(document == 'N'){
			$('#DocumentHide').hide();
		}
		if(color == 'N'){
			$('#ColorHide').hide();
		}
		if(test == 'N'){
			$('#TestingHide').hide();
		}
		if(sertifikat == 'N'){
			$('#SertifikatHide').hide();
		}
		if(abrasi == 'N'){
			$('#AbrasiHide').hide();
		}
		if(konduksi == 'N'){
			$('#KonduksiHide').hide();
		}
		if(tahan_api == 'N'){
			$('#FireHide').hide();
		}
	});
</script>