<?php
$this->load->view('include/side_menu_dashboard'); 
?>
	
    <!-- Content Header (Page header) -->
		<section class="content-header">
		  <h1>
			<?=$title;?>
		  </h1>
		  <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Dashboard</li>
		  </ol>
		</section>
		<!-- Main content -->
		<section class="content">

			<div class='row' style='font-size: 20px;'>
			
				<div class="col-lg-6 col-xs-6">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h2 class="box-title"  style='font-size: 24px;'>Finish <small>Update on <?= date('l, d F Y', strtotime($finish[0]->created_date));?></small></h2>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<!-- /.box-body -->
						<?php
						$rasio = 0;
						if($finish[0]->real_harga > 0 AND $finish[0]->est_harga > 0){
							$rasio = $finish[0]->real_harga / $finish[0]->est_harga;
						}
						if(number_format(($rasio) * 100) > 100){
							$warna = 'badge bg-red';
						}
						else{
							$warna = 'badge bg-green';
						}
						
						if(number_format(($finish[0]->est_harga - $finish[0]->real_harga)) < 0){
							$warna2 = 'badge bg-red';
						}
						else{
							$warna2 = 'badge bg-green';
						}
						
						?>
						<div class="box-footer  no-padding">
							<table class="table table-condensed"  style='font-size: 22px;'>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="<?=$warna;?>" style='font-size: 20px;'><?= number_format(($rasio) * 100);?> %</span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Digunakan Untuk Project Yang Selesai</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="badge bg-blue" style='font-size: 20px;'> $ <?= number_format($finish[0]->real_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Tergunakan</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="badge bg-blue" style='font-size: 20px;'> $ <?= number_format($finish[0]->est_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Estimasi</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="<?=$warna2;?>" style='font-size: 20px;'> $ <?= number_format($finish[0]->est_harga - $finish[0]->real_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Balance</a></td>
								</tr>
							</table>
						</div>
						<!-- /.footer -->
					</div>
				</div>
				
				<div class="col-lg-6 col-xs-6">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h2 class="box-title" style='font-size: 24px;'>Finish <small>Update on <?= date('l, d F Y', strtotime($finish[0]->created_date));?></small></h2>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<!-- /.box-body -->
						<div class="box-footer no-padding">
							<table class="table table-condensed"  style='font-size: 22px;'>
								<tr>
									<td width='10%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/cost_control/1') ?>" target='_blank'><span class="badge bg-red" style='font-size: 20px;'><?= $overbudget[0]->jumlah;?></span></a></td>
									<td><a href="<?php echo site_url('cost_control/cost_control/1') ?>" target='_blank'>Project Pemakaian Material Over Budget</a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/cost_control/2') ?>" target='_blank'><span class="badge bg-green" style='font-size: 20px;'><?= $goodbudget1[0]->jumlah;?></span></a></td>
									<td><a href="<?php echo site_url('cost_control/cost_control/2') ?>" target='_blank'>Project Pemakaian Material Sesuai Standard ( 90% - 100% )</a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/cost_control/3') ?>" target='_blank'><span class="badge bg-yellow" style='font-size: 20px;'><?= $goodbudget2[0]->jumlah;?></span></a></td>
									<td><a href="<?php echo site_url('cost_control/cost_control/3') ?>" target='_blank'>Project Pemakaian Material Dibawah Standard ( < 90% )</a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'></td>
									<td>&nbsp;</td>
								</tr>
							</table>
						</div>
						<!-- /.footer -->
					</div>
				</div>
				
				<!--PROGRESS-->
				<div class="col-lg-6 col-xs-6">
					<div class="box box-primary">
						<div class="box-header with-border">
							<h2 class="box-title"  style='font-size: 24px;'>On Progress <small>Update on <?= date('l, d F Y', strtotime($finish2[0]->created_date));?></small></h2>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<!-- /.box-body -->
						<?php
						if(number_format(($finish2[0]->real_harga / $finish2[0]->est_harga) * 100) > 100){
							$warna = 'badge bg-red';
						}
						else{
							$warna = 'badge bg-green';
						}
						
						if(number_format(($finish2[0]->est_harga - $finish2[0]->real_harga)) < 0){
							$warna2 = 'badge bg-red';
						}
						else{
							$warna2 = 'badge bg-green';
						}
						
						?>
						<div class="box-footer  no-padding">
							<table class="table table-condensed"  style='font-size: 22px;'>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="<?=$warna;?>" style='font-size: 20px;'><?= number_format(($finish2[0]->real_harga / $finish2[0]->est_harga) * 100);?> %</span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Digunakan Untuk Project Yang Selesai</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="badge bg-blue" style='font-size: 20px;'> $ <?= number_format($finish2[0]->real_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Tergunakan</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="badge bg-blue" style='font-size: 20px;'> $ <?= number_format($finish2[0]->est_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Material Estimasi</a></td>
								</tr>
								<tr>
									<td width='25%' style='text-align:right; padding-right:20px;'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'><span class="<?=$warna2;?>" style='font-size: 20px;'> $ <?= number_format($finish2[0]->est_harga - $finish2[0]->real_harga);?></span></a></td>
									<td width='75%'><a href="<?php echo site_url('cost_control/print_hasil_finish_project') ?>" target='_blank'>Balance</a></td>
								</tr>
							</table>
						</div>
						<!-- /.footer -->
					</div>
				</div>
				
				
				<div class="col-lg-6 col-xs-6">
				
				</div>
				
				
				
				<div class="col-lg-12 col-xs-12">
					<div class="box box-success">
						<div class="box-header with-border">
						  <h3 class="box-title">Chart <?= date('Y');?></h3>
						  <div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						  </div>
						</div>
						<div class="box-body">
						  <div class="chart">
							<div id="chart1" height="260"></div>
						  </div>
						</div><!-- /.box-body -->
					</div>
				</div>
				
			</div>
			
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Total Used</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Category</th>
					<?php
					$dateNow = date('Y-m-d');
					$date  = date('Y-m-d', strtotime('-5 month', strtotime($dateNow)));
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("F-Y", strtotime("+".$a." month", strtotime($date)));
						echo "<th class='text-right'>".$loop_date."</th>";
					}
					?>
                    
                  </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td>AKTUAL MATERIAL</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.real_material) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>DIRECT LABOUR</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.direct_labour) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>INDIRECT LABOUR</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.indirect_labour) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <td>CONSUMABLE</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.consumable) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <td>MACHINE</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.machine) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <td>MOULD & MANDRILL</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.mould_mandrill) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <td>FOH</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.foh_consumable + a.foh_depresiasi + a.biaya_gaji_non_produksi + a.biaya_non_produksi + a.biaya_rutin_bulanan) AS real_m FROM laporan_per_hari a WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
		  
		</section>
		

<?php $this->load->view('include/footer_dashboard'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/loader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/Chart.bundle.js'); ?>"></script>
<script>
	google.charts.load('current',{'packages':['line']});
	google.charts.setOnLoadCallback(drawChart1);
	$(document).ready(function(){
		// drawChart1();
		$('.btn').click(function(){
			$('#spinner').modal('show');
		});

	});
	
	function drawChart1() 
	{
		$.ajax({
			type		: "POST",
			url 		: base_url+active_controller+'/rpt_report_revenue_json_chart1',
			dataType	: "json",
			success: function(datas)
			{
				var datax = google.visualization.arrayToDataTable(datas);
				var options = {
									curveType	: 'function',
									legend		: { position: 'bottom' },
									bars		: 'vertical',
									vAxis		: {format:'decimal'},
									height		: 350
				};

			var chart = new google.charts.Line(document.getElementById('chart1'));

			chart.draw(datax, options);
			}
		});
	}
</script>
