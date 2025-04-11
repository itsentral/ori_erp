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
				<div class="col-lg-12">
					<div class="box box-info">
						<div class="box-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                <h3><?= $api_app_bq;?></h3>
                                                <p>Waiting BQ Approval</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('machine/approve_bq') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-green">
                                                <div class="inner">
                                                <h3><?= $api_app_est;?></h3>
                                                <p>Waiting Estimation Approval</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('machine/approve_est') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-orange">
                                                <div class="inner">
                                                <h3><?= $api_app_est;?></h3>
                                                <p>Approval Revised Estimation</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('machine/approve_est') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-purple">
                                                <div class="inner">
                                                <h3><?=$api_app_est_fd;?></h3>
                                                <p>Waiting Final Drawing</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('final_drawing') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-blue">
                                                <div class="inner">
                                                <h3><?= $api_app_est_fd_parsial;?></h3>
                                                <p>Parsial Process</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('final_drawing/approve') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>
                                        
                                    </div>
                       </div>
					</div>
				</div>
			</div>
		</section>
		

<?php $this->load->view('include/footer_dashboard'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/loader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/Chart.bundle.js'); ?>"></script>
