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
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><i class="fa fa-info-circle"></i> <?php echo $title;?></h3>
		<div class="box-tool pull-right">
		
		</div>
	</div>
	<?php
	if(empty($rows_header)){
		echo'
		<div class="box-body">
			<div class="row">
				<div class="col-12 col-xs-12">
					<div class="callout callout-warning">
						<h4><i class="icon fa fa-warning"></i> Alert!</h4>

						<p>NO RECORDS WAS FOUND.</p>
					  </div>
				</div>
			</div>
		</div>
		';
	}else{
	
	?>
	<div class="box-body">
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>DETAIL STOCK TRAS</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>ID Material</b></label>
					<?php
						echo form_input(array('id'=>'code_material','name'=>'code_material','class'=>'form-control input-sm','readOnly'=>true),$rows_header->idmaterial);
						echo form_input(array('id'=>'id_material','name'=>'id_material','type'=>'hidden'),$rows_header->id_material);
						echo form_input(array('id'=>'id_gudang','name'=>'id_gudang','type'=>'hidden'),$rows_header->id_gudang);
						echo form_input(array('id'=>'tgl_stock','name'=>'tgl_stock','type'=>'hidden'),$tgl_cari);
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Nama Material</b></label>
					<?php
						echo form_input(array('id'=>'name_material','name'=>'name_material','class'=>'form-control input-sm','readOnly'=>true),$rows_header->nm_material);
						
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Category</b></label>
					<?php
						echo form_input(array('id'=>'category','name'=>'category','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->nm_category));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Gudang</b></label>
					<?php
						echo form_input(array('id'=>'gudang','name'=>'gudang','class'=>'form-control input-sm','readOnly'=>true),strtoupper($rows_header->nm_gudang));
						
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Qty</b></label>
					<?php
						echo form_input(array('id'=>'qty','name'=>'qty','class'=>'form-control input-sm','readOnly'=>true),number_format($rows_header->qty_stock_akhir,4));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Harga</b></label>
					<?php
						$Nilai_HPP			= $rows_header->harga;
						$SaldoAkhir_HPP		= (!empty($rows_header->nilai_akhir_rp) && floatval($rows_header->nilai_akhir_rp) !== 0)?$rows_header->nilai_akhir_rp:0;
						$Qty_Akhir_HPP		= (!empty($rows_header->qty_stock_akhir) && floatval($rows_header->qty_stock_akhir) !== 0)?$rows_header->qty_stock_akhir:0;
						
						
						if((floatval($Qty_Akhir_HPP) > 0 || floatval($Qty_Akhir_HPP) < 0) && (floatval($SaldoAkhir_HPP) > 0 || floatval($SaldoAkhir_HPP) < 0)){
							$Nilai_HPP		= $SaldoAkhir_HPP / $Qty_Akhir_HPP;
						}
						
						
						
						echo form_input(array('id'=>'harga_hpp','name'=>'harga_hpp','class'=>'form-control input-sm','readOnly'=>true),number_format($Nilai_HPP,2));
						
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Total</b></label>
					<?php
						echo form_input(array('id'=>'total_hpp','name'=>'total_hpp','class'=>'form-control input-sm','readOnly'=>true),number_format($SaldoAkhir_HPP,2));
						
					?>
				</div>
			</div>
			<div class="col-sm-6 col-xs-12">
				<div class="form-group">
					<label class="control-label"><b>Tanggal Stock</b></label>
					<?php
						echo form_input(array('id'=>'tgl_cari','name'=>'tgl_cari','class'=>'form-control input-sm','readOnly'=>true),date('d-m-Y',strtotime($tgl_cari)));
						
					?>
				</div>
			</div>
		</div>
		
		
		<div class="row">
			<div class="col-sm-12 col-xs-12 text-center sub-heading" style="color:white;">
				<h5>HISTORI IN OUT MATERIAL</h5>
			</div>
			<div class="col-sm-12 col-xs-12">&nbsp;</div>
		</div>
		
		<div class="row">
			<div class="col-sm-12">
				<table id="example2" class="table table-bordered table-striped table-sm mb-4">
					<thead>
						<tr class="bg-gray text-white">
							<th class="text-center">No</th>
							<th class="text-center">Tanggal</th>
							<th class="text-center">No Reff</th>
							<th class="text-center">No Jurnal</th>
							<th class="text-center">Saldo Awal</th>
							<th class="text-center">Qty In</th>
							<th class="text-center">Qty Out</th>
							<th class="text-center">Saldo Akhir</th>
						</tr>
					</thead>
					<tbody>
						<?php
						
						if($rows_detail){
							$intCls	=0;
							foreach($rows_detail as $keyDet=>$valDet){
								$intCls++;
								
								$Tgl_Trans		= date('d-m-Y H:i',strtotime($valDet->tgl_trans));
								$Temp_Reff		= $valDet->kode_trans;
								$Temp_Jurnal	= $valDet->no_jurnal;
								$Saldo_Awal		= ($valDet->qty_stock_awal !== 0)?$valDet->qty_stock_awal:0;
								$Qty_In			= ($valDet->qty_in !== 0)?$valDet->qty_in:0;
								$Qty_Out		= ($valDet->qty_out !== 0)?$valDet->qty_out:0;
								$Saldo_Akhir	= ($valDet->qty_stock_akhir !== 0)?$valDet->qty_stock_akhir:0;
								
								$Trans_HPP		= ($valDet->harga !== 0)?$valDet->harga:0;
								$Trans_Total	= ($valDet->nilai_trans_rp !== 0)?$valDet->nilai_trans_rp:0;
								$Trans_Awal		= ($valDet->nilai_awal_rp !== 0)?$valDet->nilai_awal_rp:0;
								$Trans_Akhir	= ($valDet->nilai_akhir_rp !== 0)?$valDet->nilai_akhir_rp:0;
								$Code_Gudang	= $valDet->id_gudang;
								
								$Gudang_From	= $valDet->kd_gudang_dari;
								$Gudang_FromCode= $valDet->id_gudang_dari;
								
								$Gudang_To		= $valDet->kd_gudang_ke;
								$Gudang_ToCode	= $valDet->id_gudang_ke;								
								$Keterangan		= 'Dari gudang '.$Gudang_From.' ke gudang '.$Gudang_To;
								if(!empty($valDet->kode_trans) && $valDet->kode_trans !== '-'){
									$Code_Unik	= $valDet->kode_trans.'^_^'.$Code_Gudang;
									$Temp_Reff	= '<a href="#" class="text-red" onClick="PreviewDetailHistory({code : \''.$Code_Unik.'\',action : \'preview_detail_stock_trans\'});">'.$valDet->kode_trans.'</a>';
								}
								
								if(!empty($valDet->no_jurnal) && $valDet->no_jurnal !== '-'){
									
									$Temp_Jurnal	= '<a href="#" class="text-orange" onClick="PreviewDetailHistory({code : \''.$valDet->no_jurnal.'\',action : \'preview_detail_jurnal_trans\'});">'.$valDet->no_jurnal.'</a>';
								}
								
								
								echo"<tr>";
									echo"<td class='text-center'>".$intCls."</td>";
									echo"<td class='text-center'>".$Tgl_Trans."</td>";
									echo"<td class='text-center'>".$Temp_Reff."</td>";
									echo"<td class='text-center'>".$Temp_Jurnal."</td>";
									echo"<td class='text-center'>".number_format($Saldo_Awal,4)."</td>";
									echo"<td class='text-center'>".number_format($Qty_In,4)."</td>";
									echo"<td class='text-center'>".number_format($Qty_Out,4)."</td>";
									echo"<td class='text-right'>".number_format($Saldo_Akhir,4)."</td>";
								echo"</tr>";
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		
	</div>
	<?php
	}
	?>
	
 </div>
 
 <style>
	.sub-heading{
		border-radius :5px;
		background-color :#03506F;
		color : #ffffff !important;
		width : 98% !important;
		margin-left : 10px !important;
	}
	.sub-heading-orange{
		background-color :#FC8621 !important;
		color : white !important;
	}
	.chosen-container{
		width : 100%;
	}
	/* LOADER */
	.loader span{
	  display: inline-block;
	  width: 12px;
	  height: 12px;
	  border-radius: 100%;
	  background-color: #3498db;
	  margin: 35px 5px;
	  opacity: 0;
	}

	.loader span:nth-child(1){
		background: #4285F4;
	  	animation: opacitychange 1s ease-in-out infinite;
	}

	.loader span:nth-child(2){
  		background: #DB4437;
	 	animation: opacitychange 1s ease-in-out 0.11s infinite;
	}

	.loader span:nth-child(3){
  		background: #F4B400;
	  	animation: opacitychange 1s ease-in-out 0.22s infinite;
	}
	.loader span:nth-child(4){
  		background: #0F9D58;
	  	animation: opacitychange 1s ease-in-out 0.44s infinite;
	}

	@keyframes opacitychange{
	  0%, 100%{
		opacity: 0;
	  }

	  60%{
		opacity: 1;
	  }
	}
	
	.text-center{
		text-align : center !important;
		vertical-align : middle !important;
	}
	
	.text-left{
		text-align : left !important;
		vertical-align : middle !important;
	}
	.text-right{
		text-align : right !important;
		vertical-align : middle !important;
	}
	
	.text-wrap{
		word-wrap : break-word !important;
	}
	.white-text{
		color : #ffffff !important;
	}
</style>