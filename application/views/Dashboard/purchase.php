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
                                <p>Coming Soon</p>
						</div>
					</div>
				</div>
			</div>
		</section>
		

<?php $this->load->view('include/footer_dashboard'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/loader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/Chart.bundle.js'); ?>"></script>
