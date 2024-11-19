
<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='4%'>No</th>
					<th class="text-center" style='vertical-align:middle;' width='16%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>No Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Detail</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$no = 0;
					foreach($detail_product AS $val => $valx){ 
						if($valx['qty'] > 0){
							$no++;
							$spaces = "";
							$id_delivery = strtoupper($valx['id_delivery']);
							$bgwarna	= "bg-blue";
								
							if($valx['sts_delivery'] == 'CHILD'){
								$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$id_delivery = strtoupper($valx['sub_delivery']);
								$bgwarna	= "bg-green";
							}
							echo "<tr>";
								echo "<td align='center'>".$no."</span></td>";
								echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
								echo "<td align='left'>".$valx['no_komponen']."</span></td>";
								echo "<td align='left' style='padding-left:20px;'>".spec_bq($valx['id_milik'])."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
								echo "<td align='left'>".$valx['id_product']."</span></td>";
								echo "<td align='center'>"; 
									echo "<button class='btn btn-sm btn-warning detailDT' title='Detail Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button>"; 
									if(!empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-primary detailX' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
									}
								echo "</td>";						
							echo "</tr>";
						}
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>B. BILL OF QUANTITY NON FRP</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>No</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail)){
						foreach($detail AS $val => $valx){ $id++;
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper(get_name('con_nonmat_new','material_name','code_group',$valx['id_material']))."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='left'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
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
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>No</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Qty</th>
					<th class="text-center" width='15%'>Unit</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail2)){
						foreach($detail2 AS $val => $valx){ $id++;
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>"; 
								echo "<td align='left'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']))."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='left'>".get_name('raw_pieces','kode_satuan','id_satuan','1')."</td>";
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

<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({width: '100%'});
		swal.close();
	});
</script>