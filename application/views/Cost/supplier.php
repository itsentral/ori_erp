<?php
$this->load->view('include/side_menu'); 
// $tgl1 = new DateTime("2019-11-01");
// $tgl2 = new DateTime("2019-12-31");
// $d = $tgl2->diff($tgl1)->days + 1;
// echo $d." hari";
$active1_mat = '';
if($value1_head == 'material'){
	$active1_mat = 'active';
}

$active2_acc = '';
if($value1_head == 'accessories'){
	$active2_acc = 'active';
}

$active1_trans = '';
if($value1_head == 'transport'){
	$active1_trans = 'active';
}

$active2_rutin = '';
if($value1_head == 'rutin'){
	$active2_rutin = 'active';
}

//detail
$active1 = '';
if($value1 == 'bolt nut'){
	$active1 = 'active';
}

$active2 = '';
if($value1 == 'plate'){
	$active2 = 'active';
}

$active3 = '';
if($value1 == 'gasket'){
	$active3 = 'active';
}

$active4 = '';
if($value1 == 'lainnya'){
	$active4 = 'active';
}

$active4a = '';
if($value1 == 'tanki'){
	$active4a = 'active';
}

$active5 = '';
if($value1 == 'lokal'){
	$active5 = 'active';
}

$active6 = '';
if($value1 == 'eksport'){
	$active6 = 'active';
}

?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div>
			<!-- Nav tabs -->
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="<?=$active1_mat;?>"><a href="#material" class='material' aria-controls="material" role="tab" data-toggle="tab">Material</a></li>
				<li role="presentation" class="<?=$active2_acc;?>"><a href="#accessories" class='accessories' aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a></li>
				<li role="presentation" class="<?=$active1_trans;?>"><a href="#transport" class='transport' aria-controls="transport" role="tab" data-toggle="tab">Transport</a></li>
				<li role="presentation" class="<?=$active2_rutin;?>"><a href="#rutin" class='rutin' aria-controls="rutin" role="tab" data-toggle="tab">Rutin</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content">
				<!--bold and nut-->
				<div role="tabpanel" class="tab-pane <?=$active1_mat;?>" id="material">
					<br>
					<a href="<?php echo site_url('cost/excel_price_ref_supplier') ?>" target='_blank' class="btn btn-sm btn-success" style='float:right;'>Download</a>
					<br><br>
					<table id="example4" class="table table-bordered table-striped">
						<thead>
							<tr class='bg-blue'>
								<th class="text-center" >#</th>
								<th class="text-center" >Code</th>
								<th class="text-center" >Material ID</th>
								<th class="text-center" >Material Name</th>
								<th class="text-center" >Category</th>
								<th class="text-center" >Price Supplier ($)</th>
								<th class="text-center" >Expired</th>
								<th class="text-center" >Status</th>
								<th class="text-center" >Alasan Reject</th>
								<th class="text-center" >Option</th>
							</tr>
						</thead>
						<tbody>  
						<?php 
						if($row){
								$int	=0;
								foreach($row as $datas){
									$int++;
									$class	= 'bg-green';
									$status	= 'Active';
									if($datas->id_material == 'N'){
										$class	= 'bg-red';
										$status	= 'Not Active';
									}
									
									//estimation
									$date_now 	= date('Y-m-d');
									$date_exp 	= $datas->exp_price_ref_pur;

									$tgl1x = new DateTime($date_now);
									$tgl2x = new DateTime($date_exp);
									$selisihx = $tgl2x->diff($tgl1x)->days + 1;

									$date_expv 	= date('d M Y', strtotime($date_exp));
									$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
									// $selisih	= $date_expv->diff($date_now)->days;
									
									$waiting_app = '';
									if($datas->app_price_sup == 'Y'){
										$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
									}
									
									$tambahan = "No Set";
									if($tgl2x < $tgl1x){
										$status2="Expired price";
										$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
									}
									if($tgl2x >= $tgl1x AND $selisihx <= 7){
										$status2="Less one week expired price";
										$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
									}
									if($tgl2x >= $tgl1x AND $selisihx > 7){
										$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
									}

									if(empty($date_exp)){
										$status2="Not Set";
										$tambahan = "<span class='badge bg-red'>$status2</span>";
										$date_expv 	= 'Not setting';
									}
									
									$PRE = (!empty($datas->price_from_supplier))?$datas->price_from_supplier:0;
									echo"<tr>";							
										echo"<td align='center'>$int</td>";
										echo"<td align='left'>".$datas->id_material."</td>";
										echo"<td align='left'>".$datas->idmaterial."</td>";
										echo"<td align='left'>".strtoupper($datas->nm_material)."</td>";
										echo"<td align='left'>".$datas->nm_category."</td>";
										echo"<td align='right'>".number_format($PRE,2)."</td>";
										echo"<td align='right'>".$date_expv."</td>";
										echo"<td align='left'>".$tambahan."</td>";
										echo"<td align='left'>".ucfirst(strtolower($datas->reject_reason))."</td>";
										echo"<td align='center'>"; 
											if($akses_menu['update']=='1'){
												echo"&nbsp;<a href='".site_url($this->uri->segment(1).'/edit_supplier/'.$datas->id_material)."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
											}
										echo"</td>";
									echo"</tr>";
								}
						}
						?>
						</tbody>
					</table>
				</div>
				<!--plate-->
				<div role="tabpanel" class="tab-pane <?=$active2_acc;?>" id="accessories">
					<br>
					<a href="<?php echo site_url('cost/excel_price_sup_aksesoris') ?>" target='_blank' class="btn btn-sm btn-success" style='float:right;'>Download</a>
					<!-- //DAFTAR BARU -->
					<div>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="<?=$active1;?>"><a href="#boltnut" class='boltnut' aria-controls="boltnut" role="tab" data-toggle="tab">Bolt & Nut</a></li>
							<li role="presentation" class="<?=$active2;?>"><a href="#plate" class='plate' aria-controls="plate" role="tab" data-toggle="tab">Plate</a></li>
							<li role="presentation" class="<?=$active3;?>"><a href="#gasket" class='gasket' aria-controls="gasket" role="tab" data-toggle="tab">Gasket</a></li>
							<li role="presentation" class="<?=$active4;?>"><a href="#lainnya" class='lainnya' aria-controls="lainnya" role="tab" data-toggle="tab">Lainnya</a></li>
							<li role="presentation" class="<?=$active4a;?>"><a href="#tanki" class='tanki' aria-controls="tanki" role="tab" data-toggle="tab">Tanki</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<!--bold and nut-->
							<div role="tabpanel" class="tab-pane <?=$active1;?>" id="boltnut">
								<div class="box-tool pull-right">
									<select name='barang1' id='barang1' class='form-control input-sm chosen-select'>
										<option value='0'>ALL NAME</option>
										<?php
										foreach($name_baut as $val => $valx)
										{
											echo "<option value='".$valx['nama']."'>".strtoupper($valx['nama'])."</option>";
										}
										?>
									</select>
									<select name='brand1' id='brand1' class='form-control input-sm chosen-select'>
										<option value='0'>ALL MATERIAL</option>
										<?php
										foreach($brand_baut as $val => $valx)
										{
											echo "<option value='".$valx['material']."'>".strtoupper($valx['material'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_bold_nut" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">ID Program</th>
											<th class="text-center">ID</th>
											<th class="text-center">Name</th>
											<th class="text-center">Material</th> 
											<th class="text-center">Diameter (mm)</th> 
											<th class="text-center">Panjang (mm)</th> 
											<th class="text-center">Standard/Tipe</th> 
											<th class="text-center">Radius (mm)</th> 
											<th class="text-center">Satuan</th> 
											<!-- <th class="text-center">Keterangan</th> -->
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center" >Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
							<!--plate-->
							<div role="tabpanel" class="tab-pane <?=$active2;?>" id="plate">
								<div class="box-tool pull-right">
									<select name='barang2' id='barang2' class='form-control input-sm chosen-select'>
										<option value='0'>ALL NAME</option>
										<?php
										foreach($name_plate as $val => $valx)
										{
											echo "<option value='".$valx['nama']."'>".strtoupper($valx['nama'])."</option>";
										}
										?>
									</select>
									<select name='brand2' id='brand2' class='form-control input-sm chosen-select'>
										<option value='0'>ALL MATERIAL</option>
										<?php
										foreach($brand_plate as $val => $valx)
										{
											echo "<option value='".$valx['material']."'>".strtoupper($valx['material'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_plate" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">ID Program</th>
											<th class="text-center">ID</th>
											<th class="text-center">Name</th>
											<th class="text-center">Material</th> 
											<th class="text-center">Thickness (mm)</th> 
											<th class="text-center">Density (kg/cm3)</th> 
											<th class="text-center">Ukuran Standard</th> 
											<th class="text-center">Standart</th> 
											<th class="text-center">Satuan</th> 
											<!-- <th class="text-center">Keterangan</th> -->
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
							<!--gasket-->
							<div role="tabpanel" class="tab-pane <?=$active3;?>" id="gasket">
								<div class="box-tool pull-right">
									<select name='barang3' id='barang3' class='form-control input-sm chosen-select'>
										<option value='0'>ALL NAME</option>
										<?php
										foreach($name_gasket as $val => $valx)
										{
											echo "<option value='".$valx['nama']."'>".strtoupper($valx['nama'])."</option>";
										}
										?>
									</select>
									<select name='brand3' id='brand3' class='form-control input-sm chosen-select'>
										<option value='0'>ALL MATERIAL</option>
										<?php
										foreach($brand_gasket as $val => $valx)
										{
											echo "<option value='".$valx['material']."'>".strtoupper($valx['material'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_gasket" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">ID Program</th>
											<th class="text-center">ID</th>
											<th class="text-center">Name</th>
											<th class="text-center">Material</th> 
											<th class="text-center">Thickness (mm)</th> 
											<th class="text-center">Ukuran Standart</th> 
											<th class="text-center">Standard</th>
											<th class="text-center">Satuan</th> 
											<!-- <th class="text-center">Keterangan</th> -->
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
							<!--lainnya-->
							<div role="tabpanel" class="tab-pane <?=$active4;?>" id="lainnya">
								<div class="box-tool pull-right">
									<select name='barang4' id='barang4' class='form-control input-sm chosen-select'>
										<option value='0'>ALL NAME</option>
										<?php
										foreach($name_lainnya as $val => $valx)
										{
											echo "<option value='".$valx['nama']."'>".strtoupper($valx['nama'])."</option>";
										}
										?>
									</select>
									<select name='brand4' id='brand4' class='form-control input-sm chosen-select'>
										<option value='0'>ALL MATERIAL</option>
										<?php
										foreach($brand_lainnya as $val => $valx)
										{
											echo "<option value='".$valx['material']."'>".strtoupper($valx['material'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_lainnya" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">ID Program</th>
											<th class="text-center">ID</th>
											<th class="text-center">Name</th>
											<th class="text-center">Material/Brand</th> 
											<th class="text-center">Dimensi</th> 
											<th class="text-center">Spesifikasi</th> 
											<th class="text-center">Ukuran Standart</th> 
											<th class="text-center">Standart</th> 
											<th class="text-center">Satuan</th> 
											<!-- <th class="text-center">Keterangan</th> -->
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
							<!--Tanki-->
							<div role="tabpanel" class="tab-pane <?=$active4a;?>" id="tanki">
								<div class="box-tool pull-right">
									<select name='barang4a' id='barang4a' class='form-control input-sm chosen-select'>
										<option value='0'>ALL NAME</option>
										<?php
										foreach($name_lainnya as $val => $valx)
										{
											echo "<option value='".$valx['nama']."'>".strtoupper($valx['nama'])."</option>";
										}
										?>
									</select>
									<select name='brand4a' id='brand4a' class='form-control input-sm chosen-select'>
										<option value='0'>ALL MATERIAL</option>
										<?php
										foreach($brand_lainnya as $val => $valx)
										{
											echo "<option value='".$valx['material']."'>".strtoupper($valx['material'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_tanki" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">ID Program</th>
											<th class="text-center">ID</th>
											<th class="text-center">Name</th>
											<th class="text-center">Material/Brand</th> 
											<th class="text-center">Dimensi</th> 
											<th class="text-center">Spesifikasi</th> 
											<th class="text-center">Ukuran Standart</th> 
											<th class="text-center">Standart</th> 
											<th class="text-center">Satuan</th> 
											<!-- <th class="text-center">Keterangan</th> -->
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- //Transport -->
				<div role="tabpanel" class="tab-pane <?=$active1_trans;?>" id="transport">
					<br>
					<!-- //DAFTAR BARU -->
					<div>
						<!-- Nav tabs -->
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="<?=$active5;?>"><a href="#lokal" class='lokal' aria-controls="lokal" role="tab" data-toggle="tab">Lokal</a></li>
							<li role="presentation" class="<?=$active6;?>"><a href="#eksport" class='eksport' aria-controls="eksport" role="tab" data-toggle="tab">Eksport</a></li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">
							<!--lokal-->
							<div role="tabpanel" class="tab-pane <?=$active5;?>" id="lokal">
								<div class="box-tool pull-right">
									<select name='lokal_kategori' id='lokal_kategori' class='form-control input-sm chosen-select'>
										<option value='0'>All Category</option>
										<?php
										foreach($category as $val => $valx)
										{
											echo "<option value='".$valx['category']."'>".strtoupper($valx['category'])."</option>";
										}
										?>
									</select>
									<select name='lokal_area' id='lokal_area' class='form-control input-sm chosen-select'>
										<option value='0'>All Area</option>
										<?php
										foreach($area as $val => $valx)
										{
											echo "<option value='".$valx['area']."'>".strtoupper($valx['area'])."</option>";
										}
										?>
									</select>
									<select name='lokal_dest' id='lokal_dest' class='form-control input-sm chosen-select'>
										<option value='0'>All Destination</option>
										<?php
										foreach($dest as $val => $valx)
										{
											echo "<option value='".$valx['tujuan']."'>".strtoupper($valx['tujuan'])."</option>";
										}
										?>
									</select>
									<select name='lokal_truck' id='lokal_truck' class='form-control input-sm chosen-select'>
										<option value='0'>All Truck</option>
										<?php
										foreach($truck as $val => $valx)
										{
											echo "<option value='".$valx['id']."'>".strtoupper($valx['nama_truck'])."</option>";
										}
										?>
									</select>
								</div>
								<br><br><br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="my-grid_lokal" width='100%'>
									<thead>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">Category</th>
											<th class="text-center">Area</th> 
											<th class="text-center">Destination</th> 
											<th class="text-center">Truck</th> 
											<th class="text-center">Price Supplier (IDR)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center no-sort">Option</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
								</div>
							</div>
							<!--eksport-->
							<div role="tabpanel" class="tab-pane <?=$active6;?>" id="eksport">
								<br>
								<div class="table-responsive">
								<table class="table table-bordered table-striped" id="example2" width='100%'>
									<thead width='100%'>
										<tr class='bg-blue'>
											<th class="text-center">#</th>
											<th class="text-center">Country Destination</th>
											<th class="text-center">Shipping</th> 
											<th class="text-center">Price Supplier ($)</th>
											<th class="text-center">Expired</th>
											<th class="text-center">Status</th>
											<th class="text-center">Alasan Reject</th>
											<th class="text-center">Option</th>
										</tr>
									</thead>
									<tbody>
									<?php
									foreach ($transport_export as $key => $value) { $key++;
										//estimation
										$date_now 	= date('Y-m-d');
										$date_exp 	= $value['expired_supplier'];

										$tgl1x = new DateTime($date_now);
										$tgl2x = new DateTime($date_exp);
										$selisihx = $tgl2x->diff($tgl1x)->days + 1;

										$date_expv 	= date('d M Y', strtotime($date_exp));
										$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
										// $selisih	= $date_expv->diff($date_now)->days;
										
										$waiting_app = '';
										if($value['app_price_sup'] == 'Y'){
											$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
										}
										
										$tambahan = "No Set";
										if($tgl2x < $tgl1x){
											$status2="Expired price";
											$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
										}
										if($tgl2x >= $tgl1x AND $selisihx <= 7){
											$status2="Less one week expired price";
											$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
										}
										if($tgl2x >= $tgl1x AND $selisihx > 7){
											$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
										}

										if(empty($date_exp)){
											$status2="Not Set";
											$tambahan = "<span class='badge bg-red'>$status2</span>";
											$date_expv 	= 'Not setting';
										}

										$PRE = (!empty($value['price_supplier']))?$value['price_supplier']:0;
										echo "<tr>";
											echo "<td>".$key."</td>";
											echo "<td>".strtoupper($value['country_name'])."</td>";
											echo "<td>".strtoupper($value['shipping_name'])."</td>";
											echo "<td class='text-right'>".number_format($PRE,2)."</td>";
											echo "<td class='text-center'>".$date_expv."</td>";
											echo "<td>".$tambahan."</td>";
											echo "<td>".ucfirst(strtolower($value['reject_reason']))."</td>";
											echo "<td align='center'>"; 
												if($akses_menu['update']=='1'){
													echo"&nbsp;<a href='".site_url($this->uri->segment(1).'/edit_supplier_eksport/'.$value['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
												}
											echo"</td>";
										echo "</tr>";
									}
									?>
									</tbody>
								</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- //rutin -->
				<div role="tabpanel" class="tab-pane <?=$active2_rutin;?>" id="rutin">
					<br>
					<a href="<?php echo site_url('cost/excel_price_sup_stok') ?>" target='_blank' class="btn btn-sm btn-success" style='float:right;'>Download</a>
					<br><br>
					<div class="table-responsive">
					<table class="table table-bordered table-striped" id="example3" width='100%'>
						<thead width='100%'>
							<tr class='bg-blue'>
								<th class="text-center">#</th>
								<th class="text-center">Code Program</th>
								<th class="text-center">Material</th>
								<th class="text-center">Spesification</th> 
								<th class="text-center">Brand</th> 
								<th class="text-center">Unit</th> 
								<th class="text-center">Kurs</th> 
								<th class="text-center">Price Supplier</th>
								<th class="text-center">Expired</th>
								<th class="text-center">Status</th>
								<th class="text-center">Alasan Reject</th>
								<th class="text-center">Option</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach ($rutin as $key => $value) { $key++;
							//estimation
							$date_now 	= date('Y-m-d');
							$date_exp 	= $value['expired_supplier'];

							$tgl1x = new DateTime($date_now);
							$tgl2x = new DateTime($date_exp);
							$selisihx = $tgl2x->diff($tgl1x)->days + 1;

							$date_expv 	= date('d-M-Y', strtotime($date_exp));
							$date_min 	= date('d-M-Y', strtotime('-7 days', strtotime($date_exp)));
							// $selisih	= $date_expv->diff($date_now)->days;
							
							$waiting_app = '';
							if($value['app_price_sup'] == 'Y'){
								$waiting_app= "<br><span class='badge bg-purple'>Waiting Approval Price</span>";
							}
							
							$tambahan = "No Set";
							if($tgl2x < $tgl1x){
								$status2="Expired price";
								$tambahan = "<span class='badge bg-red'>$status2</span>".$waiting_app;
							}
							if($tgl2x >= $tgl1x AND $selisihx <= 7){
								$status2="Less one week expired price";
								$tambahan = "<span class='badge bg-blue'>$status2</span>".$waiting_app;
							}
							if($tgl2x >= $tgl1x AND $selisihx > 7){
								$tambahan = "<span class='badge bg-green'>Price Oke</span>".$waiting_app;
							}

							if(empty($date_exp)){
								$status2="Not Set";
								$tambahan = "<span class='badge bg-red'>$status2</span>";
								$date_expv 	= 'Not setting';
							}

							$PRE = (!empty($value['price_supplier']))?$value['price_supplier']:0;
							echo "<tr>";
								echo "<td>".$key."</td>";
								echo "<td>".strtoupper($value['code_group'])."</td>";
								echo "<td>".strtoupper($value['material_name'])."</td>";
								echo "<td>".strtoupper($value['spec'])."</td>";
								echo "<td>".strtoupper($value['brand'])."</td>";
								echo "<td>".strtoupper($value['unit_material'])."</td>";
								echo "<td>".strtoupper($value['kurs'])."</td>";
								echo "<td class='text-right'>".number_format($PRE,2)."</td>";
								echo "<td class='text-center'>".$date_expv."</td>";
								echo "<td>".$tambahan."</td>";
								echo "<td>".ucfirst(strtolower($value['reject_reason']))."</td>";
								echo "<td align='center'>"; 
											if($akses_menu['update']=='1'){
												echo"&nbsp;<a href='".site_url($this->uri->segment(1).'/edit_supplier_rutin/'.$value['id'])."' class='btn btn-sm btn-primary' title='Edit Data' data-role='qtip'><i class='fa fa-edit'></i></a>";
											}
										echo"</td>";
							echo "</tr>";
						}
						?>
						</tbody>
					</table>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

<?php $this->load->view('include/footer'); ?>
<style>
	th{
		vertical-align:middle !important;
	}
	#barang4_chosen,
	#brand4_chosen,
	#barang3_chosen,
	#brand3_chosen,
	#barang2_chosen,
	#brand2_chosen,
	#barang1_chosen,
	#brand1_chosen{
		margin-top: 5px;
		margin-right: 10px;
		width:300px !important;
	}

	#lokal_area_chosen,
	#lokal_dest_chosen,
	#lokal_truck_chosen{
		margin-top: 5px;
		margin-right: 10px;
		width:250px !important;
	}

	#lokal_kategori_chosen{
		margin-top: 5px;
		margin-right: 10px;
		width:150px !important;
	}
</style>
<script>
	$(document).ready(function(){
		$("#example2").DataTable();
		let baut_filter = {
			'barang1' : $('#barang1').val(),
			'brand1' : $('#brand1').val()
		};
		DataTables_bold_nut(baut_filter.barang1, baut_filter.brand1);
		
		let plate_filter = {
			'barang2' : $('#barang2').val(),
			'brand2' : $('#brand2').val()
		};
		DataTables_plate(plate_filter.barang2, plate_filter.brand2);
		
		let gasket_filter = {
			'barang3' : $('#barang3').val(),
			'brand3' : $('#brand3').val()
		};
		DataTables_gasket(gasket_filter.barang3, gasket_filter.brand3);
		
		let lainnya_filter = {
			'barang4' : $('#barang4').val(),
			'brand4' : $('#brand4').val()
		};
		DataTables_lainnya(lainnya_filter.barang4, lainnya_filter.brand4);

		let tanki_filter = {
			'barang4a' : $('#barang4a').val(),
			'brand4a' : $('#brand4a').val()
		};
		DataTables_tanki(tanki_filter.barang4a, tanki_filter.brand4a);
		
		$(document).on('change', '#barang1, #brand1', function(){
			let baut_filter = {
				'barang1' : $('#barang1').val(),
				'brand1' : $('#brand1').val()
			};
			DataTables_bold_nut(baut_filter.barang1, baut_filter.brand1);
		});
		
		$(document).on('change', '#barang2, #brand2', function(){
			let plate_filter = {
				'barang2' : $('#barang2').val(),
				'brand2' : $('#brand2').val()
			};
			DataTables_plate(plate_filter.barang2, plate_filter.brand2);
		});
		
		$(document).on('change', '#barang3, #brand3', function(){
			let gasket_filter = {
				'barang3' : $('#barang3').val(),
				'brand3' : $('#brand3').val()
			};
			DataTables_gasket(gasket_filter.barang3, gasket_filter.brand3);
		});
		
		$(document).on('change', '#barang4, #brand4', function(){
			let lainnya_filter = {
				'barang4' : $('#barang4').val(),
				'brand4' : $('#brand4').val()
			};
			DataTables_lainnya(lainnya_filter.barang4, lainnya_filter.brand4);
		});

		$(document).on('click','.boltnut', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'bolt nut'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.plate', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'plate'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.gasket', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'gasket'
				},
				cache		: false,
				dataType	: 'json',
			});
		});
		
		$(document).on('click','.lainnya', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'lainnya'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.lokal', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'lokal'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.eksport', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last',
				type		: "POST",
				data		: {
					'value1' : 'eksport'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.material', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last_header',
				type		: "POST",
				data		: {
					'value1' : 'material'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.accessories', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last_header',
				type		: "POST",
				data		: {
					'value1' : 'accessories'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.transport', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last_header',
				type		: "POST",
				data		: {
					'value1' : 'transport'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		$(document).on('click','.rutin', function(e){
			$.ajax({
				url: base_url + active_controller+'/tab_last_header',
				type		: "POST",
				data		: {
					'value1' : 'rutin'
				},
				cache		: false,
				dataType	: 'json',
			});
		});

		//truvking
		var category = $('#lokal_kategori').val();
		var area = $('#lokal_area').val();
		var dest = $('#lokal_dest').val();
		var truck = $('#lokal_truck').val();
		DataTables_trucking(category,area,dest,truck);

		$(document).on('change','#lokal_kategori, #lokal_area, #lokal_dest, #lokal_truck', function(){
			var category = $('#lokal_kategori').val();
            var area = $('#lokal_area').val();
            var dest = $('#lokal_dest').val();
            var truck = $('#lokal_truck').val();
			DataTables_trucking(category,area,dest,truck);
		});

		$("#example2, #example3, #example4").DataTable({
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			}
		});
	});

	function DataTables_bold_nut(nama=null,brand=null){
		var dataTable = $('#my-grid_bold_nut').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_bold_nut',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
	function DataTables_plate(nama=null,brand=null){
		var dataTable = $('#my-grid_plate').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_plate',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
	function DataTables_gasket(nama=null,brand=null){
		var dataTable = $('#my-grid_gasket').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_gasket',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
	function DataTables_lainnya(nama=null,brand=null){
		var dataTable = $('#my-grid_lainnya').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_lainnya',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}

	function DataTables_tanki(nama=null,brand=null){
		var dataTable = $('#my-grid_tanki').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)",
				"sZeroRecords": "No matching records found",
				"sEmptyTable": "No data available in table",
				"sLoadingRecords": "Please wait - loading...",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_tanki',
				type: "post",
				data: function(d){
					d.nama = nama,
					d.brand = brand
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}

	//trucking
	function DataTables_trucking(category = null, area = null, dest = null, truck = null){

		var dataTable = $('#my-grid_lokal').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"processing": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/data_side_trucking',
				type: "post",
				data: function(d){
					d.category 	= category,
					d.area 	= area,
					d.dest 	= dest,
					d.truck = truck
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
</script>