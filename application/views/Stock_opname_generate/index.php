<?php $this->load->view('include/side_menu'); ?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title; ?></h3>
	</div>
	<div class="box-body">
		<!-- Panel Generate -->
		<div class="row" style="margin-bottom:15px;">
			<div class="col-sm-3">
				<label>Pilih Tanggal Generate:</label>
				<input type="text" id="date_target" name="date_target" class="form-control text-center" placeholder="Pilih Tanggal">
			</div>
			<div class="col-sm-3" style="padding-top:25px;">
				<button type="button" class="btn btn-success btn-sm" id="btn_generate">
					<i class="fa fa-cogs"></i> Generate Stock
				</button>
				<button type="button" class="btn btn-warning btn-sm" id="btn_reconcile">
					<i class="fa fa-balance-scale"></i> Rekonsiliasi
				</button>
			</div>
			<div class="col-sm-6" style="padding-top:25px;">
				<div id="info_panel" class="alert alert-info" style="display:none; padding:8px; margin:0;">
					<span id="info_text"></span>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<script>
$(document).ready(function(){

	// Init datepicker (jQuery UI)
	$('#date_target').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	// Generate button
	$('#btn_generate').on('click', function(){
		var date_target = $('#date_target').val();
		if(!date_target){
			alert('Pilih tanggal terlebih dahulu!');
			return;
		}

		if(!confirm('Generate data stok opname untuk tanggal '+date_target+'?\n\nProses ini akan menduplikat data stok dari tanggal sebelumnya dan menambah/mengurangi berdasarkan transaksi pada tanggal tersebut.')){
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/process_generate"); ?>',
			type: 'POST',
			data: { date_target: date_target },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-cogs"></i> Generate Stock');
				if(res.status == 1){
					$('#info_panel').removeClass('alert-danger').addClass('alert-info').show();
					$('#info_text').html('<i class="fa fa-check"></i> '+res.pesan);
				} else {
					$('#info_panel').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-cogs"></i> Generate Stock');
				alert('Terjadi kesalahan server!');
			}
		});
	});

	// Reconcile button
	$('#btn_reconcile').on('click', function(){
		var date_target = $('#date_target').val();
		if(!date_target){
			alert('Pilih tanggal terlebih dahulu!');
			return;
		}

		if(!confirm('Rekonsiliasi data gudang 3 (Sub Gudang) tanggal '+date_target+' dengan ledger_subgudang?')){
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/reconcile"); ?>',
			type: 'POST',
			data: { date_target: date_target },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-balance-scale"></i> Rekonsiliasi');
				if(res.status == 1){
					$('#info_panel').removeClass('alert-danger').addClass('alert-info').show();
					$('#info_text').html('<i class="fa fa-check"></i> '+res.pesan);
				} else {
					$('#info_panel').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-balance-scale"></i> Rekonsiliasi');
				alert('Terjadi kesalahan server!');
			}
		});
	});

});
</script>
