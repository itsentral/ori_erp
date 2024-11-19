<?php
$this->load->view('include/side_menu'); 
// echo "<pre>";
// print_r($row);
// echo "</pre>";
?> 
<form action="#" method="POST" id="form_proses_bro"> 
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<div class="box-body">
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#data">Master Customer</a></li>
					<li><a data-toggle="tab" href="#data_pic">PIC Customer</a></li>
				</ul>
				<div class="tab-content">
					<div id="data" class="tab-pane fade in active">
						<div class="box box-primary">
							<div class="box-body">
								<div class="form-group row">								
									<label class='label-control col-sm-2'><b>Customer Name <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-check"></i></span>
											<?php
												echo form_input(array('id'=>'nm_customer','name'=>'nm_customer','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Customer Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['nm_customer']);										
												echo form_input(array('id'=>'id_customer','name'=>'id_customer','class'=>'form-control input-md', 'type'=>'hidden'), $row[0]['id_customer']);										
											?>
										</div>
									</div>
									<label class='label-control col-sm-2'><b>Business Fields <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-building"></i></span>
											<select name='bidang_usaha' id='bidang_usaha' class='form-control input-md'>
											<?php
												foreach($rows_bidang AS $val => $valx){
													$sel = ($row[0]['bidang_usaha'] == $valx['id_bidang_usaha'])?'selected':'';
													echo "<option value='".$valx['id_bidang_usaha']."' ".$sel.">".ucwords(strtolower($valx['bidang_usaha']))."</option>";
												}
											 ?>
											</select>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>Selling Product<span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-folder"></i></span>
											<?php
												echo form_input(array('id'=>'produk_jual','name'=>'produk_jual','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Selling Product','style'=>'text-transform:uppercase'), $row[0]['produk_jual']);										
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Credibility <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-check"></i></span>
											<?php
											$Arr_Credibility	= array(
												'A'		=> 'A',
												'B'		=> 'B',
												'C'		=> 'C',
												'D'		=> 'D'
											);						
											echo form_dropdown('kredibilitas',$Arr_Credibility, $row[0]['kredibilitas'], array('id'=>'kredibilitas','class'=>'form-control input-sm'));
											?>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>Address<span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
											<?php
												echo form_textarea(array('id'=>'alamat','name'=>'alamat','class'=>'form-control input-md','cols'=>'75','rows'=>'2','autocomplete'=>'off','placeholder'=>'Address','style'=>'text-transform:uppercase'),$row[0]['alamat']);			
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Province</b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-home"></i></span>
											<select name='provinsi' id='provinsi' class='form-control input-md'>
											<?php
												foreach($rows_province AS $val => $valx){
													$sel = ($row[0]['provinsi'] == $valx['id_prov'])?'selected':'';
													echo "<option value='".$valx['id_prov']."' ".$sel.">".$valx['nama']."</option>";
												}
											 ?>	
											</select>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>District / City</b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-home"></i></span>
											<select name='kota' id='kota' class='form-control input-md'>
												<?php
													foreach($rows_kab AS $val => $valx){
														$sel = ($row[0]['kota'] == $valx['id_kab'])?'selected':'';
														echo "<option value='".$valx['id_kab']."' ".$sel.">".$valx['nama']."</option>";
													}
												 ?>	
											</select>											
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Post Code </b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
											<?php
											echo form_input(array('id'=>'kode_pos','name'=>'kode_pos','class'=>'form-control input-md','placeholder'=>'Post Code'), $row[0]['kode_pos']);
											?>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>Phone</b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-phone"></i></span>
											<?php
												echo form_input(array('id'=>'telpon','name'=>'telpon','class'=>'form-control input-md','placeholder'=>'Phone'), $row[0]['telpon']);		
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Fax</b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-fax"></i></span>
											<?php
											echo form_input(array('id'=>'fax','name'=>'fax','class'=>'form-control input-md','placeholder'=>'Fax'), $row[0]['fax']);
											?>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>NPWP <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
											<?php
												echo form_input(array('id'=>'npwp','name'=>'npwp','class'=>'form-control input-md','placeholder'=>'NPWP'), $row[0]['npwp']);		
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>NPWP Address <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
											<?php
											echo form_textarea(array('id'=>'alamat_npwp','name'=>'alamat_npwp','class'=>'form-control input-md','cols'=>'75','rows'=>'2','autocomplete'=>'off','placeholder'=>'NPWP Address','style'=>'text-transform:uppercase'), $row[0]['alamat_npwp']);
											?>
										</div>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2' hidden><b>Branch</b></label>
									<div class='col-sm-4' hidden>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-building"></i></span>
											<?php
												echo form_dropdown('kdcab',$rows_branch, $row[0]['kdcab'], array('id'=>'kdcab','class'=>'form-control input-md'));		
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Marketing <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
											<?php
											$rows_marketing	= array(
																''		=> 'Empty List'												
															);
												echo form_dropdown('id_marketing',$rows_marketing, $row[0]['id_marketing'], array('id'=>'id_marketing','class'=>'form-control input-md'));
											?>
										</div>
									</div>
									<label class='label-control col-sm-2'><b>Deferred Account</b></label>
									<div class='col-sm-4'>
										<?php
											echo form_dropdown('coa_deffered',$datacoa, $row[0]['coa_deffered'], array('id'=>'coa_deffered','class'=>'form-control select2'));
										?>
									</div>
								</div>
								<div class="form-group row">									
									<label class='label-control col-sm-2'><b>Website <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-globe"></i></span>
											<?php
												echo form_input(array('id'=>'website','name'=>'website','class'=>'form-control input-md','placeholder'=>'Website'), $row[0]['website']);		
											?>
										</div>
									</div>

									<label class='label-control col-sm-2'><b>Status <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
											<?php
											$rows_active	= array(
												'aktif'		=> 'Active',
												'nonaktif'	=> 'Inactive'
											);
												echo form_dropdown('sts_aktif',$rows_active, $row[0]['sts_aktif'], array('id'=>'sts_aktif','class'=>'form-control input-md'));
											?>
										</div>
									</div>
								</div>
								
								<div class="form-group row">
									<label class='label-control col-sm-2'><b>Discount Customer <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-dollar"></i></span>
											<?php
												echo form_input(array('id'=>'diskon_toko','name'=>'diskon_toko','class'=>'form-control input-md','placeholder'=>'Discount Customer'), $row[0]['diskon_toko']);	
											?>
										</div>
									</div>
									<label class='label-control col-sm-2'><b>Reference By<span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-building"></i></span>
											<?php
												$rows_reference		= array(
													''			=> 'Select An Option',
													'Event'		=> 'Event',
													'Call'		=> 'Call',
													'Sales'		=> 'Sales',
													'Socmed'	=> 'Social Media',
													'Website'	=> 'Website',
													'Agent'		=> 'Agent',
													'Adword'	=> 'Google Adword'												
												);
												$selected = (!empty($rowR[0]['reference_by']))?$rowR[0]['reference_by']:0;
												echo form_dropdown('reference_by',$rows_reference, $selected, array('id'=>'reference_by','class'=>'form-control input-md','disabled'=>'disabled'));		
											?>
										</div>
									</div>
									
								</div>
								<div class="form-group row" id="detail_reff">
									<label class='label-control col-sm-2'><b>Reference Name <span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-user"></i></span>
											<?php
												$reference_name = (!empty($rowR[0]['reference_name']))?$rowR[0]['reference_name']:'';
												echo form_input(array('id'=>'reference_name','name'=>'reference_name','class'=>'form-control input-md','placeholder'=>'Reference Name','readonly'=>'readonly'),$reference_name);	
											?>
										</div>
									</div>
									<label class='label-control col-sm-2'><b>Reference Phone<span class='text-red'>*</span></b></label>
									<div class='col-sm-4'>
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-phone"></i></span>
											<?php
												$reference_phone = (!empty($rowR[0]['reference_phone']))?$rowR[0]['reference_phone']:'';
												echo form_input(array('id'=>'reference_phone','name'=>'reference_phone','class'=>'form-control input-md','placeholder'=>'Reference Phone','readonly'=>'readonly'), $reference_phone);		
											?>
										</div>
									</div>									
								</div>
							</div>						
						</div>
					</div>
					<div id="data_pic" class="tab-pane fade">
						<div class="box box-primary">
							<div class="box-body">
								<div class="form-group row">
									<label for="nm_pic" class="col-sm-2 control-label">PIC Name<font size="4" color="red"><B>*</B></font></label>
									<div class="col-sm-4">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-building"></i></span>
											<input type="text" class="form-control input-md" id="nm_pic" name="nm_pic" value='<?= $rowP[0]['nm_pic'];?>' maxlength="45" placeholder="PIC Name" autocomplete='off' required style="text-transform:uppercase">
											<input type="hidden" class="form-control input-md" id="id_pic" name="id_pic" value='<?= $rowP[0]['id_pic'];?>' maxlength="45" placeholder="PIC Name" autocomplete='off' required style="text-transform:uppercase">
										</div>
									</div>
									<label for="divisi" class="col-sm-2 control-label">Division <font size="4" color="red"><B>*</B></font></label>
									<div class="col-sm-4">
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-building"></i></span>
										<input type="text" class="form-control" id="divisi" name="divisi" maxlength="45" value='<?= $rowP[0]['divisi'];?>' placeholder="Division" required autocomplete='off' style="text-transform:uppercase">
										</div>
									</div>
								</div>
								<div class="form-group row">                    
									<label for="hp" class="col-sm-2 control-label">Contact Number<font size="4" color="red"><B>*</B></font></label>
									<div class="col-sm-4">
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-building"></i></span>
										<input type="text" class="form-control" id="hp" name="hp" maxlength="15" value='<?= $rowP[0]['hp'];?>' placeholder="Contact Number" autocomplete='off' required>
										</div>
									</div>
									<label for="email_pic" class="col-sm-2 control-label">Email Address</label>
									<div class="col-sm-4">
										<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
										<input type="text" class="form-control" id="email_pic" name="email_pic" maxlength="45" value='<?= $rowP[0]['email_pic'];?>' placeholder="Email Address" autocomplete='off'>
										</div>
									</div>
								</div>
								<div class="box-footer">
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10">
											<button type="submit" name="btnpic" class="btn btn-success" id="btnpic"><i class="fa fa-save">&nbsp;</i>Save</button>
											<!--<a class="btn btn-success" id='btntoko' href="javascript:void(0)" title="Add" onclick="add_toko()"><i class="fa fa-save">&nbsp;</i>Save</a>-->
											<a class="btn btn-danger" href="<?=base_url('customer_master')?>" title="Back">Back</a>
										</div>
									</div>
								</div>
							</div>
							<div id="list_pic"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<!-- MODAL !-->
<!-- Modal Bidus-->
<div class="modal modal-info" id="add_bidangusaha" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-file-pdf-o"></span>&nbsp;Tambah Bidang Usaha</h4>
			</div>
			<div class="modal-body" id="MyModalBody">
			<form action="#" id="form_bidus">
			<div class="form-group">
				<label for="bidus">Bidang Usaha <font size="4" color="red"><B>*</B></font></label>
				<input type="text" class="form-control" id="bidus" name="bidus" style="text-transform:uppercase" placeholder="Input Bidang Usaha Baru" required>
				<input type="hidden" class="form-control" id="idbidus" name="idbidus">
			</div>
			<div class="form-group">
				<label for="exampleInputPassword1">Keterangan <font size="4" color="red"><B>*</B></font></label>
				<textarea class="form-control" id="keterangan" name="Input keterangan" maxlength="255" placeholder="Keterangan" required="" autofocus="" style="margin: 0px; height: 49px; width: 216px;"></textarea>
			</div>
			<!--
			<div class="form-group">
				<iframe onload="ListBD()" hidden="true"></iframe>
			</div> 
			-->
			</form>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-outline" onclick="javascript:save_bidus();">Save</button>
			<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
			</div>
			<div id="list_bd"></div> 
			<div class="modal-footer"></div>
		</div>
	</div>
</div>
<!-- End Modal Bidus-->
<!-- Modal Syarat Penagihan-->
<div class="modal modal-info" id="add_syaratdok" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-file-pdf-o"></span>&nbsp;Tambah Syarat Penagihan</h4>
			</div>
			<div class="modal-body" id="MyModalBody">
			<form action="#" id="form_syardok">
			<div class="form-group">
				<label for="bidus">Syarat Penagihan <font size="4" color="red"><B>*</B></font></label>
				<input type="text" class="form-control" id="add_nama_syarat" name="add_nama_syarat" placeholder="Input Syarat Penagihan Baru" style="text-transform:uppercase" required>
				<input type="hidden" class="form-control" id="add_id_syarat" name="add_id_syarat">
			</div>
			<!--
			<div class="form-group">
				<iframe onload="ListSD()" hidden="true"></iframe>
			</div>
			-->			
			</form>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-outline" onclick="javascript:save_syardok();">Save</button>
			<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
			</div>
			<div id="list_sd"></div>   
			<div class="modal-footer"></div>          
		</div>
	</div>
</div>
<!-- End Modal Syarat Penagihan-->
<!-- Modal Reff-->
<div class="modal modal-info" id="add_referensi" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-file-pdf-o"></span>&nbsp;Tambah Referensi</h4>
			</div>
			<div class="modal-body" id="MyModalBody">
			<form action="#" id="form_reff">
			<div class="form-group">
				<label for="reff">Referensi <font size="4" color="red"><B>*</B></font></label>
				<input type="text" class="form-control" id="reff" name="reff" placeholder="Referensi" style="text-transform:uppercase" required>
			</div>
			<div class="form-group">
				<label for="exampleInputPassword1">Keterangan></label>
				<textarea class="form-control" id="keterangan" name="keterangan" maxlength="255" placeholder="Keterangan" style="text-transform:uppercase" style="margin: 0px; height: 49px; width: 216px;"></textarea>
			</div>
			</form>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-outline" onclick="javascript:save_reff();">Save</button>
			<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
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
	#bidang_usaha_chosen{
		width: 100% !important;
	}
	#kredibilitas_chosen{
		width: 100% !important;
	}
	#provinsi_chosen{
		width: 100% !important;
	}
	#kota_chosen{
		width: 100% !important;
	}
	#kdcab_chosen{
		width: 100% !important;
	}
	#id_marketing_chosen{
		width: 100% !important;
	}
	#sts_aktif_chosen{
		width: 100% !important;
	}
	#reference_by_chosen{
		width: 100% !important;
	}
	#metode_bayar_chosen{
		width: 100% !important;
	}
	#hari_tagih_chosen{
		width: 100% !important;
	}
	#syarat_dokumen_chosen{
		width: 100% !important;
	}
	#sistem_bayar_chosen{
		width: 100% !important;
	}
	#status_milik_chosen{
		width: 100% !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('#kode_pos').mask('?99999');
		$('#telpon').mask('?999-999999999');
		$('#fax').mask('?999-999999999');
		$('#npwp').mask('?99.999.999.9-999.99');
		$('#reference_phone').mask('?9999-9999-99999');
		$('#hp').mask('?9999-9999-99999');
		$(document).on('change', '#provinsi', function(e){
			e.preventDefault();
			$.ajax({
				url: base_url +'index.php/'+ active_controller+'/getDistrict',
				cache: false,
				type: "POST",
				data: "id_prov="+this.value,
				dataType: "json",
				success: function(data){
					$("#kota").html(data.option).trigger("chosen:updated");
				}
			});
		});		
		$('#btnpic').click(function(e){
			e.preventDefault();
			//Customer
			var nm_customer		= $('#nm_customer').val();
			var bidang_usaha	= $('#bidang_usaha').val();
			var produk_jual		= $('#produk_jual').val();
			var kredibilitas	= $('#kredibilitas').val();
			var alamat			= $('#alamat').val();
			var provinsi		= $('#provinsi').val();
			var kota			= $('#kota').val();
			var kode_pos		= $('#kode_pos').val();
			var telpon			= $('#telpon').val();
			var fax				= $('#fax').val();
			var npwp			= $('#npwp').val();
			var alamat_npwp		= $('#alamat_npwp').val();
			var kdcab			= $('#kdcab').val();
			var id_marketing	= $('#id_marketing').val();
			var website			= $('#website').val();
			var sts_aktif		= $('#sts_aktif').val();
			var diskon_toko		= $('#diskon_toko').val();
			var reference_by	= $('#reference_by').val();
			var reference_name	= $('#reference_name').val();
			var reference_phone	= $('#reference_phone').val();
			
			//PIC Customer
			var nm_pic			= $('#nm_pic').val();
			var divisi			= $('#divisi').val();
			var hp				= $('#hp').val();
			var email_pic		= $('#email_pic').val();
			
			if(nm_customer=='' || nm_customer==null || nm_customer=='-' || nm_customer=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Customer Name in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(bidang_usaha=='' || bidang_usaha==null || bidang_usaha=='-' || bidang_usaha=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Business Field in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(produk_jual=='' || produk_jual==null || produk_jual=='-' || produk_jual=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Selling Producte in master customer tab is empty, please input first ...',
				  type	: "warning"
				})
				return false;
			}
			if(kredibilitas=='' || kredibilitas==null || kredibilitas=='-' || kredibilitas=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Credibility in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(alamat=='' || alamat==null || alamat=='-' || alamat=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Address in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(npwp=='' || npwp==null || npwp=='-' || npwp=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Tax ID in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(alamat_npwp=='' || alamat_npwp==null || alamat_npwp=='-' || alamat_npwp=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Tax ID Address in master customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			
			if(nm_pic=='' || nm_pic==null || nm_pic=='-' || nm_pic=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'PIC name in PIC customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(divisi=='' || divisi==null || divisi=='-' || divisi=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Division in PIC customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			if(hp=='' || hp==null || hp=='-' || hp=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'PIC phone in PIC customer tab is empty, please input first ...',
				  type	: "warning"
				});
				return false;
			}
			// $('#btnpic').prop('disabled',false);
			
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
						loading_spinner();
						var formData 	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/edit';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: formData,
							cache		: false,
							dataType	: 'json',
							processData	: false, 
							contentType	: false,	
							beforeSend : function(){
								$(this).prop('disabled',true);
							},				
							success		: function(data){								
								if(data.status == 1){
									swal({
										  title	: "Save Success!",
										  text	: data.pesan,
										  type	: "success",
										  timer	: 7000
										});
									window.location.href = base_url + active_controller;
								}
								else{
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
								$('#btnpic').prop('disabled',false);
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
								$('#btnpic').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#btnpic').prop('disabled',false);
					return false;
				  }
			});
		});
		
	});
	
</script>
