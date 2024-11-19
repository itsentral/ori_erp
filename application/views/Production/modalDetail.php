<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>IPP Number</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'IPP Number','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), str_replace('SO-','',$row[0]['so_number']));
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>SO Number</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'so_number','name'=>'so_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'SO Number', 'readonly'=>'readonly'), get_nomor_so(str_replace('SO-','',$row[0]['so_number'])));
					echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name', 'readonly'=>'readonly'), $row[0]['id_produksi']);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Machine</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Machine Name', 'readonly'=>'readonly'), $row[0]['nm_mesin']);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Plant Start Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_start_produksi'])));
			?>
			</div>
			<label class='label-control col-sm-2'><b>Plant End Production</b></label>
			<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_end_produksi'])));
			?>
			</div>
		</div>
		<button type='button' class='btn btn-sm btn-primary' id='btn_download' style='float:right;' title='Download Excel' data-id_produksi='<?=$id_produksi;?>'><i class='fa fa-file-excel-o'> &nbsp;Download Excel</i></button>
		&nbsp; <button type='button' class='btn btn-sm btn-success' id='btn_qrcode' style='float:right;' title='Print QR Code' data-id_produksi='<?=$id_produksi;?>'><i class='fa fa-qrcode'> &nbsp;Print QR Code</i></button>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center mid" style='width: 3%;'class="no-sort">NO</th>
					<th class="text-center mid" style='width: 8%;'>NO DELIVERY</th>
					<th class="text-left mid" style='width: 15%;'>PRODUCT TYPE</th>
					<th class="text-left mid" style='width: 12%;'>SPEC</th>
					<th class="text-left mid">PRODUCT NAME</th>
					<th class="text-center mid" style='width: 4%;'>BY</th>
					<th class="text-center mid" style='width: 7%;'>QTY SO</th>
					<th class="text-center mid" style='width: 7%;'>QTY APPROVE SO</th>
					<th class="text-center mid" style='width: 7%;'>QTY TURUN SPK</th>
					<th class="text-center mid" style='width: 7%;'>QTY BELUM TURUN SPK</th>
					<?php if($menu_baru == 0){ ?>
					<th class="text-center mid" style='width: 8%;'>QTY SISA SPK</th>
					<th class="text-center mid" style='width: 7%;'>TOTAL SPK</th>
					<?php } ?>
					<th class="text-center mid" style='width: 10%;'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$a=0;
					if(!empty($rowD)){
						foreach($rowD AS $val => $valx){
							$a++;
							$sqlCheck = $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'sts_produksi'=>'Y'))->result();
							$sqlCheckRed = $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'sts_produksi'=>'N'))->result();
							$sqlCheck2 = $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'print_merge'=>'N'))->result();
							$sqlCheck22 = $this->db->select('COUNT(*) as Numc')->get_where('production_detail', array('id_milik'=>$valx['id_milik'],'id_produksi'=>$valx['id_produksi'],'print_merge2'=>'N'))->result();
							
							$checkInput = $this->db
												->select('*')
												->from('production_detail')
												->where('kode_spk <>',NULL)
												->where('id_milik',$valx['id_milik'])
												->where('id_produksi',$valx['id_produksi'])
												// ->where("(upload_real = 'Y' OR upload_real2 = 'Y')")
												->get()
												->result();
							if(empty($checkInput)){
								$reject = "&nbsp;<button type='button' class='btn btn-sm btn-danger backToFD' data-id='".$valx['id_milik']."' title='Back To Final Drawing' data-role='qtip' ><i class='fa fa-reply'></i></button>";
							}
							else{
								$reject = "&nbsp;<button type='button' class='btn btn-sm btn-danger' title='Actual Produksi Sebagian sudah diinput' data-role='qtip' disabled><i class='fa fa-warning'></i></button>";
							}

							$by_from = 'SO';
							if($jalur == 'FD'){
								$by_from = 'SO';
								if(!empty($valx['id_milik2'])){
									$by_from = 'FD';
								}
							}

							$QTY_APP_SO = $sqlCheck[0]->Numc + $sqlCheckRed[0]->Numc;

							echo "<tr>";
								echo "<td align='center'>".$a."</td>";
								echo "<td align='center'>".strtoupper($valx['no_komponen'])."</td>";
								echo "<td>".strtoupper($valx['comp'])."</td>";
								echo "<td>".spec_fd($valx['id_uniq'], $HelpDet)."</td>";
								echo "<td>".$valx['id_product']."</td>";
								echo "<td align='center'>".$by_from."</td>";
								echo "<td align='center'><span class='badge bg-blue'>".$valx['qty']."</span></td>";
								echo "<td align='center'><span class='badge bg-purple'>".$QTY_APP_SO."</span></td>";
								echo "<td align='center'><span class='badge bg-green'>".$sqlCheck[0]->Numc."</span></td>";
								echo "<td align='center'><span class='badge bg-red'>".$sqlCheckRed[0]->Numc."</span></td>";
								if($menu_baru == 0){
								echo "<td align='center'><span class='badge bg-green'>".$sqlCheck2[0]->Numc."</span></td>";
								}
								if($sqlCheckRed[0]->Numc < 1){
									if($menu_baru == 0){
									echo "<td align='center'>";
									echo form_input(array('type'=>'hidden','id'=>'qty_bef','name'=>'qty_bef','id'=>'qty_bef_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'),$sqlCheck2[0]->Numc);
									echo form_input(array('type'=>'hidden','id'=>'qty_bef2','name'=>'qty_bef2','id'=>'qty_bef2_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'),$sqlCheck22[0]->Numc);
									echo form_input(array('type'=>'text','id'=>'qty_print','name'=>'qty_print','id'=>'qty_print_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'));
									echo "</td>";
									}
									echo "<td align='center'>";
									if($menu_baru == 0){
									echo "	<button type='button' class='btn btn-sm btn-success printMerge' data-nomor='$a' data-id='".$valx['id']."' data-spk='1' title='Print SPK 1' data-role='qtip' ".$Disb."><i class='fa fa-print'></i></button>";
									}
									echo "	<button type='button' class='btn btn-sm btn-primary' title='SPK sudah turun semua' data-role='qtip' ".$Disb."><i class='fa fa-check'></i></button>";
									echo $reject;
									echo "&nbsp; <input type='checkbox' value='".$valx['id_milik']."' name='cqr' class='cqr' title='QR Code'></td>";
								}
								else{
									if($menu_baru == 0){
									echo "<td align='center'>";
									echo form_input(array('type'=>'hidden','id'=>'qty_bef','name'=>'qty_bef','id'=>'qty_bef_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'),$sqlCheck2[0]->Numc);
									echo form_input(array('type'=>'hidden','id'=>'qty_bef2','name'=>'qty_bef2','id'=>'qty_bef2_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'),$sqlCheck22[0]->Numc);
									echo form_input(array('type'=>'text','id'=>'qty_print','name'=>'qty_print','id'=>'qty_print_'.$a,'class'=>'form-control input-sm numberOnly','style'=>'text-align:center;','autocomplete'=>'off','placeholder'=>'Qty'));
									echo "&nbsp; <input type='checkbox' value='".$valx['id_milik']."' name='cqr' class='cqr' title='QR Code'></td>";
									}
									echo "<td align='center'>";
									if($menu_baru == 0){
									echo "	<button type='button' class='btn btn-sm btn-success printMerge' data-nomor='$a' data-id='".$valx['id']."' data-spk='1' title='Print SPK 1' data-role='qtip' ".$Disb."><i class='fa fa-print'></i></button>";
									}
									echo "	<button type='button' class='btn btn-sm btn-success turunkanAllSpk' data-id_product = '".$valx['id_product']."' data-id_pro_detail = '".$valx['id_milik']."' data-id_produksi = '".$valx['id_produksi']."' title='Turunkan Semua SPK !' ".$Disb."><i class='fa fa-calendar-check-o '></i></button>";
									echo $reject;
									echo "&nbsp; <input type='checkbox' value='".$valx['id_milik']."' name='cqr' class='cqr' title='QR Code'></td>";
								}

							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='6'>Tidak ada data yang ditampilkan, mungkin hanya penjualan material atau aksesoris saja.</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
</div>

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
								echo "<td align='left'>".strtoupper(get_name_acc($valx['id_material']))."</td>";
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
		if(!empty($rest_mat)){?>
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
<style>
	.mid{
		vertical-align: middle !important;
	}
</style>
<script>
swal.close();
$(".numberOnly").on("keypress keyup blur",function (event) {
	if ((event.which < 48 || event.which > 57 )) {
		event.preventDefault();
	}
});
</script>
