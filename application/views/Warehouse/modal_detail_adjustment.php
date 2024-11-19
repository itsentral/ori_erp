
<div class="box-body">
	<?php if($tanda != 'request'){?>
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No ROS</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_ros;?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Name Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Diterima</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Kurang</th> 
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
				<th class="text-center" style='vertical-align:middle;' width='5'>QRCode</th> 
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				
				$qty_oke 		= number_format($valx['qty_oke'],4);
				$qty_rusak 		= number_format($valx['qty_rusak'],4);
				$keterangan 	= (!empty($valx['keterangan']))?ucfirst($valx['keterangan']):'-';
				$qty_kurang 	= number_format($valx['qty_order'] - $valx['qty_oke'],4);
				if($tanda == 'check' AND $checked == 'Y'){
					$qty_oke 		= number_format($valx['check_qty_oke'],4);
					$qty_rusak 		= number_format($valx['check_qty_rusak'],4);
					$keterangan 	= (!empty($valx['check_keterangan']))?ucfirst($valx['check_keterangan']):'-';
					$qty_kurang 	= number_format($valx['qty_order'] - $valx['check_qty_oke'],4);
				}
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['qty_order'],4)."</td>";
					echo "<td align='right'>".$qty_oke."</td>";
					echo "<td align='right'>".$qty_kurang."</td>";
					echo "<td>".$keterangan."</td>";
					echo "<td><a href='".base_url('warehouse/print_qrcode/'.$valx['id'])."' target='_blank' class='btn btn-xs btn-default'>QR</a></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
	
	<?php if($tanda == 'request'){?>
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;' width='33%'><?=$kode_trans;?></td>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No. SO / Project</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=get_name('so_number','so_number','id_bq','BQ-'.$no_ipp).' / '.strtoupper(get_name('production','project','no_ipp',$no_ipp));?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Request</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
				<td class="text-left" style='vertical-align:middle;'>Nama Product / Spec</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=strtoupper(get_name('so_detail_header','id_category','id',$id_milik)).' / '.strtoupper(spec_bq2($id_milik));?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tgl Planning</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$tanggal;?></td>
				<td class="text-left" style='vertical-align:middle;'>Qty SPK</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=(!empty($qty_spk))?number_format($qty_spk):'';?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;'>Category</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Est (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Sisa Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Total Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Actual (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td>".$valx['nm_category']."</td>";
					echo "<td align='right'>".number_format($valx['qty_est'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_sisa'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_total_req'],4)."</td>";
					echo "<td align='right'>".number_format($valx['qty_oke'],4)."</td>";
					echo "<td align='right'>".number_format($valx['check_qty_oke'],4)."</td>";
					echo "<td>".strtoupper($valx['keterangan'])."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
</div>
<script>
	swal.close();
</script>