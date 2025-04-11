<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<form method="post" action="<?=base_url("sales_order/piutang")?>">
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
					<th class="text-center">NO</th>
					<th class="text-center">CUSTOMER</th>
					<th class="text-center">PO NO</th>
					<th class="text-center">SO NO.</th>
					<th class="text-center">PROGRESS</th>
					<th class="text-center">TOP</th>
					<th class="text-center">INVOICE NO</th>
					<th class="text-center">INVOICE DATE</th>
					<th class="text-center">OVERDUE</th>
					<th class="text-center">CURRENCY</th>
					<th class="text-center">DPP</th>
					<th class="text-center">VAT</th>
					<th class="text-center">DPP</th>
					<th class="text-center">VAT</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$numb=0;
			if(!empty($result)){
				$nm_customer=''; $dpp_usd=0; $dpp_idr=0; $vat_usd=0; $vat_idr=0; $overdue=0;
				foreach($result AS $val => $valx){
					$numb++;
					$base_cur=$valx->base_cur;
					$type=$valx->jenis_invoice;
					$payment_term=$valx->payment_term;
					$tgl_invoice=$valx->tgl_invoice;
					$next_date=date ("Y-m-d", strtotime ($tgl_invoice ."+".$payment_term." days"));
					$now = time(); // or your date as well
					$your_date = strtotime($next_date);
					$datediff = $now - $your_date;
					$overdue=round($datediff / (60 * 60 * 24));
					if($overdue<0) $overdue=0;

					if($base_cur=='IDR'){
						$total_dpp_rp=$valx->total_dpp_rp;
						$total_ppn_idr=$valx->total_ppn_idr;
						$total_dpp=0;
						$total_ppn=0;
					}else{
						$total_dpp_rp=0;
						$total_ppn_idr=0;
						$total_dpp=$valx->total_dpp_usd;
						$total_ppn=$valx->total_ppn;
					}
					if($nm_customer!=$valx->nm_customer){
						if($nm_customer!="") {
							echo "<tr class='success'><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>Total</td><td>".number_format($dpp_usd,2)."</td><td>".number_format($vat_usd,2)."</td><td>".number_format($dpp_idr,2)."</td><td>".number_format($vat_idr,2)."</td></tr>";
						}
						$dpp_usd=0; $dpp_idr=0; $vat_usd=0; $vat_idr=0;
						$dpp_usd=($dpp_usd+$total_dpp);$dpp_idr=($dpp_idr+$total_dpp_rp);$vat_usd=($vat_usd+$total_ppn);$vat_idr=($vat_idr+$total_ppn_idr);
					}else{
						$dpp_usd=($dpp_usd+$total_dpp);$dpp_idr=($dpp_idr+$total_dpp_rp);$vat_usd=($vat_usd+$total_ppn);$vat_idr=($vat_idr+$total_ppn_idr);
					}
					echo '<tr><td>'.$numb.'</td><td>'.$valx->nm_customer.'</td><td style="word-wrap: break-word" width=100>'.$valx->no_po.'</td><td style="word-wrap: break-word" width=100>'.$valx->so_number.'</td><td>'.$type.'</td><td>'.$payment_term.'</td><td>'.$valx->no_invoice.'</td><td>'.date("d-m-Y",strtotime($tgl_invoice)).'</td><td>'. number_format($overdue).'</td><td>'.$base_cur.'</td><td>'.number_format($total_dpp,2).'</td><td>'.number_format($total_ppn,2).'</td><td>'.number_format($total_dpp_rp,2).'</td><td>'.number_format($total_ppn_idr,2).'</td></tr>';
					$nm_customer=$valx->nm_customer;
				}
				echo "<tr class='success'><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>Total</td><td>".number_format($dpp_usd,2)."</td><td>".number_format($vat_usd,2)."</td><td>".number_format($dpp_idr,2)."</td><td>".number_format($vat_idr,2)."</td></tr>";
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
