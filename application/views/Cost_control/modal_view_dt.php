
<div class="box-body">
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Material Est</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost Est</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Material Real</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Cost Real</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Persentase Act vs Est (%)</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				$Sum2 = 0;
				$SumX2 = 0;
				foreach($result AS $val => $valx){
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}

					$qty 		= $valx['qty'];
					$no_ipp 	= str_replace('BQ-','',$id_bq);
					$id_milik 	= $valx['id'];

					$GET_EST_ACT = getEstimasiVsAktualGroupProduct($id_milik, $no_ipp, $qty);

					$estimasi_material 	= (!empty($GET_EST_ACT['est_mat']))?$GET_EST_ACT['est_mat']:0;
					$estimasi_price 	= (!empty($GET_EST_ACT['act_mat']))?$GET_EST_ACT['act_mat']:0;
					$real_material 		= (!empty($GET_EST_ACT['est_price']))?$GET_EST_ACT['est_price']:0;
					$real_price 		= (!empty($GET_EST_ACT['act_price']))?$GET_EST_ACT['act_price']:0;

					$selisih = 0;
					if($estimasi_material > 0 AND $real_material > 0){
						$selisih = $real_material / $estimasi_material * 100;
					}

					$Sum 	+= $estimasi_material;
					$SumX 	+= $estimasi_price;
					$Sum2 	+= $real_material;
					$SumX2 	+= $real_price;
					
					echo "<tr>";
						echo "<td align='left'>".$spaces."".strtoupper($valx['id_category'])."</td>";
						echo "<td align='left'>".$spaces."".$get_dist($valx['id'])."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' class='padding10pxR'>".number_format($estimasi_material, 5)." Kg</span></td>";
						echo "<td align='right' class='padding10pxR'>".number_format($estimasi_price, 2)."</span></td>";
						echo "<td align='right' class='padding10pxR'>".number_format($real_material, 5)." Kg</span></td>";
						echo "<td align='right' class='padding10pxR'>".number_format($real_price, 2)."</span></td>";
						echo "<td align='right' class='padding10pxR'>".number_format($selisih, 2)." %</span></td>";
						echo "<td align='center'>";
								if($tanda_cost == 'cost_control'){
									// echo "<button type='button' class='btn btn-sm btn-success MatDetailCost' title='Detail Cost' data-id_product='".$valx['id_product']."' data-ipp='".str_replace('BQ-','',$id_bq)."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."'><i class='fa fa-eye'></i></button>";
									// echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/printCostControl/'.$valx['id_product'].'/'.$valx['id'].'/'.$id_bq.'/'.$valx['qty'])."' class='btn btn-sm btn-primary' target='_blank' title='Print' data-role='qtip'><i class='fa fa-print'></i></a>";
									echo "&nbsp;<a href='".site_url($this->uri->segment(1).'/excel_detail_material/'.$valx['id_product'].'/'.$valx['id'].'/'.$id_bq.'/'.$valx['qty'])."' target='_blank' class='btn btn-sm btn-info' target='_blank' title='Excel' data-role='qtip'><i class='fa fa-file-excel-o'></i></a>";
								}
								else{
									echo "<button type='button' class='btn btn-sm btn-warning MatDetail' title='Detail BQ' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button>";
								}
							echo "</td>";						
					echo "</tr>";
				}

				$selisih = 0;
				if($Sum > 0 AND $Sum2 > 0){
					$selisih = $Sum2 / $Sum * 100;
				}
			?>
			<!-- <tr>
				<th class="text-center" colspan='4' style='vertical-align:middle;'>Total</th>
				<th class="text-right padding10pxR"><?= number_format($Sum, 5);?> Kg</th>
				<th class="text-right padding10pxR"><?= number_format($SumX, 2);?></th>
				<th class="text-right padding10pxR"><?= number_format($Sum2, 5);?> Kg</th>
				<th class="text-right padding10pxR"><?= number_format($SumX2, 2);?></th>
				<th class="text-right padding10pxR"><?= number_format($selisih, 2);?> %</th>
				<th class="text-center"></th>
			</tr> -->
		</tbody>
	</table>
</div>
<style>
	.padding10pxR{
		padding-right:10px;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
	});
</script>