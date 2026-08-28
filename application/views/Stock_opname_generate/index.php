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
			<div class="col-sm-2">
				<label>Tanggal Adjust:</label>
				<input type="text" id="date_adjust" name="date_adjust" class="form-control text-center" placeholder="Pilih Tanggal">
			</div>
			<div class="col-sm-2">
				<label>No Perkiraan (COA):</label>
				<input type="text" id="coa_adjust" name="coa_adjust" class="form-control" placeholder="cth: 1103-01-03">
			</div>
			<div class="col-sm-3">
				<label>Total Inventory (Input):</label>
				<input type="number" id="total_inventory_input" name="total_inventory_input" class="form-control" placeholder="Masukkan total inventory" step="0.01">
			</div>
			<div class="col-sm-3" style="padding-top:25px;">
				<button type="button" class="btn btn-default btn-sm" id="btn_preview_adjust">
					<i class="fa fa-search"></i> Preview
				</button>
				<button type="button" class="btn btn-primary btn-sm" id="btn_adjust_harga">
					<i class="fa fa-refresh"></i> Adjust Harga Produksi
				</button>
			</div>
			<div class="col-sm-2" style="padding-top:25px;">
				<div id="info_panel_adjust" class="alert alert-info" style="display:none; padding:8px; margin:0;">
					<span id="info_text_adjust"></span>
				</div>
			</div>
		</div>

		<!-- Tabel Detail Selisih -->
		<div class="row" id="panel_detail_selisih" style="display:none;">
			<div class="col-sm-12">
				<h4>Detail Material Selisih</h4>
				<div id="summary_preview" style="margin-bottom:10px;"></div>
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-condensed" id="tbl_detail_selisih">
						<thead>
							<tr>
								<th>ID</th>
								<th>ID Material</th>
								<th>Nama</th>
								<th>Category</th>
								<th>ID Gudang</th>
								<th>Nm Gudang</th>
								<th>Qty</th>
								<th>Harga</th>
								<th>Total Hari Ini</th>
								<th>Total Kemarin</th>
								<th>Selisih</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
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

	// Preview Adjust button
	$('#btn_preview_adjust').on('click', function(){
		var date_adjust = $('#date_adjust').val();
		var total_input = $('#total_inventory_input').val();

		if(!date_adjust){
			alert('Pilih tanggal adjust terlebih dahulu!');
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/preview_adjust_inventory"); ?>',
			type: 'POST',
			data: { date_target: date_adjust, total_inventory_input: total_input || 0, coa: $('#coa_adjust').val() },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-search"></i> Preview');
				if(res.status == 1){
					// Summary
					var html_summary = '<strong>Tanggal:</strong> '+res.date_target+' | <strong>H-1:</strong> '+res.date_prev+'<br>';
					html_summary += '<strong>Total Inventory Hari Ini:</strong> '+Number(res.total_inventory_today).toLocaleString('id-ID')+'<br>';
					html_summary += '<strong>Total Inventory Kemarin:</strong> '+Number(res.total_inventory_prev).toLocaleString('id-ID')+'<br>';
					html_summary += '<strong>Total Inventory Selisih:</strong> '+Number(res.total_inventory_selisih).toLocaleString('id-ID')+'<br>';
					html_summary += '<strong>Total Input:</strong> '+Number(res.total_input).toLocaleString('id-ID')+'<br>';
					html_summary += '<strong>Rasio:</strong> '+res.rasio+'<br>';
					html_summary += '<strong>Jumlah Material Selisih:</strong> '+res.jumlah_material_selisih;
					$('#summary_preview').html(html_summary);

					// Table
					var tbody = '';
					if(res.detail_selisih && res.detail_selisih.length > 0){
						for(var i=0; i<res.detail_selisih.length; i++){
							var d = res.detail_selisih[i];
							tbody += '<tr>';
							tbody += '<td>'+d.id+'</td>';
							tbody += '<td>'+d.id_material+'</td>';
							tbody += '<td>'+d.nm_material+'</td>';
							tbody += '<td>'+d.nm_category+'</td>';
							tbody += '<td>'+d.id_gudang+'</td>';
							tbody += '<td>'+d.nm_gudang+'</td>';
							tbody += '<td align="right">'+Number(d.qty_today).toLocaleString('id-ID',{minimumFractionDigits:4})+'</td>';
							tbody += '<td align="right">'+Number(d.harga_today).toLocaleString('id-ID',{minimumFractionDigits:2})+'</td>';
							tbody += '<td align="right">'+Number(d.total_today).toLocaleString('id-ID',{minimumFractionDigits:2})+'</td>';
							tbody += '<td align="right">'+Number(d.total_prev).toLocaleString('id-ID',{minimumFractionDigits:2})+'</td>';
							tbody += '<td align="right">'+Number(d.selisih_total).toLocaleString('id-ID',{minimumFractionDigits:2})+'</td>';
							tbody += '</tr>';
						}
					} else {
						tbody = '<tr><td colspan="11" align="center">Tidak ada material selisih</td></tr>';
					}
					$('#tbl_detail_selisih tbody').html(tbody);
					$('#panel_detail_selisih').show();
				} else {
					$('#panel_detail_selisih').hide();
					$('#info_panel_adjust').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text_adjust').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-search"></i> Preview');
				alert('Terjadi kesalahan server!');
			}
		});
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
			data: { date_target: date_adjust, total_inventory_input: total_input, coa: $('#coa_adjust').val() },
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
