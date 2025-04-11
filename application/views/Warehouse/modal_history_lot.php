
<div class="box-body">
    <table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>ID Material</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['idmaterial']);?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Nama Material</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['nm_material']);?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Category</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['nm_category']);?></td>
			</tr>
            <tr>
				<td class="text-left" style='vertical-align:middle;'>Gudang</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><b><?=strtoupper(get_name('warehouse','nm_gudang','id',$id_gudang));?></b></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='4%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>No Transaksi</th>
				<th class="text-center" style='vertical-align:middle;'>Check By</th>
				<th class="text-center" style='vertical-align:middle;'>Check Date</th>
				<th class="text-right" style='vertical-align:middle;' width='12%'>Qty NG</th>
				<th class="text-right" style='vertical-align:middle;' width='12%'>Qty Oke</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Konversi</th>
				<th class="text-right" style='vertical-align:middle;' width='12%'>Qty Packing</th>
				<th class="text-center" style='vertical-align:middle;'>Lot Description</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Expired Date</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
                $unit = strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_satuan']));
                $packing = strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']));
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td align='center'>".$valx['kode_trans']."</td>";
                    echo "<td class='text-left'>".strtoupper(get_name('users','nm_lengkap','username',$valx['update_by']))."</td>";
                    echo "<td class='text-center'>".date('d-M-Y H:i', strtotime($valx['update_date']))."</td>";
                    echo "<td class='text-right'>".number_format($valx['qty_rusak'],4)." ".$unit."</td>";
                    echo "<td class='text-right'>".number_format($valx['qty_oke'],4)." ".$unit."</td>";
                    echo "<td class='text-center'>".number_format($valx['konversi'],2)."</td>";
                    $qty_packing = ($valx['qty_oke'] > 0 AND $valx['konversi'] > 0)?number_format($valx['konversi']/$valx['qty_oke'],2):'-';
                    echo "<td class='text-right'>".$qty_packing." ".$packing."</td>";
                   echo "<td>".$valx['keterangan']."</td>";
                    $expired_date = (!empty($valx['expired_date']) AND $valx['expired_date'] != '0000-00-00')?date('d-M-Y',strtotime($valx['expired_date'])):'-';
                    echo "<td align='center'>".$expired_date."</td>";
				echo "</tr>";
			}
            if(empty($result)){
                echo "<tr>";
					echo "<td colspan='10'>Tidak ada history yang ditampilkan.</td>";
				echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>