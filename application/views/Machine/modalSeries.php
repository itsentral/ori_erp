<?php
$id_bq = $this->uri->segment(3);
$ListBQipp		= $this->db->query("SELECT series  FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series")->result_array();
$ListSeries		= $this->db->query("SELECT kode_group FROM component_group WHERE deleted ='N' AND status='Y' GROUP BY kode_group ORDER BY resin_system ASC, pressure ASC, liner ASC ")->result_array();
// $dtListArray = array();
// foreach($ListBQipp AS $val => $valx){
	// $dtListArray[$val] = $valx['series'];
// }
// $dtImplode	= "".implode(", ", $dtListArray)."";

// echo $dtImplode;
?>

<div class="box box-primary">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">Series Old</th>
					<th class="text-center">Series New</th>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$no=0;
					foreach($ListBQipp AS $val=>$valx){
						$no++;
						echo "<tr>";
							echo "<td align='center' style='vertical-align: middle;'>".$no."</td>";
							echo "<td align='center' style='vertical-align: middle;'>".$valx['series']."</td>";
							echo "<td style='vertical-align: middle;'>";
							?> 
								<select name='series_new_<?=$no;?>' id='series_new_<?=$no;?>' class='form-control input-md chosen_select'>
								<?php
									foreach($ListSeries AS $valX => $valxX){
										$selx	= ($valx['series'] == $valxX['kode_group'])?'selected':''; 
										echo "<option value='".$valxX['kode_group']."' ".$selx.">".strtoupper($valxX['kode_group'])."</option>";
									}
								 ?> 
								</select>
							
							<?php
							echo "</td>";
							echo "<td align='center' style='vertical-align: middle;'><button type='button' id='series_change' data-id='".$no."' data-bq='".$id_bq."' data-series='".$valx['series']."' class='btn btn-sm btn-success'>Update</button></td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<style>
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
</style>
<script>
	swal.close();
	$(document).ready(function(){
		$('.chosen_select').chosen({width: '100%'});
	});
</script>