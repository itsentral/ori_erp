<?php
$id_supplier = $this->uri->segment(3);

$qSupplier 	= "	SELECT
					a.*,
					b.country_name,
					c.nama,
					d.nama AS nm_kab
				FROM
					supplier a 
					LEFT JOIN country b ON a.id_negara = b.country_code
					LEFT JOIN provinsi c ON a.id_prov = c.id_prov
					LEFT JOIN kabupaten d ON a.id_kab = d.id_kab
				WHERE
					a.id_supplier = '".$id_supplier."' ";
$row	= $this->db->query($qSupplier)->result_array();

// echo "<pre>";
// print_r($row);
// echo "</pre>";

?>
<div class="box box-primary">
	<br>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Supplier Name</b></label>
		<div class='col-sm-4'>              
			<?php
				echo form_input(array('id'=>'nm_supplier','name'=>'nm_supplier','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name', 'readonly' => 'readonly'), $row[0]['nm_supplier']);											
				echo form_input(array('type'=>'hidden','id'=>'id_supplier','name'=>'id_supplier','class'=>'form-control input-md','autocomplete'=>'off'), $row[0]['id_supplier']);											
			?>	
		</div>
		<label class='label-control col-sm-2'><b>Country</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('type'=>'text','id'=>'id_negara2','name'=>'id_negara2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name', 'readonly' => 'readonly'), $row[0]['country_name']);											
			?>
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Province</b></label>
		<div class='col-sm-4'>  
			<?php
				$nama = ($row[0]['id_prov'] == null || $row[0]['id_prov'] == '' || $row[0]['id_prov'] == 0)?'-':$row[0]['nama'];
				echo form_input(array('type'=>'text','id'=>'id_negara2','name'=>'id_negara2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name', 'readonly' => 'readonly'), $nama);											
			?>					 
		</div>
		<label class='label-control col-sm-2'><b>District</b></label>
		<div class='col-sm-4'>
			<?php
				$namaKab = ($row[0]['id_kab'] == null || $row[0]['id_kab'] == '' || $row[0]['id_kab'] == 0)?'-':$row[0]['nm_kab'];
				echo form_input(array('type'=>'text','id'=>'id_negara2','name'=>'id_negara2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Supplier Name', 'readonly' => 'readonly'),$namaKab);											
			?>	
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Currency</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('type'=>'text','id'=>'id_negara2','name'=>'id_negara2','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['mata_uang']);											
			?>	
		</div>
		<label class='label-control col-sm-2'><b>Telephone</b></label>
		<div class='col-sm-4'>            
			<?php
				echo form_input(array('id'=>'telpon','name'=>'telpon','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['telpon']);											
			?>
		</div>
	</div>	
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Telephone 2</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'telpon','name'=>'telpon','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['telpon2']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Telephone 3</b></label>
		<div class='col-sm-4'>            
			<?php
				echo form_input(array('id'=>'telpon','name'=>'telpon','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['telpon3']);											
			?>
		</div>
	</div>	
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Fax</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'fax','name'=>'fax','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['fax']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Email</b></label>
		<div class='col-sm-4'>          
			<?php
				echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['email']);											
			?>	
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Email 2</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['email2']);											
			?>	
		</div>
		<label class='label-control col-sm-2'><b>Email 3</b></label>
		<div class='col-sm-4'>          
			<?php
				echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['email3']);											
			?>	
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Contact Person</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'cp','name'=>'cp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['cp']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Contact</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'hp_cp','name'=>'hp_cp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['hp_cp']);											
			?>
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Webchat ID</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'id_webchat','name'=>'id_webchat','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['id_webchat']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Tax ID Number</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_input(array('id'=>'npwp','name'=>'npwp','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'T-', 'readonly' => 'readonly'),$row[0]['npwp']);											
			?>
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Address</b></label>
		<div class='col-sm-4'>             
			
			<?php
				echo form_textarea(array('id'=>'alamat','name'=>'alamat','class'=>'form-control input-md','rows'=>'3','cols'=>'75','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['alamat']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Tax ID Address</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_textarea(array('id'=>'alamat_npwp','name'=>'alamat_npwp','class'=>'form-control input-md','rows'=>'3','cols'=>'75','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['alamat_npwp']);											
			?>
		</div>
	</div>
	<div class='form-group row'>		 	 
		<label class='label-control col-sm-2'><b>Information</b></label>
		<div class='col-sm-4'>             
			<?php
				echo form_textarea(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','rows'=>'3','cols'=>'75','autocomplete'=>'off','placeholder'=>'-', 'readonly' => 'readonly'), $row[0]['keterangan']);											
			?>
		</div>
		<label class='label-control col-sm-2'><b>Bank Account</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'data_bank','name'=>'data_bank','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Bank Account'),$row[0]['data_bank']);
			?>
		</div>

		<label class='label-control col-sm-2'><br><b>Flag Active</b></label>
		<div class='col-sm-4'><br>
			<?php
						$status	= 'Active';
						$class	= 'bg-green';
						if($row[0]['sts_aktif'] == 'N'){
							$class	= 'bg-red';
							$status	= 'Not Active';
						}
					?>
					<span class='badge <?=$class;?>'><?= $status;?></span>
		</div>
	</div>
</div>

<script>
	swal.close();
</script>