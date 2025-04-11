
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
    <input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td> 
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Request Date</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
            <tr>
				<td class="text-left" style='vertical-align:middle;'>Request By</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=ucwords(strtolower($createdBy));?></td>
			</tr>
		</thead>
	</table>
	<br>
	<h4>Request List</h4>
	<table border='1' width="60%" style='border-color: #cbcbcb;'>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Qty (Pack)</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Unit (Pack)</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total2 = 0;
            $No=0;
			foreach($ArrRequestMaterial AS $val => $valx){
                $No++;
				$qty 			= $valx['qty'];
				$konversi 		= $valx['konversi'];
				$qty_packing	= 0;
				if($qty > 0 AND $konversi > 0){
					$qty_packing	= $qty/$konversi;
				}
				$expired = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'-';
				echo "<tr>";
					echo "<td class='text-center'>".$No."</td>";
					echo "<td class='text-left'>".$valx['nm_material']."</td>";
					echo "<td class='text-center listReq' id='max_".$valx['id_material']."' data-idmat='".$valx['id_material']."'>".number_format($qty_packing,2)."</td>";
					echo "<td class='text-center'>".strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']))."</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<br>
	<h4>Lot list</h4>
	<table border='1' width="100%" style='border-color: #cbcbcb;'>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty (Unit)</th>
                <th class="text-center" style='vertical-align:middle;' width='5%'>Konversi</th>
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty (Pack)</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Expired</th> 
				<th class="text-center" style='vertical-align:middle;' width='12%'>Lot Desc</th> 
				<th class="text-center" style='vertical-align:middle;' width='10%'>Check By</th> 
				<th class="text-center" style='vertical-align:middle;' width='10%'>Check Date</th> 
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Input Qty (Pack)</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total2 = 0;
            $No=0;
			foreach($listLotMaterial AS $val => $valx){
                $No++;
				$qty 			= $valx['qty_oke'] - $valx['qty_out'] - $valx['qty_booking'];
				$konversi 		= $valx['konversi'];
				$qty_packing	= 0;
				if($qty > 0 AND $konversi > 0){
					$qty_packing	= $qty/$konversi;
				}
				$expired = (!empty($valx['expired_date']))?date('d-M-Y',strtotime($valx['expired_date'])):'-';
				echo "<tr>";
					echo "<td class='text-center'>".$No."</td>";
					echo "<td class='text-left paddingLeft'>".$valx['nm_material']."</td>";
					echo "<td class='text-right paddingRight'>".number_format($qty,2)." ".ucwords(strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_satuan'])))."</td>";
					echo "<td class='text-center'>".number_format($konversi,2)."</td>";
					echo "<td class='text-right paddingRight'>".number_format($qty_packing,2)." ".ucwords(strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing'])))."</td>";
					echo "<td class='text-right paddingRight'>".$expired."</td>";
					echo "<td class='text-left paddingLeft'>".$valx['keterangan']."</td>";
					echo "<td class='text-left paddingLeft'>".ucwords(strtolower(get_name('users','nm_lengkap','username',$valx['update_by'])))."</td>";
					echo "<td class='text-right paddingRight'>".date('d-M-Y H:i',strtotime($valx['update_date']))."</td>";
					echo "<td class='text-center'><input type='checkbox' name='id_lot[]' value='".$valx['id']."' class='lotList'></td>";
					echo "<td class='text-center'>
						<input type='hidden' name='id_material_".$valx['id']."' value='".$valx['id_material']."'>
						<input type='hidden' name='id_satuan_".$valx['id']."' value='".$valx['id_satuan']."'>
						<input type='hidden' name='id_packing_".$valx['id']."' value='".$valx['id_packing']."'>
						<input type='hidden' name='pack_".$valx['id']."' id='pack_".$valx['id']."' value='".$qty_packing."'>
						<input type='hidden' name='konversi_".$valx['id']."' id='konversi_".$valx['id']."' value='".$konversi."'>
						<input type='text' name='request_".$valx['id']."' id='request_".$valx['id']."' data-idmaterial='".$valx['id_material']."' class='autoNumeric2 text-center changeRequest placeholder='Input Qty'>
						</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 5px 0px 5px 0px;','content'=>'Create SPK','id'=>'create_spk'));
	?>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
	.paddingRight{
		padding-right:10px;
	}
	.paddingLeft{
		padding-left:10px;
	}
</style> 
<script>
	$(document).ready(function(){
		$('.chosen_select').chosen({width:'100%'});
		$('.autoNumeric2').autoNumeric('init', {mDec: '2', aPad: false});
        swal.close();
		let ArrSummary = []
		let idmat
		$('.listReq').each(function(){
			idmat 	= $(this).data('idmat')
			// console.log(idmat)
			ArrSummary[idmat] = []
		})

		$(document).on('click','.lotList',function(){
			calRequest(ArrSummary)
		})
		$(document).on('keyup','.changeRequest',function(){
			calRequest(ArrSummary)
		})

    });

	function calRequest(ArrSummary){
		let lotValue
		let stokPack
		let qtyReq
		let idMaterial
		
		$('.lotList').each(function(){
			if ($(this).is(':checked')) {
				if($('#request_'+lotValue).val() != ''){
					lotValue 	= $(this).val()
					idMaterial 	= $('#request_'+lotValue).data('idmaterial')
					stokPack 	= getNum($('#pack_'+lotValue).val())
					qtyReq 		= getNum($('#request_'+lotValue).val().split(',').join(''))
					
					if(qtyReq > stokPack){
						$('#request_'+lotValue).val(stokPack)
						qtyReq = stokPack
					}

					ArrSummary[idMaterial][lotValue] = []
					ArrSummary[idMaterial][lotValue].push(qtyReq)
				}
			}
		});

		let NewArray = []
		ArrSummary.map((row,idx)=>{
			// NewArray[idx] = 0

			// row.map((row2,idx2)=>{
			// 	NewArray[idx] += row2
			// })

			console.log(idx)
		})

		console.log(ArrSummary)
		// console.log(NewArray)
	}

</script>