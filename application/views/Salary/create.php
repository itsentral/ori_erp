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
				<label class='label-control col-sm-2'><b>No Dokumen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<input type="hidden" id="id" name="id" value="<?php echo set_value('id', isset($data->id) ? $data->id: ""); ?>">
					<input type="text" class="form-control" id="kode" name="kode" value="<?php echo set_value('kode', isset($data->kode) ? $data->kode: ""); ?>" placeholder="Automatic" readonly tabindex="-1">
				</div>
				<label class='label-control col-sm-2'><b>Periode</b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'periode','name'=>'periode','class'=>'form-control tanggal','autocomplete'=>'off','placeholder'=>'Periode', 'readonly'=>'readonly', 'value'=>(isset($data->periode)?$data->periode:date("Y-m-d"))));
					?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Keterangan <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
						echo form_input(array('id'=>'keterangan','name'=>'keterangan','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Keterangan', 'value'=>(isset($data->keterangan)?$data->keterangan:'')));
					?>
				</div>
			</div>
			<table class="table">
				<tr>
					<th>No COA</th>
					<th>DEBET</th>
					<th>KREDIT</th>
					<th>DEPARTEMAN / SUB DEPARTEMEN</th>
				</tr>
				<?php
				$i=0;
				foreach ($data_detail as $keys=>$vals){
					echo "<tr>
						<td>".$vals->coa." - ".$vals->nama."</td>
						<td><input type=text id='debet".$i."' name='debet[]' class='form-control divide' value='".$vals->debet."'></td>
						<td><input type=text id='credit".$i."' name='credit[]' class='form-control divide' value='".$vals->credit."'></td>
						<td>".$vals->keterangan."</td>
					</tr>";
					$i++;
				}
				?>
			</table>
			<button type="submit" name="save" class="btn btn-success btn-sm stsview" id="simpan-bro"><i class="fa fa-save">&nbsp;</i>Simpan</button>
			<a class="btn btn-default btn-sm" href="<?=base_url("salary")?>"><i class="fa fa-reply">&nbsp;</i>Batal</a>
		</div>
	</div>
</form>
<!-- DataTables -->
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
				url: base_url +'salary/cektanggal',
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
		if(depresiasi == '' || depresiasi == null || depresiasi == 0){
			swal({
				title	: "Error Message!",
				text	: 'Jangka waktu asset belum dipilih ...',
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
					var baseurl		= base_url +'salary/saved';
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
								window.location.href = base_url + 'salary';
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
