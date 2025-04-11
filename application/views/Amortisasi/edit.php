<?php
$this->load->view('include/side_menu');
$periode_akhir='';
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"></h3>
		</div>
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Amortisasi <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type="hidden" id="idamt" name="idamt" value="<?php echo set_value('id', isset($data->id) ? $data->id: ""); ?>">
					<input type="text" class="form-control" id="kd_asset" name="kd_asset" value="<?php echo set_value('kd_asset', isset($data->kd_asset) ? $data->kd_asset: ""); ?>" placeholder="Automatic" readonly tabindex="-1">
				</div>
			</div>
			<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Kategori Amortisasi<span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='category' id='category' class='form-control input-md'>
						<?php
						foreach($list_catg as $key=>$val){
							$sele='';
							if(isset($data->category)){
								if($data->category==$val['id']) $sele=' selected';
							}
							echo '<option value="'.$val['id'].'" '.$sele.'>'.$val['nm_category'].'</option>';
						}
						?>
					</select>
				</div>
				<label for="coa" class="col-sm-2 control-label">COA Expense Amortisasi<font size="4" color="red"><B>*</B></font></label>
				<div class="col-sm-4">
					<?php
					$dataaset[0]	= 'Select An Option';
					echo form_dropdown('coa',$dataaset, set_value('coa', isset($data->coa) ? $data->coa: '0'), array('id'=>'coa','class'=>'form-control','required'=>'required'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Keterangan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nm_asset','name'=>'nm_asset','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Keterangan', 'value'=>(isset($data->nm_asset)?$data->nm_asset:'')));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Jangka Waktu <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='depresiasi' id='depresiasi' class='form-control' onblur="cekamor()">
						<option value='0'>Pilih Jangka Waktu</option>
						<?php $sexd='';
							for($a=1; $a <= 36; $a++ ){
								if(isset($data->depresiasi)){
									$sexd	= ($a == $data->depresiasi)?'selected':'';
								}
								echo "<option value='".$a."' ".$sexd.">".$a." Bulan</option>";
							}
						?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nilai Amortisasi <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'nilai_asset', 'onblur'=>'cekamor()','name'=>'nilai_asset','class'=>'form-control divide', 'value'=>(isset($data->nilai_asset)?$data->nilai_asset:0)));
					?>
				</div>
				<label class='label-control col-sm-2'><b>Amortisasi Perbulan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('type'=>'hidden', 'id'=>'qty','name'=>'qty','value'=>'1'));
						echo form_input(array('id'=>'value','name'=>'value','value'=>(isset($data->value)?$data->value:0),'class'=>'form-control divide','readonly'=>'readonly'));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Periode Awal</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'tanggal','name'=>'tanggal','class'=>'form-control tanggal','autocomplete'=>'off','placeholder'=>'Periode Awal', "onblur"=>"cekamor()", 'readonly'=>'readonly', 'value'=>(isset($data->tgl_perolehan)?$data->tgl_perolehan:date("Y-m-d"))));
						if(isset($data->tgl_perolehan)){
							$periode_akhir=date("Y-m-d",strtotime("+".$data->depresiasi." months", strtotime($data->tgl_perolehan)));
						}
					?>
				</div>
				<label class='label-control col-sm-2'><b>Periode Akhir</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'akhir','name'=>'akhir','value'=>$periode_akhir,'class'=>'form-control','placeholder'=>'Periode Akhir', 'readonly'=>'readonly'));
					?>
				</div>
			</div>
			<button type="submit" name="save" class="btn btn-success btn-sm stsview" id="simpan-bro"><i class="fa fa-save">&nbsp;</i>Simpan</button>
			<a class="btn btn-default btn-sm" href="<?=base_url("amortisasi")?>"><i class="fa fa-reply">&nbsp;</i>Batal</a>
		</div>
	</div>
</form>
	<div class="box box-warning">
		<div class="box-body">
		<?php
		if(isset($status)){
			if($status=='view') {
				?>
				<table class="table table-striped">
				<tr>
					<td>Bulan</td><td>Tahun</td><td>Nilai</td><td>Status</td>
				</tr>
				<?php
				$result = $this->db->query("SELECT * FROM amortisasi_generate WHERE kd_asset='".$data->kd_asset."' order by nomor")->result();
				if(!empty($result)){
					foreach($result as $val){
						echo '
						<tr>
							<td>'.$val->bulan.'</td><td>'.$val->tahun.'</td><td>'.number_format($val->nilai_susut).'</td><td>'.$val->flag.'</td>
						</tr>						
						';
					}
				}
				?>
				</table>
				<?php
			}
		}
		?>
		</div>
	</div>

<?php $this->load->view('include/footer'); ?>
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script type="text/javascript">
	$('.divide').divide();
	$(function () {
		$(".tanggal").datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
	$('#depresiasi').on('change', function(evt, params) {
	   cekamor()
	 });
	function cekamor(){
		depresiasi=$("#depresiasi").val();
		nilai=$("#nilai_asset").val();
		if(depresiasi!=0){
			values=parseInt(parseInt(nilai)/parseInt(depresiasi));
			$("#value").val(values);
		}else{
			$("#value").val(0);
		}
		var sdate = $('#tanggal').val();
			$.ajax({
				url: base_url +'amortisasi/cektanggal',
				dataType : "html",
				type: 'POST',
				data: {tanggal:sdate,bulan:depresiasi},
				success: function(msg){
					$('#akhir').val(msg);
				},
				error: function(msg){
				}
			});
	}
	$('#simpan-bro').click(function(e){
		e.preventDefault();
		$(this).prop('disabled',true);
		var nm_asset		= $('#nm_asset').val();
		var depresiasi		= $('#depresiasi').val();
		var nilai_asset		= $('#nilai_asset').val();
		var coa=$("#coa").val();
		var qty				= $('#qty').val();
		if(nm_asset == '' || nm_asset == null){
			swal({
				title	: "Error Message!",
				text	: 'Nama Amortisasi masih kosong ...',
				type	: "warning"
			});

			$('#simpan-bro').prop('disabled',false);
			return false;
		}
		if(coa == '' || coa == null || coa == 0){
			swal({
				title	: "Error Message!",
				text	: 'COA belum dipilih ...',
				type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
		}
		if(depresiasi == '' || depresiasi == null || depresiasi == 0){
			swal({
				title	: "Error Message!",
				text	: 'Jangka waktu belum dipilih ...',
				type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
		}
		if(nilai_asset == '' || nilai_asset == null || nilai_asset == 0){
			swal({
				title	: "Error Message!",
				text	: 'Jangka waktu belum dipilih ...',
				type	: "warning"
			});
			$('#simpan-bro').prop('disabled',false);
			return false;
		}		
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
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url +'amortisasi/edited';
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
								window.location.href = base_url + 'amortisasi';
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
<?php
if(isset($status)){
	if($status=='view') echo '$("#form_proses_bro :input").prop("disabled", true);$(".stsview").addClass("hidden");';
}
?>
</script>
