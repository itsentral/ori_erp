<div class="alert alert-warning alert-dismissible">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<h4><i class="icon fa fa-info"></i> Info!</h4>
Konfirmasi tidak boleh melebihi stock yang tersedia.
</div>
<?php
$tanda_req 	= ($category_req == 'request produksi')?'Kg':'Pack';
$tandaPack 	= ($category_req == 'request produksi')?'hidden':'text';
$tandaKg 	= ($category_req == 'request produksi')?'text':'hidden';
?>
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
    <input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<input type="hidden" name='category_req' id='category_req' value='<?= $category_req;?>'>
	<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td> 
				<td rowspan='2' width='20%'>
					<span class='text-bold'>Upload Enginnering Change</span>
					<input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload Enginnering Change'>
				</td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Request</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Konversi</th>
				<th class="text-center" style='vertical-align:middle;' width='8%'>Stock<br>(<?=$tanda_req;?>)</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Request<br>(<?=$tanda_req;?>)</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Confirm<br>(<?=$tanda_req;?>)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Expired Date</th> 
				<!-- <th class="text-center" style='vertical-align:middle;' width='8%'>Stock Expired</th>  -->
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total2 = 0;
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				
                $konversi = $valx['konversi'];
				// <input type='hidden' name='detail[".$No."][id_material]' value='".$valx['id_material_req']."'>
				$listMat = "";
				$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category2'],'delete'=>'N'))->result_array();
				foreach($list_material AS $valMat => $valxMat){
					$sel = ($valxMat['id_material'] == $valx['id_material_req'])?'selected':'';
					$listMat .= "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
				}

				$STOCK_PACK = 0;
				$sisa = $valx['qty_oke'];
				if($tanda_req == 'Pack'){
					if($valx['stock'] > 0 AND $konversi > 0){
						$STOCK_PACK = $valx['stock']/$konversi;
					}
					if($valx['qty_oke'] > 0 AND $konversi > 0){
						$sisa = $valx['qty_oke']/$konversi;
					}
					$satuan_pack = get_name('raw_pieces','kode_satuan','id_satuan',$valx['id_packing']);
				}
				if($tanda_req == 'Kg'){
					$STOCK_PACK = $valx['stock'];
					$sisa = $valx['qty_oke'];
					$satuan_pack = '';
				}

				
				echo "<tr class='baris_".$No."'>";
                    echo "<td rowspan='1' class='id_".$No."' align='center'>".$No."
							<input type='hidden' name='detail[".$No."][id]' value='".$valx['id']."'>
							<input type='hidden' name='detail[".$No."][konversi]' id='konversi_".$No."' value='".$konversi."'>
							<input type='hidden' name='detail[".$No."][konversi]' id='stock_".$No."' value='".$STOCK_PACK."'>
							<input type='hidden' name='detail[".$No."][use_stock]' value='".$valx['qty_order']."'>
							<input type='hidden' name='detail[".$No."][request_awal]' value='".$sisa."'>
							<input type='hidden' name='detail[".$No."][id_material]' value='".$valx['id_material_req']."'>
							
						</td>";
					if($tanda_req == 'Kg'){
						echo "<td rowspan='1' class='id_".$No."' ><select name='detail[".$No."][id_material]' class='form-control chosen_select changeMaterial'>".$listMat."</select></td>";
						// echo "<td rowspan='1' class='id_".$No."' >".$valx['nm_material_stock']."</td>";
					}
					if($tanda_req == 'Pack'){
						echo "<td rowspan='1' class='id_".$No."' align='left'>".$valx['nm_material_stock']."</td>";
					}
                    echo "<td rowspan='1' class='id_".$No."' align='center'>".number_format($konversi,2)."</td>";
					echo "<td rowspan='1' class='id_".$No." stockval' align='right'>".number_format($STOCK_PACK,2)." ".$satuan_pack."</td>";
                    echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($sisa,2)." ".$satuan_pack."</td>";

					$REQUEST = $sisa;
					if($STOCK_PACK  < $sisa AND $STOCK_PACK  > 0){
						$REQUEST = $STOCK_PACK ;
					}

					if($STOCK_PACK  < 0){
						$REQUEST = 0;
					}

                    echo "<td align='center'>
							<input type='".$tandaPack."' name='detail[".$No."][detail][1][qty_pack]' id='cstkpack_".$No."_1' data-no='$No' data-no2='1' class='form-control input-sm text-center autoNumeric4a checkRequest' value='".$REQUEST."'>
							<input type='".$tandaKg."' name='detail[".$No."][detail][1][qty_oke]' id='cstk_".$No."_1' data-no='$No' data-no2='1' class='form-control input-sm text-right autoNumeric4 checkRequest' value='".$REQUEST*$konversi."'>
						</td>";
					echo "<td align='center'>
							<select name='detail[".$No."][detail][1][expired]' class='form-control input-sm chosen_select list_expired'>
								".get_list_expired($valx['id_material'], $gudang_before)."
							</select>
						</td>";
					// echo "<td class='text-right stockExp'></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][check_keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
					echo "<td align='center'>
							<button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$No."' data-material='".$valx['id_material']."' data-gudang='".$gudang_before."'><i class='fa fa-plus'></i></button>
						</td>";
				echo "</tr>";

				$Total2 += $REQUEST;
			}
			?>
			<!-- <tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2, 2);?></b></td> 
                <td colspan='5'><b></b></td>
			</tr> -->
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='9'><b>Material tidak berada di gudang yang dipilih, silahkan coba gudang yang lain.</b></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
		if(!empty($result)){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'check_material'));
		}
	?>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
</style> 
<script>
	$(document).ready(function(){
		$('.chosen_select').chosen({width:'100%'});
		$(".autoNumeric4").autoNumeric('init', {mDec: '4', aPad: false});
		$(".autoNumeric4a").autoNumeric('init', {mDec: '4', aPad: false});
        swal.close();
    });

</script>