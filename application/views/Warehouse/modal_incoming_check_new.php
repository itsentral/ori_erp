
<div class="box-body">
	<div class="callout callout-info">
		<p>Klik sembarang untuk lock checking tidak melebihi sisa incoming</p>
	</div>
	<table width="100%">
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
			<?php
			$LINK = "-";
			if(!empty($dokumen_file)){
				$LINK = "<a href='".base_url($dokumen_file)."' target='_blank'>Download</a> ";
			}
			?>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>File Dokumen</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$LINK;?></td>
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
				<th class="text-center" style='vertical-align:middle;' width='2%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Nama Barang</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Diterima</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Konversi</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Pack</th>
				<th class="text-center" style='vertical-align:middle;' width='4%'>Unit Pack</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty Oke</th>
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty NG</th> 
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty Pack</th> 
				<th class="text-center" style='vertical-align:middle;' width='7%'>Expired Date</th> 
				<th class="text-center" style='vertical-align:middle;' width='10%'>Dokumen</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Lot Description</th>
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
					echo "<td rowspan='1' class='id_".$No."'>".$valx['nm_material2']."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='right'>In: ".number_format($valx['check_qty_oke'],4)."<br>Order: ".number_format($valx['qty_oke'],4)."<br>Kurang: <span id='belumDiterima_".$No."'>".number_format($totIn,4)."</span><br>".$ICON."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='center'>".strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_satuan']))."</td>";
					$pack_in = 0;
                    $pack_order = 0;
                    $pack_kurang = 0;
                    $nilai_konversi = $valx['nilai_konversi'];
                    if($nilai_konversi > 0 AND $valx['check_qty_oke'] > 0){
                        $pack_in = $valx['check_qty_oke'] / $nilai_konversi;
                    }
                    if($nilai_konversi > 0 AND $valx['qty_oke'] > 0){
                        $pack_order = $valx['qty_oke'] / $nilai_konversi;   
                    }
                    if($nilai_konversi > 0 AND $totIn > 0){
                        $pack_kurang = $totIn / $nilai_konversi;  
                    }
                    echo "<td rowspan='1' class='id_".$No."' align='center' id='konversi_".$No."'>".number_format($nilai_konversi)."</td>";
                    echo "<td rowspan='1' class='id_".$No."' align='right'>In: ".number_format($pack_in,4)."<br>Order: ".number_format($pack_order,4)."<br>Kurang: ".number_format($pack_kurang,4)."</td>";
					echo "<td rowspan='1' class='id_".$No."' align='center'>".strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']))."</td>";
					
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_oke]' data-no='$No' data-kolom='1' class='form-control input-sm text-right maskM qtyDiterima' $DISABLED></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM' $DISABLED></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][qty_pack]' data-no='$No' id='pack_".$No."_1' class='form-control input-sm text-right maskM' $DISABLED readonly></td>";
					echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][expired]' data-no='$No' class='form-control text-center input-sm text-left tanggal' readonly placeholder='Expired Date' $DISABLED></td>";
                    echo "<td align='center'>
									<input type='file' name='file_".$No."_1' class='form-control input-sm'>
									<input type='hidden' name='detail[".$No."][detail][1][konversi]' data-no='$No' value='".$nilai_konversi."' class='form-control input-sm text-center maskM' $DISABLED>
									</td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][keterangan]' data-no='$No' class='form-control input-sm text-left' $DISABLED></td>";
					echo "<td align='center'>
							<button type='button' class='btn btn-sm btn-primary plus2' title='Plus' data-id='".$No."' $DISABLED><i class='fa fa-plus'></i></button>
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
        var konv	= $('#konversi_'+idNomor).text()
		// let belumDiterima 	= getNum($(this).parent().parent().find('#belumDiterima_'+idNomor).text().split(',').join(''))
		let belumDiterima 	= getNum($('#belumDiterima_'+idNomor).text().split(',').join(''))
		// let qtyDiterima 	= getNum($(this).val().split(',').join(''))

		// if(qtyDiterima > belumDiterima){
		// 	$(this).val(belumDiterima)
		// }
		let inputQty
		let sisaDiterima
		let ID
		let Kolom
		let IdPack
		$('.qtyDiterima').each(function(){
			ID = $(this).data('no')
			Kolom = $(this).data('kolom')
			if(ID == idNomor){
				inputQty = getNum($(this).val().split(',').join(''))
				sisaDiterima = belumDiterima - inputQty

                IdPack = "#pack_"+ID+"_"+Kolom

				console.log(inputQty)
				console.log(belumDiterima)
				console.log(sisaDiterima)
				console.log(IdPack)

				if(sisaDiterima >= 0){
					$(this).val(number_format(inputQty,2))
					// console.log('kurang')
                    if(konv > 0){
                        $(IdPack).val(number_format(inputQty/konv,2))
                    }
				}
				else{
					// console.log('lebih')
					if(belumDiterima < 0){
						$(this).val(0)
						$(IdPack).val(0)
					}
					else{
						$(this).val(number_format(belumDiterima,2))
                        if(konv > 0){
                            $(IdPack).val(number_format(belumDiterima/konv,2))
                        }

					}
				}

				belumDiterima = sisaDiterima
			}
			
		})
	})
</script>