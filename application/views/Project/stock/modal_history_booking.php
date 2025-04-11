
<div class="box-body">
    <table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>Code</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['code_group']);?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Nama Material</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['material_name']);?></td>
			</tr>
		</thead>
	</table><br>
	<h4>History All</h4>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;'>#</th>
				<th class="text-center" style='vertical-align:middle;'>No IPP</th>
				<th class="text-center" style='vertical-align:middle;'>No SO</th>
				<th class="text-center" style='vertical-align:middle;'>Gudang Dari</th>
				<th class="text-center" style='vertical-align:middle;'>Gudang Ke</th>
				<th class="text-center" style='vertical-align:middle;'>Qty Booking</th>
				<th class="text-center" style='vertical-align:middle;'>Booking Awal</th>
				<th class="text-center" style='vertical-align:middle;'>Booking Akhir</th>
				<th class="text-center" style='vertical-align:middle;'>Keterangan</th>
				<th class="text-center" style='vertical-align:middle;'>Tanggal</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
            $SUM = 0;
			foreach($result AS $val => $valx){
                $No++;
                $bold = '';
                $bold2 = '';
                $color = 'text-blue';
				

				$gudang_dari 	= get_name('warehouse','nm_gudang','id',$valx['id_gudang_dari']);
				$dari_gudang 	= (!empty($gudang_dari))?$gudang_dari:$valx['gudang_dari'];
				$ke_gudang 		= $valx['gudang_ke'];

				if($ke_gudang != 'BOOKING'){
					$bold = 'text-bold';
					$color = 'text-red';
				}
				if($ke_gudang == 'BOOKING'){
					$bold2 = 'text-bold';
				}

				$SUM += $valx['jumlah_qty'];
				echo "<tr>";
					echo "<td class='text-center'>".$No."</td>";
					echo "<td class='text-center'>".strtoupper($valx['no_trans'])."</td>";
					echo "<td class='text-center'>".strtoupper(get_name('so_number','so_number','id_bq','BQ-'.$valx['no_trans']))."</td>";
					echo "<td class='text-center ".$bold."'>".strtoupper($dari_gudang)."</td>";
					echo "<td class='text-center ".$bold2."'>".strtoupper($ke_gudang)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['jumlah_qty'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty_booking_awal'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty_booking_akhir'],4)."</td>";
					echo "<td>".strtoupper($valx['ket'])."</td>";
					echo "<td class='text-center'>".date('d-M-Y H:i:s', strtotime($valx['update_date']))."</td>";
				echo "</tr>";
			}
            
            if(empty($result)){
                echo "<tr>";
					echo "<td colspan='10'>Tidak ada history yang ditampilkan.</td>";
				echo "</tr>";
            }
            else{
                echo "<tr>";
                    echo "<td></td>";
                    echo "<td colspan='6' class='text-bold'>STOK BOOKING</td>";
                    echo "<td class='text-right text-bold'>".number_format($valx['qty_booking_akhir'],4)."</td>";
                    echo "<td colspan='2'></td>";
                echo "</tr>";
            }
			?>
		</tbody>
	</table>
	<br>
	<h4>History Per Sales Order</h4>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr>
				<td colspan='8'>
					<select name="sales_order" id="sales_order" class='from-control chosen-select'>
						<option value="0">Pilih Sales Order</option>
						<?php
							foreach ($listSO as $key => $value) {
								$NO_SO = (!empty($GET_SO[$value['no_trans']]['so_number']))?$GET_SO[$value['no_trans']]['so_number']:'Not Found';
								echo "<option value='".$value['no_trans']."'>".$NO_SO."</option>";
							}
						?>
					</select>
					<input type="hidden" id='id_material' value='<?=$id_material;?>'>
					<input type="hidden" id='id_gudang' value='<?=$id_gudang;?>'>
					<button type='button' class='btn btn-md btn-default' id='showHist'>SEARCH</button>
				</td>
			</tr>
		</thead>
		<tbody id='htmlHist'></tbody>
	</table>
</div>
<script>
	swal.close();
	$('.chosen-select').chosen({
		allow_single_deselect	: true,
		search_contains			: true,
		no_results_text			: 'No result found for : ',
		placeholder_text_single	: 'Select an option',
		width : '150px'
	});

	$(document).on('click', '#showHist', function(e){
		let sales_order   	= $('#sales_order').val();
		let id_material   	= $('#id_material').val();
		let id_gudang   	= $('#id_gudang').val();

		if(sales_order == 0){
			swal({
				title	: "Error Message!",
				text	: 'No SO wajib dipilih ...',
				type	: "warning"
			});
			return false;	
		}

		var baseurl=base_url + active_controller +'/show_history_booking';
		$.ajax({
			url			: baseurl,
			type		: "POST",
			data		: {
				'no_ipp' 	: sales_order,
				'id_material' 	: id_material,
				'id_gudang' 	: id_gudang,
			},
			cache		: false,
			dataType	: 'json',
			success		: function(data){
				if(data.status == 1){
				$('#htmlHist').html(data.data_html);
				swal.close();
				}
				else{
					swal({
						title	: "Save Failed!",
						text	: data.pesan,
						type	: "warning",
						timer	: 3000
					});
				}
			},
			error: function() {

				swal({
					title		: "Error Message !",
					text		: 'An Error Occured During Process. Please try again..',
					type		: "warning",
					timer		: 3000
				});
			}
		});
	});
</script>