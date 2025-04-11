<?php
$id_bq 	= $this->uri->segment(3);
$ipp 	= str_replace('BQ-','',$id_bq);

$MAX_REVISI = get_MaxRevisedSellingPrice()[$id_bq];
$qBQdetailHeader 	= "	SELECT 
							a.*, b.id_product, b.qty AS qty_sell, b.est_material, b.id_bq
						FROM 
							billing_so_product a 
							INNER JOIN laporan_revised_detail b ON a.id_milik = b.id_milik
						WHERE 
							a.no_ipp = '$ipp' 
							AND b.id_bq = '$id_bq' 
							AND b.revised_no = '$MAX_REVISI' 
							AND a.product <> 'pipe slongsong'
							AND a.product <> 'product kosong'";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();

$sql_non_frp 	= "SELECT b.* FROM billing_so_add b WHERE (b.category='baut' OR b.category='plate' OR b.category='gasket' OR b.category='lainnya') AND b.no_ipp = '".$ipp."'";
$non_frp		= $this->db->query($sql_non_frp)->result_array();

$sql_material 	= "SELECT b.* FROM billing_so_add b WHERE b.category='mat' AND b.no_ipp = '".$ipp."'";
$material		= $this->db->query($sql_material)->result_array();

?>

<div class="box-body">
	
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<td class="text-center" style='vertical-align:middle;' width='12%'></td>
		<td class="text-center" style='vertical-align:middle;' width='12%'></td>
		<td class="text-center" style='vertical-align:middle;' width='5%'></td>
		<td class="text-center" style='vertical-align:middle;' width='22%'></td>
		<td class="text-center" style='vertical-align:middle;' width='9%'></td>
		<td class="text-center" style='vertical-align:middle;' width='8%'></td>
		<td class="text-center" style='vertical-align:middle;' width='10%'></td>
		<?php
		if(!empty($qBQdetailRest)){
		?>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='12%'>PRODUCT</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>SPESIFIKASI</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='22%'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Material Est</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost Est</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				$Sum2 = 0;
				$SumX2 = 0;
				foreach($qBQdetailRest AS $val => $valx){
					$spaces = "";
					$bgwarna	= "bg-blue";

					$QTY_DEAL 	= $valx['qty'];
					$QTY_SELL 	= $valx['qty_sell'];
					$QTY_MAT 	= $valx['est_material'];
					$MAT_UNIT	= $QTY_MAT / $QTY_SELL;
					$MAT_DEAL	= $MAT_UNIT * $QTY_DEAL;
					$PRICE_DEAL = $valx['total_deal_usd'];

					$Sum += $MAT_DEAL;
					$SumX += $PRICE_DEAL;
					
					echo "<tr>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['product'])."</td>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['spec'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($MAT_DEAL, 3)." Kg</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($PRICE_DEAL, 2)."</span></td>";
						echo "<td align='center'>";
								echo "<button class='btn btn-sm btn-success MatDetailCost' title='Detail Cost' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id_milik']."' data-id_bq='".$valx['id_bq']."' data-qty='".$QTY_DEAL."'><i class='fa fa-eye'></i></button>";
								// echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printCostControl/'.$valx['id_product'].'/'.$valx['id_milik'].'/'.$id_bq)."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
								echo "&nbsp;<button class='btn btn-sm btn-primary detail_group' title='Detail Group Cost Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id_milik']."' data-id_bq='".$valx['id_bq']."' data-qty='".$QTY_DEAL."'><i class='fa fa-eye'></i></button>";
						echo "</td>";						
					echo "</tr>";
				}
				?>
				<tr>
					<th class="text-center" colspan='4' style='vertical-align:middle;'>Total</th>
					<th class="text-right"><?= number_format($Sum, 3);?> Kg</th>
					<th class="text-right"><?= number_format($SumX, 2);?></th>
					<th class="text-center"></th>
				</tr>
				<?php
				}
				$SUM_NONFRP = 0;
				if(!empty($non_frp)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='8'><b>BQ NON FRP</b></td>";
						echo "</tr>";
						echo "<tr class='bg-bluexyz'>";
							echo "<th class='text-center' colspan='3'>Material Name</th>";
							echo "<th class='text-center'>Qty</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($non_frp AS $val => $valx){
						$QTY            = $valx['qty'];
						$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
						$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

						$SUM_NONFRP += $TOTAL_PRICE;
						
						echo "<tr>";
							echo "<td colspan='3'>".get_name_acc($valx['id_material'])."</td>";
							echo "<td align='right'>".number_format($QTY,2)."</td>";
							echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
							echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
							echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='6'><b>TOTAL BQ NON FRP</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
				$SUM_MAT = 0;
				if(!empty($material)){
					echo "<tbody>";
						echo "<tr class='bg-blue'>";
							echo "<td class='text-left headX HeaderHr' colspan='8'><b>MATERIAL</b></td>";
						echo "</tr>";
						echo "<tr class='bg-bluexyz'>";
							echo "<th class='text-center' colspan='3'>Material Name</th>";
							echo "<th class='text-center'>Weight</th>";
							echo "<th class='text-center'>Unit</th>";
							echo "<th class='text-center'>Unit Price</th>";
							echo "<th class='text-center'>Total Price</th>";
						echo "</tr>";
					echo "</tbody>";
					echo "<tbody class='body_x'>";
					foreach($material AS $val => $valx){
						$QTY            = $valx['qty'];
						$UNIT_PRICE     = $valx['total_deal_usd'] / $QTY;
						$TOTAL_PRICE    = $UNIT_PRICE * $QTY;

						$SUM_MAT += $TOTAL_PRICE;
						echo "<tr>";
							echo "<td colspan='3'>".strtoupper($valx['nm_material'])."</td>";
							echo "<td align='right'>".number_format($QTY,2)."</td>";
							echo "<td align='center'>".strtoupper($valx['satuan'])."</td>";
							echo "<td align='right'>".number_format($UNIT_PRICE,2)."</td>";
							echo "<td align='right'>".number_format($TOTAL_PRICE,2)."</td>";
						echo "</tr>";
					}
					echo "<tr class='FootColor'>";
						echo "<td colspan='6'><b>TOTAL MATERIAL</b></td> ";
						echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
					echo "</tr>";
					echo "</tbody>";
				}
			?>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){ 
		swal.close();
	});
</script>