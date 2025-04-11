<?php
$id_bq = $this->uri->segment(3);

$qBQ 	= "SELECT
				a.*,
				b.nm_customer,
				b.project,
				b.ref_quo,
				b.status AS sts_ipp
			FROM
				hist_bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
			WHERE a.id_bq = '".$id_bq."' ORDER BY a.rev ASC";
$row	= $this->db->query($qBQ)->result_array();

?>

<div class="box-body">

	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>BQ</th>
				<th class="text-center" style='vertical-align:middle;' width='22%'>Customer</th>
				<th class="text-center" style='vertical-align:middle;' width='25%'>Project</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Type</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Series</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Rev</th>
				<th class="text-center" style='vertical-align:middle;' width='6%'>Option</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				foreach($row AS $val => $valx){
					$no++;
					
					$ListBQipp		= $this->db->query("SELECT series FROM hist_bq_detail_header WHERE id_bq = '".$valx['id_bq']."' AND hist_date='".$valx['hist_date']."' GROUP BY series")->result_array();
					$dtListArray = array();
					foreach($ListBQipp AS $val2 => $valx2){
						$dtListArray[$val2] = $valx2['series'];
					}
					$dtImplode	= "".implode(", ", $dtListArray)."";
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['id_bq']."</td>";
						echo "<td align='left'>".$valx['nm_customer']."</td>";
						echo "<td align='left'>".strtoupper($valx['project'])."</td>";
						echo "<td align='left'>".$valx['order_type']."</td>";
						echo "<td align='left'>".$dtImplode."</td>";
						echo "<td align='center'><span class='badge bg-green'>".$valx['rev']."</span></td>";
						echo "<td align='center'><button class='btn btn-sm btn-primary' id='detailHist' title='Detail Hist BQ' data-id_bq='".$valx['id_bq']."' data-time_det='".str_replace(' ', 'Z', $valx['hist_date'])."'><i class='fa fa-eye'></i></button></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>

<script>
	$(document).on('click', '#detailHist', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>DETAIL HISTORY STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailHist/'+$(this).data('id_bq')+'/'+$(this).data('time_det'));
		$("#ModalView2").modal();
	});

</script>