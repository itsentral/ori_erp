<?php
$this->load->view('include/side_menu'); 
//echo"<pre>";print_r($data_menu);
$id					= (!empty($header[0]->id))?$header[0]->id:'';
$nik				= (!empty($header[0]->id))?$header[0]->nik:'';
$nm_karyawan		= (!empty($header[0]->id))?strtoupper($header[0]->nm_karyawan):'';
$no_ktp				= (!empty($header[0]->id))?$header[0]->no_ktp:'';
$tmp_lahir			= (!empty($header[0]->id))?strtoupper($header[0]->tmp_lahir):'';
$tgl_lahir			= (!empty($header[0]->id))?$header[0]->tgl_lahir:'';
$gender				= (!empty($header[0]->id))?$header[0]->gender:'';
$agama				= (!empty($header[0]->id))?$header[0]->agama:'';
$department			= (!empty($header[0]->id))?$header[0]->department:'';
$cost_center		= (!empty($header[0]->id))?$header[0]->cost_center:'';
$no_ponsel			= (!empty($header[0]->id))?$header[0]->no_ponsel:'';
$email				= (!empty($header[0]->id))?$header[0]->email:'';
$pendidikan			= (!empty($header[0]->id))?$header[0]->pendidikan:'';
$position			= (!empty($header[0]->id))?$header[0]->position:'';
$ktp_provinsi		= (!empty($header[0]->id))?$header[0]->ktp_provinsi:'';
$domisili_provinsi	= (!empty($header[0]->id))?$header[0]->domisili_provinsi:'';
$ktp_kota			= (!empty($header[0]->id))?$header[0]->ktp_kota:'';
$domisili_kota		= (!empty($header[0]->id))?$header[0]->domisili_kota:'';
$ktp_kecamatan		= (!empty($header[0]->id))?$header[0]->ktp_kecamatan:'';
$domisili_kecamatan	= (!empty($header[0]->id))?$header[0]->domisili_kecamatan:'';
$ktp_kelurahan		= (!empty($header[0]->id))?$header[0]->ktp_kelurahan:'';
$domisili_kelurahan	= (!empty($header[0]->id))?$header[0]->domisili_kelurahan:'';
$ktp_kode_pos		= (!empty($header[0]->id))?$header[0]->ktp_kode_pos:'';
$domisili_kode_pos	= (!empty($header[0]->id))?$header[0]->domisili_kode_pos:'';
$ktp_alamat			= (!empty($header[0]->id))?$header[0]->ktp_alamat:'';
$domisili_alamat	= (!empty($header[0]->id))?$header[0]->domisili_alamat:'';
$npwp				= (!empty($header[0]->id))?$header[0]->npwp:'';
$bpjs				= (!empty($header[0]->id))?$header[0]->bpjs:'';
$tgl_join			= (!empty($header[0]->id))?$header[0]->tgl_join:'';
$tgl_end			= (!empty($header[0]->id))?$header[0]->tgl_end:'';
$rek_number			= (!empty($header[0]->id))?$header[0]->rek_number:'';
$bank_account		= (!empty($header[0]->id))?$header[0]->bank_account:'';
$sts_karyawan		= (!empty($header[0]->id))?$header[0]->sts_karyawan:'';
$status				= (!empty($header[0]->id))?$header[0]->status:'';
// echo "Agama = ".$agama;
?> 
<form action="#" method="POST" id="form_employee" autocomplete='off'>   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
            <div class="box box-warning">
                <div class="box-header">
                    <h3 class="box-title">Personal</h3>		
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Employee Name <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>              
                        <?php
							echo form_input(array('type'=>'hidden','id'=>'id','name'=>'id','class'=>'form-control input-md'),$id);
							echo form_input(array('type'=>'hidden','id'=>'nik','name'=>'nik','class'=>'form-control input-md'),$nik);			
                            echo form_input(array('id'=>'nm_karyawan','name'=>'nm_karyawan','class'=>'form-control input-md'),$nm_karyawan);											
                        ?>	
                    </div>
                    <label class='label-control col-sm-2'><b>ID Number <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>              
                        <?php
                            echo form_input(array('id'=>'no_ktp','name'=>'no_ktp','class'=>'form-control input-md','placeholder'=>'ID Number'),$no_ktp);											
                        ?>	
                    </div>
                </div>
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Place of birth <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>              
                        <?php
                            echo form_input(array('id'=>'tmp_lahir','name'=>'tmp_lahir','class'=>'form-control input-md','placeholder'=>'Place of birth'),$tmp_lahir);											
                        ?>	
                    </div>
                    <label class='label-control col-sm-2'><b>Date of birth <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>              
                        <?php
                            echo form_input(array('id'=>'tgl_lahir','name'=>'tgl_lahir','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Date of birth'),$tgl_lahir);											
                        ?>		
                    </div>
                </div>
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Religion <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='agama' id='agama' class='form-control input-md'>
                            <option value='0'>Select An Religion</option>
                            <?php
							foreach($agamax AS $val => $valx){
								$selected = ($valx['name'] == $agama)?'selected':'';
								echo "<option value='".$valx['name']."' ".$selected.">".strtoupper($valx['data1'])."</option>";
							}
							?>
                        </select>	
                    </div>
                    <label class='label-control col-sm-2'><b>Gender <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='gender' id='gender' class='form-control input-md'>
                            <option value='0'>Select An Gender</option>
                            <?php
							foreach($genderx AS $val => $valx){
								$selected = ($valx['name'] == $gender)?'selected':'';
								echo "<option value='".$valx['name']."' ".$selected.">".strtoupper($valx['data1'])."</option>";
							}
							?>
                        </select>	
                    </div>
                </div>	
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Department <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='department' id='department' class='form-control input-md'>
                            <option value='0'>Select An Department</option>
							<?php
							foreach($departmentx AS $val => $valx){
								$selected = ($valx['id'] == $department)?'selected':'';
								echo "<option value='".$valx['id']."' ".$selected.">".$valx['nm_dept']."</option>";
							}
							?>
                        </select>
                    </div>
                    <label class='label-control col-sm-2'><b>Division <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='cost_center' id='cost_center' class='form-control input-md'>
                            <option value='0'>List Empty</option>
                        </select>		
                    </div>
                </div>
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Contact Number <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>             
                        <?php
                            echo form_input(array('id'=>'no_ponsel','name'=>'no_ponsel','class'=>'form-control input-md numberOnly','placeholder'=>'Contact Number'),$no_ponsel);											
                        ?>
                    </div>
                    <label class='label-control col-sm-2'><b>Email <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>          
                        <?php
                            echo form_input(array('id'=>'email','name'=>'email','class'=>'form-control input-md','placeholder'=>'Email'),$email);											
                        ?>	
                    </div>
                </div>
                <div class='form-group row'>		 	 
                    <label class='label-control col-sm-2'><b>Last Education <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='pendidikan' id='pendidikan' class='form-control input-md'>
                            <option value='0'>Select An Last Education</option>
							<?php
							foreach($pendidikanx AS $val => $valx){
								$selected = ($valx['name'] == $pendidikan)?'selected':'';
								echo "<option value='".$valx['name']."' ".$selected.">".$valx['data1']."</option>";
							}
							?>
                        </select>
                    </div>
                    <label class='label-control col-sm-2'><b>Position <span class='text-red'>*</span></b></label>
                    <div class='col-sm-4'>
                        <select name='position' id='position' class='form-control input-md'>
                            <option value='0'>List Empty</option>
                        </select>
                    </div>
                </div>
                </div>
            </div>

            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Address</h3>		
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Province <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='ktp_provinsi' id='ktp_provinsi' class='form-control input-md'>
                                <option value='0'>Select An Province</option>
								<?php
								foreach($provinsix AS $val => $valx){
									$selected = ($valx['id_prov'] == $ktp_provinsi)?'selected':'';
									echo "<option value='".$valx['id_prov']."' ".$selected.">".strtoupper($valx['nama'])."</option>";
								}
								?>
                            </select>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Province <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='domisili_provinsi' id='domisili_provinsi' class='form-control input-md'>
                                <option value='0'>Select An Province</option>
								<?php
								foreach($provinsix AS $val => $valx){
									$selected = ($valx['id_prov'] == $domisili_provinsi)?'selected':'';
									echo "<option value='".$valx['id_prov']."' ".$selected.">".strtoupper($valx['nama'])."</option>";
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Districts <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='ktp_kota' id='ktp_kota' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Districts <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='domisili_kota' id='domisili_kota' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Sub-district <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='ktp_kecamatan' id='ktp_kecamatan' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Sub-district <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='domisili_kecamatan' id='domisili_kecamatan' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Village <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='ktp_kelurahan' id='ktp_kelurahan' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Village  <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='domisili_kelurahan' id='domisili_kelurahan' class='form-control input-md'>
                                <option value='0'>List Empty</option>
                            </select>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Postcode <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>            
                            <?php
                                echo form_input(array('id'=>'ktp_kode_pos','name'=>'ktp_kode_pos','class'=>'form-control input-md numberOnly','maxlength'=>'5','placeholder'=>'ID Card Postcode'),$ktp_kode_pos);											
                            ?>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Postcode <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>             
                            <?php
                                echo form_input(array('id'=>'domisili_kode_pos','name'=>'domisili_kode_pos','class'=>'form-control input-md numberOnly','maxlength'=>'5','placeholder'=>'Domicile Postcode'),$domisili_kode_pos);											
                            ?>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>ID Card Address <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>            
                            <?php
                                echo form_textarea(array('id'=>'ktp_alamat','name'=>'ktp_alamat','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'ID Card Address'),$ktp_alamat);											
                            ?>
                        </div>
                        <label class='label-control col-sm-2'><b>Domicile Address <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>             
                            <?php
                                echo form_textarea(array('id'=>'domisili_alamat','name'=>'domisili_alamat','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Domicile Address'),$domisili_alamat);											
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-danger">
                <div class="box-header">
                    <h3 class="box-title">Etc</h3>		
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>TAX / NPWP Number <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>          
                            <?php
                                echo form_input(array('id'=>'npwp','name'=>'npwp','class'=>'form-control input-md numberOnly ','placeholder'=>'TAX / NPWP Number'),$npwp);											
                            ?>	
                        </div>
                        <label class='label-control col-sm-2'><b>BPJS Number <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>             
                            <?php
                                echo form_input(array('id'=>'bpjs','name'=>'bpjs','class'=>'form-control input-md','placeholder'=>'BPJS Number'),$bpjs);											
                            ?>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>Join Date <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>          
                            <?php
                                echo form_input(array('id'=>'tgl_join','name'=>'tgl_join','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Join Date'),$tgl_join);											
                            ?>	
                        </div>
                        <label class='label-control col-sm-2'><b>End Date <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>             
                            <?php
                                echo form_input(array('id'=>'tgl_end','name'=>'tgl_end','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'End Date'),$tgl_end);											
                            ?>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>Account Number <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>          
                            <?php
                                echo form_input(array('id'=>'rek_number','name'=>'rek_number','class'=>'form-control input-md','placeholder'=>'Account Number'),$rek_number);											
                            ?>	
                        </div>
                        <label class='label-control col-sm-2'><b>Account Bank <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='bank_account' id='bank_account' class='form-control input-md'>
                                <option value='0'>Select An Account Bank</option>
								<?php
								foreach($bankx AS $val => $valx){
									$selected = ($valx['code'] == $bank_account)?'selected':'';
									echo "<option value='".$valx['code']."' ".$selected.">".strtoupper($valx['name'])."</option>";
								}
								?>
                            </select>
                        </div>
                    </div>
                    <div class='form-group row'>		 	 
                        <label class='label-control col-sm-2'><b>Employee Status <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='sts_karyawan' id='sts_karyawan' class='form-control input-md'>
                                <option value='0'>Select An Employee Status</option>
								<?php
								foreach($sts_karyawanx AS $val => $valx){
									$selected = ($valx['name'] == $sts_karyawan)?'selected':'';
									echo "<option value='".$valx['name']."' ".$selected.">".strtoupper($valx['data1'])."</option>";
								}
								?>
                            </select>
                        </div>
                        <label class='label-control col-sm-2'><b>Status <span class='text-red'>*</span></b></label>
                        <div class='col-sm-4'>
                            <select name='status' id='status' class='form-control input-md'>
                                <option value='0'>Select An Status</option>
								<?php
								foreach($statusx AS $val => $valx){
									$selected = ($valx['name'] == $status)?'selected':'';
									echo "<option value='".$valx['name']."' ".$selected.">".strtoupper($valx['data1'])."</option>";
								}
								?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'back'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>

<style type="text/css">
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	#id_category_chosen{
		width: 100% !important;
	}
	#id_satuan_chosen{
		width: 100% !important;
	}
	.tgl{
		cursor:pointer;
	}
</style>
<script> 
	
	$('.tgl').datepicker({
        dateFormat: "DD, d MM yy",
        changeMonth: true,
        changeYear: true,
	});
	$(document).ready(function(){
		// $('#price_ref_purchase').maskMoney();
		// $('#price_ref_estimation').maskMoney();
		// $('#nilai_konversi').maskMoney();
		// $('#head_table').hide();
		// $('#head_table_en').hide();
		// $('#head_table_bq').hide();
		$('#detail_body_enKosong').hide();
		$('#detail_body_bqKosong').hide();
		$('#add_en').hide();
		$('#add_bq').hide();
		// $('#simpan-bro').hide();
		$('.maskM').maskMoney();
		
		$(".numberOnly").on("keypress keyup blur",function (event) {    
			// $(this).val($(this).val().replace(/[^\d].+/, ""));
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
			if($(this).val() == ''){
				$(this).val(0);
			}
		});

		var id = $('#id').val();
        if(id != ''){
            var department = $('#department').val();
			var cost_center = "<?php echo $cost_center;?>";
			var position = "<?php echo $position;?>";
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/list_center/'+department+'/'+cost_center,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#cost_center").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/list_position/'+cost_center+'/'+position,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#position").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			//KTP
			var ktp_provinsi = "<?php echo $ktp_provinsi;?>";
			var ktp_kota = "<?php echo $ktp_kota;?>";
			var ktp_kecamatan = "<?php echo $ktp_kecamatan;?>";
			var ktp_kelurahan = "<?php echo $ktp_kelurahan;?>";
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kota/'+ktp_provinsi+'/'+ktp_kota,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#ktp_kota").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kecamatan/'+ktp_kota+'/'+ktp_kecamatan,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#ktp_kecamatan").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_desa/'+ktp_kecamatan+'/'+ktp_kelurahan,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#ktp_kelurahan").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
			

			//domisili
			var domisili_provinsi = "<?php echo $domisili_provinsi;?>";
			var domisili_kota = "<?php echo $domisili_kota;?>";
			var domisili_kecamatan = "<?php echo $domisili_kecamatan;?>";
			var domisili_kelurahan = "<?php echo $domisili_kelurahan;?>";
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kota/'+domisili_provinsi+'/'+domisili_kota,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#domisili_kota").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kecamatan/'+domisili_kota+'/'+domisili_kecamatan,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#domisili_kecamatan").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});

			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_desa/'+domisili_kecamatan+'/'+domisili_kelurahan,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$("#domisili_kelurahan").html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});


        }

		$(document).on('change', '#department', function(){
			var department = $(this).val();
			var cost_center = $("#cost_center");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/list_center/'+department,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(cost_center).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});
		
		$(document).on('change', '#cost_center', function(){
			var cost_center = $(this).val();
			var position = $("#position");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/list_position/'+cost_center,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(position).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('change', '#ktp_provinsi', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#ktp_kota");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kota/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('change', '#domisili_provinsi', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#domisili_kota");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kota/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		//get kecamatan
		$(document).on('change', '#ktp_kota', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#ktp_kecamatan");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kecamatan/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('change', '#domisili_kota', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#domisili_kecamatan");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_kecamatan/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		//get desa
		//get kecamatan
		$(document).on('change', '#ktp_kecamatan', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#ktp_kelurahan");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_desa/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('change', '#domisili_kecamatan', function(){
			var ktp_provinsi = $(this).val();
			var ktp_kota = $("#domisili_kelurahan");
			loading_spinner();
			$.ajax({
				url: base_url+active_controller+'/get_desa/'+ktp_provinsi,
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(ktp_kota).html(data.option).trigger("chosen:updated");
					swal.close();
				},
				error: function() {
					swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
					});
				}
			});
		});

		$(document).on('click', '#back', function(){
			window.location.href = base_url + active_controller;
		});

		$('#simpan-bro').click(function(e){
			e.preventDefault();
			var nm_karyawan = $("#nm_karyawan").val();
			var tmp_lahir = $("#tmp_lahir").val();
			var tgl_lahir = $("#tgl_lahir").val();
			var department = $("#department").val();
			var cost_center = $("#cost_center").val();
			var gender = $("#gender").val();
			var agama = $("#agama").val();
			var pendidikan = $("#pendidikan").val();
			var position = $("#position").val();
			var ktp_provinsi = $("#ktp_provinsi").val();
			var ktp_kota = $("#ktp_kota").val();
			var ktp_kecamatan = $("#ktp_kecamatan").val();
			var ktp_kelurahan = $("#ktp_kelurahan").val();
			var ktp_kode_pos = $("#ktp_kode_pos").val();
			var ktp_alamat = $("#ktp_alamat").val();
			var domisili_provinsi = $("#domisili_provinsi").val();
			var domisili_kota = $("#domisili_kota").val();
			var domisili_kecamatan = $("#domisili_kecamatan").val();
			var domisili_kelurahan = $("#domisili_kelurahan").val();
			var domisili_kode_pos = $("#domisili_kode_pos").val();
			var domisili_alamat = $("#domisili_alamat").val();
			var no_ponsel = $("#no_ponsel").val();
			var email = $("#email").val();
			var npwp = $("#npwp").val();
			var bpjs = $("#bpjs").val();
			var no_ktp = $("#no_ktp").val();
			var tgl_join = $("#tgl_join").val();
			var tgl_end = $("#tgl_end").val();
			var sts_karyawan = $("#sts_karyawan").val();
			var rek_number = $("#rek_number").val();
			var bank_account = $("#bank_account").val();
			var status = $("#status").val();
			// $(this).prop('disabled',true);
			
			if(nm_karyawan == ''){
				swal({
				  title	: "Error Message!",text	: 'Employee name is empty, please input first ...',type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
				

			// $('#simpan-bro').prop('disabled',false);
			
			swal({
				  title: "Are you sure?",
				  text: "You will not be able to process again this data!",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonClass: "btn-danger",
				  confirmButtonText: "Yes, Process it!",
				  cancelButtonText: "No, cancel process!",
				  closeOnConfirm: false,
				  closeOnCancel: false
				},
				function(isConfirm) {
				  if (isConfirm) {
					  // alert($('#numberMax_bq').val());
					  // return false;
						loading_spinner();
						var formData  	= new FormData($('#form_employee')[0]);
						var baseurl		= base_url + active_controller +'/add';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: formData,
							cache		: false,
							dataType	: 'json',
							processData	: false, 
							contentType	: false,				
							success		: function(data){								
								if(data.status == 1){											
									swal({
										  title	: "Save Success!",
										  text	: data.pesan,
										  type	: "success",
										  timer	: 7000,
										  showCancelButton	: false,
										  showConfirmButton	: false,
										  allowOutsideClick	: false
										});
									window.location.href = base_url + active_controller;
								}
								if(data.status == 2){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								}
								if(data.status == 3){
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								}
								$('#simpan-bro').prop('disabled',false);
							},
							error: function() {
								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',						
								  type				: "warning",								  
								  timer				: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
	});
	
	
</script>
