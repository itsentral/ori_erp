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
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item active">
                                    <a class="nav-link active" id="sales-tab" data-toggle="tab" href="#sales" role="tab" aria-controls="sales" aria-selected="true">Sales</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="finance-tab" data-toggle="tab" href="#finance" role="tab" aria-controls="finance" aria-selected="false">Finance</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="purchase-tab" data-toggle="tab" href="#purchase" role="tab" aria-controls="purchase" aria-selected="false">Purchase</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="material-tab" data-toggle="tab" href="#material" role="tab" aria-controls="material" aria-selected="false">Material</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="eng-tab" data-toggle="tab" href="#eng" role="tab" aria-controls="eng" aria-selected="false">Engineering</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <!-- Sales -->
                                <div class="tab-pane active" id="sales" role="tabpanel" aria-labelledby="sales-tab">
                                    <br>
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
                                <!-- Finance -->
                                <div class="tab-pane" id="finance" role="tabpanel" aria-labelledby="finance-tab">
                                    <br>
                                    <!-- Material -->
                                    <div class="col-lg-6 col-xs-6">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>A. Material</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18;'>
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
                                                        <td><a href='<?php echo site_url('cost/material/approve/material') ?>' target='_blank'><span class='text-yellow'>Waiting Approval</span></a></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/material') ?>' target='_blank'><span class='text-yellow'>To Link</span></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>
                                    <!-- Accsessories -->
                                    <div class="col-lg-6 col-xs-6">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>B. Accessories</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18;'>
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
                                                        <td><a href='<?php echo site_url('cost/material/approve/accessories') ?>' target='_blank'><span class='text-yellow'>Waiting Approval</span></a></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/accessories') ?>' target='_blank'><span class='text-yellow'>To Link</span></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>

                                    <!-- Transport -->
                                    <div class="col-lg-6 col-xs-6">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>C. Transport</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18;'>
                                                    <tr>
                                                        <td width='15%' style='text-align:right; padding-right:20px;'><span class="badge bg-blue" style='font-size: 20px;'><?=$sum_material_trans;?></span></td>
                                                        <td width='65%'><span class='text-blue'>Total Transport</span></td>
                                                        <td width='20%'><a href='<?php echo site_url('dashboard/print_price_ref_trans/all') ?>' target='_blank'><span class='text-blue'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-green" style='font-size: 20px;'><?=$expired_trans;?></span></td>
                                                        <td><span class='text-green'>Price OKE</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_trans/oke') ?>' target='_blank'><span class='text-green'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-purple" style='font-size: 20px;'><?=$hampir_exp_trans;?></span></td>
                                                        <td><span class='text-purple'>Expired (Less One Week)</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_trans/less') ?>' target='_blank'><span class='text-purple'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-red" style='font-size: 20px;'><?=$price_oke_trans;?></span></td>
                                                        <td><span class='text-red'>Expired Price</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_trans/expired') ?>' target='_blank'><span class='text-red'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-yellow" style='font-size: 20px;'><?=$waiting_approval_trans;?></span></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/transport') ?>' target='_blank'><span class='text-yellow'>Waiting Approval</span></a></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/transport') ?>' target='_blank'><span class='text-yellow'>To Link</span></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>
                                    <!-- Rutin -->
                                    <div class="col-lg-6 col-xs-6">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>D. Rutin</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18;'>
                                                    <tr>
                                                        <td width='15%' style='text-align:right; padding-right:20px;'><span class="badge bg-blue" style='font-size: 20px;'><?=$sum_material_rutin;?></span></td>
                                                        <td width='65%'><span class='text-blue'>Total Rutin</span></td>
                                                        <td width='20%'><a href='<?php echo site_url('dashboard/print_price_ref_rutin/all') ?>' target='_blank'><span class='text-blue'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-green" style='font-size: 20px;'><?=$expired_rutin;?></span></td>
                                                        <td><span class='text-green'>Price OKE</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_rutin/oke') ?>' target='_blank'><span class='text-green'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-purple" style='font-size: 20px;'><?=$hampir_exp_rutin;?></span></td>
                                                        <td><span class='text-purple'>Expired (Less One Week)</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_rutin/less') ?>' target='_blank'><span class='text-purple'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-red" style='font-size: 20px;'><?=$price_oke_rutin;?></span></td>
                                                        <td><span class='text-red'>Expired Price</span></td>
                                                        <td><a href='<?php echo site_url('dashboard/print_price_ref_rutin/expired') ?>' target='_blank'><span class='text-red'>Download</span></a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style='text-align:right; padding-right:20px;'><span class="badge bg-yellow" style='font-size: 20px;'><?=$waiting_approval_rutin;?></span></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/rutin') ?>' target='_blank'><span class='text-yellow'>Waiting Approval</span></a></td>
                                                        <td><a href='<?php echo site_url('cost/material/approve/rutin') ?>' target='_blank'><span class='text-yellow'>To Link</span></a></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>
                                </div>
                                <!-- Purchase -->
                                <div class="tab-pane" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                                    <br>
                                    <p>Coming Soon</p>
                                </div>
                                <!-- Material -->
                                <div class="tab-pane" id="material" role="tabpanel" aria-labelledby="material-tab">
                                    <br>
                                    <div class="col-xs-12" hidden>
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>A. Material</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18px;'>
                                                    <thead>
                                                        <tr>
                                                            <th width='5%'>#</th>
                                                            <th>Kategori</th>
                                                            <th class='text-right' width='20%'>Stock</th>
                                                            <th class='text-right' width='20%'>Booking</th>
                                                            <th class='text-right' width='20%'>Purchase Request</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            foreach ($list_category as $key => $value) { $key++;
                                                                echo "<tr>";
                                                                    echo "<td>".$key."</td>";
                                                                    echo "<td>".$value['category']."</td>";
                                                                    echo "<td class='text-right'>0</td>";
                                                                    echo "<td class='text-right'>0</td>";
                                                                    echo "<td class='text-right'>0</td>";
                                                                echo "</tr>";
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>
                                    <!-- Resin Expired -->
                                    <div class="col-xs-12">
                                        <div class="box box-info">
                                            <div class="box-header with-border">
                                                <h2 class="box-title"  style='font-size: 20px;'>A. Expired Material</h2>
                                            </div>
                                            <div class="box-footer  no-padding">
                                                <table class="table table-condensed"  style='font-size: 18px;'>
                                                    <thead>
                                                        <tr>
                                                            <th width='5%'>#</th>
                                                            <th>Resin</th>
                                                            <th class='text-right' width='12%'>Stock</th>
                                                            <th class='text-right' width='12%'>Expired Date</th>
                                                            <th class='text-right' width='18%'>Status</th>
                                                            <th class='text-right' width='12%'>Warehouse</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            foreach ($list_expired as $key => $value) { $key++;
                                                                $tgl1 = new DateTime($value['expired']);
                                                                $tgl2 = new DateTime(date('Y-m-d'));
                                                                $d = $tgl2->diff($tgl1)->days;
                                                                $ket = "Expired";
                                                                $warna = "red";
                                                                if($tgl1 > $tgl2){
                                                                    $ket = "Expired dalam";
                                                                    $warna = "green";
                                                                }
                                                                echo "<tr>";
                                                                    echo "<td class='text-".$warna."''>".$key."</td>";
                                                                    echo "<td class='text-".$warna."''>".$value['nm_material']."</td>";
                                                                    echo "<td class='text-right text-".$warna."''>".number_format($value['stock'],2)."</td>";
                                                                    echo "<td class='text-right text-".$warna."''>".date('d-M-Y', strtotime($value['expired']))."</td>";
                                                                    echo "<td class='text-right text-".$warna."'>".$ket." ".$d." Hari</td>";
                                                                    echo "<td class='text-right text-".$warna."''>".get_name('warehouse','kd_gudang','id',$value['id_gudang'])."</td>";
                                                                echo "</tr>";
                                                            }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.footer -->
                                        </div>
                                    </div>
                                </div>
                                <!-- EEngineering -->
                                <div class="tab-pane" id="eng" role="tabpanel" aria-labelledby="eng-tab">
                                    <br>
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