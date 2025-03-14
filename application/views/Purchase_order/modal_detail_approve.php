
<div class="box-body"> 
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" width='7%'>No RFQ</th>
				<th class="text-center" width='13%'>Supplier Name</th>
				<th class="text-center">Pengiriman</th>
				<th class="text-center" width='15%'>Material Name</th>
				<th class="text-right" width='7%'>Price Ref <!--($)--></th>
				<th class="text-right" width='7%'>Price From Supplier</th>
				<th class="text-right" width='7%'>Harga <!--(IDR)--></th>
				<th class="text-right" width='7%'>Qty PR</th>
				<th class="text-center" width='4%'>MOQ (Kg)</th>
				<th class="text-center" width='4%'>Lead Time (Days)</th>
				<th class="text-center" width='6%'>Tgl Dibutuhkan</th>
				<th class="text-center" width='8%'>Total Harga</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$jumlah = count($result);
			$no  = 0;
			
            foreach($result AS $val => $valx){ $no++;
				$sql2x 		= "	SELECT 
							a.* 
						FROM 
							tran_material_rfq_detail a 
							LEFT JOIN tran_material_rfq_header b ON a.no_rfq=b.no_rfq
						WHERE 
							a.no_rfq='".$valx['no_rfq']."' 
							AND a.id_supplier = '".$valx['id_supplier']."'
							AND a.hub_rfq=b.hub_rfq
							AND (b.sts_ajuan = 'APV' OR b.sts_ajuan = 'CLS')
							AND (a.status='SETUJU' OR a.status='CLOSE')
							AND a.deleted='N'
						";
							
				$num_rowsx		= $this->db->query($sql2x)->num_rows();
				
				$rows2 = $num_rowsx;
				
				$no2 = 1;
				$dataArr = array();
				foreach($result AS $valx2 => $valxx){
					$dataArr[] = $no2 += $rows2;
				}
				
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
					if(in_array($no, $dataArr) || $no == '1'){
						echo "<td align='left' rowspan='".$rows2."'><b>".strtoupper($valx['lokasi'])."</b><br>".strtoupper($valx['alamat_supplier'])."<br /><b>CURRENCY : ".$valx['currency']."</b></td>";
					}
					echo "<td align='left'>".strtoupper($nm_material)."</td>";
					echo "<td align='right'>".number_format($valx['price_ref'],2)."</td>";
					echo "<td align='right'>".number_format($valx['price_ref_sup'],2)." <b class='text-primary'>".strtoupper($valx['currency'])."</b></td>";
					echo "<td align='right'>".number_format($valx['harga_idr'],2)."</td>";
					echo "<td align='right'>".number_format($valx['qty'])." ".strtolower($satuan)."</td>";
					echo "<td align='center'>".number_format($valx['moq'])."</td>";
					echo "<td align='center'>".number_format($valx['lead_time'])."</td>";
					$tgl_dibutuhkan = (!empty($valx['tgl_dibutuhkan']) AND $valx['tgl_dibutuhkan'] != '0000-00-00')?date('d-M-Y', strtotime($valx['tgl_dibutuhkan'])):'-';
					echo "<td align='center'>".$tgl_dibutuhkan."</td>";
					echo "<td align='right'>".number_format($valx['total_harga'])."</td>";
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