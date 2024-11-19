<?php $this->load->view('include/side_menu'); ?>
<form action="" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
			<div class="box-tool">
				<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Bulan<span class='text-red'>*</span></b></label>
					<div class='col-sm-3'>
						<select name='bulan' id='bulan' class='form-control'>
							<?php
							for($i=1;$i<12;$i++){
								$options='';
								if($i==$bulan) $options=' selected';
								echo '<option value="'.sprintf('%02d', $i).'" '.$options.'>'.date("F",strtotime("2000-".$i."-01")).'</option>';
							}
							?>
						</select>
					</div>
					<label for="id_aset" class="col-sm-2 control-label">Tahun<font size="4" color="red"><B>*</B></font></label>
					<div class="col-sm-2">
						<select name='tahun' id='tahun' class='form-control'>
						<?php
						for($i=date("Y");$i>=2021;$i--){
							$options='';
							if($i==$tahun) $options=' selected';
							echo '<option value="'.$i.'" '. $options.'>'.$i.'</option>';
						}
						?>
						</select>
					</div>
					<div class="col-sm-2">
					<button type="submit" name="submit" class="btn btn-success btn-sm"><i class="fa fa-search">&nbsp;</i>View</button>
					</div>
				</div>
			</div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class="table-responsive col-md-12">
				<table class="table table-bordered table-striped" width='100%'>
					<thead>
						<tr class='bg-blue' >
							<th class="text-center">No</th>
							<th class="text-center">Nama Amortisasi</th>
							<th class="text-center">Kategori</th>
							<th class="text-center">Periode</th>
							<th class="text-center">Nilai Amortisasi</th>
							<th class="text-center">Status
							<input type="checkbox" name="checkall" onClick="cekall(this)" /></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$i=0;$flag="";
					if(!empty($data)){
						foreach($data as $keys=>$val){
							$i++;
							if($val->flag=="N") $flag=$val->flag;
							echo '<tr><td>'.$i.'</td><td>'.$val->nm_asset.'</td><td>'.$val->nm_category.'</td>
							<td>'.$val->bulan.'-'.$val->tahun.'</td>
							<td align=right>'.number_format($val->nilai_susut).'</td><td>'.($val->flag=='N'?'<input type="checkbox" name="nomor[]" value="'.$val->nomor.'#'.$val->kd_asset.'" class="jurnal" />':'Sudah diproses').'</td></tr>';
						}
					}
					?>
					</tbody>
					<tfoot>
						<tr><td colspan=3></td><td colspan=2 align=right><button type="button" class="btn btn-warning btn-sm <?=($flag=="N"?'':'hidden')?>" id="simpan-bro">Create Jurnal</button></td></tr>
					</tfoot>
				</table>
			</div>
		</div>
		<!-- /.box-body -->
	</div>
</form>
<!-- DataTables -->
<?php $this->load->view('include/footer'); ?>
<!-- page script -->
<script type="text/javascript">
$('#simpan-bro').click(function(e){
	e.preventDefault();
	$(this).prop('disabled',true);
	swal({
		  title: "Are you sure?",
		  text: "You will not be able to process again this data!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-info",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: false,
		  closeOnCancel: false
		},
		function(isConfirm) {
		  if (isConfirm) {
				// loading_spinner();
				var formData  	= new FormData($('#form_proses_bro')[0]);
				var baseurl		= base_url +'amortisasi/jurnal_generate';
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
								  timer	: 7000
								});
							window.location.reload();
						}
						else{
							if(data.status == 2){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							else if(data.status == 3){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
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
							$('#simpan-bro').prop('disabled',false);
						}
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

function cekall(source){
  checkboxes = document.getElementsByName('nomor[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>
