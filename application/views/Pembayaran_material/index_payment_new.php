<?php
$this->load->view('include/side_menu');
?>
<div id='alert_edit' class="alert alert-success alert-dismissable" style="padding: 15px; display: none;"></div>
<div class="box">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<div class="box-body">
	<?php
		if($akses_menu['create']=='1'){ 
	?>      <div class="btn-group">
			  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">Pembayaran
				<span class="caret"></span>
				<span class="sr-only">Toggle Dropdown</span>
			  </button>
			  <ul class="dropdown-menu" role="menu">
				<li><a class="" href="<?=base_url("pembayaran_material/payment_new")?>">
				<i class="fa fa-plus">&nbsp;</i> Tambah Pembayaran Material </a></li>
				<li><a class="" href="<?=base_url("pembayaran_material/payment_new_nonmaterial")?>">
				<i class="fa fa-plus">&nbsp;</i> Tambah Pembayaran Non Material </a></li>
			  </ul>
			</div>
		<?php
		}
	?><br /><br />
	 <ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#material" aria-controls="material" role="tab" data-toggle="tab">Material</a></li>
		<li role="presentation"><a href="#non_material" aria-controls="non_material" role="tab" data-toggle="tab">Non Material</a></li>
		<li role="presentation"></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="material">
		  <div class="box-body">
			<table class="table table-bordered table-striped" id="mytabledata" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">No Payment</th>
						<th class="text-center">No Request</th>
						<th class="text-center">No PO</th>
						<th class="text-center">Tgl Bayar</th>
						<th class="text-center">Supplier</th>
						<th class="text-center">Nilai Bayar</th>
						<th class="text-center" width='110px'>Option</th>
					</tr>
				</thead>
				
			<tbody>
			<?php if(empty($results)){
			}else{
				$numb=0; foreach($results AS $record){ 
				$numb++;
                $payment = $record->no_payment;
				$req = $this->db->query("SELECT * FROM purchase_order_request_payment WHERE no_payment='$payment'")->row();
                if(!empty($req)){
				$noreq =$req->no_request;
				$nopo  =$req->no_po;
				}else{
				$noreq ='-';
				$nopo  ='-';
				}
				 
				?>
			<tr>
				<td><?= $record->no_payment ?></td>
				<td><?= $noreq ?></td>
				<td><?= $nopo ?></td>
				<td><?= $record->payment_date ?></td>
				<td><?= $record->nm_supplier?></td>
				<td><?= number_format($record->bank_nilai)?></td>
				<td><?php if($akses_menu['read']) : ?>
					<a href='<?=base_url().'pembayaran_material/view_payment_new/'.$record->id?>' class='btn btn-sm btn-default view' title='View Request Payment'><i class='fa fa-search'></i></a>
					<a href='<?=base_url().'pembayaran_material/print_payment_new/'.$record->id?>' class='btn btn-sm btn-info prints' title='Print Request Payment Material' target="_blank"><i class='fa fa-print'></i></a>
					<?php endif;?>
				</td>
			</tr>
			<?php } 
			}  ?>
			</tbody>
			</table>
		  </div>
		</div>
		<div role="tabpanel" class="tab-pane" id="non_material">
		  <div class="box-body">
			<table class="table table-bordered table-striped" id="mytabledatanonmaterial" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">No Payment</th>
						<th class="text-center">No Payment</th>
						<th class="text-center">No Request</th>
						<th class="text-center">No PO</th>
						<th class="text-center">Tgl Bayar</th>
						<th class="text-center">Supplier</th>
						<th class="text-center">Nilai Bayar</th>
						<th class="text-center" width='110px'>Option</th>
					</tr>
				</thead>
				
			<tbody>
			<?php if(empty($results2)){
			}else{
				$numb=0; foreach($results2 AS $record){ 
				$numb++;

				$payment2 = $record->no_payment;
				$req2 = $this->db->query("SELECT * FROM purchase_order_request_payment_nm WHERE no_payment='$payment2'")->row();
                if(!empty($req2)){
				$noreq2 =$req2->no_request;
				$nopo2  =$req2->no_po;
				}else{
				$noreq2 ='-';
				$nopo2  ='-';
				}
				?>
			<tr>
				<td><?= $record->no_payment ?></td>
				<td><?= $noreq2 ?></td>
				<td><?= $nopo2 ?></td>
				<td><?= $record->payment_date ?></td>
				<td><?= $record->nm_supplier?></td>
				<td><?= number_format($record->bank_nilai)?></td>
				<td><?php if($akses_menu['read']) : ?>
					<a href='<?=base_url().'pembayaran_material/view_payment_new_nonmaterial/'.$record->id?>' class='btn btn-sm btn-default view' title='View Request Payment'><i class='fa fa-search'></i></a>
					<a href='<?=base_url().'pembayaran_material/print_payment_new_nonmaterial/'.$record->id?>' class='btn btn-sm btn-info prints' title='Print Request Payment Non Material' target="_blank"><i class='fa fa-print'></i></a>
					<?php endif;?>
				</td>
			</tr>
			<?php } 
			}  ?>
			</tbody>
			</table>
		  </div>
		</div>
	</div>
</div>
<div id="form-data">
</div>
<?php $this->load->view('include/footer'); ?>
<!-- page script -->
<script type="text/javascript">
$(function() {
	$("#mytabledata").DataTable( {"order": [[ 0, "desc" ]] } );
	$("#form-data").hide();
});
$(function() {
	$("#mytabledatanonmaterial").DataTable( {"order": [[ 0, "desc" ]] } );
});
</script>
