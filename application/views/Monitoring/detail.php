
<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center">#</th>
				<th class="text-center">No SO</th>
				<th class="text-left">Product</th>
				<th class="text-left">Spec</th>
				<th class="text-center">No SPK</th>
				<th class="text-center">Qty SO</th>
				<th class="text-center">Ots Qty SO</th>
				<th class="text-center">Kode SPK</th>
				<th class="text-center">SPK Created</th>
				<th class="text-center">Qty SPK</th>
				<th class="text-center">Lap.Produksi Date</th>
				<th class="text-center">QC</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				foreach($result AS $val => $valx){ $no++;
                    $no_ipp     = str_replace('BQ-','',$valx['id_bq']);
                    $so_number  = (!empty($GET_RELEASE_IPP[$no_ipp]['so_number']))?$GET_RELEASE_IPP[$no_ipp]['so_number']:'-';
					$getQuery 	= $this->db->get_where('production_spk_parsial',array('id_milik'=>$valx['id'],'spk'=>'1'))->result_array();
					$COUNT 		= COUNT($getQuery) + 1;

					$SUM_SPK = 0;
					if(!empty($getQuery)){
						foreach ($getQuery as $key => $value) {
							$SUM_SPK += $value['qty'];
						}
					}

					$Outstanding_SPK = $valx['qty'] - $SUM_SPK;

					echo "<tr>";
						echo "<td align='center' rowspan='".$COUNT."'>".$no."</td>";
						echo "<td align='center' rowspan='".$COUNT."'>".$so_number."</td>";
						echo "<td align='left' rowspan='".$COUNT."'>".strtoupper($valx['id_category'])."</td>";
						echo "<td align='left' rowspan='".$COUNT."'>".spec_bq2($valx['id'])."</td>";
                        echo "<td align='center' rowspan='".$COUNT."'>".$valx['no_spk']."</td>";
						echo "<td align='center' rowspan='".$COUNT."'>".number_format($valx['qty'])."</td>";
						echo "<td align='center' rowspan='".$COUNT."'>".number_format($Outstanding_SPK)."</td>";
					echo "</tr>";
					
					if(!empty($getQuery)){
						foreach ($getQuery as $key => $value) {
							$closing_produksi_date = (!empty($value['closing_produksi_date']))?date('d-M-Y',strtotime($value['closing_produksi_date'])):'-';

							$getQC 	= $this->db->select('qc_pass_date, COUNT(id) AS qty')->group_by('qc_pass_date')->get_where('production_detail',array('kode_spk'=>$value['kode_spk'],'print_merge2_date'=>$value['created_date'],'qc_pass_date !='=>NULL))->result_array();
							$ArrDaycode = [];
							if(!empty($getQC)){
								foreach ($getQC as $key2 => $value2) {
									$ArrDaycode[] = date('d-M-Y',strtotime($value2['qc_pass_date'])).' ('.$value2['qty'].')';
								}
							}

							$DaycodeHTML = implode('<br>',$ArrDaycode);
							
							echo "<tr>";
								echo "<td align='center'>".$value['kode_spk']."</td>";
								echo "<td align='center'>".date('d-M-Y',strtotime($value['created_date']))."</td>";
								echo "<td align='center'>".$value['qty']."</td>";
								echo "<td align='center'>".$closing_produksi_date."</td>";
								echo "<td align='left'>".$DaycodeHTML."</td>";
							echo "</tr>";
						}
					}
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