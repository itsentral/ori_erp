<?php
$id_plant = $this->uri->segment(3);

$qSupplier 	= "	SELECT
					*
				FROM
					company_plants a 
					LEFT JOIN branch b ON a.kdcab = b.nocab
				WHERE
					a.id_plant = '".$id_plant."' ";
$row	= $this->db->query($qSupplier)->result_array();

// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Plant Name<span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'nm_plant','name'=>'nm_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['nm_plant']);
				echo form_input(array('type'=>'hidden','id'=>'id_plant','name'=>'id_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_plant']);
			?>				
		</div>
		<label class='label-control col-sm-2'><b>Initial Plants</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'inisial_plant','name'=>'inisial_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','maxlength'=>'10', 'style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['inisial_plant']);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Branch<span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
		<?php
			echo form_input(array('id'=>'inisial_plant','name'=>'inisial_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','maxlength'=>'10', 'style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['cabang']);
		?>
		</div>
		<label class='label-control col-sm-2'><b>Province <span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
		<?php
			echo form_input(array('id'=>'inisial_plant','name'=>'inisial_plant','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','maxlength'=>'10', 'style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['province']);
		?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Phone <span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'phone','name'=>'phone','class'=>'form-control input-md','placeholder'=>'User Phone', 'readonly'=>'readonly'), $row[0]['phone']);
			?>
		</div>
		<label class='label-control col-sm-2'><b>Fax <span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'fax','name'=>'fax','class'=>'form-control input-md','placeholder'=>'Fax Number', 'readonly'=>'readonly'), $row[0]['fax']);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Email <span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
			<?php
			 echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','placeholder'=>'Email company plant', 'readonly'=>'readonly'), $row[0]['email']);
			?>
		</div>
		
		<label class='label-control col-sm-2'><b>Address</b></label>
		<div class='col-sm-4'>
			 <?php
				// echo form_hidden('id',$row[0]->kode_divisi);
				echo form_textarea(array('id'=>'address','name'=>'address','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Address company plants', 'readonly'=>'readonly'), $row[0]['address']);
			?>
		</div>
	</div>
</div>