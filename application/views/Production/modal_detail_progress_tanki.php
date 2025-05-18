<div class="box box-primary">
	<div class="box-body">
		<!-- <button type='button' class='btn btn-sm btn-primary' id='print' style='float:right;' title='Print' data-id_produksi='<?=$id_produksi;?>'><i class='fa fa-print'> &nbsp;Print</i></button> -->
		
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='width: 3%;'class="no-sort">#</th>
					<th class="text-left">PRODUCT TYPE</th>
					<th class="text-center" style='width: 10%;'>NO SPK</th>
					<th class="text-left" style='width: 12%;'>SPEC</th>
					<th class="text-center" style='width: 8%;'>QTY ORDER</th>
					<th class="text-center" style='width: 8%;'>QTY ACTUAL</th>
					<th class="text-center" style='width: 8%;'>QTY BALANCE</th>
					<th class="text-center" style='width: 8%;'>QTY DELIVERY</th>
					<th class="text-center" style='width: 8%;'>QTY FG</th>
					<th class="text-center" style='width: 8%;'>PROGRESS</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$a=0;
					if(!empty($rowD)){
						foreach($rowD AS $val => $valx){
							$a++;

							//check delivery
							$sqlCheck3 		= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'kode_delivery !='=>NULL))->result();
							$sqlCheckDead 	= $this->db->select('COUNT(*) as Numc')->get_where('deadstok', array('id_milik'=>$valx['id_milik'],'no_ipp'=>str_replace('PRO-','',$valx['id_produksi']),'kode_delivery !='=>NULL))->result();
							$QTY_DELIVERY	= $sqlCheck3[0]->Numc + $sqlCheckDead[0]->Numc;

							$sqlCheck2 	= $this->db
												->select('COUNT(*) as Numc')
												->group_start()
												->group_start()
												->where('daycode !=', NULL)
												->where('daycode !=', '')
												->group_end()
												->or_where('id_deadstok_dipakai !=', NULL)
												->group_end()
												->get_where('production_detail', 
													array(
														'id_milik'=>$valx['id_milik'],
														'id_produksi'=>$valx['id_produksi']
														)
													)
												->result();
							$QTY_PRODUCT 		= $valx['qty'];
							$QTY 		= $valx['qty'];
							$ACT 		= $sqlCheck2[0]->Numc;
							$ACT_OUT 	= $sqlCheck2[0]->Numc;
							$balance 	= $QTY - $ACT;
							$progress = 0;
							if($ACT != 0 AND $QTY != 0){
							$progress 	= ($ACT/$QTY) *(100);
							}
							if($progress == 100){
								$bgc = '#75e975';
							}
							else if($progress == 0){
								$bgc = '#f65b5b';
							}
							else{
								$bgc = '#67a4ff';
							}
							$bal_dev	= $ACT_OUT - $QTY_DELIVERY;
							
							$spec = $tanki_model->get_spec($valx['id_milik']);

							echo "<tr>";
								echo "<td align='center'>".$a."</td>";
								echo "<td title='".$valx['id_milik']."'>".strtoupper($valx['id_product'])."</td>";
								echo "<td align='center'>".strtoupper($valx['no_spk'])."</td>";
								echo "<td>".$spec."</td>";
								echo "<td align='center'>".$QTY."</td>";
								echo "<td align='center'>".$ACT_OUT."</td>";
								echo "<td align='center'>".$balance."</td>";
								echo "<td align='center'>".$QTY_DELIVERY."</td>";
								echo "<td align='center'>".$bal_dev."</td>";
								echo "<td align='center' style='background-color: ".$bgc.";'><b>".number_format($progress,2)." %</b></td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<script>
swal.close();
$(".numberOnly").on("keypress keyup blur",function (event) {
	if ((event.which < 48 || event.which > 57 )) {
		event.preventDefault();
	}
});
</script>
