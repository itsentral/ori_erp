<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "	SELECT * FROM bq_header WHERE id_bq = '".$id_bq."' ";
$row	= $this->db->query($qBQ)->result_array();

$qBQdetailHeader 	= "SELECT * FROM hasil_material_project WHERE id_bq='".$id_bq."' ";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body"> 
	<a href="<?php echo site_url('price/PrintTotalMaterial/'.$id_bq) ?>" target='_blank' class="btn btn-sm btn-primary" id='btn-add' style='float:right;'>
	<i class="fa fa-print"></i> Print Total Material
	</a>
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Category</th>
				<th class="text-center" style='vertical-align:middle;' width='30%'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Material</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Est Cost</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Real Material</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Real Harga</th>
				<th class="text-center" style='vertical-align:middle;' width='11%'>Selisih</th>   
			</tr>
		</thead>
		<tbody>
			<?php
			$Total1 = 0;
			$Total2 = 0;
			$Total3 = 0;
			$Total4 = 0;
			foreach($qBQdetailRest AS $val => $valx){
				$Total1 += $valx['est_material'];
				$Total2 += $valx['est_harga'];
				$Total3 += $valx['real_material'];
				$Total4 += $valx['real_harga'];
				
				if($Total3 == 0 OR $Total1 == 0){
					$hasilx = 0;
				}
				else{
					$hasilx = ((($Total3/$Total1)*100)-100);
				}
				
				if($valx['est_material'] == 0 OR $valx['real_material'] == 0){
					$hasilxx = 0;
				}
				else{
					$hasilxx = ((($valx['real_material']/$valx['est_material'])*100)-100);
				}
				
				echo "<tr>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['est_material'],3)." Kg</td>";
					echo "<td align='right'>".number_format($valx['est_harga'],2)."</td>";
					echo "<td align='right'>".number_format($valx['real_material'],3)." Kg</td>";
					echo "<td align='right'>".number_format($valx['real_harga'],2)."</td>";
					echo "<td align='right'>".back_number(number_format($hasilxx,2))." %</td>";
				echo "</tr>";
			}
			?>
			<tr>
				<td colspan='2'><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?> Kg</b></td>
				<td align='right'><b><?= number_format($Total2, 2);?></b></td>
				<td align='right'><b><?= number_format($Total3, 3);?> Kg</b></td>
				<td align='right'><b><?= number_format($Total4, 2);?></b></td>
				<td align='right'><b><?= back_number(number_format($hasilx,2));?> %</b></td>  
			</tr>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});

</script>