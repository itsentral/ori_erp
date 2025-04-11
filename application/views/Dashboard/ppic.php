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
                       </div>
					</div>
				</div>
			</div>
		</section>
		

<?php $this->load->view('include/footer_dashboard'); ?>
<script type="text/javascript" src="<?php echo base_url('assets/loader.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/Chart.bundle.js'); ?>"></script>
