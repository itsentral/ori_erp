
<div class="box-body"> 
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" width='10%'>No RFQ</th> 
				<th class="text-center" width='30%'>Supplier Name</th>
				<th class="text-center">Material Name</th>
				<th class="text-center" width='10%'>Qty</th>
				<th class="text-center" width='5%'>Unit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$jumlah = count($result);
			$rows2 = $jumlah / $num_rows;
			$no  = 0;
			$no2 = 1;
			$dataArr = array();
			foreach($result AS $val => $valx){
				$dataArr[] = $no2 += $rows2;
			}
			
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
					if($no == '1'){
						echo "<td align='center' rowspan='".$jumlah."'>".$valx['no_rfq']."</td>";
					}
					if(in_array($no, $dataArr) || $no == '1'){
						echo "<td align='left' rowspan='".$rows2."'>".$valx['nm_supplier']."</td>";
					}
					echo "<td align='left'>".$nm_material."</td>";
					echo "<td align='right'>".number_format($valx['qty'])."</td>";
					echo "<td align='left'>".strtoupper($satuan)."</td>";
					
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>

<script>
	$(document).ready(function(){
		swal.close();
	});
</script>