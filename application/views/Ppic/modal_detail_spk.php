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
		<button type='button' class='btn btn-sm btn-primary' id='btn_download' style='float:right;' title='Download Excel' data-id_produksi='<?=$id_produksi;?>'><i class='fa fa-file-excel-o'> &nbsp;Download Excel</i></button>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='width: 3%;'class="no-sort">NO</th>
					<th class="text-center" style='width: 8%;'>NO DELIVERY</th>
					<th class="text-left" style='width: 15%;'>PRODUCT TYPE</th>
					<th class="text-left" style='width: 12%;'>SPEC</th>
					<th class="text-left">PRODUCT NAME</th>
					<th class="text-center" style='width: 4%;'>BY</th>
					<th class="text-center" style='width: 7%;'>QTY</th>
					<th class="text-center" style='width: 12%;'>#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$a=0;
					if(!empty($rowD)){
						foreach($rowD AS $val => $valx){
							$a++;
							
                            $by_from = 'SO';
							if($jalur == 'FD'){
								$by_from = 'SO';
								if(!empty($valx['id_milik2'])){
									$by_from = 'FD';
								}
							}

							$get_qty = $this->db->group_by('id_bq_header')->select('COUNT(id) AS sisa')->get_where('so_detail_detail', array('id_bq_header'=>$valx['id_bq_header'],'approve'=>'P'))->result();

							echo "<tr>";
								echo "<td align='center'>".$a."</td>";
								echo "<td align='center'>".strtoupper($valx['no_komponen'])."</td>";
								echo "<td>".strtoupper($valx['comp'])."</td>";
								echo "<td>".spec_fd($valx['id_uniq'], $HelpDet)."</td>";
								echo "<td>".$valx['id_product']."</td>";
								echo "<td align='center'>".$by_from."</td>";
								echo "<td align='center'><span class='badge bg-blue'>".$valx['qty']."</span></td>";
								echo "<td align='center'>";
                                if($valx['app_so'] == 'P' OR $get_qty[0]->sisa > 0){
								    echo "<span class='text-green text-bold printMerge' data-id_uniq='".$valx['id_milik']."' data-id_produksi='".$valx['id_produksi']."' placeholder='Print Per 1 QTY'>Print</span>";
                                }
                                else{
                                    echo "<span class='text-red text-bold'>Waiting Process!!!</span>";
                                }
                                echo "</td>";

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
    .printMerge{
        cursor:pointer;
    }
</style>
<script>
swal.close();
</script>
