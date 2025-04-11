
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
					<th class="text-center" style='vertical-align:middle;' width='29%'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='15%'>Material</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Detail</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$no = 0;
					foreach($detail AS $val => $valx){ $no++;
						$spaces = "";
						$id_delivery = strtoupper($valx['id_delivery']);
						$bgwarna	= "bg-blue";
							
						$SumQty	= $valx['sum_mat'] * $valx['qty'];
						$Sum += $SumQty;
						if($valx['sts_delivery'] == 'CHILD'){
							$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
							$id_delivery = strtoupper($valx['sub_delivery']);
							$bgwarna	= "bg-green";
						}
						echo "<tr>";
							echo "<td align='center'>".$no."</span></td>";
							echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
							echo "<td align='left'>".$valx['no_komponen']."</span></td>";
							echo "<td align='left' style='padding-left:20px;'>".spec_fd($valx['id'], 'so_detail_header')."</td>";
							echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
							echo "<td align='left'>".$valx['id_product']."</span></td>";
							echo "<td align='right' style='padding-right:20px;'>".number_format($SumQty, 3)." Kg</span></td>";
							echo "<td align='center' ><button class='btn btn-sm btn-warning detail_data' title='Detail Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button></td>";						
						echo "</tr>";
					}
				?>
				<tr>
					<th class="text-center" colspan='6' style='vertical-align:middle;'>Total</th> 
					<th class="text-right" style='padding-right:20px;'><?= number_format($Sum, 3);?> Kg</th>
					<th class="text-center" style='vertical-align:middle;'></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>B. MUR BAUT</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='38%'>Material</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='19%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail3)){
						foreach($detail3 AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
							echo "<tr class='header3_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->material)."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='6'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>C. PLATE</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='10%'>Ukuran Standart</th>
					<th class="text-center" width='10%'>Standart</th>
					<th class="text-center" width='9%'>Lebar (mm)</th>
					<th class="text-center" width='9%'>Panjang (mm)</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Berat (kg)</th>
					<th class="text-center" width='9%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4)){
						foreach($detail4 AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							echo "<tr class='header4_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='right'>".number_format($valx['berat'],3)."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>D. GASKET</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Standart</th>
					<th class="text-center" width='12%'>Dimensi</th>
					<th class="text-center" width='9%'>Lebar (mm)</th>
					<th class="text-center" width='9%'>Panjang (mm)</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='9%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4g)){
						foreach($detail4g AS $val => $valx){ $id++;
							$get_detail = $this->db->get_where('accessories', array('id'=>$valx['id_material']))->result();
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->dimensi)."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>E. LAINNYA</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='20%'>Ukuran Standart</th>
					<th class="text-center" width='18%'>Standart</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='19%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail5)){
						foreach($detail5 AS $val => $valx){ $id++;
							$get_detail = $this->db->select('nama, material, spesifikasi, standart, ukuran_standart, dimensi')->get_where('accessories', array('id'=>$valx['id_material']))->result();
							echo "<tr class='header5_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->ukuran_standart)."</td>";
								echo "<td align='left'>".strtoupper($get_detail[0]->standart)."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-info">
	<div class="box-header">
		<label>F. MATERIAL</label>
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
		swal.close();
	});
</script>