<?php
if($restChkSO < 1){
	?>
	<div class='note'>
		<p>
			<strong>Info!</strong><br> 
			IPP ini tidak dapat dilakukan approve, please update data.<br>
		</p>
	</div>
	<?php
}
else{
?>
<div class="box-body">
	<div class="form-group row">
		<div class='col-sm-4 '>
		   <label class='label-control'>Approve Action</label>
		   <select name='status' id='status' class='form-control input-md'>
				<option value='0'>Select Action</option>
				<option value='Y'>APPROVE</option>
				<option value='N'>REJECT</option>
			</select>
			<?php
			echo form_input(array('type'=>'hidden','id'=>'id_bq','name'=>'id_bq'),$id_bq);
			?>
		</div>
		<div class='col-sm-8 '>
			<div id='HideReject'>
				<label class='label-control'>Reject Reason</label>          
				<?php
					echo form_textarea(array('id'=>'approve_reason','name'=>'approve_reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Revision reason'));
				?>		
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class='col-sm-12 '>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'float:right; margin-left:5px;','value'=>'Process','content'=>'Process','id'=>'proses_so')).' ';
		?>
		<button type='button' class='btn btn-md btn-primary download_excel' title='Download Data' data-id_bq='<?=$id_bq?>' style='float:right;'><i class='fa fa-file-excel-o'></i> &nbsp;Budget SO</button>
		
		</div>
	</div>
	<!-- <div class="box-body"> -->
	<?php
	//berat pipa
	$data_berat_pipa = $this->db
		->select('
		SUM(a.est_material) AS berat_pipa, 
		SUM(a.est_harga) AS biaya_pipa,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_pipa_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_pipa_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_pipa_ga,
		SUM(a.unit_price * a.qty) AS biaya_pipa_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_pipa_profit,
		SUM(a.total_price) AS biaya_pipa_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_pipa_allow,
		SUM(a.total_price_last) AS biaya_pipa_sp,
		SUM((a.total_price_last / a.qty) * c.qty) AS biaya_pipa_sp_so
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->join('so_bf_detail_header c','a.id_milik=c.id_milik','left')
	->where('b.type_costing','pipa')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->berat_pipa:0;
	$biaya_pipa = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa:0;
	$biaya_pipa_mp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_mp:0;
	$biaya_pipa_foh = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_foh:0;
	$biaya_pipa_ga = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_ga:0;
	$biaya_pipa_dasar = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_dasar:0;
	$biaya_pipa_profit = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_profit:0;
	$biaya_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp:0;
	$kg_pipa_bp = 0;
	if($berat_pipa <> 0){
		$kg_pipa_bp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_bp/$data_berat_pipa[0]->berat_pipa:0;
	}
	$biaya_pipa_allow = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_allow:0;
	$biaya_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp:0;
	$kg_pipa_sp = 0;
	if($berat_pipa <> 0){
		$kg_pipa_sp = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp/$data_berat_pipa[0]->berat_pipa:0;
	}
	$biaya_pipa_sp_so = (!empty($data_berat_pipa))?$data_berat_pipa[0]->biaya_pipa_sp_so:0;
	//berat flange
	$data_berat_flange = $this->db
		->select('
		SUM(a.est_material) AS berat_flange, 
		SUM(a.est_harga) AS biaya_flange,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_flange_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_flange_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_flange_ga,
		SUM(a.unit_price * a.qty) AS biaya_flange_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_flange_profit,
		SUM(a.total_price) AS biaya_flange_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_flange_allow,
		SUM(a.total_price_last) AS biaya_flange_sp,
		SUM((a.total_price_last / a.qty) * c.qty) AS biaya_flange_sp_so
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->join('so_bf_detail_header c','a.id_milik=c.id_milik','left')
	->where('b.type_costing','flange')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->berat_flange:0;
	$biaya_flange = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange:0;
	$biaya_flange_mp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_mp:0;
	$biaya_flange_foh = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_foh:0;
	$biaya_flange_ga = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_ga:0;
	$biaya_flange_dasar = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_dasar:0;
	$biaya_flange_profit = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_profit:0;
	$biaya_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp:0;
	$kg_flange_bp = 0;
	if($berat_flange <> 0){
		$kg_flange_bp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_bp/$data_berat_flange[0]->berat_flange:0;
	}
	$biaya_flange_allow = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_allow:0;
	$biaya_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp:0;
	$kg_flange_sp = 0;
	if($berat_flange <> 0){
		$kg_flange_sp = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp/$data_berat_flange[0]->berat_flange:0;
	}
	$biaya_flange_sp_so = (!empty($data_berat_flange))?$data_berat_flange[0]->biaya_flange_sp_so:0;
	//berat fitting
	$data_berat_fitting = $this->db
		->select('
		SUM(a.est_material) AS berat_fitting, 
		SUM(a.est_harga) AS biaya_fitting,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_fitting_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_fitting_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_fitting_ga,
		SUM(a.unit_price * a.qty) AS biaya_fitting_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_fitting_profit,
		SUM(a.total_price) AS biaya_fitting_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_fitting_allow,
		SUM(a.total_price_last) AS biaya_fitting_sp,
		SUM((a.total_price_last / a.qty) * c.qty) AS biaya_fitting_sp_so
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->join('so_bf_detail_header c','a.id_milik=c.id_milik','left')
	->where('b.type_costing',NULL)
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->berat_fitting:0;
	$biaya_fitting = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting:0;
	$biaya_fitting_mp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_mp:0;
	$biaya_fitting_foh = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_foh:0;
	$biaya_fitting_ga = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_ga:0;
	$biaya_fitting_dasar = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_dasar:0;
	$biaya_fitting_profit = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_profit:0;
	$biaya_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp:0;
	$kg_fitting_bp = 0;
	if($berat_fitting <> 0){
		$kg_fitting_bp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_bp/$data_berat_fitting[0]->berat_fitting:0;
	}
	$biaya_fitting_allow = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_allow:0;
	$biaya_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp:0;
	$kg_fitting_sp = 0;
	if($berat_fitting <> 0){
		$kg_fitting_sp = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp/$data_berat_fitting[0]->berat_fitting:0;
	}
	$biaya_fitting_sp_so = (!empty($data_berat_fitting))?$data_berat_fitting[0]->biaya_fitting_sp_so:0;
	//berat bw
	$data_berat_bnw = $this->db
		->select('
		SUM(a.est_material) AS berat_bnw, 
		SUM(a.est_harga) AS biaya_bnw,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_bnw_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_bnw_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_bnw_ga,
		SUM(a.unit_price * a.qty) AS biaya_bnw_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_bnw_profit,
		SUM(a.total_price) AS biaya_bnw_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_bnw_allow,
		SUM(a.total_price_last) AS biaya_bnw_sp,
		SUM((a.total_price_last / a.qty) * c.qty) AS biaya_bnw_sp_so
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->join('so_bf_detail_header c','a.id_milik=c.id_milik','left')
	->where('b.type_costing','bw')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->berat_bnw:0;
	$biaya_bnw = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw:0;
	$biaya_bnw_mp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_mp:0;
	$biaya_bnw_foh = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_foh:0;
	$biaya_bnw_ga = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_ga:0;
	$biaya_bnw_dasar = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_dasar:0;
	$biaya_bnw_profit = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_profit:0;
	$biaya_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp:0;
	$kg_bnw_bp = 0;
	if($berat_bnw <> 0){
		$kg_bnw_bp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_bp/$data_berat_bnw[0]->berat_bnw:0;
	}
	$biaya_bnw_allow = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_allow:0;
	$biaya_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp:0;
	$kg_bnw_sp = 0;
	if($berat_bnw <> 0){
		$kg_bnw_sp = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp/$data_berat_bnw[0]->berat_bnw:0;
	}
	$biaya_bnw_sp_so = (!empty($data_berat_bnw))?$data_berat_bnw[0]->biaya_bnw_sp_so:0;
	//berat field joint
	$data_berat_field = $this->db
		->select('
		SUM(a.est_material) AS berat_field, 
		SUM(a.est_harga) AS biaya_field,
		SUM(a.direct_labour + a.indirect_labour + a.machine + a.mould_mandrill + a.consumable) AS biaya_field_mp,
		SUM(a.foh_consumable + a.foh_depresiasi) AS biaya_field_foh,
		SUM(a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS biaya_field_ga,
		SUM(a.unit_price * a.qty) AS biaya_field_dasar,
		SUM(a.total_price - (a.unit_price * a.qty)) AS biaya_field_profit,
		SUM(a.total_price) AS biaya_field_bp,
		SUM(a.total_price_last - a.total_price) AS biaya_field_allow,
		SUM(a.total_price_last) AS biaya_field_sp,
		SUM((a.total_price_last / a.qty) * c.qty) AS biaya_field_sp_so
		')
	->from('laporan_revised_detail a')
	->join('product_parent b','a.product_parent=b.product_parent','left')
	->join('so_bf_detail_header c','a.id_milik=c.id_milik','left')
	->where('b.type_costing','field')
	->where('a.revised_no',$revised_no)
	->where('a.id_bq',$id_bq)
	->get()
	->result();
	$berat_field = (!empty($data_berat_field))?$data_berat_field[0]->berat_field:0;
	$biaya_field = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field:0;
	$biaya_field_mp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_mp:0;
	$biaya_field_foh = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_foh:0;
	$biaya_field_ga = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_ga:0;
	$biaya_field_dasar = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_dasar:0;
	$biaya_field_profit = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_profit:0;
	$biaya_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp:0;

	$kg_field_bp = 0;
	if($berat_field <> 0){
		$kg_field_bp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_bp/$data_berat_field[0]->berat_field:0;
	}
	$biaya_field_allow = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_allow:0;
	$biaya_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp:0;
	
	$kg_field_sp = 0;
	if($berat_field <> 0){
		$kg_field_sp = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp/$data_berat_field[0]->berat_field:0;
	}
	$biaya_field_sp_so = (!empty($data_berat_field))?$data_berat_field[0]->biaya_field_sp_so:0;

	$berat_total = $berat_pipa + $berat_flange + $berat_fitting + $berat_bnw + $berat_field;
	$biaya_total = $biaya_pipa + $biaya_flange + $biaya_fitting + $biaya_bnw + $biaya_field;
	$biaya_total_mp = $biaya_pipa_mp + $biaya_flange_mp + $biaya_fitting_mp + $biaya_bnw_mp + $biaya_field_mp;
	$biaya_total_foh = $biaya_pipa_foh + $biaya_flange_foh + $biaya_fitting_foh + $biaya_bnw_foh + $biaya_field_foh;
	$biaya_total_ga = $biaya_pipa_ga + $biaya_flange_ga + $biaya_fitting_ga + $biaya_bnw_ga + $biaya_field_ga;
	$biaya_total_dasar = $biaya_pipa_dasar + $biaya_flange_dasar + $biaya_fitting_dasar + $biaya_bnw_dasar + $biaya_field_dasar;
	$biaya_total_profit = $biaya_pipa_profit + $biaya_flange_profit + $biaya_fitting_profit + $biaya_bnw_profit + $biaya_field_profit;
	$biaya_total_bp = $biaya_pipa_bp + $biaya_flange_bp + $biaya_fitting_bp + $biaya_bnw_bp + $biaya_field_bp;
	$kg_total_bp = 0;
	if($berat_total <> 0){
	$kg_total_bp = $biaya_total_bp / $berat_total;
	}
	$biaya_total_allow = $biaya_pipa_allow + $biaya_flange_allow + $biaya_fitting_allow + $biaya_bnw_allow + $biaya_field_allow;
	$biaya_total_sp = $biaya_pipa_sp + $biaya_flange_sp + $biaya_fitting_sp + $biaya_bnw_sp + $biaya_field_sp;
	$biaya_total_sp_so = $biaya_pipa_sp_so + $biaya_flange_sp_so + $biaya_fitting_sp_so + $biaya_bnw_sp_so + $biaya_field_sp_so;
	$kg_total_sp = 0;
	if($berat_total <> 0){
	$kg_total_sp = $biaya_total_sp / $berat_total;
	}
	?>
	<!-- <div class="table-responsive"> -->
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN (QTY BERDASARKAN PENAWARAN)</th>
					<th class="text-right" id='harga_penawaran2'></th>
				</tr><tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN (QTY BERDASARKAN SO)</th>
					<th class="text-right" id='harga_penawaran_so2'></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>TOTAL COST</th>
					<th class="text-right" id='total_cost2'></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='6'>NET PROFIT</th>
					<th class="text-center" id='net_profit_persen2'></th>
					<th class="text-right" id='net_profit_cost2'></th>
				</tr>
                <tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>DEAL SO</th>
					<th class="text-right" id='deal_so2'></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='6'>ESTIMASI NET PROFIT</th>
					<th class="text-center" id='est_net_profit_persen2'></th>
					<th class="text-right" id='est_net_profit_cost2'></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>A. FRP</th>
				</tr>
				<tr class='bg-blue'>
					<!-- <th class="text-left">Item</th>
					<th class="text-left">Deskripsi</th>
					<th class="text-center">Pipa</th>
					<th class="text-center">Flange</th>
					<th class="text-center">Fitting</th>
					<th class="text-center">B&W</th>
					<th class="text-center">Field Joint</th>
					<th class="text-center">Total</th> -->
					<th class="text-left" width='15%'>Item</th>
					<th class="text-left" width='25%'>Deskripsi</th>
					<th class="text-center" width='10%'>Pipa</th>
					<th class="text-center" width='10%'>Flange</th>
					<th class="text-center" width='10%'>Fitting</th>
					<th class="text-center" width='10%'>B&W</th>
					<th class="text-center" width='10%'>Field Joint</th>
					<th class="text-center" width='10%'>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Nama Resin</td>
					<td>Resin Yang digunakan</td>
					<td><?=$resin_pipa;?></td>
					<td><?=$resin_flange;?></td>
					<td><?=$resin_fitting;?></td>
					<td><?=$resin_bw;?></td>
					<td><?=$resin_field;?></td>
					<td class='text-right'>#</td>
				</tr>
				<tr>
					<td>Harga Resin/Kg</td>
					<td>Harga resin yang digunakan untuk estimasi</td>
					<td class='text-left'><?=$harga_resin_pipa;?></td>
					<td class='text-left'><?=$harga_resin_flange;?></td>
					<td class='text-left'><?=$harga_resin_fitting;?></td>
					<td class='text-left'><?=$harga_resin_bw;?></td>
					<td class='text-left'><?=$harga_resin_field;?></td>
					<td class='text-right'>#</td>
				</tr>
				<tr style='background-color: bisque;'>
					<td>Berat Material</td>
					<td></td>
					<td class='text-right'><?=number_format($berat_pipa,3);?></td>
					<td class='text-right'><?=number_format($berat_flange,3);?></td>
					<td class='text-right'><?=number_format($berat_fitting,3);?></td>
					<td class='text-right'><?=number_format($berat_bnw,3);?></td>
					<td class='text-right'><?=number_format($berat_field,3);?></td>
					<td class='text-right'><?=number_format($berat_total,3);?></td>
				</tr>
				<tr>
					<td>Biaya Material</td>
					<td>Biaya material sesuai dengan total kebutuhan material yang diestimasi engineering</td>
					<td class='text-right'><?=number_format($biaya_pipa,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw,3);?></td>
					<td class='text-right'><?=number_format($biaya_field,3);?></td>
					<td class='text-right'><?=number_format($biaya_total,3);?></td>
				</tr>
				<tr>
					<td>Biaya MP & Utilities</td>
					<td>Biaya direct labour dan indirect labour, depresiasi mesin, biaya mold mandrill dan consumable produksi</td>
					<td class='text-right'><?=number_format($biaya_pipa_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_mp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_mp,3);?></td>
				</tr>
				<tr>
					<td>Biaya FOH</td>
					<td>Biaya depresiasi FOH dan consumable FOH</td>
					<td class='text-right'><?=number_format($biaya_pipa_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_foh,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_foh,3);?></td>
				</tr>
				<tr>
					<td>Biaya General Admin</td>
					<td>Biaya gaji non produksi, tagihan rutin (listrik, air, telp, internet dll), sales dan general admin</td>
					<td class='text-right'><?=number_format($biaya_pipa_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_ga,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_ga,3);?></td>
				</tr>
				<tr>
					<td>Biaya Dasar</td>
					<td>Total biaya</td>
					<td class='text-right'><?=number_format($biaya_pipa_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_dasar,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_dasar,3);?></td>
				</tr>
				<tr>
					<td>Profit</td>
					<td>Nilai Profit</td>
					<td class='text-right'><?=number_format($biaya_pipa_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_profit,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_profit,3);?></td>
				</tr>
				<tr>
					<td>Bottom Price</td>
					<td>Biaya Dasar + Profit</td>
					<td class='text-right'><?=number_format($biaya_pipa_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_bp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_bp,3);?></td>
				</tr>
				<tr style='background-color: #d7d7d7;'>
					<td>$/Kg (dari Bottom Price)</td>
					<td>Bottom price / Berat material</td>
					<td class='text-right'><?=number_format($kg_pipa_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_flange_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_fitting_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_bnw_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_field_bp,3);?></td>
					<td class='text-right'><?=number_format($kg_total_bp,3);?></td>
				</tr>
				<tr>
					<td>Allowance</td>
					<td>Persentase untuk ruang negosiasi sales</td>
					<td class='text-right'><?=number_format($biaya_pipa_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_allow,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_allow,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td>Selling Price</td>
					<td>Bottom price + Allowance</td>
					<td class='text-right'><?=number_format($biaya_pipa_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_flange_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_fitting_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_bnw_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_field_sp,3);?></td>
					<td class='text-right'><?=number_format($biaya_total_sp,3);?></td>
				</tr>
				<tr style='background-color: #d7d7d7;'>
					<td>$/Kg (Selling Price)</td>
					<td>Selling Price / Berat material</td>
					<td class='text-right'><?=number_format($kg_pipa_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_flange_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_fitting_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_bnw_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_field_sp,3);?></td>
					<td class='text-right'><?=number_format($kg_total_sp,3);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<?php
			//BAUT
			// $data_berat_baut = $this->db
			// 		->select('
			// 		SUM(a.fumigasi) AS harga_baut, 
			// 		SUM(a.price - a.fumigasi) AS profit_baut,
			// 		SUM(a.price) AS bp_baut,
			// 		SUM(a.price_total - a.price) AS allow_baut,
			// 		SUM(a.price_total) AS sp_baut
			// 		')
			// 	->from('laporan_revised_etc a')
			// 	->where('a.category','baut')
			// 	->where('a.revised_no',$revised_no)
			// 	->where('a.id_bq',$id_bq)
			// 	->get()
			// 	->result();
			$data_berat_baut = $this->db
				->select('
				SUM(a.fumigasi) AS harga_baut, 
				SUM(a.price - a.fumigasi) AS profit_baut,
				SUM(a.price) AS bp_baut,
				SUM(a.price_total - a.price) AS allow_baut,
				SUM(a.price_total) AS sp_baut,
				SUM((a.price_total / a.qty) * c.qty) AS sp_baut_so
				')
			->from('cost_project_detail a')
			->join('so_bf_acc_and_mat c','a.id_milik=c.id_milik','left')
			->where('a.category','baut')
			// ->where('a.revised_no',$revised_no)
			->where('a.id_bq',$id_bq)
			->get()
			->result();
			$harga_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->harga_baut:0;
			$profit_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->profit_baut:0;
			$bp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->bp_baut:0;
			$allow_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->allow_baut:0;
			$sp_baut = (!empty($data_berat_baut))?$data_berat_baut[0]->sp_baut:0;
			$sp_baut_so = (!empty($data_berat_baut))?$data_berat_baut[0]->sp_baut_so:0;

			//GASKET
			// $data_berat_gasket = $this->db
			// 					->select('
			// 					SUM(a.fumigasi) AS harga_gasket, 
			// 					SUM(a.price - a.fumigasi) AS profit_gasket,
			// 					SUM(a.price) AS bp_gasket,
			// 					SUM(a.price_total - a.price) AS allow_gasket,
			// 					SUM(a.price_total) AS sp_gasket
			// 					')
			// 					->from('laporan_revised_etc a')
			// 					->where('a.category','gasket')
			// 					->where('a.revised_no',$revised_no)
			// 					->where('a.id_bq',$id_bq)
			// 					->get()
			// 					->result();
			$data_berat_gasket = $this->db
								->select('
								SUM(a.fumigasi) AS harga_gasket, 
								SUM(a.price - a.fumigasi) AS profit_gasket,
								SUM(a.price) AS bp_gasket,
								SUM(a.price_total - a.price) AS allow_gasket,
								SUM(a.price_total) AS sp_gasket,
								SUM((a.price_total / a.qty) * c.qty) AS sp_gasket_so
								')
								->from('cost_project_detail a')
								->join('so_bf_acc_and_mat c','a.id_milik=c.id_milik','left')
								->where('a.category','gasket')
								->where('a.id_bq',$id_bq)
								->get()
								->result();
			$harga_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->harga_gasket:0;
			$profit_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->profit_gasket:0;
			$bp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->bp_gasket:0;
			$allow_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->allow_gasket:0;
			$sp_gasket = (!empty($data_berat_gasket))?$data_berat_gasket[0]->sp_gasket:0;
			$sp_gasket_so = (!empty($data_berat_gasket))?$data_berat_gasket[0]->sp_gasket_so:0;

			//PLATE
			// $data_berat_plate = $this->db
			// 		->select('
			// 		SUM(a.fumigasi) AS harga_plate, 
			// 		SUM(a.price - a.fumigasi) AS profit_plate,
			// 		SUM(a.price) AS bp_plate,
			// 		SUM(a.price_total - a.price) AS allow_plate,
			// 		SUM(a.price_total) AS sp_plate
			// 		')
			// 	->from('laporan_revised_etc a')
			// 	->where('a.category','plate')
			// 	->where('a.revised_no',$revised_no)
			// 	->where('a.id_bq',$id_bq)
			// 	->get()
			// 	->result();
			$data_berat_plate = $this->db
				->select('
				SUM(a.fumigasi) AS harga_plate, 
				SUM(a.price - a.fumigasi) AS profit_plate,
				SUM(a.price) AS bp_plate,
				SUM(a.price_total - a.price) AS allow_plate,
				SUM(a.price_total) AS sp_plate,
				SUM((a.price_total / a.qty) * c.qty) AS sp_plate_so
				')
			->from('cost_project_detail a')
			->join('so_bf_acc_and_mat c','a.id_milik=c.id_milik','left')
			->where('a.category','plate')
			->where('a.id_bq',$id_bq)
			->get()
			->result();
			$harga_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->harga_plate:0;
			$profit_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->profit_plate:0;
			$bp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->bp_plate:0;
			$allow_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->allow_plate:0;
			$sp_plate = (!empty($data_berat_plate))?$data_berat_plate[0]->sp_plate:0;
			$sp_plate_so = (!empty($data_berat_plate))?$data_berat_plate[0]->sp_plate_so:0;

			//LAINNYA
			// $data_berat_lainnya = $this->db
			// 		->select('
			// 		SUM(a.fumigasi) AS harga_lainnya, 
			// 		SUM(a.price - a.fumigasi) AS profit_lainnya,
			// 		SUM(a.price) AS bp_lainnya,
			// 		SUM(a.price_total - a.price) AS allow_lainnya,
			// 		SUM(a.price_total) AS sp_lainnya
			// 		')
			// 	->from('laporan_revised_etc a')
			// 	->where('a.category','lainnya')
			// 	->where('a.revised_no',$revised_no)
			// 	->where('a.id_bq',$id_bq)
			// 	->get()
			// 	->result();
			$data_berat_lainnya = $this->db
				->select('
				SUM(a.fumigasi) AS harga_lainnya, 
				SUM(a.price - a.fumigasi) AS profit_lainnya,
				SUM(a.price) AS bp_lainnya,
				SUM(a.price_total - a.price) AS allow_lainnya,
				SUM(a.price_total) AS sp_lainnya,
				SUM((a.price_total / a.qty) * c.qty) AS sp_lainnya_so
				')
			->from('cost_project_detail a')
			->join('so_bf_acc_and_mat c','a.id_milik=c.id_milik','left')
			->where('a.category','lainnya')
			->where('a.id_bq',$id_bq)
			->get()
			->result();
			$harga_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->harga_lainnya:0;
			$profit_lainnya = (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->profit_lainnya:0;
			$bp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->bp_lainnya:0;
			$allow_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->allow_lainnya:0;
			$sp_lainnya 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->sp_lainnya:0;
			$sp_lainnya_so 	= (!empty($data_berat_lainnya))?$data_berat_lainnya[0]->sp_lainnya_so:0;

			//MATERIAL
			// $data_berat_material = $this->db
			// 		->select('
			// 		SUM(a.fumigasi) AS harga_material, 
			// 		SUM(a.price - a.fumigasi) AS profit_material,
			// 		SUM(a.price) AS bp_material,
			// 		SUM(a.price_total - a.price) AS allow_material,
			// 		SUM(a.price_total) AS sp_material
			// 		')
			// 	->from('laporan_revised_etc a')
			// 	->where('a.category','aksesoris')
			// 	->where('a.revised_no',$revised_no)
			// 	->where('a.id_bq',$id_bq)
			// 	->get()
			// 	->result();
			$data_berat_material = $this->db
				->select('
				SUM(a.fumigasi) AS harga_material, 
				SUM(a.price - a.fumigasi) AS profit_material,
				SUM(a.price) AS bp_material,
				SUM(a.price_total - a.price) AS allow_material,
				SUM(a.price_total) AS sp_material,
				SUM((a.price_total / a.weight) * c.qty) AS sp_material_so
				')
			->from('cost_project_detail a')
			->join('so_bf_acc_and_mat c','a.id_milik=c.id_milik','left')
			->where('a.category','aksesoris')
			->where('a.id_bq',$id_bq)
			->get()
			->result();
			$harga_material 	= (!empty($data_berat_material))?$data_berat_material[0]->harga_material:0;
			$profit_material = (!empty($data_berat_material))?$data_berat_material[0]->profit_material:0;
			$bp_material 	= (!empty($data_berat_material))?$data_berat_material[0]->bp_material:0;
			$allow_material 	= (!empty($data_berat_material))?$data_berat_material[0]->allow_material:0;
			$sp_material 	= (!empty($data_berat_material))?$data_berat_material[0]->sp_material:0;
			$sp_material_so 	= (!empty($data_berat_material))?$data_berat_material[0]->sp_material_so:0;

			$harga_total 	= $harga_baut + $harga_gasket + $harga_plate + $harga_lainnya + $harga_material;
			$profit_total 	= $profit_baut + $profit_gasket + $profit_plate + $profit_lainnya + $profit_material;
			$bp_total 		= $bp_baut + $bp_gasket + $bp_plate + $bp_lainnya + $bp_material;
			$allow_total 	= $allow_baut + $allow_gasket + $allow_plate + $allow_lainnya + $allow_material;
			$sp_total 		= $sp_baut + $sp_gasket + $sp_plate + $sp_lainnya + $sp_material;
			$sp_total_so 		= $sp_baut_so + $sp_gasket_so + $sp_plate_so + $sp_lainnya_so + $sp_material_so;

			?>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>B. NON FRP</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left">Item</th>
					<th class="text-left">Deskripsi</th>
					<th class="text-center">Material</th>
					<th class="text-center">Bolt & Nut</th>
					<th class="text-center">Gasket</th>
					<th class="text-center">Plate</th>
					<th class="text-center">Lainnya</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Harga Costing</td>
					<td>Harga yang didapat dari supplier</td>
					<td class="text-right"><?=number_format($harga_material,3);?></td>
					<td class="text-right"><?=number_format($harga_baut,3);?></td>
					<td class="text-right"><?=number_format($harga_gasket,3);?></td>
					<td class="text-right"><?=number_format($harga_plate,3);?></td>
					<td class="text-right"><?=number_format($harga_lainnya,3);?></td>
					<td class="text-right"><?=number_format($harga_total,3);?></td>
				</tr>
				<tr>
					<td>Profit</td>
					<td>Nilai Profit</td>
					<td class="text-right"><?=number_format($profit_material,3);?></td>
					<td class="text-right"><?=number_format($profit_baut,3);?></td>
					<td class="text-right"><?=number_format($profit_gasket,3);?></td>
					<td class="text-right"><?=number_format($profit_plate,3);?></td>
					<td class="text-right"><?=number_format($profit_lainnya,3);?></td>
					<td class="text-right"><?=number_format($profit_total,3);?></td>
				</tr>
				<tr>
					<td>Bottom Price</td>
					<td>Biaya Dasar + Profit</td>
					<td class="text-right"><?=number_format($bp_material,3);?></td>
					<td class="text-right"><?=number_format($bp_baut,3);?></td>
					<td class="text-right"><?=number_format($bp_gasket,3);?></td>
					<td class="text-right"><?=number_format($bp_plate,3);?></td>
					<td class="text-right"><?=number_format($bp_lainnya,3);?></td>
					<td class="text-right"><?=number_format($bp_total,3);?></td>
				</tr>
				<tr>
					<td>Allowance</td>
					<td>Persentase untuk ruang negosiasi sales</td>
					<td class="text-right"><?=number_format($allow_material,3);?></td>
					<td class="text-right"><?=number_format($allow_baut,3);?></td>
					<td class="text-right"><?=number_format($allow_gasket,3);?></td>
					<td class="text-right"><?=number_format($allow_plate,3);?></td>
					<td class="text-right"><?=number_format($allow_lainnya,3);?></td>
					<td class="text-right"><?=number_format($allow_total,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td>Selling Price</td>
					<td>Bottom price + Allowance</td>
					<td class="text-right"><?=number_format($sp_material,3);?></td>
					<td class="text-right"><?=number_format($sp_baut,3);?></td>
					<td class="text-right"><?=number_format($sp_gasket,3);?></td>
					<td class="text-right"><?=number_format($sp_plate,3);?></td>
					<td class="text-right"><?=number_format($sp_lainnya,3);?></td>
					<td class="text-right"><?=number_format($sp_total,3);?></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</tbody>
			<?php
			$data_packing = $this->db
						->select('
						SUM(a.price_total) AS harga_packing
						')
						->from('laporan_revised_etc a')
						->where('a.category','packing')
						->where('a.revised_no',$revised_no)
						->where('a.id_bq',$id_bq)
						->get()
						->result();
			$harga_packing = (!empty($data_packing))?$data_packing[0]->harga_packing:0;

			$data_transport = $this->db
		  					->select('
							  SUM(a.price_total) AS harga_transport
							  ')
							->from('laporan_revised_etc a')
							->where("(a.category = 'export' OR a.category = 'lokal')")
							->where('a.revised_no',$revised_no)
							->where('a.id_bq',$id_bq)
							->get()
							->result();
			$harga_transport = (!empty($data_transport))?$data_transport[0]->harga_transport:0;

			$data_engine = $this->db
								->select('
								SUM(a.price_total) AS harga_engine
								')
								->from('laporan_revised_etc a')
								->where('a.category','engine')
								->where('a.revised_no',$revised_no)
								->where('a.id_bq',$id_bq)
								->get()
								->result();
			$harga_engine = (!empty($data_engine))?$data_engine[0]->harga_engine:0;
			?>
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th colspan='8'>C. PACKING & TRANSPORT</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-left" colspan='7'>Kategori</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Packing</td>
					<td class="text-right"><?=number_format($harga_packing,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Transportasi</td>
					<td class="text-right"><?=number_format($harga_transport,3);?></td>
				</tr>
				<tr style='background-color: aquamarine;'>
					<td colspan='7'>Engineering</td>
					<td class="text-right"><?=number_format($harga_engine,3);?></td>
				</tr>
				<tr>
					<td colspan='7'>&nbsp;</td>
					<td></td>
				</tr>
			</tbody>
			<?php
			$harga_penawaran 	= $biaya_total_sp + $sp_total + $harga_packing + $harga_transport + $harga_engine;
			$harga_penawaran_so = $biaya_total_sp_so + $sp_total_so + $harga_packing + $harga_transport + $harga_engine;
			$harga_cost			= $biaya_total_dasar + $harga_total + $harga_packing + $harga_transport + $harga_engine;
			$net_profit			= $harga_penawaran - $harga_cost;
			$net_persent = 0;
			if($harga_penawaran <> 0 AND $net_profit <> 0 ){
			$net_persent		= $net_profit / $harga_penawaran_so * 100;
			}

            $deal_so            = $deal_usd;
            $est_net_profit		= $deal_usd - $harga_cost;
            $est_net_persent = 0;
			if($deal_usd <> 0 AND $est_net_profit <> 0 ){
			$est_net_persent	= $est_net_profit / $harga_penawaran_so * 100;
			}
			?>
			<thead id='head_table'>
                <tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN (QTY BERDASARKAN PENAWARAN)</th>
					<th class="text-right" id='harga_penawaran'><?=number_format($harga_penawaran,3);?></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>HARGA PENAWARAN (QTY BERDASARKAN SO)</th>
					<th class="text-right" id='harga_penawaran_so'><?=number_format($harga_penawaran_so,3);?></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>TOTAL COST</th>
					<th class="text-right" id='total_cost'><?=number_format($harga_cost,3);?></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='6'>NET PROFIT</th>
					<th class="text-center" id='net_profit_persen'><?=number_format($net_persent,2);?> %</th>
					<th class="text-right" id='net_profit_cost'><?=number_format($net_profit,3);?></th>
				</tr>
                <tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='7'>DEAL SO</th>
					<th class="text-right" id='deal_so'><?=number_format($deal_so,3);?></th>
				</tr>
				<tr style='background-color: #8ed6ec;'>
					<th class="text-left" colspan='6'>ESTIMASI NET PROFIT</th>
					<th class="text-center" id='est_net_profit_persen'><?=number_format($est_net_persent,2);?> %</th>
					<th class="text-right" id='est_net_profit_cost'><?=number_format($est_net_profit,3);?></th>
				</tr>
			</thead>
		</table>
<!-- </div> -->
	<br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<tr>
			<th class="text-center" style='vertical-align:middle;' width='17%'></th>
			<th class="text-center" style='vertical-align:middle;' width='13%'></th>
			<th class="text-center" style='vertical-align:middle;' width='5%'></th>
			<th class="text-center" colspan='4' style='vertical-align:middle;' width='28%'></th>
			<th class="text-center" style='vertical-align:middle;' width='7%'></th>
		</tr>
		<?php
			$SUM = 0;
			if(!empty($qBQdetailRest)){ ?>
				<thead id='head_table'>
					<tr class='bg-blue'>
						<td class="text-left" colspan='8'><b>PRODUCT</b></td>
					</tr>
					<tr class='bg-blue'>
						<th class="text-center" style='vertical-align:middle;' width='17%'>Component</th>
						<th class="text-center" style='vertical-align:middle;' width='13%'>Dimensi</th>
						<th class="text-center" style='vertical-align:middle;' width='5%'>Qty</th>
						<th class="text-center" colspan='4' style='vertical-align:middle;' width='28%'>Product ID</th>
						<th class="text-center" style='vertical-align:middle;' width='7%'>Cost</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$SUM = 0;
						$no = 0;
						foreach($qBQdetailRest AS $val => $valx){ $no++;
							
							$SUM += $valx['cost'];
							
							echo "<tr>";
								echo "<td align='left' class='so_style_list'>".strtoupper($valx['id_category'])."<input type='hidden' id='est_harga_".$no."' value='".$valx['cost']."'></td>";
								echo "<td align='left' class='so_style_list'>".spec_bq($valx['id_milik'])."</td>";
								echo "<td align='center' class='so_style_list'>".$valx['qty']."</td>";
								echo "<td align='left' colspan='4' class='so_style_list'>".$valx['id_product']."</span></td>";
								echo "<td align='right' class='so_style_list'><div id='sumAk_".$no."'>".number_format($valx['cost'], 2)."</div></span></td>";
							echo "</tr>";
						}
					?>
					<tr>
						<th class="text-center"></th>
						<th class="text-left" colspan='6' style='vertical-align:middle;'>TOTAL COST PRODUCT</th>
						<th class="text-right"><div id='sumSO'><?= number_format($SUM, 2);?></div></th>
					</tr>
				</tbody>
			<?php
			}
			$SUM_NONFRP = 0;
			if(!empty($non_frp)){
				echo "<tbody>";
					echo "<tr class='bg-blue'>";
						echo "<td class='text-left' colspan='8'><b>BQ NON FRP</b></td>";
					echo "</tr>";
					echo "<tr  class='bg-blue'>";
						echo "<th class='text-center'>Material Name</th>";
						echo "<th class='text-center'>Qty</th>";
						echo "<th class='text-center'>Satuan</th>";
						echo "<th class='text-center'>Unit</th>";
						echo "<th class='text-center'>Profit</th>";
						echo "<th class='text-center'>Unit Price</th>";
						echo "<th class='text-center'>Allow</th>";
						echo "<th class='text-center'>Total Price</th>";
					echo "</tr>";
				echo "</tbody>";
				echo "<tbody class='body_x'>";
				foreach($non_frp AS $val => $valx){
					$SUM_NONFRP += $valx['price_total'];
					$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
					$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
					$nama_acc = "";
					if($valx['category'] == 'baut'){
						$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
					}
					if($valx['category'] == 'plate'){
						$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
					}
					if($valx['category'] == 'gasket'){
						$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
					}
					if($valx['category'] == 'lainnya'){
						$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
					}
						
					$qty = $valx['qty'];
					$satuan = $valx['option_type'];
					if($valx['category'] == 'plate'){
						$qty = $valx['weight'];
						$satuan = '1';
					}
						
					echo "<tr>";
						echo "<td>".$nama_acc."</td>";
						echo "<td align='right'>".number_format($qty,2)."</td>";
						echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
						echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
						echo "<td align='right'>".number_format($valx['persen'],2)."</td>";
						echo "<td align='right'>".number_format($valx['price'],2)."</td>";
						echo "<td align='right'>".number_format($valx['extra'],2)."</td>";
						echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
					echo "</tr>";
				}
				echo "<tr class='FootColor'>";
					echo "<td colspan='7'><b>TOTAL BQ NON FRP</b></td> ";
					// echo "<td align='center'><b>IDR</b></td> ";
					echo "<td align='right'><b>".number_format($SUM_NONFRP,2)."</b></td>";
				echo "</tr>";
				echo "</tbody>";
			}
			$SUM_MAT = 0;
			if(!empty($material)){
				echo "<tbody>";
					echo "<tr class='bg-blue'>";
						echo "<td class='text-left' colspan='8'><b>MATERIAL</b></td>";
					echo "</tr>";
					echo "<tr  class='bg-blue'>";
						echo "<th class='text-center'>Material Name</th>";
						echo "<th class='text-center'>Weight</th>";
						echo "<th class='text-center'>Satuan</th>";
						echo "<th class='text-center'>Unit</th>";
						echo "<th class='text-center'>Profit</th>";
						echo "<th class='text-center'>Unit Price</th>";
						echo "<th class='text-center'>Allow</th>";
						echo "<th class='text-center'>Total Price</th>";
					echo "</tr>";
				echo "</tbody>";
				echo "<tbody class='body_x'>";
				foreach($material AS $val => $valx){
					$SUM_MAT += $valx['price_total'];
					echo "<tr>";
						echo "<td>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
						echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
						echo "<td align='left'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type']))."</td>";
						echo "<td align='right'>".number_format($valx['unit_price'],2)."</td>";
						echo "<td align='right'>".number_format($valx['persen'],2)."</td>";
						echo "<td align='right'>".number_format($valx['price'],2)."</td>";
						echo "<td align='right'>".number_format($valx['extra'],2)."</td>";
						echo "<td align='right'>".number_format($valx['price_total'],2)."</td>";
					echo "</tr>";
				}
				echo "<tr class='FootColor'>";
					echo "<td colspan='7'><b>TOTAL MATERIAL</b></td> ";
					echo "<td align='right'><b>".number_format($SUM_MAT, 2)."</b></td>";
				echo "</tr>";
				echo "</tbody>";
			}
			?>
		
	</table>
</div>
<?php } ?>
<style>
	.so_style_list{
		vertical-align:middle;
		padding-left:20px;
	}
</style>
<script>
	swal.close();
	$('#HideReject').hide();
	$(document).on('change', '#status', function(){
		if($(this).val() == 'N'){
			$('#HideReject').show();
		}
		else{
			$('#HideReject').hide();
		}
	});

	$(document).ready(function(){
		var harga_penawaran 	= $('#harga_penawaran').html();
		var harga_penawaran_so 	= $('#harga_penawaran_so').html();
		var total_cost 			= $('#total_cost').html();
		var net_profit_persen 	= $('#net_profit_persen').html();
		var net_profit_cost 	= $('#net_profit_cost').html();

        var deal_so 			    = $('#deal_so').html();
		var est_net_profit_persen 	= $('#est_net_profit_persen').html();
		var est_net_profit_cost 	= $('#est_net_profit_cost').html();

		$('#harga_penawaran2').html(harga_penawaran);
		$('#harga_penawaran_so2').html(harga_penawaran_so);
		$('#total_cost2').html(total_cost);
		$('#net_profit_persen2').html(net_profit_persen);
		$('#net_profit_cost2').html(net_profit_cost);	

        $('#deal_so2').html(deal_so);
		$('#est_net_profit_persen2').html(est_net_profit_persen);
		$('#est_net_profit_cost2').html(est_net_profit_cost);

		if(deal_so <= 0){
			$('#deal_so').attr('class','text-right text-red');
			$('#est_net_profit_persen').attr('class','text-center text-red')
			$('#est_net_profit_cost').attr('class','text-right text-red')
			$('#deal_so2').attr('class','text-right text-red');
			$('#est_net_profit_persen2').attr('class','text-center text-red')
			$('#est_net_profit_cost2').attr('class','text-right text-red')
		}
	});

	$(document).on('click', '.download_excel', function(){
		var id_bq		= $(this).data('id_bq');
		
		var Links		= base_url +'budget_so/ExcelBudgetSo/'+id_bq;
		window.open(Links,'_blank');
	});
</script>