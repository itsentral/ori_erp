
<table class='gridtable3' width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tbody>
		<tr>
			<td colspan='5'>Jakarta, <?=date('F d, Y');?></td>
			<td rowspan='6' style='vertical-align:top; text-align:right;'><img src='<?=$sroot;?>/assets/images/alamatori.png' style='float:right; padding-top:-42px;' alt="" height='160' width='90'></td>
		</tr>
		<tr>
			<td colspan='2'>Ref. No</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$quo_number;?></td>
		</tr>
		<tr>
			<td colspan='5' height='40px;' style='vertical-align:bottom;'><b><?=$customer;?></b></td>
		</tr>
		<tr>
			<td colspan='4' height='40px;' style='vertical-align:top; font-size:10px;'><?= $alamat_cust;?></td>
			<td></td>
		</tr>
		<tr>
			<td colspan='2'>Attn</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$attn;?></td>
		</tr>
		<tr>
			<td colspan='2'>Telp.</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$telephone;?></td>
		</tr>
		<tr>
			<td colspan='2'>Subject</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$subject;?></td>
		</tr>
		<tr>
			<td colspan='5' height='40px;'></td>
		</tr>
		<tr>
			<td colspan='6' class='justify'>
				Dear Sirs/Madame,<br>
				Refer to our discussion by email between <?=$customer;?> and PT Ori Polytec Composite, a limited liability company incorporated under the laws of the Republic of Indonesia, domiciled and having its address at Jl. Akasia II Blok A9/3, Delta Silicon Industrial Park, Kawasan Industri Lippo Cikarang Bekasi 17340, hereby we submit our proposal to offer the following products:
			</td>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td><b>A.</b></td>
			<td colspan='5'><b>Product Types and Conditions</b></td>
		</tr>
		<tr>
			<td width='5%'></td>
			<td width='4%'>1. </td>
			<td width='27%'>Products</td>
			<td width='3%'>:</td>
			<td width='61%' colspan='2'><?=$product;?></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td>Material </td>
			<td>:</td>
			<td colspan='2'><?=$resin;?></td>
		</tr>
		<tr>
			<td></td>
			<td>3.</td>
			<td>Delivery term</td>
			<td>:</td>
			<td colspan='2'><?=$pengiriman;?></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td class='valign'>Offer Periode</td>
			<td class='valign'>:</td>
			<td colspan='2'><?=$jangka_waktu_penawaran;?></td>
		</tr>
		<tr>
			<td></td>
			<td>5.</td>
			<td>Lead time</td>
			<td>:</td>
			<td colspan='2'><?=$waktu_pengiriman;?></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>6.</td>
			<td class='valign'>Stage payment of fees</td>
			<td class='valign'>:</td>
			<td colspan='2' class='justify'><?=$tahap_pembayaran;?></td>
		</tr>
		<tr>
			<td></td>
			<td>7.</td>
			<td>Warranty</td>
			<td>:</td>
			<td colspan='2'><?=$garansi_porduct;?></td>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td><b>B.</b></td>
			<td colspan='6'><b>Type and Stage Payment Rates</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='4' class='justify'>
				Related to the products mentioned above, we hereby submit the product price on  attached<br>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='4' class='justify'>The whole process of payment will be made by wire transfer to our account at OCBC NISP - Branch Mangga Dua Le Grandeur, on behalf of PT Ori Polytec Composites with Account Number: 0278.0001.6993 (USD) or 0278.0001.6993 (IDR), 30 days after commencing and PT Ori Polytec Composite / <?=$customer;?> receive a copy /copies of related documents such as (Travel Letter / DO, BA / DCN / MDR, Invoicing, PPN.PPH).
			</td>
		</tr>
	</tbody>
</table>
<?php
echo "<pagebreak />";
?>
<table class='gridtable3' width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tbody>
		<tr>
			<td width='5%'></td>
			<td width='4%'></td>
			<td width='27%'></td>
			<td width='3%'></td>
			<td width='61%' style='vertical-align:top; text-align:right;'><img src='<?=$sroot;?>/assets/images/alamatori.png' style='float:right; padding-top:-42px;' alt="" height='160' width='90'></td>
		</tr>
		<tr>
			<td><b>C.</b></td>
			<td colspan='5'><b>Penalty</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>Every event of late payment will incur 0.1%/day and maximum penalty of 10% of the total price.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>
				Any delay in delivery and / or termination of production either in workshop or in project upon request from Customer side, will incur additional cost as follows:<br>
				<ul>
					<li>Temporary storage charges will be billed if the product is in our Factory and / or a temporary stop of delivery within 2 weeks or more, at a price of Rp 15.000 / m2 / day. (If export USD 1 / m2 / day).</li>
					<li>PT Ori Polytec Composites is entitled to billing according to the agreed delivery date since the beginning of the order.</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='3' class='justify'>Penalty Fees will not be liable in any way if there is a Force Majeure.</td>
		</tr>
		<tr>
			<td colspan='5' height='10px;'></td>
		</tr>
		<tr>
			<td><b>D.</b></td>
			<td colspan='5'><b>Price Adjustment</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>In the event of material increases due to the product drawing changes, ORI reserves the right to apply and charge any resulting price increases.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>In the event of material price increase, ORI reserves the right to apply this increase in its own price.</td>
		</tr>
		<tr>
			<td colspan='5' height='10px;'></td>
		</tr>
		<tr>
			<td><b>E.</b></td>
			<td colspan='5'><b>Dispute</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>Any disputes arising from the implementation of the activities referred to in this Proposal Offer, all parties involved will resolve by deliberation to reach consensus.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>If the process of deliberation and consensus does not occur, then all parties involved agree and agree to resolve it and choosing  legal common law in Central Jakarta District Court.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='3' class='justify'>The laws governing this agreement is the Law of the Republic of Indonesia.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td colspan='3' class='justify'>Purchase orders cannot be canceled under any circumstances and reasons.</td>
		</tr>
		<tr>
			<td colspan='5' height='10px;'></td>
		</tr>
		<tr>
			<td><b>F.</b></td>
			<td colspan='5'><b>Cover</b></td>
		</tr>
		<tr>
			<td colspan='5' class='justify'>
				As above are the basic condition from us, if there are unclear matter, please do not hesitate to contact our representative offices or agents. Thank you for your attention and trust.
			</td>
		</tr>
		<tr>
			<td colspan='5' height='30px;'></td>
		</tr>
		<tr>
			<td colspan='5'>
				Yours Faithfully,
			</td>
		</tr>
		<tr>
			<td colspan='5' height='50px;'></td>
		</tr>
		<tr>
			<td colspan='5'>
				<b><u><?=$sales;?></u></b>
			</td>
		</tr>
	</tbody>
</table>

<?php
echo "<pagebreak />";
?>
<table class="gridtable" width='100%' border='0' cellpadding='2'>
	<tbody>
		<tr>
			<td colspan='11' style='background-color: white; padding-left:0px; font-size: 14px; height:30px; vertical-align:top; text-align: left;'><b>ATTACHMENT</b></td>
		</tr>
	</tbody>
	<?php
	$SUM = 0;
	if(!empty($detail_product)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>PRODUCT</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='3' width='22%'>Item Product</th>
			<th class="text-center" width='7%'>Dim 1</th>
			<th class="text-center" width='7%'>Dim 2</th>
			<th class="text-center" width='10%'>Series</th>
			<th class="text-center" width='17%'>Specification</th>
			<th class="text-center" width='9%'>Qty</th>
			<th class="text-center" width='6%'>Unit</th>
			<th class="text-center" width='11%'>Unit Price</th>
			<th class="text-center" width='11%'>Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no = 0;
		foreach($detail_product AS $val => $valx){
			$no++;
			$dataSum = 0;
			if($valx['qty'] <> 0){
				$dataSum	= $valx['cost'];
			}
			$SUM += $dataSum;
			
			if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
				$unitT = "Btg";
			}
			else{
				$unitT = "Pcs";
			}
			echo "<tr>";
				echo "<td colspan='3'>".strtoupper($valx['id_category'])."</td>";
				echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
				echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
				echo "<td align='center'>".$valx['series']."</td>";
				echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
				echo "<td align='center'>".$valx['qty']."</td>";
				echo "<td align='center'>".$unitT."</td>";
				echo "<td align='right'>".number_format($dataSum / $valx['qty'],2)."</td>";
				echo "<td align='right'>".number_format($dataSum,2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
			<td align='right'><b><?= number_format($SUM,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr>";
				echo "<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>BQ NON FRP</b></th>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Qty</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		
		foreach($non_frp AS $val => $valx){
			$SUM_NONFRP += $valx['price_total'];
			
			$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
			$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
			$nama_acc = "";
			if($valx['category'] == 'baut'){
				$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
			}
			if($valx['category'] == 'plate'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'gasket'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'lainnya'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
			}
				
			$qty = $valx['qty'];
			$satuan = $valx['option_type'];
			if($valx['category'] == 'plate'){
				$qty = $valx['weight'];
				$satuan = '1';
			}
			echo "<tr>";
				echo "<td colspan='7'>".$nama_acc."</td>";
				echo "<td align='right'>".number_format($qty,2)."</td>";
				echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan)))."</td>";
				$harga_tot = number_format($valx['price_total'],2);
				$harga_sat = number_format($valx['price_total']/$qty,2);
				if($valx['price_total'] <= 0){
					$harga_tot = 'No Quote';
					$harga_sat = 'No Quote';
				}
				echo "<td align='right'>".$harga_sat."</td>";
				echo "<td align='right'>".$harga_tot."</td>";
			echo "</tr>";
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL COST OF BQ NON FRP</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<tbody>";
			echo "<tr>";
				echo "<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>MATERIAL</b></th>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Qty</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center' colspan='2'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		
		foreach($material AS $val => $valx){
			if($valx['price_total'] > 0){
				$SUM_MAT += $valx['price_total'];
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['qty_berat'],2)."</td>";
					echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type'])))."</td>";
					echo "<td align='right' colspan='2'>".number_format($valx['price_total'],2)."</td>";
				echo "</tr>";
			}
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL COST OF MATERIAL</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	?>
	<?php
	if(!empty($enggenering)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>ENGINEERING</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='7'>Test Name</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Unit</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no1=0;
		$SUM1=0;
		foreach($enggenering AS $val => $valx){
			$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$Price1 	= (!empty($valx['price']))?number_format($valx['price'],2):'-';
			$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'],2):'-';
			$SUM1 += $valx['price_total'];
			$no1++;
			echo "<tr>";
				echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
				echo "<td align='center'>".$Qty1."</td>";
				echo "<td align='center'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF ENGINEERING</b></td>
			<td align='right'><b><?= number_format($SUM1,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($packing)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>PACKING</b></th>
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
		foreach($packing AS $val => $valx){
			$no2++;
			$SUM2 += $valx['price_total'];
			echo "<tr>";
				echo "<td colspan='9'>".strtoupper($valx['name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['option_type']);
				echo "</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2);
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF PACKING</b></td>
			<td align='right'><b><?= number_format($SUM2,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($export)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>TRUCKING EXPORT</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='6'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Fumigation</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no3=0;
		$SUM3=0;
		foreach($export AS $val => $valx){
			$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$SUM3 += $valx['price_total'];
			$no3++;
			echo "<tr>";
				echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['type'])."</td>";
				echo "<td align='center'>".$Qty3."</td>";
				echo "<td align='right'>".number_format($valx['fumigasi'],2)."</td>";
				echo "<td align='right'>".number_format($valx['price'],2)."</td>";
				echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF TRUCKING EXPORT</b></td>
			<td align='right'><b><?= number_format($SUM3,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($local)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>TRUCKING LOCAL</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center">Via</th>
			<th class="text-center" colspan='3'>Area</th>
			<th class="text-center" colspan='2'>Destination</th>
			<th class="text-center" colspan='2'>Vehicle</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no4=0;
		$SUM4=0;
		foreach($local AS $val => $valx){
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
				echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
				echo "<td style='vertical-align:top' align='left' colspan='2'>".$Tujuanx."</td>";
				echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
				echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
				echo "<td style='vertical-align:top' align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'],2)."</div>";
				echo "</td>";
				echo "<td style='vertical-align:top' align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'],2)."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF TRUCKING LOCAL</b></td>
			<td align='right'><b><?= number_format($SUM4,2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	?>
	<?php
		$SUM_OTHER = 0;
		if(!empty($otherArray)){
		?>
		<tbody>
			<tr>
				<td class='bg-bluexyz' style='text-align:left;' colspan='11'><b>OTHER</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='8'>Description</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit Price</th>
				<th class="text-center">Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			
			foreach($otherArray AS $val => $value){
				$SUM_OTHER += $value['price_total'];
				
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'  colspan='8'>".strtoupper($value['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='center'>".number_format($value['qty'],2)."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price'],2)."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price_total'],2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL OTHER</b></td>
				<td align='right'><b><?= number_format($SUM_OTHER,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
	<tfoot>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='10'>TOTAL QUOTATION</th>
			<th class='bg-bluexyz' style='text-align:right;' ><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP + $SUM_OTHER, 2);?></th>
		</tr>
		<?php
		// if($resultNONFRP_Num > 0){
			// echo "<tr>";
				// echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='9'></th>";
				// echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
				// echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
			// echo "</tr>";
		// }
		?>
	</tfoot>
	
	
</table>
	
	
<style>
	
	.justify{
		text-align: justify;
	}
	
	.valign{
		vertical-align: top;
	}
	
	table.gridtable3 {
		font-family: "Garamond", serif;
		font-size:14px;
		color:#333333;
		margin-left: 60px;
		margin-right: 60px;
	}
	table.gridtable3 td {
		padding: 3px;
	}
	table.gridtable3 td.cols {
		padding: 3px;
	}
	
	<!-- BAGIAN LAMPIRAN -->
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
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 4px;
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