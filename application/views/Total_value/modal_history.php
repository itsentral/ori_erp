
<div class="box-body">
    <table id="my-grid" class="table" width="100%">
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
				<th class="text-center" style='vertical-align:middle;'>#</th>
				<th class="text-center" style='vertical-align:middle;'>No Dokumen</th>
				<th class="text-center" style='vertical-align:middle;'>Gudang Dari</th>
				<th class="text-center" style='vertical-align:middle;'>Gudang Ke</th>
				<th class="text-center" style='vertical-align:middle;'>Stock Awal</th>
				<th class="text-center" style='vertical-align:middle;'>Qty Material</th>
				<th class="text-center" style='vertical-align:middle;'>Stock Akhir</th>
				<th class="text-center" style='vertical-align:middle;'>Keterangan</th>
				<th class="text-center" style='vertical-align:middle;'>Tanggal</th>
				<?php if($this->uri->segment(5) =='1'){ ?>
					<th class="text-center" style='vertical-align:middle;'>Cost Book</th>
					<th class="text-center" style='vertical-align:middle;'>Total</th>
					<th class="text-center" style='vertical-align:middle;'>Saldo Awal</th>
					<th class="text-center" style='vertical-align:middle;'>Saldo Akhir</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
                $bold = '';
                $bold2 = '';
				$color = '';
                $color2 = '';
                if($id_gudang == $valx['id_gudang_dari']){
                    $bold = 'text-bold';
                    $color = 'text-red';
                }
                if($id_gudang == $valx['id_gudang_ke']){
                    $bold2 = 'text-bold';
					$color = 'text-green';
                }
				
				echo "<tr>";
					echo "<td>".$No."</td>";
					echo "<td>".strtoupper($valx['no_ipp'])."</td>";
					echo "<td class='".$bold."'>".strtoupper(get_name('warehouse','nm_gudang','id',$valx['id_gudang_dari']))."</td>";
					echo "<td class='".$bold2."'>".strtoupper(get_name('warehouse','nm_gudang','id',$valx['id_gudang_ke']))."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty_stock_awal'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['jumlah_mat'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty_stock_akhir'],4)."</td>";
					echo "<td class='".$color."'>".strtoupper($valx['ket'])."</td>";
					echo "<td class='text-right'>".date('d-M-Y H:i:s', strtotime($valx['update_date']))."</td>";
					if($this->uri->segment(5) =='1'){
					echo "<td class='text-right ".$color."'>".number_format($valx['harga_baru'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['jumlah_mat']*$valx['harga'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['saldo_awal'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['saldo_akhir'],2)."</td>";
					}
				echo "</tr>";
			}
            if(empty($result)){
                echo "<tr>";
					echo "<td colspan='9'>Tidak ada history yang ditampilkan.</td>";
				echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close(); 
</script>