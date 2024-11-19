<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM so_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "	SELECT 
							a.*, 
							b.sum_mat, 
							b.est_harga, 
							b.sum_mat2, 
							b.est_harga2
						FROM 
							so_detail_header a 
							INNER JOIN so_estimasi_cost_and_mat b 
								ON a.id = b.id
						WHERE 
							a.id_bq = '".$id_bq."' 
							AND b.parent_product <> 'pipe slongsong'						
						ORDER BY 
							a.id_delivery ASC, 
							a.sub_delivery ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();

?>

<div class="box-body">
	
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Dimensi</th>
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
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$SumQty	= $valx['sum_mat2'];
					$Sum += $SumQty;
					
					$SumQtyX	= $valx['est_harga2'];
					$SumX += $SumQtyX;
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
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
						echo "<td align='left'>".$spaces."".spec_fd($valx['id'], 'so_detail_header')."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($SumQty, 3)." Kg</span></td>";
						echo "<td align='right' style='padding-right:10px;'>".number_format($SumQtyX, 2)."</span></td>";
						echo "<td align='center'>";
								echo "<button class='btn btn-sm btn-success MatDetailCost' title='Detail Cost' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-qty='".$valx['qty']."'><i class='fa fa-eye'></i></button>";
								// echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printCostControl/'.$valx['id_product'].'/'.$valx['id_milik'].'/'.$id_bq)."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
								echo "&nbsp;<button class='btn btn-sm btn-primary detail_group' title='Detail Group Cost Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-id_bq='".$valx['id_bq']."' data-qty='".$valx['qty']."'><i class='fa fa-eye'></i></button>";
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
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){ 
		swal.close();
	});
</script>