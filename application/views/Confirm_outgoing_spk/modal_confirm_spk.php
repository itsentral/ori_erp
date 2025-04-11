
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
	<h4>List Outgoing Confirm</h4>
    <div class="form-group row">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon" style="padding: 4px 10px 0px 10px;">
                    <i class="fa fa-qrcode fa-2x"></i>
                </span>
                <input type="text" name="qr_code" id="qr_code" class="form-control input-lg" placeholder="QR Code">
            </div>
        </div>
        <div class="col-md-8">
            <span id="help-text" class="text-success text-bold text-lg"></span>
            <div class="notif">
            </div>
        </div>
    </div>
	<table border='1' width="100%" style='border-color: #cbcbcb;'>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty Lot<br>(Unit)</th>
                <th class="text-center" style='vertical-align:middle;' width='5%'>Konversi</th>
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty Lot<br>(Pack)</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Expired</th> 
				<th class="text-center" style='vertical-align:middle;' width='12%'>Lot Desc</th> 
				<th class="text-center" style='vertical-align:middle;' width='7%'>Request Qty<br>(Pack)</th> 
				<th class="text-center" style='vertical-align:middle;' width='9%'>Request By</th> 
				<th class="text-center" style='vertical-align:middle;' width='9%'>Request Date</th> 
				<!-- <th class="text-center" style='vertical-align:middle;' width='3%'>#</th> -->
				<th class="text-center" style='vertical-align:middle;' width='8%'>Confirm Qty<br>(Pack)</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total2 = 0;
            $No=0;
			foreach($listLotMaterial AS $val => $valx){
                $No++;
				$qty 			= $valx['qty_oke'] - $valx['qty_out'] - $valx['qty_booking'] + $valx['qty_unit'];
				$konversi 		= $valx['konversi'];
				$qty_packing	= 0;
				if($qty > 0 AND $konversi > 0){
					$qty_packing	= ($qty/$konversi) + $valx['qty_pack'];
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
                    echo "<td class='text-right paddingRight text-bold' style='font-size:18px;'>".number_format($valx['qty_pack'],2)." ".ucwords(strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing'])))."</td>";
					echo "<td class='text-left paddingLeft'>".ucwords(strtolower(get_name('users','nm_lengkap','username',$valx['update_by'])))."</td>";
					echo "<td class='text-right paddingRight'>".date('d-M-Y H:i',strtotime($valx['update_date']))."</td>";
					// echo "<td class='text-center'><input type='checkbox' name='id_lot[]' value='".$valx['id']."' class='lotList'></td>";
                    if(empty($tanda)){
					echo "<td class='text-center'>
						<input type='hidden' name='detail[$val][id]' value='".$valx['id_spk']."'>
						<input type='hidden' name='detail[$val][id_lot]' value='".$valx['id']."'>
						<input type='hidden' name='detail[$val][id_material]' value='".$valx['id_material']."'>
						<input type='hidden' name='detail[$val][qty_pax_max]' id='pack_".$valx['id']."' value='".$valx['qty_pack']."'>
						<input type='hidden' name='detail[$val][konversi]' id='konversi_".$valx['id']."' value='".$konversi."'>
						<input type='text' name='detail[$val][qty_out]' data-id='".$valx['id']."' id='request_".$valx['id']."' data-idmaterial='".$valx['id_material']."' style='background:floralwhite; border-ccolor:floralwhite;border:none;' placeholder='Otomatis' class='autoNumeric2 text-center changeRequest placeholder='Input Qty' readonly>
						</td>";
                    }
                    else{
                        echo "<td class='text-right paddingRight'>".number_format($valx['qty_confirm'],2)." ".ucwords(strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing'])))."</td>";
                    }
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
        if(empty($tanda)){
		    echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 5px 0px 5px 0px;','content'=>'Confirm SPK','id'=>'create_spk'));
        }
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
        setTimeout(() => {
			$("#qr_code").focus();
			$('#help-text').html('<i>Ready to Scan QR...!!</i>')
		}, 500)

        $(document).on('focus', '#qr_code', function() {
			$('#help-text').html('<i>Ready to Scan QR...!!</i>')
		})
		$(document).on('blur', '#qr_code', function() {
			$('#help-text').html('')
		})

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
		
		$('.changeRequest').each(function(){
            if($('#request_'+lotValue).val() != ''){
                lotValue 	= $(this).data('id')
                stokPack 	= getNum($('#pack_'+lotValue).val())
                qtyReq 		= getNum($(this).val().split(',').join(''))
                
                if(qtyReq > stokPack){
                    $(this).val(stokPack)
                    qtyReq = stokPack
                }
            }
		});
	}

</script>