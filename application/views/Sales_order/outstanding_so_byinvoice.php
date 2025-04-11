<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<form method="post" action="<?=base_url("sales_order/outstanding_salesorder")?>">
			<input type="text" name="search" value="<?=$search?>" />
			<input type="submit" value="Search" class="btn btn-sm btn-default" />
			<button class="btn btn-sm btn-default" onclick="gotoexcel();"><i class="fa fa-file-excel-o"></i> Excel</button>
		</form>
	</div>
	<!-- /.box-header -->
	<div class="box-body table-responsive">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">SALES</th>
					<th class="text-center">YEAR</th>
					<th class="text-center">MONTH</th>
					<th class="text-center">TGL SO</th>
					<th class="text-center">CUSTOMER</th>
					<th class="text-center">PIC SALES</th>
					<th class="text-center">SO</th>
					<th class="text-center">PO</th>
					<th class="text-center">PROJECT</th>
					<th class="text-center">TOTAL VALUE PO (IDR)</th>
					<th class="text-center">TOTAL VALUE PO (US$)</th>
					<th class="text-center">TOTAL VALUE INVOICE</th>
					<th class="text-center">OUTSTANDING</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$numb=0;
			if(!empty($result)){
				$total_all_deals=0;
				foreach($result AS $val => $valx){
					$total_inv=0; $numb++; $nomor_po=$valx->nomor_po; $total_deal=0;
					$resultdtl = $this->db->query("select * from billing_so_gabung where no_po='".$nomor_po."'")->result();
					foreach($resultdtl AS $valdtl => $valxdtl){
						$total_deal=0;
						echo '<tr><td></td>
						<td>'.date( "Y",strtotime($valxdtl->tgl_so)).'</td>
						<td>'.date( "F",strtotime($valxdtl->tgl_so)).'</td>
						<td>'.date( "d-m-Y",strtotime($valxdtl->tgl_so)).'</td>
						<td>'.$valx->nm_customer.'</td><td></td>
						<td>'.($valxdtl->no_so==''?get_nomor_so($valxdtl->no_ipp): $valxdtl->no_so).'</td>
						<td>'.$valx->nomor_po.'</td><td>'.$valxdtl->project.'</td>';
						$base_cur=$valxdtl->base_cur;
						if($base_cur=='IDR'){
							$total_deal=$valxdtl->total_deal_idr;
							echo '<td>'.number_format($total_deal,2).'</td><td></td>';
						}else{
							$total_deal=$valxdtl->total_deal_usd;
							echo '<td></td><td>'.number_format($total_deal,2).'</td>';
						}
						$total_all_deals=($total_all_deals+$total_deal);
						echo '<td></td><td></td></tr>';
					}
					if($base_cur=='IDR'){
						$total_inv=($valx->total_invoice_idr+$valx->total_um_idr);
					}else{
						$total_inv=($valx->total_invoice+$valx->total_um);
					}
					echo '<tr class="success"><td></td><td></td><td></td><td></td>
					<td>'.$valx->nm_customer.'</td><td></td><td></td>
					<td>'.$valx->nomor_po.'</td><td></td><td></td><td></td><td>'.number_format($total_inv,2).'</td><td>'.number_format($total_all_deals-$total_inv,2).'</td</tr>';
					$total_all_deals=0;
					$nm_customer=$valx->nm_customer;
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
<?php $this->load->view('include/footer'); ?>
<script src="<?=base_url()?>assets/js/jquery.table2excel.min.js"></script>
<script>
	$(document).ready(function(){
	//	$('#my-grid').DataTable();
	});
	function gotoexcel(){
		$("#my-grid").table2excel({
		name: "<?php echo $title;?>",
		filename: "<?php echo $title;?>.xls", // do include extension
		preserveColors: true // set to true if you want background colors and font colors preserved
		});
	}

</script>
