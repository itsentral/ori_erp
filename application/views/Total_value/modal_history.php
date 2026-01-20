
<div class="box-body">
    <table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No SO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['no_so']);?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No SPK</td> 
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['no_spk']);?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Kode Trans</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper($material[0]['kode_trans']);?></td>
			</tr>
            <tr>
				<td class="text-left" style='vertical-align:middle;'>Product</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><b><?=strtoupper(get_name('warehouse','nm_gudang','id',$id_gudang));?></b></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"> 
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;'>#</th>
				<th class="text-center" style='vertical-align:middle;'>No SO</th>
				<th class="text-center" style='vertical-align:middle;'>No SPK</th>
				<th class="text-center" style='vertical-align:middle;'>Kode Trans</th>
                <th class="text-center" style='vertical-align:middle;'>Product</th>
                <th class="text-center" style='vertical-align:middle;'>Jenis</th>
				<th class="text-center" style='vertical-align:middle;'>Qty</th>
				<th class="text-center" style='vertical-align:middle;'>Nilai Unit</th>
				<th class="text-center" style='vertical-align:middle;'>Nilai Total</th>
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
                if('out' == $valx['jenis']){
                    $bold = 'text-bold';
                    $color = 'text-red';
                }
                if('in' == $valx['jenis']){
                    $bold2 = 'text-bold';
					$color = 'text-green';
                }
				
				echo "<tr>";
					echo "<td>".$No."</td>";
					echo "<td>".strtoupper($valx['no_so'])."</td>";
					echo "<td class='".$bold."'>".strtoupper($valx['no_spk'])."</td>";
					echo "<td class='".$bold2."'>".strtoupper($valx['kode_trans'])."</td>";
                    echo "<td class='".$color."'>".strtoupper($valx['product'])."</td>";
                    echo "<td class='".$color."'>".strtoupper($valx['jenis'])."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['nilai_wip'],4)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['qty']*$valx['nilai_wip'],4)."</td>";
					echo "<td class='text-right'>".date('d-M-Y H:i:s', strtotime($valx['created_date']))."</td>";
					if($this->uri->segment(5) =='1'){
					echo "<td class='text-right ".$color."'>".number_format($valx['nilai_wip'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['nilai_wip']*$valx['harga'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['nilai_wip'],2)."</td>";
					echo "<td class='text-right ".$color."'>".number_format($valx['nilai_wip'],2)."</td>";
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