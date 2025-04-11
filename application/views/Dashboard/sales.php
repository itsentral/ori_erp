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
                                <!-- Sales -->
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="small-box bg-aqua">
                                                <div class="inner">
                                                <h3><?= $late_enggenering;?></h3>
                                                <p>Late Estimation</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('dashboard/print_late_eng') ?>" target='_blank' class="small-box-footer">Print <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-green">
                                                <div class="inner">
                                                <h3><?= $late_costing;?></h3>
                                                <p>Late Costing</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('dashboard/print_late_cos') ?>" target='_blank' class="small-box-footer">Print <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-orange">
                                                <div class="inner">
                                                <h3><?= $late_quotation;?></h3>
                                                <p>Waiting Approval Quotation</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('sales/approve_quotation') ?>" target='_blank' class="small-box-footer">To Link <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-purple">
                                                <div class="inner">
                                                <h3>$ <?= number_format($total_quotation,2);?></h3>
                                                <p>Total Quotation <?=date('Y')?></p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('dashboard/print_total_quotation') ?>" target='_blank' class="small-box-footer">Print <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="small-box bg-blue">
                                                <div class="inner">
                                                <h3>$ <?= number_format($total_so,2);?></h3>
                                                <p>Total Sales Order <?=date('Y')?></p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('dashboard/print_total_sales_order') ?>" target='_blank' class="small-box-footer">Print <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div>

                                        <!-- <div class="col-lg-4">
                                            <div class="small-box bg-red">
                                                <div class="inner">
                                                <h3>Coming Soon </h3>
                                                <p>Cancel Order</p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="<?php echo site_url('dashboard') ?>" target='_blank' class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                                            </div>
                                        </div> -->
                                        <div class="col-lg-4">
                                            <div class="small-box bg-red">
                                                <div class="inner">
                                                <h3><?=number_format($ttl_inv->ttl_inv); ?></h3>
                                                <p>Jumlah Invoice <?=date("Y")?></p>
                                                </div>
                                                <div class="icon">
                                                <i class="ion ion-stats-bars"></i>
                                                </div>
                                                <a href="#()" class="small-box-footer detail">More info <i class="fa fa-arrow-circle-right"></i></a>
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
<script>
	$(document).on('click', '.detail', function(e){
		e.preventDefault();
		loading_spinner();
		$(".modal-title").html("<b>LIST INVOICE</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/list_invoice_dash/',
			success:function(data){
				$("#Mymodal").modal();
				$("#listCoa").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
</script>