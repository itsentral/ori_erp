<?php
$id_category = $this->uri->segment(3);

$qMaterial 	= "SELECT * FROM raw_categories WHERE id_category = '".$id_category."' ";
$row	= $this->db->query($qMaterial)->result_array();

$detailBQ	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id_category."' AND type='BQ' ")->result_array();
$detailEn	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id_category."' AND type='ENG' ")->result_array();

$NumdetailBQ	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id_category."' AND type='BQ' ")->num_rows();
$NumdetailEn	= $this->db->query("SELECT * FROM raw_category_standard WHERE id_category = '".$id_category."' AND type='ENG' ")->num_rows();

// echo "<pre>";
// echo $NumdetailBQ."<br>";
// echo $NumdetailEn."<br>";
// echo "</pre>";

?>
<div class="box box-primary">
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Category Name</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'category','name'=>'category','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Menu Name', 'readonly'=>'readonly'), $row[0]['category']);	
					echo form_input(array('type'=>'hidden','id'=>'id_category','name'=>'id_category','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Menu Name'), $row[0]['id_category']);								
				?>		
			</div>
			<label class='label-control col-sm-2'><b>Status Category</b></label>
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
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Description</b></label>
			<div class='col-sm-4'>
				<?php
					echo form_textarea(array('id'=>'descr','name'=>'descr','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Description type material', 'readonly'=>'readonly'), $row[0]['descr']);											
				?>
			</div>
		</div>
		<br>
		<span style='margin-left: 10px; font-size:14px;'><b>ENGINEERING STANDARD</b></span>
		<div class="box-body" style="">
			<table id="my-grid_en" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table_enEdit'>
					<tr class='bg-blue'>
						<th class="text-center" class="no-sort" width="50px">No</th>
						<th class="text-center"  style='width: 400px;'>Standard Name</th>
						<th class="text-center">Description</th>
						<th class="text-center" style='width: 100px;'>Flag</th>
					</tr>
				</thead>
				<tbody id='detail_body_enEd'>
				<?php 
				if($NumdetailEn > 0){
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
							<td>
								<div class='dataR'><?= ucwords(strtolower($valx['nm_category_standard']));?></div>
							</td>
							<td>
								<div class='dataR'><?= ucfirst($valx['descr']);?></div>
							</td>
							<td align='center'>
								<div class='dataR'><span style='width:75px;' class='badge <?=$class;?>'><?= $status;?></span></div>
							</td>
						</tr>
						<?php
					}
					
				} 
				else{
					echo "<td colspan='4'>No data displayed</td>";
				}
				?>
				</tbody>
			</table>
		</div>
		<br>
		<span style='margin-left: 10px; font-size:14px;'><b>BQ STANDARD</b></span>
		<div class="box-body" style="">
			<table id="my-grid_bq" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<thead id='head_table_bqEdit'>
					<tr class='bg-blue'>
						<th class="text-center" class="no-sort" width="50px">No</th>
						<th class="text-center"  style='width: 400px;'>Standard Name</th>
						<th class="text-center">Description</th>
						<th class="text-center" style='width: 100px;'>Flag</th>
					</tr>
				</thead>
				<?php 
				if($NumdetailBQ > 0){
				?>
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
									<td>
										<div class='dataR'><?= ucwords(strtolower($valx['nm_category_standard']));?></div>
									</td>
									<td>
										<div class='dataR'><?= ucfirst($valx['descr']);?></div>
									</td>
									<td align='center'>
										<div class='dataR'><span style='width:75px;' class='badge <?=$class;?>'><?= $status;?></span></div>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				<?php 
					} 
				else{
					echo "<td colspan='4'>No data displayed</td>";
				}
				?>
			</table>
		</div>
	</div>
</div>