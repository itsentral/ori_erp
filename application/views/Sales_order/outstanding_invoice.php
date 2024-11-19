<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<form method="post" action="<?=base_url("sales_order/outstanding_invoice")?>">
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
					<th class="text-center">SO No.</th>
					<th class="text-center">PO No</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Currency</th>
					<th class="text-center">Type</th>
					<th class="text-center">Nomor Invoice</th>
					<th class="text-center">Progress</th>
					<th class="text-center">AMOUNT INVOICE (EXC.PPN)</th>
					<th class="text-center">Potongan DP (EXC.PPN)</th>
					<th class="text-center">Potongan Retensi</th>
					<th class="text-center">Sisa DP (EXC.PPN)</th>
					<th class="text-center">total nilai Retensi</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$numb=0;
			if(!empty($result)){
				$no_po='';
				$sisa_dp='0';$ttl_retensi='0';$aa='';
				foreach($result AS $val => $valx){
					$numb++;
					$base_cur=$valx->base_cur;
					$type=$valx->jenis_invoice;
					if($base_cur=='IDR'){
						$nilai_dpp=$valx->total_dpp_rp;
						$nilai_um=$valx->total_um_idr;
						$nilai_retensi=$valx->total_retensi2_idr;
					}else{
						$nilai_dpp=$valx->total_dpp_usd;
						$nilai_um=$valx->total_um;
						$nilai_retensi=$valx->total_retensi2;
					}
					if($no_po!=$valx->no_po){
						$sisa_dp=0;$ttl_retensi=0;
						if($type=='uang muka'){
							$sisa_dp=$nilai_dpp;
						}
						if($type=='progress'){
							$ttl_retensi=$nilai_retensi;
						}
					}else{
						if($type=='uang muka'){
							$sisa_dp=($sisa_dp+$nilai_dpp);
						}
						if($type=='progress'){
							$sisa_dp=($sisa_dp-$nilai_um);
							$ttl_retensi=($ttl_retensi+$nilai_retensi);
						}
					}
					echo '<tr><td>'.$numb.'</td><td style="word-wrap: break-word" width=100>'.$valx->so_number.'</td><td style="word-wrap: break-word" width=100>'.$valx->no_po.'</td><td>'.$valx->nm_customer.'</td><td>'.$base_cur.'</td><td>'.$type.'</td><td>'.$valx->no_invoice.'</td><td>'.$valx->persentase.'</td><td>'.number_format($nilai_dpp,2).'</td><td>'.number_format($nilai_um,2).'</td><td>'.number_format($nilai_retensi,2).'</td><td>'.number_format($sisa_dp,2).'</td><td>'.number_format($ttl_retensi,2).'</td></tr>';
					$no_po=$valx->no_po;
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
