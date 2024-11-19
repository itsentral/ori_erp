<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM so_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT a.*, b.sum_mat FROM so_detail_header a LEFT JOIN so_estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' AND b.parent_product <> 'pipe slongsong' ORDER BY a.id_bq_header ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='4%'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='16%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>No Component</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='29%'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Material</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$no = 0;
				foreach($qBQdetailRest AS $val => $valx){ $no++;
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
						
					$SumQty	= $valx['sum_mat'] * $valx['qty'];
					$Sum += $SumQty;
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					echo "<tr>";
						echo "<td align='center'>".$no."</span></td>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'elbow mitter' OR $valx['id_category'] == 'elbow mould'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']).", ".$valx['type']." ".$valx['sudut'];
							}
							elseif($valx['id_category'] == 'concentric reducer' OR $valx['id_category'] == 'reducer tee mould' OR $valx['id_category'] == 'eccentric reducer' OR $valx['id_category'] == 'reducer tee slongsong' OR $valx['id_category'] == 'branch joint'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['diameter_2'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'end cap' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'equal tee mould' OR $valx['id_category'] == 'blind flange' OR $valx['id_category'] == 'equal tee slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['thickness']);
							}
							elseif($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' ){
								$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length']);
							}
							else{$dim = "belum di set";} 
						echo "<td align='left'>".$valx['no_komponen']."</span></td>";
						echo "<td align='left' style='padding-left:20px;'>".$dim."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						echo "<td align='right' style='padding-right:20px;'>".number_format($SumQty, 3)." Kg</span></td>";
						echo "<td align='center' ><button class='btn btn-sm btn-warning' id='detailDT' title='Detail Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button></td>";						
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-center" colspan='6' style='vertical-align:middle;'>Total</th> 
				<th class="text-right" style='padding-right:20px;'><?= number_format($Sum, 3);?> Kg</th>
				<th class="text-center" style='vertical-align:middle;'></th>
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
		loading_spinner();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length'));
		$("#ModalView2").modal();
	});

</script>