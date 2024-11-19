<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT a.*, b.sum_mat, b.est_harga FROM bq_detail_header a INNER JOIN estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' ORDER BY a.id_delivery ASC, a.sub_delivery ASC";
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
				<th class="text-center" style='vertical-align:middle;' width='19%'>Component</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Dimensi</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Product ID</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Cost</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$Sum = 0;
				$SumX = 0;
				foreach($qBQdetailRest AS $val => $valx){
					$spaces = "";
					$id_delivery = strtoupper($valx['id_delivery']);
					$bgwarna	= "bg-blue";
					$SumQty	= $valx['sum_mat'] * $valx['qty'];
					$Sum += $SumQty;
					
					$SumQtyX	= $valx['est_harga'] * $valx['qty'];
					$SumX += $SumQtyX;
					
					if($valx['sts_delivery'] == 'CHILD'){
						$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						$id_delivery = strtoupper($valx['sub_delivery']);
						$bgwarna	= "bg-green";
					}
					echo "<tr>";
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							if($valx['id_category'] == 'pipe'){$dim = floatval($valx['diameter_1'])." x ".floatval($valx['length'])." x ".floatval($valx['thickness']);}
							else{$dim = "belum di set";} 
						echo "<td align='left' style='padding-left:20px;'>".$spaces."".$dim."</td>";
						echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
						echo "<td align='left'>".$valx['id_product']."</span></td>";
						
						echo "<td align='right' style='padding-right:20px;'>".number_format($SumQtyX, 2)."</span></td>";
					echo "</tr>";
				}
			?>
			<tr>
				<th class="text-left" colspan='4' style='vertical-align:middle;'>Total</th>
				<th class="text-right" style='padding-right:20px;'><?= number_format($SumX, 2);?></th>
			</tr>
		</tbody>
	</table>
</div>

<script>
	$(document).on('click', '#detailDT', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailDT/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '#MatDetail', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});

</script>