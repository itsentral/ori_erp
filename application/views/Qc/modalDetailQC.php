
<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" width='5%'>No</th>
				<th class="text-center">Product</th>
				<th class="text-center" width='15%'>No SPK</th>
				<th class="text-center" width='10%'>SPEC</th>
				<th class="text-center" width='8%'>QTY</th>
				<th class="text-center" width='8%'>QTY QC</th>
				<th class="text-center" width='8%'>BALANCE</th>
				<th class="text-center" width='25%'>Detail</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$no=0;
				foreach($get_detail AS $val => $valx){ $no++;
                    $get_detail_        = $this->db->select('COUNT(id) AS qty_qc')->get_where('production_detail',array('qc_pass_date <>'=>NULL,'id_produksi'=>$valx['id_produksi'],'id_milik'=>$valx['id_milik']))->result();
                    $get_detail_list    = $this->db->order_by('daycode','ASC')->select('daycode, qc_pass_date')->get_where('production_detail',array('qc_pass_date <>'=>NULL,'id_produksi'=>$valx['id_produksi'],'id_milik'=>$valx['id_milik']))->result_array();
					$QTY_QC = $get_detail_[0]->qty_qc;
                    $BALANCE = $valx['qty'] - $QTY_QC;
                    $warna = 'bg-green';
                    if($BALANCE > 0){
                        $warna = 'bg-red';
                    }
                    echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".strtoupper($valx['id_category'])."</td>";
						echo "<td align='center'>".$valx['no_spk']."</td>";
						echo "<td align='left'>".spec_bq2($valx['id_milik'],'so_detail_header')."</td>";
						echo "<td align='center'><span class='badge bg-blue'>".$valx['qty']."</span></td>";
						echo "<td align='center'><span class='badge bg-green'>".$QTY_QC."</span></td>";
						echo "<td align='center'><span class='badge ".$warna."'>".$BALANCE."</span></td>";
						echo "<td align='left'>";
						if (in_array($valx['id_category'], NotInProductArray())) {
							echo "<span class='badge bg-green'>Tidak Perlu QC !!!</span>";
						}
						else{
							foreach ($get_detail_list as $key => $value) {$key++;
								echo "<b>".$key.'. '.date('d-M-Y',strtotime($value['qc_pass_date'])).' ('.$value['daycode'].')</b><br>';
							}
						}
						echo "</td>";
					echo "</tr>";
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