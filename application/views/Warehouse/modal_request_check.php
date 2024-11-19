<div class="alert alert-warning alert-dismissible">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<h4><i class="icon fa fa-info"></i> Info!</h4>
Konfirmasi tidak boleh melebihi stock yang tersedia.
</div>
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
    <input type="hidden" name='kode_trans' id='kode_trans' value='<?= $kode_trans;?>'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td> 
			</tr>
			<!-- <tr>
				<td class="text-left" style='vertical-align:middle;'>No IPP</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_ipp;?></td>
			</tr> -->
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
				<th class="text-center" style='vertical-align:middle;' width='8%'>Stock</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='8%'>Req Check (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Expired Date</th> 
				<th class="text-center" style='vertical-align:middle;' width='8%'>Stock Expired</th> 
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
				
                
                $sisa = $valx['qty_oke'];

				// <input type='hidden' name='detail[".$No."][id_material]' value='".$valx['id_material_req']."'>

				$listMat = "";
				$list_material = $this->db->get_where('raw_materials', array('id_category'=>$valx['id_category2'],'delete'=>'N'))->result_array();
				foreach($list_material AS $valMat => $valxMat){
					$sel = ($valxMat['id_material'] == $valx['id_material_req'])?'selected':'';
					$listMat .= "<option value='".$valxMat['id_material']."' ".$sel.">".$valxMat['nm_material']."</option>";
				}
				
				echo "<tr class='baris_".$No."'>";
                    echo "<td rowspan='1' class='id_".$No."' align='center'>".$No."
							<input type='hidden' name='detail[".$No."][id]' value='".$valx['id']."'>
							<input type='hidden' name='detail[".$No."][use_stock]' value='".$valx['qty_order']."'>
							<input type='hidden' name='detail[".$No."][request_awal]' value='".$sisa."'>
							
						</td>";
                    echo "<td rowspan='1' class='id_".$No."' ><select name='detail[".$No."][id_material]' class='form-control chosen_select changeMaterial'>".$listMat."</select></td>";
					echo "<td rowspan='1' class='id_".$No." stockval' id='stock_".$No."' align='right'>".number_format($valx['stock'],4)."</td>";
                    echo "<td rowspan='1' class='id_".$No."' align='right'>".number_format($sisa,4)."</td>";

					$REQUEST = $sisa;
					if($valx['stock'] < $sisa AND $valx['stock'] > 0){
						$REQUEST = $valx['stock'];
					}

					if($valx['stock'] < 0){
						$REQUEST = 0;
					}

                    echo "<td align='center'>
							<input type='text' name='detail[".$No."][detail][1][qty_oke]' id='cstk_".$No."_1' data-no='$No' class='form-control input-sm text-right maskM checkRequest' value='".$REQUEST."'>
						</td>";
					echo "<td align='left'>
							<select name='detail[".$No."][detail][1][expired]' class='form-control input-sm chosen_select list_expired'>
								".get_list_expired($valx['id_material'], $gudang_before)."
							</select>
						</td>";
					echo "<td class='text-right stockExp'></td>";
                    echo "<td align='center'><input type='text' name='detail[".$No."][detail][1][check_keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
					echo "<td align='center'>
							<button type='button' class='btn btn-sm btn-primary plus' title='Plus' data-id='".$No."' data-material='".$valx['id_material']."' data-gudang='".$gudang_before."'><i class='fa fa-plus'></i></button>
						</td>";
				echo "</tr>";

				$Total2 += $REQUEST;
			}
			?>
			<tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2, 2);?></b></td> 
                <td colspan='5'><b></b></td>
			</tr>
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
        swal.close();
    });

</script>