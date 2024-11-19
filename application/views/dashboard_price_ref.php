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
					<div class="box box-success">
						<div class="box-header with-border">
							<h2 class="box-title"  style='font-size: 24px;'>A. Material</h2>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-footer  no-padding">
							<table class="table table-condensed"  style='font-size: 22px;'>
								<tr>
									<td width='15%' style='text-align:right; padding-right:20px;'><span class="badge bg-blue" style='font-size: 20px;'><?=$sum_material;?></span></td>
									<td width='65%'><span class='text-blue'>Total Material</span></td>
									<td width='20%'><a href='<?php echo site_url('dashboard/print_price_ref/all') ?>' target='_blank'><span class='text-blue'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-green" style='font-size: 20px;'><?=$expired;?></span></td>
									<td><span class='text-green'>Price OKE</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref/oke') ?>' target='_blank'><span class='text-green'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-purple" style='font-size: 20px;'><?=$hampir_exp;?></span></td>
									<td><span class='text-purple'>Expired (Less One Week)</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref/less') ?>' target='_blank'><span class='text-purple'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-red" style='font-size: 20px;'><?=$price_oke;?></span></td>
									<td><span class='text-red'>Expired Price</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref/expired') ?>' target='_blank'><span class='text-red'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-yellow" style='font-size: 20px;'><?=$waiting_approval;?></span></td>
									<td><a href='<?php echo site_url('cost/material/approve/material') ?>'><span class='text-yellow'>Waiting Approval</span></a></td>
									<td><a href='<?php echo site_url('cost/material/approve/material') ?>'><span class='text-yellow'>To Link</span></a></td>
								</tr>
							</table>
						</div>
						<!-- /.footer -->
					</div>
				</div>
				<!-- Accsessories -->
				<div class="col-lg-6 col-xs-6">
					<div class="box box-success">
						<div class="box-header with-border">
							<h2 class="box-title"  style='font-size: 24px;'>B. Accessories</h2>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
								</button>
								<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
							</div>
						</div>
						<div class="box-footer  no-padding">
							<table class="table table-condensed"  style='font-size: 22px;'>
								<tr>
									<td width='15%' style='text-align:right; padding-right:20px;'><span class="badge bg-blue" style='font-size: 20px;'><?=$sum_material_acc;?></span></td>
									<td width='65%'><span class='text-blue'>Total Accessories</span></td>
									<td width='20%'><a href='<?php echo site_url('dashboard/print_price_ref_acc/all') ?>' target='_blank'><span class='text-blue'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-green" style='font-size: 20px;'><?=$expired_acc;?></span></td>
									<td><span class='text-green'>Price OKE</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref_acc/oke') ?>' target='_blank'><span class='text-green'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-purple" style='font-size: 20px;'><?=$hampir_exp_acc;?></span></td>
									<td><span class='text-purple'>Expired (Less One Week)</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref_acc/less') ?>' target='_blank'><span class='text-purple'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-red" style='font-size: 20px;'><?=$price_oke_acc;?></span></td>
									<td><span class='text-red'>Expired Price</span></td>
									<td><a href='<?php echo site_url('dashboard/print_price_ref_acc/expired') ?>' target='_blank'><span class='text-red'>Download</span></a></td>
								</tr>
								<tr>
									<td style='text-align:right; padding-right:20px;'><span class="badge bg-yellow" style='font-size: 20px;'><?=$waiting_approval_acc;?></span></td>
									<td><a href='<?php echo site_url('cost/material/approve/accessories') ?>'><span class='text-yellow'>Waiting Approval</span></a></td>
									<td><a href='<?php echo site_url('cost/material/approve/accessories') ?>'><span class='text-yellow'>To Link</span></a></td>
								</tr>
							</table>
						</div>
						<!-- /.footer -->
					</div>
				</div>
			  </div>
			  <!-- /.row -->
		  
		</section>
		

<?php $this->load->view('include/footer_dashboard'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/loader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/Chart.bundle.js'); ?>"></script>
