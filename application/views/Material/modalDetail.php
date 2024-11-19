<?php
$id_material = $this->uri->segment(3);

$qMaterial 	= "SELECT * FROM raw_materials WHERE id_material = '".$id_material."' ";
$row	= $this->db->query($qMaterial)->result_array();

$detailBQ	= $this->db->query("SELECT * FROM raw_material_bq_standard WHERE id_material = '".$id_material."' ")->result_array();
$detailEn	= $this->db->query("SELECT * FROM raw_material_engineer_standard WHERE id_material = '".$id_material."' ")->result_array();
$Supply		= $this->db->query("SELECT * FROM raw_material_supplier WHERE id_material = '".$id_material."' ")->result_array();

// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box box-primary">
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Material Name</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_material','name'=>'nm_material','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Material Name','readonly'=>'readonly'), $row[0]['nm_material']);
					echo form_input(array('type'=>'hidden','id'=>'id_material','name'=>'id_material','class'=>'form-control input-md'), $row[0]['id_material']);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Trade Name</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_dagang','name'=>'nm_dagang','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Trade Name','readonly'=>'readonly'), $row[0]['nm_dagang']);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Internasional Name</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_international','name'=>'nm_international','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name','readonly'=>'readonly'),$row[0]['nm_international']);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Type Material</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_international','name'=>'nm_international','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name','readonly'=>'readonly'),$row[0]['nm_category']);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Pieces Type</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_international','name'=>'nm_international','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Internasional Name','readonly'=>'readonly'),$row[0]['cost_satuan']);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Conversion Value</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nilai_konversi','name'=>'nilai_konversi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Conversion Value', 'data-decimal'=>'.', 'data-thousand'=>'', 'data-prefix'=>'', 'data-precision'=>'0', 'data-allow-zero'=>'true','readonly'=>'readonly'), number_format(floatval($row[0]['nilai_konversi']),2));
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Piece Of Kilogram</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'satuan_kg','name'=>'satuan_kg','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-','readonly'=>'readonly'),$row[0]['satuan_kg']);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Kilogram Saldo</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'saldo_kg','name'=>'saldo_kg','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-','readonly'=>'readonly'),$row[0]['saldo_kg']);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Description <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_textarea(array('id'=>'descr','name'=>'descr','class'=>'form-control input-md','rows'=>'2','cols'=>'75','autocomplete'=>'off','placeholder'=>'Description','readonly'=>'readonly'), strtoupper($row[0]['descr']));
				?>
			</div>
			<label class='label-control col-sm-2'><b>Status Material</b></label>
			<div class='col-sm-4'>
				<div class="input-group">
					<?php
						$status	= 'Active';
						$class	= 'bg-green';
						if($row[0]['flag_active'] == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
					?>
					<span style='width:100px;' class='badge <?=$class;?>'><?= $status;?></span>
				</div>
			</div>
		</div>
	</div>
	<span style='margin-left: 10px; font-size:14px;'><b>ENGINEERING STANDARD</b></span>
	<div class="box-body" style="">
		<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table_enEdit'>
				<tr class='bg-blue'>
					<th class="text-center" class="no-sort" width="50px">No</th>
					<th class="text-center">Standart ENG Name</th>
					<th class="text-center" style='width: 300px;'>Standard ENG Value</th>
					<th class="text-center" style='width: 250px;'>Descr ENG</th>
					<th class="text-center" style='width: 100px;'>Flag ENG</th>
				</tr>
			</thead>
			<tbody id='detail_body_enEd'>
				<?php
					$number = 0;
					foreach($detailEn AS $val =>$valx){
						$number++;
						$status	= 'Active';
						$class	= 'bg-green';
						if($valx['flag_active'] == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						?>
						<tr id="trenEd_<?= $number;?>">
							<td align='center'><?= $number;?></td>
							<td><div class='dataR' align='left'><?= ucfirst($valx['nm_standard']);?></div></td>
							<td><div class='dataR' style='margin-right:150px;' align='right'><?= ucfirst($valx['nilai_standard']);?></div></td>
							<td><div class='dataR'><?= ucfirst($valx['descr']);?></div></td>
							<td align='center'><div class='dataR'><span style='width:75px;' class='badge <?=$class;?>'><?= $status;?></span></div></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
	<span style='margin-left: 10px; font-size:14px;'><b>BQ STANDARD</b></span>

	<div class="box-body" style="">
		<table id="my-grid_bq" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table_bqEdit'>
				<tr class='bg-blue'>
					<th class="text-center" class="no-sort" width="50px">No</th>
					<th class="text-center">Standart BQ Name</th>
					<th class="text-center" style='width: 300px;'>Standard BQ Value</th>
					<th class="text-center" style='width: 250px;'>Descr BQ</th>
					<th class="text-center" style='width: 100px;'>Flag BQ</th>
				</tr>
			</thead>
			<tbody id='detail_body_bqEd'>
				<?php
					$number = 0;
					foreach($detailBQ AS $val =>$valx){
						$number++;
						$status	= 'Active';
						$class	= 'bg-green';
						if($valx['flag_active'] == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
						?>
						<tr id="trbqEd_<?= $number;?>">
							<td align='center'><?= $number;?></td>
							<td><div class='dataR' align='left'><?= ucfirst($valx['nm_standard']);?></div></td>
							<td><div class='dataR' style='margin-right:150px;' align='right'><?= ucfirst($valx['nilai_standard']);?></div></td>
							<td><div class='dataR'><?= ucfirst($valx['descr']);?></div></td>
							<td align='center'><div class='dataR'><span style='width:75px;' class='badge <?=$class;?>'><?= $status;?></span></div></td>
						</tr>
						<?php
					}
				?>
			</tbody>
		</table>
	</div>
</div>
