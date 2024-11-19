<?php
$this->load->view('include/side_menu_dashboard'); 
?>
	
    <!-- Content Header (Page header) -->
		<section class="content-header">
		  <h1>
			<?=$title;?>
			<small></small>
		  </h1>
		  <ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="active">Dashboard</li>
		  </ol>
		</section>
		<!-- Main content -->
		<section class="content">
			<!-- <div class="row">
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-aqua">
					<div class="inner">
					  <h3><?=$qty_ipp;?></h3>
					  <p>IPP terbit dari Sales</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_ipp_by_sales') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
				
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-green">
					<div class="inner">
					  <h3><?=$qty_eng;?></h3>
					  <p>IPP sudah diestimasi oleh Engineering</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_ipp_est_by_eng') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
				
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-yellow">
					<div class="inner">
					  <h3><?=$qty_late;?></h3>
					  <p>IPP lebih dari 3 hari belum diestimasi</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_late_project') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
				
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-red">
					<div class="inner">
					  <h3><?=$qty_cost;?></h3>
					  <p>IPP dalam process Costing</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_ipp_costing') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
			
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-purple">
					<div class="inner">
					  <h3><?=$qty_quo;?></h3>
					  <p>IPP sudah diterbitkan ke Sales</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_ipp_terbit_ke_sales') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
				
				<div class="col-lg-3 col-xs-6">
				  <div class="small-box bg-blue">
					<div class="inner">
					  <h3><?=$qty_quo2;?></h3>
					  <p>IPP belum dibuatkan Penawaran</p>
					</div>
					<div class="icon">
					  <i class="ion ion-stats-bars"></i>
					</div>
					<a href="<?php echo site_url('dashboard/excel_ipp_quotation') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
				  </div>
				</div>
			</div> -->
		
		
			<!-- TABLE: LATEST ORDERS -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Total Production (Kg)</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Product Category</th>
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
                    <td>PIPE</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.real_material) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND b.type='pipe' ")->result();
						$real_m = number_format($count[0]->real_m,3);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>FITTING</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.real_material) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND b.type='fitting' ")->result();
						$real_m = number_format($count[0]->real_m,3);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>JOINT</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.real_material) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND (b.type='joint' OR b.type='field') ")->result();
						$real_m = number_format($count[0]->real_m,3);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <th>TOTAL MATERIAL</th>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.real_material) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,3);
						// $real_m = 0;
						echo "<th class='text-right'>".$real_m."</th>";
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
	
		
	
		<!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Total Man Hours Used</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                  <tr>
                    <th>Product Category</th>
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
                    <td>PIPE</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.man_hours) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND b.type='pipe' ")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>FITTING</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.man_hours) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND b.type='fitting' ")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
                  <tr>
                    <td>JOINT</td>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.man_hours) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%' AND (b.type='joint' OR b.type='field') ")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<td class='text-right'>".$real_m."</td>";
					}
					?>
                  </tr>
				  <tr>
                    <th>TOTAL MAN HOURS</th>
                    <?php
					for ($a=0; $a<6; $a++) {
						$loop_date  = date("Y-m", strtotime("+".$a." month", strtotime($date)));
						$count = $this->db->query("SELECT SUM(a.man_hours) AS real_m FROM laporan_per_hari a LEFT JOIN product_parent b ON a.id_category=b.product_parent WHERE a.`date` LIKE '%".$loop_date."%'")->result();
						$real_m = number_format($count[0]->real_m,2);
						// $real_m = 0;
						echo "<th class='text-right'>".$real_m."</th>";
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
<script>
	$(document).ready(function(){
		$('.btn').click(function(){
			$('#spinner').modal('show');
		});
	});
</script>
