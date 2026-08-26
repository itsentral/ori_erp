<?php $this->load->view('include/side_menu'); ?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title; ?></h3>
	</div>
	<div class="box-body">
		<div class="row" style="margin-bottom:15px;">
			<div class="col-sm-2">
				<label>Bulan:</label>
				<select id="bulan" class="form-control input-sm">
					<option value="1">Januari</option>
					<option value="2">Februari</option>
					<option value="3">Maret</option>
					<option value="4">April</option>
					<option value="5">Mei</option>
					<option value="6">Juni</option>
					<option value="7">Juli</option>
					<option value="8">Agustus</option>
					<option value="9">September</option>
					<option value="10">Oktober</option>
					<option value="11">November</option>
					<option value="12">Desember</option>
				</select>
			</div>
			<div class="col-sm-2">
				<label>Tahun:</label>
				<input type="number" id="tahun" class="form-control input-sm" value="2026">
			</div>
			<div class="col-sm-3">
				<label>No Perkiraan (COA):</label>
				<select id="no_perkiraan" class="form-control input-sm">
					<option value="">Semua Gudang</option>
					<?php foreach($rows_coa as $coa){ ?>
						<option value="<?php echo $coa->coa_1; ?>"><?php echo $coa->coa_1; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-2" style="padding-top:25px;">
				<button type="button" class="btn btn-success btn-sm" id="btn_load">
					<i class="fa fa-search"></i> Tampilkan
				</button>
			</div>
		</div>

		<div id="panel_result" style="display:none;">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-condensed" id="tbl_rekap" style="max-width:800px;">
					<thead>
						<tr class="bg-blue">
							<th class="text-center" width="5%">#</th>
							<th class="text-center">Tanggal</th>
							<th class="text-center">Total Qty</th>
							<th class="text-center">Total Nilai (Rp)</th>
						</tr>
					</thead>
					<tbody id="tbody_rekap">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<script>
$(document).ready(function(){

	$('#btn_load').on('click', function(){
		var bulan = $('#bulan').val();
		var tahun = $('#tahun').val();
		var coa   = $('#no_perkiraan').val();

		if(!bulan || !tahun){
			alert('Pilih bulan dan tahun!');
			return;
		}

		var btn = $(this);
		btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');

		$.ajax({
			url: '<?php echo site_url("warehouse_stock_tras/get_rekap_harian"); ?>',
			type: 'POST',
			data: { bulan: bulan, tahun: tahun, no_perkiraan: coa },
			dataType: 'json',
			success: function(res){
				btn.prop('disabled', false).html('<i class="fa fa-search"></i> Tampilkan');
				if(res.status == 1){
					var html = '';
					var grandTotal = 0;
					$.each(res.data, function(i, val){
						grandTotal += val.total_nilai;
						html += '<tr>';
						html += '<td class="text-center">'+(i+1)+'</td>';
						html += '<td class="text-center">'+val.tanggal+'</td>';
						html += '<td class="text-right">'+formatNumber(val.total_qty)+'</td>';
						html += '<td class="text-right">'+formatRupiah(val.total_nilai)+'</td>';
						html += '</tr>';
					});
					html += '<tr class="bg-yellow"><td colspan="3" class="text-right"><b>Grand Total</b></td>';
					html += '<td class="text-right"><b>'+formatRupiah(grandTotal)+'</b></td></tr>';
					$('#tbody_rekap').html(html);
					$('#panel_result').show();
				} else {
					alert(res.pesan);
				}
			},
			error: function(){
				btn.prop('disabled', false).html('<i class="fa fa-search"></i> Tampilkan');
				alert('Terjadi kesalahan server!');
			}
		});
	});

	function formatNumber(num){
		if(!num) return '0';
		return parseFloat(num).toLocaleString('id-ID', {maximumFractionDigits: 4});
	}
	function formatRupiah(num){
		if(!num) return '0';
		return parseInt(num).toLocaleString('id-ID');
	}
});
</script>
