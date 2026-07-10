<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_tracking_so">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Customer</b></label>
				<div class='col-sm-6'>
					<select name='customer' id='customer' class='form-control input-md'>
						<option value='0'>-- Select Customer --</option>
					<?php
						foreach($list_customer AS $val => $valx){
							echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
						}
					?>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No. PO</b></label>
				<div class='col-sm-6'>
					<select name='no_po' id='no_po' class='form-control input-md'>
						<option value='0'>-- Select No. PO --</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No. SO</b></label>
				<div class='col-sm-6'>
					<select name='no_so' id='no_so' class='form-control input-md'>
						<option value='0'>-- Select No. SO --</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-6'>
					<?php
					echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Show','content'=>'<i class="fa fa-search"></i> Show','id'=>'btnShow'));
					echo "&nbsp;";
					echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','value'=>'Download','content'=>'<i class="fa fa-file-excel-o"></i> Download Excel','id'=>'btnDownload'));
					?>
				</div>
			</div>
			<div id='tracking_result' class="table-responsive"></div>
		</div>
		<!-- /.box-body -->
	</div>
	<!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>

<script>
$(document).ready(function(){

	// On change Customer -> load PO
	$(document).on('change', '#customer', function(){
		var id_customer = $(this).val();
		$('#no_po').html("<option value='0'>-- Select No. PO --</option>").trigger("chosen:updated");
		$('#no_so').html("<option value='0'>-- Select No. SO --</option>").trigger("chosen:updated");
		$('#tracking_result').html('');

		if(id_customer == '0') return;

		$.ajax({
			url: base_url + active_controller + '/get_po',
			type: 'POST',
			data: { id_customer: id_customer },
			dataType: 'json',
			success: function(data){
				if(data.status == 1){
					$('#no_po').html(data.option).trigger("chosen:updated");
				}
			},
			error: function(){
				swal({ title: "Error!", text: "Gagal memuat data PO.", type: "warning" });
			}
		});
	});

	// On change PO -> load SO
	$(document).on('change', '#no_po', function(){
		var no_po = $(this).val();
		$('#no_so').html("<option value='0'>-- Select No. SO --</option>").trigger("chosen:updated");
		$('#tracking_result').html('');

		if(no_po == '0') return;

		$.ajax({
			url: base_url + active_controller + '/get_so',
			type: 'POST',
			data: { no_po: no_po },
			dataType: 'json',
			success: function(data){
				if(data.status == 1){
					$('#no_so').html(data.option).trigger("chosen:updated");
				}
			},
			error: function(){
				swal({ title: "Error!", text: "Gagal memuat data SO.", type: "warning" });
			}
		});
	});

	// Show tracking
	$(document).on('click', '#btnShow', function(){
		var id_bq	= $('#no_so').val();
		var no_po	= $('#no_po').val();

		if(id_bq == '0' || no_po == '0'){
			swal({ title: "Perhatian!", text: "Customer, No. PO dan No. SO wajib dipilih.", type: "warning" });
			return false;
		}

		$.ajax({
			url: base_url + active_controller + '/show_tracking',
			type: 'POST',
			data: { id_bq: id_bq, no_po: no_po },
			dataType: 'json',
			beforeSend: function(){
				loading_spinner();
			},
			success: function(data){
				if(data.status == 1){
					$('#tracking_result').html(data.data_html);
					swal.close();
				} else {
					swal({ title: "Error!", text: "Data tidak ditemukan.", type: "warning" });
				}
			},
			error: function(){
				swal({ title: "Error!", text: "Terjadi kesalahan saat memuat data.", type: "warning" });
			}
		});
	});

	// Download Excel
	$(document).on('click', '#btnDownload', function(){
		var id_bq	= $('#no_so').val();
		var no_po	= $('#no_po').val();

		if(id_bq == '0' || no_po == '0'){
			swal({ title: "Perhatian!", text: "Customer, No. PO dan No. SO wajib dipilih.", type: "warning" });
			return false;
		}

		var link = base_url + active_controller + '/download_excel/' + id_bq + '/' + encodeURIComponent(no_po);
		window.open(link);
	});
});
</script>
