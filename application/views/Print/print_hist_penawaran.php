<?php
	
	$sroot 		= $_SERVER['DOCUMENT_ROOT'];
	include $sroot."/application/libraries/MPDF57/mpdf.php";
	$mpdf=new mPDF('utf-8','A4');
	// $mpdf=new mPDF('utf-8','A4-L');
	
	set_time_limit(0);
	ini_set('memory_limit','1024M');

	//Beginning Buffer to save PHP variables and HTML tags
	ob_start();
	date_default_timezone_set('Asia/Jakarta');
	$today = date('l, d F Y [H:i:s]');
	
	$qBQ 		= "SELECT * FROM production WHERE no_ipp = '".str_replace('BQ-','',$id_bq)."' ";
	$dHeaderBQ	= $this->db->query($qBQ)->result_array();
	
	$result1BTS		= $this->db->get_where('laporan_revised_detail', array('id_bq'=>$id_bq,'revised_no'=>$rev))->result_array();
	$result2BTS		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'engine','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$result3BTS		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'packing','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$result4BTS		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'export','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$result5BTS		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'lokal','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$rest_mat		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'aksesoris','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$rest_baut		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'baut','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$rest_plate		= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'plate','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$rest_gasket	= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'gasket','revised_no'=>$rev,'price_total >'=>0))->result_array();
	$rest_lainnya	= $this->db->get_where('laporan_revised_etc',array('id_bq'=>$id_bq,'category'=>'lainnya','revised_no'=>$rev,'price_total >'=>0))->result_array();
	
	echo "<htmlpageheader>";
	// exit;
	?>
	<table class='header_style' border='0' width='100%' cellpadding='2'>
		<thead>
		<tr>
			<td><b><h2>Quotation <span style='color:#0e5ca9;'><?=str_replace('BQ-', '', $id_bq);?></span> for Internal (Revision <?=$rev;?>)</h2></b></td>
		</tr>
		</thead>
	</table>
	<br>
	
	<table class='header_style2' border='0' width='100%' cellpadding='2'>
		<tr>
			<td width='100px' align='center' style='vertical-align:top;' rowspan='7'><img src='<?=$sroot;?>/assets/images/ori_logo.jpg' alt="" height='120' width='110' ></td>
			<td rowspan='7' width='20px'></td>
			<td colspan='3' style='background-color: #ffffff !important;color: #0e5ca9;'><b><h3>PT  ORI POLYTEC COMPOSITES</h3></b></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td width='100px'>IPP No</td>
			<td width='15px'>:</td>
			<td><?= str_replace('BQ-','',$id_bq); ?></td>
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>Project</td>
			<td style='vertical-align:top;'>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ[0]['project'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>Customer</td>
			<td>:</td>
			<td><?= strtoupper(strtolower($dHeaderBQ[0]['nm_customer'])); ?></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		
		</tr>
		<tr style='background-color: #ffffff;'>
			<td style='vertical-align:top;'>&nbsp;</td>
			<td style='vertical-align:top;'></td> 
			<td style='vertical-align:top;'></td>
		
		</tr>
	</table>
	<br>
	<?php echo "<htmlpageheader>";?>
	<table class="gridtable" width='100%' border='0' cellpadding='2'>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PRODUCT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3'>Item Product</th>
				<th class="text-center" width='7%'>Dim 1</th>
				<th class="text-center" width='7%'>Dim 2</th>
				<th class="text-center" width='9%'>Series</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='7%'>Qty</th>
				<th class="text-center" width='7%'>Unit</th>
				<th class="text-center" width='10%'>Unit Price</th>
				<th class="text-center" width='10%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$SUM = 0;
			$SUMx = 0;
			$SUM0 = 0;
			$no = 0;
			$HPP_Tot = 0;
			foreach($result1BTS AS $val => $valx){
				$no++;
				
				$est_harga 	= (($valx['est_harga']+$valx['direct_labour']+$valx['indirect_labour']+$valx['machine']+$valx['mould_mandrill']+$valx['consumable']+$valx['foh_consumable']+$valx['foh_depresiasi']+$valx['biaya_gaji_non_produksi']+$valx['biaya_non_produksi']+$valx['biaya_rutin_bulanan'])) / $valx['qty'];
				$profit 		= ($valx['total_price'] / $valx['qty']) - $est_harga;
				$bottom_price 	= ($valx['total_price'] / $valx['qty']);
				
				$allow 			= ($valx['total_price_last'] / $valx['qty']) - $bottom_price;
				$selling_price 	= $valx['total_price_last'] / $valx['qty'];
				
				$persen 	= $profit/$est_harga * 100;
				$extra 		= $allow/$bottom_price*100; 
				
				$HPP_Tot 	+= $est_harga * $valx['qty'];
				
				$SUM0 		+= $est_harga;
				$SUMx 		+= $valx['total_price'];
				$SUM 		+= $valx['total_price_last'];
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['product_parent'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($selling_price,2)."</td>";
					echo "<td align='right'>".number_format($valx['total_price_last'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='8'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b></b></td>
				<td align='right'><b></b></td>
				<td align='right'><b><?= number_format($SUM,2);?></b></td>
			</tr>
		</tbody>
		<?php
		$SUM_NONFRP = 0;
		$SUM_BAUT = 0;
		if(!empty($rest_baut)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>BAUT</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_baut AS $val => $valx){
				$SUM_BAUT += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name_acc($valx['caregory_sub']))."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL BAUT</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_BAUT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		$SUM_PLATE = 0;
		if(!empty($rest_plate)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PLATE</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_plate AS $val => $valx){
				$SUM_PLATE += $valx['price_total'];
				
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name_acc($valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['weight'],2)."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['fumigasi']/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL PLATE</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_PLATE, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_GASKET = 0;
		if(!empty($rest_gasket)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>GASKET</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_gasket AS $val => $valx){
				$SUM_GASKET += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name_acc($valx['caregory_sub']))."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL GASKET</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_GASKET, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_LAINNYA = 0;
		if(!empty($rest_lainnya)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>LAINNYA</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Qty</th>";
					echo "<th class='text-center'>Satuan</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_lainnya AS $val => $valx){
				$SUM_LAINNYA += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name_acc($valx['caregory_sub']))."</td>";
					echo "<td align='center'>".number_format($valx['qty'])."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($valx['price_total']/$valx['qty'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL LAINNYA</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_LAINNYA, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		
		$SUM_MAT = 0;
		if(!empty($rest_mat)){
			echo "<tbody>";
				echo "<tr>";
					echo "<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>MATERIAL</b></td>";
				echo "</tr>";
				echo "<tr class='bg-bluexyz'>";
					echo "<th class='text-center' colspan='7'>Material Name</th>";
					echo "<th class='text-center'>Weight</th>";
					echo "<th class='text-center'>Unit</th>";
					echo "<th class='text-center'>Unit Price</th>";
					echo "<th class='text-center'>Total Price</th>";
				echo "</tr>";
			echo "</tbody>";
			echo "<tbody class='body_x'>";
			
			foreach($rest_mat AS $val => $valx){
				$SUM_MAT += $valx['price_total'];
				$unit_price = 0;
				if($valx['weight'] > 0){
					$unit_price = $valx['price_total']/$valx['weight'];
				}
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['weight'],2)."</td>";
					echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
					echo "<td align='right'>".number_format($unit_price,2)."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			echo "<tr class='FootColor'>";
				echo "<td colspan='10'><b>TOTAL MATERIAL</b></td> ";
				echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
			echo "</tr>";
			echo "</tbody>";
		}
		?>
		<?php
		if(!empty($result2BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>ENGINEERING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='6'>Test Name</th>
				<th class="text-center">Opt</th>
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no1=0;
			$SUM1=0;
			foreach($result2BTS AS $val => $valx){
				$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
				$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';

				$Opt 	= ($valx['option_type'] == 'Y')?'YES':'NO';
				$SUM1 += $valx['price_total'];
				$no1++;
				echo "<tr>";
					echo "<td colspan='6'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td align='center'>".$Opt."</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
					echo "</td>";
					echo "<td align='center'>".$Qty1."</td>";
					echo "<td align='center'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
					echo "</td>";
					echo "<td align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL ENGINEERING COST</b></td> 
				<td align='right'><b><?= number_format($SUM1,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result3BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>PACKING COST</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='9'>Category</th>
				<th class="text-center">Type</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no2=0;
			$SUM2=0;
			foreach($result3BTS AS $val => $valx){
				$no2++;
				$SUM2 += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='9'>".strtoupper($valx['caregory_sub']);
					echo "</td>";
					echo "<td align='center'>".strtoupper($valx['option_type']);
					echo "</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2);
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL PACKING COST</b></td> 
				<td align='right'><b><?= number_format($SUM2,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result4BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING EXPORT</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='4'>Country</th>
				<th class="text-center" colspan='3'>Shipping</th>
				<th class="text-center" colspan='2'>Unit Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no3=0;
			$SUM3=0;
			foreach($result4BTS AS $val => $valx){
				$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
				$SUM3 += $valx['price_total'];
				$country_name = get_name('production_delivery','country_code','no_ipp',str_replace('BQ-','',$valx['id_bq']));
				$no3++;
				echo "<tr>";
					echo "<td colspan='4'>".strtoupper(get_name('country_all','name','iso3',$country_name))."</td>";
					echo "<td align='center' colspan='3'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td align='center'>".$valx['option_type']."</td>";
					echo "<td align='right'>".number_format($valx['price'],2)."</td>";
					echo "<td align='center'>".$Qty3."</td>";
					echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING EXPORT</b></td>
				<td align='right'><b><?= number_format($SUM3,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		if(!empty($result5BTS)){
		?>
		<tbody>
			<tr>
				<td style='background-color: #0e5ca9 !important;color: white;' colspan='11'><b>TRUCKING LOKAL</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center">Via</th>
				<th class="text-center" colspan='2'>Area</th>
				<th class="text-center" colspan='3'>Destination</th>
				<th class="text-center" colspan='2'>Vehicle</th>
				
				<th class="text-center">Price</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			$no4=0;
			$SUM4=0;
			foreach($result5BTS AS $val => $valx){
				$SUM4 += $valx['price_total'];
				$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
				$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
				if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
					$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
				}
				else{
					$Kendaraanx = strtoupper($valx['kendaraan']);
				}
				$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
				
				$no4++;
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Areax."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='3'>".$Tujuanx."</td>";
					echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
					
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
					echo "</td>";
					echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
					echo "<td style='vertical-align:top' align='right'>";
						echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
					echo "</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL TRUCKING LOKAL</b></td>
				<td align='right'><b><?= number_format($SUM4,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
		<tfoot>
			<tr>
				<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='10'>TOTAL QUOTATION</th>
				<!--<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>USD</th>-->
				<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_BAUT + $SUM_PLATE + $SUM_GASKET + $SUM_LAINNYA, 2);?></th>
			</tr>
			<?php
			// if(!empty($rest_non_frp)){
				// echo "<tr>";
					// echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='11'></th>";
					// echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
					// echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
				// echo "</tr>";
			// }
			?>
		</tfoot>
		
		
	</table>
	<style type="text/css">
	@page {
		margin-top: 0cm;
		margin-left: 0cm;
		margin-right: 0cm;
		margin-bottom: 0cm;
	}
	
	#header{
		position:fixed;
	}
	p.foot1 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
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
		border-color: #ffffff;
		border-collapse: collapse;
	}
	
	.headX{
		background-color: #0e5ca9 !important;
		color: white;
	}
	
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
	}
	
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
	
	
</style>

	
	<?php
	$footer = "<p style='font-family: verdana,arial,sans-serif; font-size:10px;'><i>Printed by : ".ucfirst(strtolower($printby)).", ".$today."</i></p>";
	
	$html = ob_get_contents(); 
	ob_end_clean(); 
	
	$mpdf->showWatermarkText = true;
	$mpdf->SetTitle('Quotation '.str_replace('BQ-', '', $id_bq).' Rev.'.$rev);
	// $mpdf->AddPage('L');
	// $mpdf->SetDisplayMode('fullpage');
	$mpdf->SetFooter($footer);
	$mpdf->WriteHTML($html);
	$mpdf->Output("QUOTATION REV.".$rev." ".str_replace('BQ-', '', $id_bq)."_".date('d/m/Y/H/i/s').".pdf" ,'I');