
<div class="box-body">
	<table id="my-grid" class="table" width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='2%'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>No Transaksi</th>
				<th class="text-center" style='vertical-align:middle;'>Detail Material</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Total Aktual</th>
				<th class="text-center" style='vertical-align:middle;' width='12%'>Aktual By</th>
                <th class="text-center" style='vertical-align:middle;' width='15%'>Aktual Date</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Option</th> 
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){
                $No++;
				$NM_LENGKAP = (!empty($GET_USERNAME[$valx['update_by']]['nm_lengkap']))?$GET_USERNAME[$valx['update_by']]['nm_lengkap']:$valx['update_by'];
                $UNIQ = $kode_trans.'-'.$valx['update_by'].'-'.$valx['update_date'];
                $KET_MATERIAL = '';
                if(!empty($DETAIL_MATERIAL[$UNIQ])){
                    foreach ($DETAIL_MATERIAL[$UNIQ] as $value) {
						$CHECK = substr($value['material'],0,4);
						if($CHECK == 'MTL-'){
                        	$KET_MATERIAL .= "<b><span class='text-blue'>".$value['layer']."</span> | <span class='text-green'>".get_name('raw_materials','nm_material','id_material',$value['material'])."</span> | <span class='text-red'>".number_format($value['qty'],4)."</span><b><br>";
						}
						else{
                        	$KET_MATERIAL .= "<b><span class='text-blue'>".$value['layer']."</span> | <span class='text-green'>".$value['material']."</span> | <span class='text-red'>".number_format($value['qty'],4)."</span><b><br>";
                    	}
					}
                }
                // echo '<pre>';
                // print_r($DETAIL_MATERIAL[$UNIQ]);
                // echo '</pre>';
				$NO_RQUEST = (!empty($valx['no_ipp']))?'<br>'.$valx['no_ipp']:'';
				$LINK_RQUEST = (!empty($valx['no_ipp']))?'/'.$valx['no_ipp']:'';
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td align='center'>".$kode_trans.$NO_RQUEST."</td>";
					echo "<td align='left'>".$KET_MATERIAL."</td>";
					echo "<td align='right'>".number_format($valx['qty_aktual'],4)." Kg</td>";
					echo "<td align='center'>".strtoupper($NM_LENGKAP)."</td>";
					echo "<td align='center'>".date('d-M-Y H:i:s',strtotime($valx['update_date']))."</td>";
					// echo "<td align='center'><button type='button' class='btn btn-sm btn-primary history_print' title='Print' data-kode_trans='".$value['kode_trans']."' data-update_by='".$value['update_by']."' data-update_date='".$value['update_date']."'>Print</button></td>";
					echo "<td align='center'><a href='".base_url('warehouse/print_request_check/'.$kode_trans.'/'.get_name('users','id_user','username',$valx['update_by']).'/'.date('YmdHis',strtotime($valx['update_date'])).$LINK_RQUEST)."' target='_blank' class='text-blue text-bold' title='Print'>Print</a></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>
<script>
	swal.close();
</script>