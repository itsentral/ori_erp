<?php
$this->load->view('include/side_menu');
?>
<style>
	.table-ledger tbody tr td {
		vertical-align: middle;
		border: 1px solid #ddd;
		padding: 5px 8px;
		font-size: 12px;
	}
	.table-ledger thead tr th {
		vertical-align: middle;
		border: 1px solid #ddd;
		text-align: center;
		padding: 8px;
		font-size: 12px;
	}
	.row-header td {
		font-weight: bold;
		background-color: #f9f9f9;
	}
</style>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<div class="box-body">
		<div class="form-group row">
			<div class="col-sm-12">
				<h5><strong>PERIODE :</strong></h5>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-2 text-right" style="line-height:34px;">
				<label>Bulan</label>
			</div>
			<div class="col-sm-10">
				<select class="form-control" id="bulan">
					<option value="01" <?php echo (date('m')=='01')?'selected':''; ?>>Januari</option>
					<option value="02" <?php echo (date('m')=='02')?'selected':''; ?>>Februari</option>
					<option value="03" <?php echo (date('m')=='03')?'selected':''; ?>>Maret</option>
					<option value="04" <?php echo (date('m')=='04')?'selected':''; ?>>April</option>
					<option value="05" <?php echo (date('m')=='05')?'selected':''; ?>>Mei</option>
					<option value="06" <?php echo (date('m')=='06')?'selected':''; ?>>Juni</option>
					<option value="07" <?php echo (date('m')=='07')?'selected':''; ?>>Juli</option>
					<option value="08" <?php echo (date('m')=='08')?'selected':''; ?>>Agustus</option>
					<option value="09" <?php echo (date('m')=='09')?'selected':''; ?>>September</option>
					<option value="10" <?php echo (date('m')=='10')?'selected':''; ?>>Oktober</option>
					<option value="11" <?php echo (date('m')=='11')?'selected':''; ?>>November</option>
					<option value="12" <?php echo (date('m')=='12')?'selected':''; ?>>Desember</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-2 text-right" style="line-height:34px;">
				<label>Tahun</label>
			</div>
			<div class="col-sm-10">
				<select class="form-control" id="tahun">
					<?php
					$thn_now = date('Y');
					for($y = $thn_now; $y >= $thn_now - 3; $y--){
						$sel = ($y == $thn_now) ? 'selected' : '';
						echo "<option value='".$y."' ".$sel.">".$y."</option>";
					}
					?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-sm-2"></div>
			<div class="col-sm-10">
				<button type="button" class="btn btn-md btn-primary" id="btn_tampilkan"><i class="fa fa-search"></i> Tampilkan</button>
				<button type="button" class="btn btn-md btn-success" id="btn_download_excel"><i class="fa fa-file-excel-o"></i> Download Excel</button>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-12">
				<div class="text-center" id="title_periode" style="margin-bottom:10px;">
					<strong>LAPORAN LEDGER IN CUSTOMER</strong><br>
					<strong>Periode : <?php echo date('F Y'); ?></strong>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered table-ledger" id="tbl_ledger" width="100%">
						<thead>
							<tr class="bg-blue">
								<th>Keterangan</th>
								<th>Tanggal Bukti</th>
								<th>Nomor Bukti</th>
								<th>SM</th>
								<th>In</th>
								<th>Out</th>
								<th>Saldo</th>
							</tr>
						</thead>
						<tbody id="tbody_ledger">
							<tr><td colspan="7" class="text-center">Silahkan pilih periode lalu klik Tampilkan</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('include/footer'); ?>
<script>
$(document).ready(function(){
	var Arr_Bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

	$('#btn_tampilkan').on('click', function(){
		var bulan = $('#bulan').val();
		var tahun = $('#tahun').val();
		var bulanInt = parseInt(bulan);

		$('#title_periode').html('<strong>LAPORAN LEDGER IN CUSTOMER</strong><br><strong>Periode : '+Arr_Bulan[bulanInt]+' '+tahun+'</strong>');
		$('#tbody_ledger').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

		$.ajax({
			url: '<?php echo site_url("Ledger_incustomer/get_data_json"); ?>',
			type: 'GET',
			data: { bulan: bulan, tahun: tahun },
			dataType: 'json',
			success: function(response){
				var html = '';
				if(response.data && response.data.length > 0){
					var totalIn = 0;
					var totalOut = 0;
					$.each(response.data, function(i, group){
						if(group.detail && group.detail.length > 0){
							$.each(group.detail, function(j, det){
								totalIn += parseFloat(det.in) || 0;
								totalOut += parseFloat(det.out) || 0;
								html += '<tr>';
								html += '<td>'+det.keterangan+'</td>';
								html += '<td class="text-center">'+det.tanggal+'</td>';
								html += '<td class="text-center">'+det.nomor_bukti+'</td>';
								html += '<td>'+det.sm+'</td>';
								html += '<td class="text-right">'+formatNumber(det.in)+'</td>';
								html += '<td class="text-right">'+formatNumber(det.out)+'</td>';
								html += '<td class="text-right">'+formatNumber(det.saldo)+'</td>';
								html += '</tr>';
							});
						}
					});
					// Total row
					html += '<tr class="row-header">';
					html += '<td colspan="4" class="text-right"><strong>TOTAL</strong></td>';
					html += '<td class="text-right"><strong>'+formatNumber(totalIn)+'</strong></td>';
					html += '<td class="text-right"><strong>'+formatNumber(totalOut)+'</strong></td>';
					html += '<td class="text-right"><strong>'+formatNumber(totalIn - totalOut)+'</strong></td>';
					html += '</tr>';
				} else {
					html = '<tr><td colspan="7" class="text-center">Data tidak ditemukan</td></tr>';
				}
				$('#tbody_ledger').html(html);
			},
			error: function(){
				$('#tbody_ledger').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data</td></tr>');
			}
		});
	});

	$('#btn_download_excel').on('click', function(){
		var bulan = $('#bulan').val();
		var tahun = $('#tahun').val();
		window.location.href = '<?php echo site_url("Ledger_incustomer/excel_ledger_incustomer"); ?>/'+bulan+'/'+tahun;
	});

	function formatNumber(num){
		if(num == 0 || num == '0' || num == null || num == undefined) return '0,00';
		var n = parseFloat(num);
		var parts = n.toFixed(2).split('.');
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
		return parts.join(',');
	}
});
</script>
