<?php
$department 	= (!empty($header))?$header[0]->department:'';
$costcenter 	= (!empty($header))?$header[0]->costcenter:'';
$tanda 			= (!empty($code))?'Update':'Insert';
?>

<div class="box box-primary" style='margin-right: 17px;'><br>
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Department <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<select name='department' id='department' class='form-control input-md' disabled>
					<option value='0'>Select An Department</option>
					<?php
						foreach(get_list_dept() AS $val => $valx){
							$dept = ($valx['id'] == $department)?'selected':'';
							echo "<option value='".$valx['id']."' ".$dept.">".$valx['nm_dept']."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Cost Center <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<select name='costcenter' id='costcenter' class='form-control input-md' disabled>
					<option value='0'>Select An Cost Center</option>
					<?php
						foreach(get_list_costcenter() AS $val => $valx){
							$cc = ($valx['id_costcenter'] == $costcenter)?'selected':'';
							echo "<option value='".$valx['id_costcenter']."' ".$cc.">".strtoupper($valx['nm_costcenter'])."</option>";
						}
					 ?>
				</select>
			</div>
		</div>
		<br>
		<?php
		foreach(get_list_jenis_rutin() AS $valH => $valxHeader){
			$detail 		= $this->db->query("SELECT * FROM budget_rutin_detail WHERE code_budget='".$code."' AND jenis_barang='".$valxHeader['id']."' ")->result_array();
			$jenis_barang2	= $this->db->query("SELECT * FROM con_nonmat_new WHERE category_awal='".$valxHeader['id']."' ORDER BY material_name ASC ")->result_array();
			echo "<h5><b>".strtoupper($valxHeader['category'])."</b></h5>";
			?>
			<table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class='text-center' style='width: 5%;'>#</th>
						<th class='text-center' style='width: 30%;'>Nama Barang</th>
						<th class='text-center'>Spesifikasi</th>
						<th class='text-center'>Brand</th>
						<th class='text-center' style='width: 10%;'>Kebutuhan 1 Bulan</th>
						<th class='text-center' style='width: 10%;'>Price From Supplier</th>
						<th class='text-center' style='width: 10%;'>Total Budget</th>
						<th class='text-center' style='width: 10%;'>Satuan</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$id = 0;
						$SUM_QTY=0;
						$SUM_BUDGET=0;
						if(!empty($detail)){
							foreach($detail AS $val => $valx){ $id++;
								$spec 		= get_name('con_nonmat_new', 'spec', 'code_group', $valx['id_barang']);
								$nm_barang 	= get_name('con_nonmat_new', 'material_name', 'code_group', $valx['id_barang']);
								$brand 		= get_name('con_nonmat_new', 'brand', 'code_group', $valx['id_barang']);
								$satuan 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);
								$price_from_supplier = (!empty($valx['price_from_supplier']))?$valx['price_from_supplier']:0;
								$total_budget = $price_from_supplier * $valx['kebutuhan_month'];
								echo "<tr class='header_".$valxHeader['id'].$id."'>";
									echo "<td align='center'>".$id."</td>";
									echo "<td align='left'>".strtoupper($nm_barang)."</td>";
									echo "<td align='left'>".strtoupper($spec)."</td>";
									echo "<td align='left'>".strtoupper($brand)."</td>";
									echo "<td align='center'>".number_format($valx['kebutuhan_month'])."</td>";
									echo "<td align='right'>".number_format($price_from_supplier,2)."</td>";
									echo "<td align='right'>".number_format($total_budget,2)."</td>";
									echo "<td align='center'>".strtoupper($satuan)."</td>";
								echo "</tr>";

								$SUM_QTY += $valx['kebutuhan_month'];
								$SUM_BUDGET += $total_budget;
							}
						}
						else{
					?>
					<tr>
						<td align='left' colspan='6'>List data empty ...</td>
					</tr>
						<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th align='center'></th>
						<th align='center'>TOTAL BUDGET</th>
						<th align='center'></th>
						<th align='center'></th>
						<th class='text-center'><?=number_format($SUM_QTY);?></th>
						<th align='center'></th>
						<th class='text-right'><?=number_format($SUM_BUDGET,2);?></th>
						<th align='center'></th>
					</tr>
				</tfoot>
			</table>
			<br>
			<?php
		}
		?>
	</div>
 </div>
 
 <script>
	swal.close();
 </script>