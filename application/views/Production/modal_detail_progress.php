<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body">
		<button type='button' class='btn btn-sm btn-primary' id='print' style='float:right;' title='Print' data-id_produksi='<?=$id_produksi;?>'><i class='fa fa-print'> &nbsp;Print</i></button>
		
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='width: 3%;'class="no-sort">#</th>
					<th class="text-left" style='width: 15%;'>PRODUCT TYPE</th>
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
					$GET_MATERIAL_FIELD = get_MaterialOutJoint();
					$GET_MATERIAL_FIELD_EST = get_MaterialEstJoint();
					if(!empty($rowD)){
						foreach($rowD AS $val => $valx){
							$a++;

							//check delivery
							$sqlCheck3 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'kode_delivery !='=>NULL))->result();
							$QTY_DELIVERY	=$sqlCheck3[0]->Numc;

							//check selain shop joint & type field
							if($valx['typeProduct'] != 'field'){
								$sqlCheck2 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'daycode !='=>NULL))->result();
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
								$bal_dev	=$ACT_OUT - $QTY_DELIVERY;
							}
							//check type field
							if($valx['typeProduct'] == 'field'){
								$sqlCheck2 	= $this->db->select('SUM(qty) as Numc')->get_where('outgoing_field_joint', array('id_milik'=>$valx['id_milik'],'no_ipp'=>str_replace('PRO-','',$valx['id_produksi']),'deleted_date'=>NULL))->result();
								$QTY_PRODUCT 		= $valx['qty'];
								$QTY 		= $valx['qty'];
								$ACT 		= $sqlCheck2[0]->Numc;
								$ACT_OUT 	= number_format($sqlCheck2[0]->Numc);
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
								$bal_dev	=$ACT_OUT-$QTY_DELIVERY;
							}
							//check shop joint
							if (in_array($valx['comp'], NotInProductArray())) {
								$sqlCheck2 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi']))->result();
								$QTY 		= number_format($valx['qty']);
								$QTY_ 		= $valx['qty'];
								
								$checkActShopJoin 	= $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'closing_produksi_date !='=>NULL))->result();
								$ACT_OUT 	= number_format($checkActShopJoin[0]->Numc);
								$ACT_OUT_ 	= $checkActShopJoin[0]->Numc;

								$balance 	= number_format($QTY_ - $ACT_OUT_);
								$progress = 0;
								if($ACT_OUT_ != 0 AND $QTY_ != 0){
									$progress 	= ($ACT_OUT_/$QTY_) *(100);
								}

								$bal_dev	=$ACT_OUT - $QTY_DELIVERY;
								if($progress == 100){
									$bgc = '#75e975';
								}
								else if($progress == 0){
									$bgc = '#f65b5b';
								}
								else{
									$bgc = '#67a4ff';
								}
							}
							echo "<tr>";
								echo "<td align='center'>".$a."</td>";
								echo "<td title='".$valx['id_milik']."'>".strtoupper($valx['comp'])."</td>";
								echo "<td align='center'>".strtoupper($valx['no_spk'])."</td>";
								echo "<td>".spec_fd($valx['id_uniq'], $HelpDet)."</td>";
//								echo "<td>".$valx['id_product']."</td>";
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
							echo "<td colspan='9'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<!--
<div class="box box-success">
	<div class="box-header">
		<label>B. BILL OF QUANTITY NON FRP</label>
		<?php
		if(!empty($rest_acc)){?>
		<button type='button' class='btn btn-sm btn-success spk_mat_acc' style='float:right;' title='Print SPK BQ NON FRP' data-tanda='acc' data-id_bq='<?=$id_bq;?>'><i class='fa fa-print'> &nbsp;Print SPK</i></button>
		<?php } ?>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>NO</th>
					<th class="text-left">MATERIAL NAME</th>
					<th class="text-center" width='15%'>QTY</th>
					<th class="text-center" width='15%'>UNIT</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($rest_acc)){
						foreach($rest_acc AS $val => $valx){ $id++;
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper(get_name('con_nonmat_new','material_name','code_group',$valx['id_material']))."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='4'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-info">
	<div class="box-header">
		<label>C. MATERIAL</label>
		<?php
		if(!empty($rest_acc)){?>
		<button type='button' class='btn btn-sm btn-info spk_mat_acc' style='float:right;' title='Print SPK Material' data-tanda='mat' data-id_bq='<?=$id_bq;?>'><i class='fa fa-print'> &nbsp;Print SPK</i></button>
		<?php } ?>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>NO</th>
					<th class="text-left">MATERIAL NAME</th>
					<th class="text-center" width='15%'>QTY</th>
					<th class="text-center" width='15%'>UNIT</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($rest_mat)){
						foreach($rest_mat AS $val => $valx){ $id++;
		
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>"; 
								echo "<td align='left'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']))."</td>";
								echo "<td align='center'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan','1')."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='4'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>
-->
<script>
swal.close();
$(".numberOnly").on("keypress keyup blur",function (event) {
	if ((event.which < 48 || event.which > 57 )) {
		event.preventDefault();
	}
});
</script>
