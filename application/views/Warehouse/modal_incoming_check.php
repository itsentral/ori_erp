
<div class="box-body">
	<div class="callout callout-info">
		<p>Klik sembarang untuk lock checking tidak melebihi sisa incoming</p>
	</div>
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No ROS</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_ros;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po."/".$dated;?></td>
				<td class="text-left" style='vertical-align:middle;' width='15%'></td>
				<td class="text-left" style='vertical-align:middle;' width='2%'></td>
				<td class="text-left" style='vertical-align:middle;'></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
				<td class="text-left" style='vertical-align:middle;' width='15%'></td>
				<td class="text-left" style='vertical-align:middle;' width='2%'></td>
				<td class="text-left" style='vertical-align:middle;'></td>
			</tr>
		</thead>
	</table><br>
	<input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
	<input type="hidden" name='id_header' id='id_header' value='<?= $id_header;?>'>
	<input type="hidden" name='gudang_tujuan' id='gudang_tujuan' value='<?= $gudang_tujuan;?>'>
	<input type="hidden" name='id_tujuan' id='id_tujuan' value='<?= $id_tujuan;?>'>
	<input type="hidden" name='no_pox' id='no_pox' value='<?= $no_po;?>'>
	<input type="hidden" name='no_rosx' id='no_rosx' value='<?= $id_ros;?>'>
	<input type="hidden" name='total_freight' id='total_freight' value='<?= $total_freight;?>'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Qty Order</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>UoM Order</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Diterima</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Qty Kurang</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Qty NG</th> 
				<th class="text-center" style='vertical-align:middle;' width='8%'>Expired Date</th> 
				<th class="text-center" style='vertical-align:middle;' width='8%'>Konversi (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Keterangan</th>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				$totIn = $valx['qty_oke'] - $valx['check_qty_oke'];
				$ICON = "<i class='fa fa-clock-o text-orange' title='Menunggu cek incoming.'></i>";
				$DISABLED = "";
				if($valx['qty_oke'] <= 0){
					$ICON = "<i class='fa fa-times text-red' title='Tidak ada cek incoming.'></i>";
					$DISABLED = "disabled";
				}
				if($valx['check_qty_oke'] > 0){
					$ICON = "<i class='fa fa-check text-green' title='Sudah di cek.'></i>";
					$DISABLED = "disabled";
				}
				if($valx['check_qty_oke'] < $valx['qty_oke']){
					$ICON = "<i class='fa fa-clock-o text-orange' title='Parsial cek.'></i>";
					$DISABLED = "";
				}
				echo "<tr class='baris_".$No."'>";
					echo "<td rowspan='1' class='id_".$No."' align='center'>".$No."<input type='hidden' name='detail[$No][id]' value='".$valx['id']."' $DISABLED><input type='hidden' name='detail[$No][id2]' value='".$valx['id2']."' $DISABLED></td>";
					echo "<td rowspan='1' class='id_".$No."'>".$valx['nm_material']."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($valx['qty_order'],4)."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='center'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($valx['check_qty_oke'],4)." / ".number_format($valx['qty_oke'],4)." ".$ICON."</td>";
					echo "<td rowspan='1' class='id_".$No." belumDiterima' id='belumDiterima_".$No."' align='right'>".number_format($totIn,4)."</td>";
					echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_oke]' data-no='$No' class='form-control input-sm text-right maskM qtyDiterima' $DISABLED></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM' $DISABLED></td>";
					echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][expired]' data-no='$No' class='form-control text-center input-sm text-left tanggal' readonly placeholder='Expired Date' $DISABLED></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][konversi]' data-no='$No' value='1' class='form-control input-sm text-center maskM' $DISABLED></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][keterangan]' data-no='$No' class='form-control input-sm text-left' $DISABLED></td>";
					echo "<td align='center'>
							<button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$No."' $DISABLED><i class='fa fa-plus'></i></button>
						</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'checkMaterial')).' ';
	?>
</div>
<style>
	.tanggal{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});
    });

	$(document).on('change','.qtyDiterima',function(){
		let idNomor = $(this).data('no')
		// let belumDiterima 	= getNum($(this).parent().parent().find('#belumDiterima_'+idNomor).text().split(',').join(''))
		let belumDiterima 	= getNum($('#belumDiterima_'+idNomor).text().split(',').join(''))
		// let qtyDiterima 	= getNum($(this).val().split(',').join(''))

		// if(qtyDiterima > belumDiterima){
		// 	$(this).val(belumDiterima)
		// }
		let inputQty
		let sisaDiterima
		let ID
		$('.qtyDiterima').each(function(){
			ID = $(this).data('no')
			if(ID == idNomor){
				inputQty = getNum($(this).val().split(',').join(''))
				sisaDiterima = belumDiterima - inputQty

				console.log(inputQty)
				console.log(belumDiterima)
				console.log(sisaDiterima)

				if(sisaDiterima >= 0){
					$(this).val(inputQty)
					// console.log('kurang')
				}
				else{
					// console.log('lebih')
					if(belumDiterima < 0){
						$(this).val(0)
					}
					else{
						$(this).val(belumDiterima)

					}
				}

				belumDiterima = sisaDiterima
			}
			
		})
	})
</script>