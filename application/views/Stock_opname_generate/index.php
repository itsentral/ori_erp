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
				<button type="button" class="btn btn-info btn-sm" id="btn_fix_tras">
					<i class="fa fa-wrench"></i> Fix Tran Detail
				</button>
			</div>
			<div class="col-sm-6" style="padding-top:25px;">
				<div id="info_panel" class="alert alert-info" style="display:none; padding:8px; margin:0;">
					<span id="info_text"></span>
				</div>
			</div>
		</div>

		<!-- Panel Adjust Harga Inventory Produksi -->
		<div class="row" style="margin-bottom:15px; border-top:1px solid #ddd; padding-top:15px;">
			<div class="col-sm-3">
				<label>Tanggal Adjust:</label>
				<input type="text" id="date_adjust" name="date_adjust" class="form-control text-center" placeholder="Pilih Tanggal">
			</div>
			<div class="col-sm-3">
				<label>Total Inventory (Input):</label>
				<input type="number" id="total_inventory_input" name="total_inventory_input" class="form-control" placeholder="Masukkan total inventory" step="0.01">
			</div>
			<div class="col-sm-3" style="padding-top:25px;">
				<button type="button" class="btn btn-primary btn-sm" id="btn_adjust_harga">
					<i class="fa fa-refresh"></i> Adjust Harga Produksi
				</button>
			</div>
			<div class="col-sm-3" style="padding-top:25px;">
				<div id="info_panel_adjust" class="alert alert-info" style="display:none; padding:8px; margin:0;">
					<span id="info_text_adjust"></span>
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

	// Fix Tran Detail button
	$('#btn_fix_tras').on('click', function(){
		var date_target = $('#date_target').val();
		if(!date_target){
			alert('Pilih tanggal terlebih dahulu!');
			return;
		}

		if(!confirm('Fix harga di tran_warehouse_jurnal_detail gudang 3 tanggal '+date_target+' berdasarkan ledger_subgudang?')){
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/fix_tran_detail"); ?>',
			type: 'POST',
			data: { date_target: date_target },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-wrench"></i> Fix Tran Detail');
				if(res.status == 1){
					$('#info_panel').removeClass('alert-danger').addClass('alert-info').show();
					$('#info_text').html('<i class="fa fa-check"></i> '+res.pesan);
				} else {
					$('#info_panel').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-wrench"></i> Fix Tran Detail');
				alert('Terjadi kesalahan server!');
			}
		});
	});

	// Init datepicker adjust
	$('#date_adjust').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	// Adjust Harga Inventory Produksi button
	$('#btn_adjust_harga').on('click', function(){
		var date_adjust = $('#date_adjust').val();
		var total_input = $('#total_inventory_input').val();

		if(!date_adjust){
			alert('Pilih tanggal adjust terlebih dahulu!');
			return;
		}
		if(!total_input || parseFloat(total_input) <= 0){
			alert('Total inventory harus diisi dan lebih dari 0!');
			return;
		}

		if(!confirm('Adjust harga di tran_warehouse_jurnal_detail gudang produksi tanggal '+date_adjust+'?\n\nTotal inventory target: '+Number(total_input).toLocaleString('id-ID')+'\n\nRumus: harga_baru = (total_inventory / total_input) * harga_lama')){
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/adjust_harga_inventory"); ?>',
			type: 'POST',
			data: { date_target: date_adjust, total_inventory_input: total_input },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Adjust Harga Produksi');
				if(res.status == 1){
					$('#info_panel_adjust').removeClass('alert-danger').addClass('alert-info').show();
					$('#info_text_adjust').html('<i class="fa fa-check"></i> '+res.pesan);
				} else {
					$('#info_panel_adjust').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text_adjust').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-refresh"></i> Adjust Harga Produksi');
				alert('Terjadi kesalahan server!');
			}
		});
	});

});
</script>
