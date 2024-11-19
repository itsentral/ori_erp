
<div class="box box-primary">
	<div class="box-body"><br>
		<div class="form-group row">
			<div class="col-md-2">
				<label for="inventory_1">PRODDUCT NAME</label>
			</div>
			 <div class="col-md-8">
					<input type="text" class="form-control input-sm" id="spec6"  name="spec6" readonly="readonly" value="<?= strtoupper(get_name('ms_inventory_category2', 'nama', 'id_category2', $header[0]->id_product)); ?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-12">
				<div class="tableFixHead" style="height:600px;">
					<table id="example1" border='0' width='100%' class="table table-striped table-bordered table-hover table-condensed">
						<thead  class="thead">
							<tr class='bg-blue'>
								<th class='text-center th' width='7%'>#</th>
								<th class='text-center th'>Cost Center</th>
								<th class='text-center th' width='15%'>Machine</th>
								<th class='text-center th' width='15%'>Mould/Tools</th>
								<th class='text-center th' width='20%'>Information</th>
							</tr>
						</thead>
						<tbody>
							<?php
							  $q_header_test = $this->db->query("SELECT * FROM cycletime_detail_header WHERE id_time='".$header[0]->id_time."'")->result_array();
							$nox = 0;
							  foreach($q_header_test AS $val2 => $val2x){ $nox++;
									$CT2 = (get_name('asset', 'nm_asset', 'kd_asset', $val2x['machine']) != '0')?get_name('asset', 'nm_asset', 'kd_asset', $val2x['machine']):'';
									$MP2 = (get_name('asset', 'nm_asset', 'kd_asset', $val2x['mould']) != '0')?get_name('asset', 'nm_asset', 'kd_asset', $val2x['mould']):'';
							  echo "<tr>";
								echo "<td align='center'>".$nox."</td>";
								echo "<td align='left'><b>".strtoupper(get_name('ms_costcenter', 'nama_costcenter', 'id_costcenter', $val2x['costcenter']))."</b></td>";
								echo "<td align='right'><b>".strtoupper($CT2)."</b></td>";
								echo "<td align='right'><b>".strtoupper($MP2)."</b></td>";
								echo "<td align='left'></td>";
							  echo "</tr>";
								$q_dheader_test = $this->db->query("SELECT * FROM cycletime_detail_detail WHERE id_costcenter='".$val2x['id_costcenter']."'")->result_array();
								foreach($q_dheader_test AS $val2D => $val2Dx){ $val2D++;
								  $nomor = ($val2D==1)?$val2D:'';

									$CT = ($val2Dx['cycletime'] != 0)?$val2Dx['cycletime']:'';
									$MP = ($val2Dx['qty_mp'] != 0)?$val2Dx['qty_mp']:'';
								  echo "<tr>";
									echo "<td align='center'></td>";
									echo "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".strtoupper(get_name('ms_process', 'nm_process', 'id', $val2Dx['id_process']))."</td>";
									echo "<td align='right'>".$CT." <b>minutes</b></td>";
									echo "<td align='right'>".$MP." <b>MP</b></td>";
									echo "<td align='left'>".$val2Dx['note']."</td>";
								  echo "</tr>";
								}
							  }
							  ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<style media="screen">
  /* JUST COMMON TABLE STYLES... */
  .table { border-collapse: collapse; width: 100%; }
  .td { background: #fff; padding: 8px 16px; }

  .tableFixHead {
    overflow: auto;
    height: 300px;
    position: sticky;
    top: 0;
  }

  .thead .th {
    position: sticky;
    top: 0;
    z-index: 9999;
  	background: #0073b7;
  }
</style>
