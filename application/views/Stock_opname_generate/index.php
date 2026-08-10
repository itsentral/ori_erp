<?php $this->load->view('include/side_menu'); ?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title; ?></h3>
		<div class="box-tools pull-right">
		</div>
	</div>
	<div class="box-body">
		<!-- Panel Generate -->
		<div class="row" style="margin-bottom:15px;">
			<div class="col-sm-3">
				<label>Pilih Tanggal Generate:</label>
				<input type="text" id="date_target" name="date_target" class="form-control text-center" placeholder="Pilih Tanggal" readonly>
			</div>
			<div class="col-sm-3" style="padding-top:25px;">
				<button type="button" class="btn btn-success btn-sm" id="btn_generate">
					<i class="fa fa-cogs"></i> Generate Stock
				</button>
				<button type="button" class="btn btn-danger btn-sm" id="btn_delete" style="display:none;">
					<i class="fa fa-trash"></i> Hapus Data
				</button>
			</div>
			<div class="col-sm-6" style="padding-top:25px;">
				<div id="info_panel" class="alert alert-info" style="display:none; padding:8px; margin:0;">
					<span id="info_text"></span>
				</div>
			</div>
		</div>

		<!-- Panel View Data -->
		<div class="row" style="margin-bottom:10px;">
			<div class="col-sm-3">
				<label>Lihat Data Tanggal:</label>
				<select id="date_filter" class="form-control input-sm">
					<option value="">-- Pilih Tanggal --</option>
				</select>
			</div>
			<div class="col-sm-9" style="padding-top:25px;">
				<div id="summary_panel" class="well well-sm" style="display:none; margin:0;">
					<b>Summary:</b> 
					<span id="summary_item">0</span> item | 
					Total Qty: <span id="summary_qty">0</span> | 
					Total Nilai (Harga): <b>Rp <span id="summary_harga">0</span></b>
				</div>
			</div>
		</div>

		<!-- Tabel Tanggal yang Tersedia -->
		<div class="row" style="margin-bottom:15px;">
			<div class="col-sm-12">
				<h4>Daftar Tanggal Tersedia:</h4>
				<div class="table-responsive">
					<table class="table table-bordered table-condensed" id="tbl_dates" style="max-width:600px;">
						<thead>
							<tr class="bg-blue">
								<th class="text-center" width="5%">#</th>
								<th class="text-center">Tanggal</th>
								<th class="text-center">Total Item</th>
								<th class="text-center">Total Nilai</th>
							</tr>
						</thead>
						<tbody id="tbody_dates">
							<tr><td colspan="4" class="text-center">Loading...</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<hr>

		<!-- DataTable Detail -->
		<div id="panel_data" style="display:none;">
			<h4>Detail Data Stock Opname: <span id="lbl_date_selected" class="text-blue"></span></h4>
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-condensed" id="my-grid" width="100%">
					<thead>
						<tr class="bg-blue">
							<th class="text-center" width="3%">#</th>
							<th class="text-center">ID Material</th>
							<th class="text-center">Nama Material</th>
							<th class="text-center">Category</th>
							<th class="text-center">Gudang</th>
							<th class="text-center">Qty Stock</th>
							<th class="text-center">Qty Booking</th>
							<th class="text-center">Qty Rusak</th>
							<th class="text-center">Cost Book</th>
							<th class="text-center">Total Value</th>
							<th class="text-center">Harga</th>
							<th class="text-center">Total Harga</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	var table = null;

	// Init datepicker (jQuery UI)
	$('#date_target').datepicker({
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true
	});

	// Load available dates
	loadAvailableDates();

	function loadAvailableDates(){
		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/get_available_dates"); ?>',
			type: 'GET',
			dataType: 'json',
			success: function(res){
				var html = '';
				var options = '<option value="">-- Pilih Tanggal --</option>';
				if(res.data && res.data.length > 0){
					$.each(res.data, function(i, val){
						html += '<tr>';
						html += '<td class="text-center">'+(i+1)+'</td>';
						html += '<td class="text-center">'+val.tgl+'</td>';
						html += '<td class="text-center">'+formatNumber(val.total_item)+'</td>';
						html += '<td class="text-right">'+formatRupiah(val.total_nilai)+'</td>';
						html += '</tr>';
						options += '<option value="'+val.tgl+'">'+val.tgl+' ('+val.total_item+' item)</option>';
					});
				} else {
					html = '<tr><td colspan="4" class="text-center">Belum ada data</td></tr>';
				}
				$('#tbody_dates').html(html);
				$('#date_filter').html(options);
			}
		});
	}

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
					loadAvailableDates();
					// Auto select tanggal yang baru digenerate
					setTimeout(function(){
						$('#date_filter').val(date_target).trigger('change');
					}, 500);
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

	// Delete button
	$('#btn_delete').on('click', function(){
		var date_filter = $('#date_filter').val();
		if(!date_filter){
			alert('Pilih tanggal yang akan dihapus!');
			return;
		}
		if(!confirm('Hapus semua data stok opname pada tanggal '+date_filter+'?\n\nPerhatian: Tindakan ini tidak bisa dibatalkan!')){
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Deleting...');

		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/delete_by_date"); ?>',
			type: 'POST',
			data: { date: date_filter },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Hapus Data');
				if(res.status == 1){
					$('#info_panel').removeClass('alert-danger').addClass('alert-info').show();
					$('#info_text').html('<i class="fa fa-check"></i> '+res.pesan);
					loadAvailableDates();
					$('#panel_data').hide();
					$('#summary_panel').hide();
					$('#btn_delete').hide();
				} else {
					$('#info_panel').removeClass('alert-info').addClass('alert-danger').show();
					$('#info_text').html('<i class="fa fa-warning"></i> '+res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Hapus Data');
				alert('Terjadi kesalahan server!');
			}
		});
	});

	// Filter change - load data
	$('#date_filter').on('change', function(){
		var date_val = $(this).val();
		if(!date_val){
			$('#panel_data').hide();
			$('#summary_panel').hide();
			$('#btn_delete').hide();
			return;
		}

		$('#lbl_date_selected').text(date_val);
		$('#panel_data').show();

		// Show delete button (kecuali tanggal awal)
		if(date_val != '2026-01-01'){
			$('#btn_delete').show();
		} else {
			$('#btn_delete').hide();
		}

		// Load summary
		$.ajax({
			url: '<?php echo site_url("stock_opname_generate/get_summary"); ?>',
			type: 'POST',
			data: { date: date_val },
			dataType: 'json',
			success: function(res){
				$('#summary_panel').show();
				$('#summary_item').text(formatNumber(res.total_item));
				$('#summary_qty').text(formatNumber(res.total_qty));
				$('#summary_harga').text(formatRupiah(res.total_value_harga));
			}
		});

		// Load/reload DataTable
		if(table != null){
			table.destroy();
		}
		table = $('#my-grid').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: '<?php echo site_url("stock_opname_generate/server_side"); ?>',
				type: 'POST',
				data: function(d){
					d.date_filter = date_val;
				}
			},
			pageLength: 25,
			ordering: true,
			columns: [
				{ className: 'text-center' },
				{ className: 'text-center' },
				{ className: 'text-left' },
				{ className: 'text-center' },
				{ className: 'text-center' },
				{ className: 'text-right' },
				{ className: 'text-right' },
				{ className: 'text-right' },
				{ className: 'text-right' },
				{ className: 'text-right' },
				{ className: 'text-right' },
				{ className: 'text-right' },
			]
		});
	});

	// Helper format
	function formatNumber(num){
		if(!num) return '0';
		return parseInt(num).toLocaleString('id-ID');
	}
	function formatRupiah(num){
		if(!num) return '0';
		return parseInt(num).toLocaleString('id-ID');
	}
});
</script>
