<?php
		if($pembeda == 'P'){
			$sql 	= "SELECT a.* FROM tran_material_po_header a WHERE a.no_po='".$no_po."'";
		}
		if($pembeda == 'N'){
			$sql 	= "SELECT a.*, a.no_non_po AS no_po FROM tran_material_non_po_header a WHERE a.no_non_po='".$no_po."'";
		}
		$datapo	= $this->db->query($sql)->row();
		// cek currency
		if($datapo->mata_uang=='IDR'){
		}else{
			if($no_ros=="") {
				echo "No ROS Harus diisi.
				<script>
					$(document).ready(function(){
						swal.close();
					});
				</script>				
				";
				die();
			}
		}
	?>
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
<div class="box-body"> 
	No PO : <?= $no_po;?><br />
	No ROS : <?= $no_ros;?><br />
	<br>
    <input type="hidden" name='no_po' id='no_po' value='<?= $no_po;?>'>
    <input type="hidden" name='gudang' id='gudang' value='<?= $gudang;?>'>
    <input type="hidden" name='pembeda' id='pembeda' value='<?= $pembeda;?>'>
    <input type="hidden" name='asal_incoming' id='asal_incoming' value='<?= $asal_incoming;?>'>
    <input type="hidden" name='adjustment' id='adjustment' value='IN'>
    <input type="hidden" name='no_ros' id='no_ros' value='<?= $no_ros;?>'>
	<?php
	$total_freight=0;
	$kurs=1;
	if($no_ros!=''){
		$dataros= $this->db->query("SELECT * from report_of_shipment WHERE id='".$no_ros."'")->row();
		$total_freight=$dataros->fc_cost;
		$kurs=$dataros->freight_curs;
	}
	?>
	<input type="hidden" name='total_freight' id='total_freight' value='<?= $total_freight;?>'>
	<?php if($pembeda == 'P'){?>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
                <th class="text-center" style='vertical-align:middle;'>Material ID</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>UoM Order</th>
				<th class="text-center" style='vertical-align:middle;' width='13%'>Qty belum dikirim</th>
                <th class="text-center" style='vertical-align:middle;' width='7%'>Qty Diterima</th> 
				<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total1 = 0;
			$Total2 = 0;
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
                $Total1 += $valx['qty_purchase'] - $valx['qty_in'];
				$Total2 += $valx['qty_purchase'];
                
                $totIn = $valx['qty_purchase'] - $valx['qty_in'];
				$idpo  = $valx['id'];
				$ros   = $this->db->query("SELECT bm FROM report_of_shipment_product WHERE idpo='$idpo'")->row();
				
				if($ros->bm > 0){
					$bm = $ros->bm;
				}
				else{
					$bm = 0;
				}
				
				echo "<tr>";
                    echo "<td align='center'>".$No."
                        <input type='hidden' name='addInMat[$No][no_po]' value='".$valx['no_po']."'>
                        <input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
                        <input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty_purchase']."'>
						<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
						<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
						<input type='hidden' name='addInMat[$No][harga]' value='".$valx['net_price']."'>
						<input type='hidden' name='addInMat[$No][bm]' value='".$bm."'>
                    </td>";
                    echo "<td>".$valx['idmaterial']."</td>";
                    echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['qty_purchase'],4)."</td>";
					echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan'])."</td>";
                    echo "<td align='right' class='belumDiterima'>".number_format($totIn,4)."</td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][qty_in]' data-no='$No' class='form-control input-sm text-right maskM qtyDiterima'></td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
                echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2, 4);?></b></td> 
				<td><b></b></td>
				<td align='right'><b><?= number_format($Total1, 4);?></b></td> 
                <td colspan='2'><b></b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
	<?php if($pembeda == 'N'){ ?>
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>No</th>
                <th class="text-center" style='vertical-align:middle;'>Material ID</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='20%'>Qty Order (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='17%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($result)){
            $Total1 = 0;
			$Total2 = 0;
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				$Total2 += $valx['qty_purchase'];
				
				echo "<tr>";
                    echo "<td align='center'>".$No."
                        <input type='hidden' name='addInMat[$No][no_po]' value='".$valx['no_po']."'>
                        <input type='hidden' name='addInMat[$No][id_material_req]' value='".$valx['id_material']."'>
                        <input type='hidden' name='addInMat[$No][id]' value='".$valx['id']."'>
                        <input type='hidden' name='addInMat[$No][qty_order]' value='".$valx['qty_purchase']."'>
						<input type='hidden' name='addInMat[$No][qty_rusak]' data-no='$No' class='form-control input-sm text-right maskM'>
						<input type='hidden' name='addInMat[$No][expired]' data-no='$No' class='form-control input-sm text-left tanggal' readonly placeholder='Expired Date'>
						<input type='hidden' name='addInMat[$No][qty_in]' value='".$valx['qty_purchase']."' data-no='$No' class='form-control input-sm text-right'>
                    </td>";
                    echo "<td>".$valx['idmaterial']."</td>";
                    echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='right'>".number_format($valx['qty_purchase'],4)."</td>";
                    echo "<td align='center'><input type='text' name='addInMat[$No][keterangan]' data-no='$No' class='form-control input-sm text-left'></td>";
                echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td colspan='2'><b>SUM TOTAL</b></td> 
				<td align='right'><b><?= number_format($Total2, 2);?></b></td> 
                <td><b></b></td>
			</tr>
			<?php 
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php } ?>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'saveINMaterial')).' ';
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
        swal.close();
		$('.maskM').autoNumeric('init', {mDec: '4', aPad: false});
		$('.tanggal').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		});

		$(document).on('keyup','.qtyDiterima',function(){
			let belumDiterima 	= getNum($(this).parent().parent().find('.belumDiterima').text().split(',').join(''))
			let qtyDiterima 	= getNum($(this).val().split(',').join(''))

			if(qtyDiterima > belumDiterima){
				$(this).val(belumDiterima)
			}
		})
    });
</script>