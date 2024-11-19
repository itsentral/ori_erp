<style>
	.table-bordered tbody tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.table-bordered thead tr th,thead tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.table-bordered tfoot tr td {
		vertical-align:middle;
		border: 1px solid #ccc;
	}
	.text-up{
		text-transform: uppercase;
	}
</style>
<div class="box box-warning box-solid">
	<div class="box-header">
		<h5 class="box-title text-center"> <?= $title;?></h5>
	</div>
	
	<?php
	if(empty($rows_detail)){
		echo"<div class='box-body'>
			<div class='row'>";
				echo"<div class='col-sm-12'>";
					echo"<h4 class='text-red'><b>NO RECORD WAS FOUND.....</b></h4>";
				echo"</div>";
			echo"</div>
		</div>";
	}else{
	
	
	?>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>DETAIL TRANSAKSI</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Nomor Transaksi</b></label>
					<?php
						echo form_input(array('id'=>'no_trans','name'=>'no_trans','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->kode_trans));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Tanggal</b></label>
					<?php
						echo form_input(array('id'=>'date_trans','name'=>'date_trans','class'=>'form-control input-sm','readOnly'=>true),date('d-m-Y',strtotime($rows_header->tanggal)));
						
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Kategori</b></label>
					<?php
						echo form_input(array('id'=>'kategori_trans','name'=>'kategori_trans','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->category));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>No Reff</b></label>
					<?php
						echo form_input(array('id'=>'reff_trans','name'=>'reff_trans','class'=>'form-control input-sm','readOnly'=>true),$rows_header->no_ipp);
						
					?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Gudang Asal</b></label>
					<?php
						echo form_input(array('id'=>'gudang_asal','name'=>'gudang_asal','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->kd_gudang_dari));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Gudang Tujuan</b></label>
					<?php
						echo form_input(array('id'=>'gudang_tujuan','name'=>'gudang_tujuan','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->kd_gudang_ke));
						
					?>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Cek By</b></label>
					<?php
						echo form_input(array('id'=>'cek_by','name'=>'cek_by','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->checked_by));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Notes</b></label>
					<?php
						echo form_input(array('id'=>'notes','name'=>'notes','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->note));
						
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>DETAIL MATERIAL TRANSAKSI</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		
		
		<div class="table-responsive">
			<table id="example_tb" class="table table-striped table-bordered table-sm font_table" width="100%" >
				<thead>
					<tr class="bg-navy-active text-white">
						<th class="text-center">No.</th>
						<th class="text-center">Kode Item</th>
						<th class="text-center">Nama Item</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Qty</th>
						<th class="text-center">Total</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if($rows_detail){
							$no	= 0;
							$Total_Qty = $Total_Price  = 0;
							foreach($rows_detail as $row){
								$no++;
								$Code_Material 	= $row['id_material'];
								$Code_MateERP 	= $row['idmaterial'];
								$Name_Material	= $row['nm_material'];
								$Qty_Awal 		= $row['qty_stock_awal'];
								$Qty_In			= $row['qty_in'];
								$Qty_Out 		= $row['qty_out'];
								$Qty_Akhir 		= $row['qty_stock_akhir'];
								$Harga_HPP 		= $row['harga'];
								$Total_Awal		= $row['nilai_awal_rp'];
								$Total_HPP 		= $row['nilai_trans_rp'];
								$Total_Akhir	= $row['nilai_akhir_rp'];
								$Nomor_Jurnal	= $row['no_jurnal'];
								
								$Qty_Proses		= $Qty_In;
								if($Qty_Out > 0){
									$Qty_Proses		= $Qty_Out;
								}
								
								
								echo '<tr>
										<td class="text-center">'.$no.'</td>
										<td class="text-center">'.$Code_MateERP.'</td>
										<td class="text-left">'.$Name_Material.'</td>
										<td class="text-right">'.number_format($Harga_HPP,2).'</td>										
										<td class="text-center">'.$Qty_Proses.'</td>
										<td class="text-right">'.number_format($Total_HPP,2).'</td>
									</tr>';
								
								$Total_Qty 		+=$Qty_Proses;
								$Total_Price  	+=$Total_HPP;
							}
							echo '<tr class="text-bold bg-gray">
									<td class="text-center" colspan="4"> TOTAL</td>
									<td class="text-center">'.$Total_Qty.'</td>
									<td class="text-right">'.number_format($Total_Price,2).'</td>
								</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	}
	?>
	
</div>

