
<div class="box-body"> 
	<input type='hidden' id='no_rfq' name='no_rfq' value='<?=$no_rfq;?>'>
	<!-- <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center mid">SUPPLIER NAME</th>
				<th class="text-center mid">MATERIAL NAME</th>
				<th class="text-right mid" width='7%'>PRICE REF</th>
				<th class="text-right mid" width='7%'>NET PRICE</th>
				<th class="text-center mid" width='7%'>MOQ</th>
				<th class="text-center mid" width='7%'>LEAD TIME</th>
				<th class="text-right mid" width='10%'>QTY</th>
				<th class="text-right mid" width='10%'>TOTAL</th>
				<th class="text-center mid" width='12%'>KOMITE</th>
				<th class="text-center mid" width='5%'>CHECK</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no=0;
            // foreach($result AS $val => $valx){ $no++;
			// 	$nm_material = $valx['nm_material'];
			// 	$satuan = 'KG';
			// 	if($valx['category'] == 'acc'){
			// 		$nm_material = get_name_acc($valx['id_material']);
			// 		$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$valx['idmaterial']);
			// 		if(empty($valx['idmaterial'])){
			// 			$nm_material = $valx['nm_material'];
			// 		}
			// 	}
            //     echo "<tr>";
			// 		echo "<td class='mid' >".strtoupper($valx['nm_supplier'])."</td>";
			// 		echo "<td class='mid' >".strtoupper($nm_material)."</td>";
			// 		echo "<td class='text-right mid'>".number_format($valx['price_ref'],2)."</td>";
			// 		echo "<td class='text-right mid'>".number_format($valx['harga_idr'],2)." <b class='text-primary'>".strtoupper($valx['currency'])."</b></td>";
			// 		echo "<td class='text-center mid'>".number_format($valx['moq'],2)."</td>";
			// 		echo "<td class='text-center mid'>".number_format($valx['lead_time'],2)."</td>";
			// 		echo "<td class='text-right mid'>".number_format($valx['qty'],2)."</td>";
			// 		echo "<td class='text-right mid'>".number_format($valx['harga_idr'] * $valx['qty'],2)."</td>";
			// 		echo "<td class='mid' >".strtoupper($valx['status'])."</td>";
			// 		echo "<td class='text-center mid'><input type='checkbox' name='check[".$valx['id']."]' class='chk_personal' value='".$valx['id']."'></td>";
			// 	echo "</tr>";
			// }
			?>
		</tbody> 
		</table>-->
		<?php
		$ColsPan = COUNT($resultSup) * 5;
		?>
		<div class="table-responsive">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center mid" rowspan='3' width='2%'>#</th>
					<th class="text-center mid" rowspan='3'>Item Name</th>
					<th class="text-center mid" rowspan='3'>Price Ref.</th>
					<th class="text-center mid" rowspan='3' width='3%'>Qty</th>
					<th class="text-center mid" colspan='<?=$ColsPan;?>'>COMPARISON</th>
				</tr>
				<tr class='bg-darkgoldenrod'>
					<?php
						foreach($resultSup AS $val => $valx){
							echo "<th class='text-center mid' colspan='5'>".$valx['nm_supplier']."</th>";
						}
					?>
				</tr>
				<tr class='bg-default'>
					<?php
						foreach($resultSup AS $val => $valx){
							echo "<th class='text-center mid' width='100px;'>Net Price</th>";
							echo "<th class='text-center mid' width='60px;'>MOQ</th>";
							echo "<th class='text-center mid' width='60px;'>L.Time</th>";
							echo "<th class='text-center mid' width='100px;'>Toal Price</th>";
							echo "<th class='text-center mid' width='50px;'>App?</th>";
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$no=0;
				foreach($resultNew AS $val => $valx){ $no++;
					$nm_material = $valx['nm_material'];
					$satuan = 'KG';
					if($valx['category'] == 'acc'){
						$nm_material = get_name_acc($valx['id_material']);
						$satuan = get_name('raw_pieces','kode_satuan','id_satuan',$valx['idmaterial']);
						if(empty($valx['idmaterial'])){
							$nm_material = $valx['nm_material'];
						}
					}
					echo "<tr>";
						echo "<td class='text-center mid'>".$no."</td>";
						echo "<td class='mid' >".strtoupper($nm_material)."</td>";
						echo "<td class='text-center mid'>".number_format($valx['price_ref'],2)."</td>";
						echo "<td class='text-center mid'>".number_format($valx['qty'],2)."</td>";
						foreach($resultSup AS $val2 => $valx2){
							$UNIQ = $valx['id_material'].'-'.$valx2['hub_rfq'];
							$moq 		= $ArraySerach[$UNIQ]['moq'];
							$lead_time 	= $ArraySerach[$UNIQ]['lead_time'];
							$harga_idr 	= $ArraySerach[$UNIQ]['harga_idr'];
							$total_harga= $ArraySerach[$UNIQ]['total_harga'];
							$id			= $ArraySerach[$UNIQ]['id'];

							echo "<td class='text-right mid'>".number_format($harga_idr,2)."</td>";
							echo "<td class='text-center mid'>".number_format($moq)."</td>";
							echo "<td class='text-center mid'>".number_format($lead_time)."</td>";
							echo "<td class='text-right mid'>".number_format($total_harga,2)."</td>";
							echo "<td class='text-center mid'><input type='checkbox' name='check[".$id."]' class='chk_personal' value='".$id."'></td>";
						}
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
		</div>
	
	<?php
		echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Create Pengajuan','content'=>'Save','id'=>'saveAju')).' ';
	?>
</div>
<style>
	.mid{
		vertical-align: middle !important;
	}
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen-select').chosen();
	});
	
	
</script>