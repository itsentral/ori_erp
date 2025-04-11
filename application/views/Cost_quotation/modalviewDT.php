<?php
$id_bq = $this->uri->segment(3);
$tanda_cost = $this->uri->segment(4);

$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "	SELECT 
							a.*,
							b.foh_consumable,
							b.foh_depresiasi,
							b.biaya_gaji_non_produksi,
							b.biaya_non_produksi,
							b.biaya_rutin_bulanan
						FROM 
							cost_lain a INNER JOIN cost_lain_sum_detail b ON a.id=b.id
						WHERE 
							a.id_bq = '".$id_bq."' ";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body">
	<a href="<?php echo site_url('price/PrintHasilProjectPerBQ/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-primary" id='btn-add' style='float:right;'>
	<i class="fa fa-print"></i> Print Detail
  </a><br><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='22%'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Material Est</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost Est</th>
				<?php
				if($tanda_cost == 'cost_control'){
					echo "<th class='text-center' style='vertical-align:middle;' width='9%'>Material Real</th>";
					echo "<th class='text-center' style='vertical-align:middle;' width='8%'>Cost Real</th>";
					echo "<th class='text-center' style='vertical-align:middle;' width='7%'>Selisih</th>";
				}
				else{
					echo "<th class='text-center' style='vertical-align:middle;' width='8%'>Cost Process</th>";
				}
				?>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				$Sum2 = 0;
				$SumX2 = 0;
				$Cost = 0;
				foreach($qBQdetailRest AS $val => $valx){
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$SumQty	= $valx['sum_mat2'];
					$Sum += $SumQty;
					
					$SumQtyX	= $valx['est_harga2'];
					$SumX += $SumQtyX;
					
					$SumQty2	= $valx['real_material'];
					$Sum2 += $SumQty2;
					
					$SumQtyX2	= $valx['real_harga'];
					$SumX2 += $SumQtyX2;
					
					$Costx2	= $valx['direct_labour'] + $valx['indirect_labour'] + $valx['consumable'] + $valx['biaya_mesin'] + $valx['biaya_mould'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
					$Cost += $Costx2;
					
					$TotalCost = $valx['direct_labour'] + $valx['indirect_labour'] + $valx['consumable'] + $valx['biaya_mesin'] + $valx['biaya_mould'] + $valx['foh_consumable'] + $valx['foh_depresiasi'] + $valx['biaya_gaji_non_produksi'] + $valx['biaya_non_produksi'] + $valx['biaya_rutin_bulanan'];
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					
					if($valx['persenx'] > 0){
						$persenc = "-".number_format(abs($valx['persenx']),2);
					}
					if($valx['persenx'] <= 0){
						$persenc = number_format(abs($valx['persenx']),2);
					}
					
					$HasilAKhir = number_format(((($Sum2 / $Sum) * 100) - 100), 2);
					if($HasilAKhir > 0){
						$HasilAkhir2 = "-".abs($HasilAKhir);
					}
					if($HasilAKhir <= 0){
						$HasilAkhir2 = abs($HasilAKhir);
					}
					
					echo "<tr>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
							}
							elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
							}
							else{$dim = "belum di set";} 
						echo "<td align='left'>".$spaces."".$dim."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($SumQty, 3)." Kg</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($SumQtyX, 2)."</span></td>";
						if($tanda_cost == 'cost_control'){
							echo "<td align='right' style='padding-right:10px;'>".number_format($valx['real_material'], 3)." Kg</span></td>";
							echo "<td align='right' style='padding-right:10px;'>".number_format($valx['real_harga'], 2)."</span></td>";
							echo "<td align='right' style='padding-right:10px;'>".$persenc." %</span></td>";
						}
						else{
							echo "<td class='text-right' style='padding-right:10px;'><a id='detail_process_cost2' style='cursor:pointer;' data-id='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-id_product='".$valx['id_product']."'>".number_format($TotalCost, 2)."</a></td>";
						}
						
						echo "<td align='center'>";
								if($tanda_cost == 'cost_control'){
									echo "<button class='btn btn-sm btn-success' id='MatDetailCost' title='Detail Cost' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
									echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printCostControl/'.$valx['id_product'].'/'.$valx['id'].'/'.$id_bq)."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
								}
								else{
									echo "<button class='btn btn-sm btn-warning' id='MatDetail' title='Detail BQ' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button>";
								}
							echo "</td>";						
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-center" colspan='4' style='vertical-align:middle;'>Total</th>
				<th class="text-right"><?= number_format($Sum, 3);?> Kg</th>
				<th class="text-right"><?= number_format($SumX, 2);?></th>
				<?php
				if($tanda_cost == 'cost_control'){
					echo "<th class='text-right'>".number_format($Sum2, 3)." Kg</th>";
					echo "<th class='text-right'>".number_format($SumX2, 2)."</th>";
					echo "<th class='text-right'>".$HasilAkhir2." %</th>";
				}
				else{
					echo "<th class='text-right'>".number_format($Cost, 2)."</th>";
				}
				?>
				<th class="text-center"></th>
			</tr>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});
		
	$(document).on('click', '#detailDT', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailDT/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#MatDetail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#MatDetailCost', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMatCost/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});

</script>