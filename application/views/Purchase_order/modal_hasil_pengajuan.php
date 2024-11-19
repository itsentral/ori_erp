
<div class="box-body"> 
	<input type='hidden' id='no_rfq' name='no_rfq' value='<?=$no_rfq;?>'>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center mid">SUPPLIER NAME</th>
				<th class="text-center mid">MATERIAL NAME</th>
				<th class="text-right mid" width='7%'>PRICE REF</th>
				<th class="text-right mid" width='7%'>NET PRICE</th>
				<th class="text-center mid" width='7%'>MOQ</th>
				<th class="text-center mid" width='7%'>LEAD TIME</th>
				<th class="text-right mid" width='7%'>QTY</th>
				<th class="text-right mid" width='10%'>TOTAL HARGA</th>
				<th class="text-center mid" width='15%'>CHECK</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$no=0;
            foreach($result AS $val => $valx){ $no++;
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
					echo "<td class='mid' >".strtoupper($valx['nm_supplier'])."</td>";
					echo "<td class='mid' >".strtoupper($nm_material)."</td>";
					echo "<td class='text-right mid'>".number_format($valx['price_ref'],2)."</td>";
					echo "<td class='text-right mid'>".number_format($valx['harga_idr'],2)." <b class='text-primary'>".strtoupper($valx['currency'])."</b></td>";
					echo "<td class='text-center mid'>".number_format($valx['moq'],2)."</td>";
					echo "<td class='text-center mid'>".number_format($valx['lead_time'],2)."</td>";
					echo "<td class='text-right mid'>".number_format($valx['qty'],2)."</td>";
					echo "<td class='text-right mid'>".number_format($valx['harga_idr'] * $valx['qty'],2)."</td>";
					echo "<td class='text-left mid'><b>".strtoupper($valx['status'])."</b></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
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