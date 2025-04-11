
<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>No. Surat Jalan <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('type'=>'hidden','id'=>'kode_delivery','name'=>'kode_delivery','class'=>'form-control input-md'), $kode_delivery);
					echo form_input(array('id'=>'nomor_sj','name'=>'nomor_sj','class'=>'form-control input-md'), $header[0]->nomor_sj);
				
				?>
			</div>
			<label class='label-control col-sm-2'><b>Alamat <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_textarea(array('id'=>'alamat','name'=>'alamat','class'=>'form-control input-md','rows'=>3,'placeholder'=>'Alamat Delivery'), $header[0]->alamat);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Delivery Date <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'delivery_date','name'=>'delivery_date','class'=>'form-control input-md','readonly'=>true), $header[0]->delivery_date);
				?>
			</div>
			<label class='label-control col-sm-2'><b>Project <span class='text-red'>*</span></b></label>
			<div class='col-sm-4'>
				<?php
					echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>3,'placeholder'=>'Project'), $header[0]->project);
				?>
			</div>
		</div>
		<div class='form-group row'>
			<div class='col-sm-12'>
				<h4>EDIT DESCRIPTION</h4>
				<table class="table table-sm table-bordered table-striped" width='100%' border='1' cellpadding='2'>
					<tr>
						<th class='text-center' width='3%'>#</th>
						<th class='text-center' width='8%'>QTY</th>
						<th class='text-center' width='5%'>UNIT</th>
						<th class='text-center' width='35%' style='vertical-align:middle;'>NAMA DAN UKURAN BARANG</th>
						<th class='text-center' style='vertical-align:middle;'>DESC</th>
					</tr>
					<?php
					if(!empty($result_print)){
						$NOMOR = 0;
						foreach($result_print AS $val => $valx){ $val++; $NOMOR++;
							$series 	= get_name('so_detail_header','series','id',$valx['id_milik']);
							$product 	= strtoupper($valx['product']).", ".$series.", DIA ".spec_bq2($valx['id_milik']);
							$SATUAN 	= 'PCS';
							$QTY 		= $valx['qty_product'];

							$ID_MILIK 	= (!empty($GET_ID_MILIK[$valx['id_milik']]))?$GET_ID_MILIK[$valx['id_milik']]:'';
							$GET_DESC 	= (!empty($GET_DESC_DEAL[$ID_MILIK]))?$GET_DESC_DEAL[$ID_MILIK]:'';
							$ID_UNIQ 	= $valx['id_milik'];
							if($valx['sts_product'] == 'so material'){
								$product 	= strtoupper(get_name('raw_materials','nm_material','id_material',$valx['product']));
								$SATUAN 	= 'KG';
								$QTY 		= number_format($valx['berat'],4);
								$ID_MILIK 	= '';
								$GET_DESC 	= '';
								$ID_UNIQ 	= $valx['id_uniq'];
							}

							$DESC = (!empty($valx['desc']))?$valx['desc']:$GET_DESC;
							echo "<tr>";
								echo "<td align='center'>".$NOMOR."</td>";
								echo "<td align='center'>".$QTY."</td>";
								echo "<td align='center'>".strtolower($SATUAN)."</td>";
								echo "<td align='left'>".$product."</td>";
								echo "<td align='left'>";
								if($valx['sts_product'] == 'so material'){
									echo "<input type='hidden' name='edit_desc_mat[$val][id_milik]' class='form-control' value='".$ID_UNIQ."'>";
									echo "<input type='text' name='edit_desc_mat[$val][desc]' class='form-control' value='".$DESC."'>";
								}
								else{
									echo "<input type='hidden' name='edit_desc[$val][id_milik]' class='form-control' value='".$ID_UNIQ."'>";
									echo "<input type='text' name='edit_desc[$val][desc]' class='form-control' value='".$DESC."'>";
								}
								echo "</td>";
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td align='left' colspan='5'>List Empty</td>";
						echo "</tr>";
					}
					?>
				</table>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-4'>
				<button type='button' class='btn btn-sm btn-primary' id='update_print'>Update Setting Print</button>
			</div>
			<label class='label-control col-sm-2'><b></b></label>
			<div class='col-sm-4'>
				
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-4'>
			</div>
			<label class='label-control col-sm-2'><b></b></label>
			<div class='col-sm-4 text-right'>
				<a href="<?php echo site_url('delivery') ?>" class="btn btn-sm btn-default" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        		<button type='button' class='btn btn-sm btn-danger' style='float:right; margin-bottom:10px;' id='delete_spool'><i class='fa fa-times'></i>&nbsp;Delete Delivery</button>
			</div>
		</div>
		<?php if(!empty($result)){?>
        <h4>LOOSE</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">ID Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result as $key => $value) { $key++;
					$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
				
                    $IMPLODE = explode('.', $value['product_code']);
                    $product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE; 
					if($value['sts_product'] == 'so material'){
                        $product_code = '';
                    }

					$PRODUCT = strtoupper($value['product']);
                    if($value['sts_product'] == 'so material'){
                        $PRODUCT = strtoupper(get_name('raw_materials','nm_material','id_material',$value['product']));
                    }

					$SPEC = spec_bq3($value['id_milik']);
                    if($value['sts_product'] == 'so material'){
                        $SPEC = number_format($value['berat'],2).' kg';
                    }
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                        echo "<td align='left'>".$PRODUCT."</td>";
                        echo "<td align='left'>".$SPEC."</td>";
                        echo "<td align='center'>".$product_code."</td>";
                        echo "<td align='center'>".$value['no_spk']."</td>";
						if($value['sts'] == 'loose'){
                        	echo "<td align='center'><input type='checkbox' name='check[".$value['id_uniq']."]' class='chk_personal' value='".$value['id_uniq']."&".$value['id']."' ></td>";
						}
						if($value['sts'] == 'cut'){
							echo "<td align='center'><input type='checkbox' name='check_cut[".$value['id_uniq']."]' class='chk_personal' value='".$value['id_uniq']."&".$value['id']."' ></td>";
						}	
						if($value['sts_product'] == 'so material'){
							echo "<td align='center'></td>";
						}	
					echo "</tr>";
                }
                ?>
            </tbody>
        </table>
		<?php } ?>
		<?php if(!empty($result2)){?>
		<h4>SPOOL</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">ID Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result2 as $key => $value) { $key++;
				
                    $IMPLODE = explode('.', $value['product_code']);
                    $product_code = $IMPLODE[0].'.'.$value['product_ke'];

					$get_split_ipp = $this->db->order_by('id','asc')->get_where('delivery_product_detail',array('kode_delivery'=>$value['kode_delivery'],'kode_spool'=>$value['kode_spool'],'spool_induk'=>$value['spool_induk']))->result_array();
					$ArrNo_Spool = [];
					$ArrNo_IPP = [];
					$ArrNo_SPK = [];
					$ArrNo_ID = [];
					foreach ($get_split_ipp as $key2 => $value2) { $key2++;
						$LENGTH = '';
						if($value2['product'] == 'pipe'){
							$no_spk_list = $this->db->select('length')->get_where('so_detail_header',array('id'=>$value2['id_milik']))->result();
							$LENGTH = ($value2['sts'] == 'cut')?number_format($value2['length']):number_format($no_spk_list[0]->length);
						}

						$ArrNo_IPP[] = $key2.'. '.strtoupper($value2['product'].' '.$LENGTH);
						$ArrNo_Spool[] = $key2.'. '.strtoupper(spec_bq2($value2['id_milik']));
						
						$CUTTING_KE = (!empty($value2['cutting_ke']))?'.'.$value2['cutting_ke']:'';
						
						$IMPLODE = explode('.', $value2['product_code']);
						$ArrNo_SPK[] = $key2.'. '.$value2['no_spk'];
						$ArrNo_ID[] = $key2.'. '.$IMPLODE[0].'.'.$value2['product_ke'].$CUTTING_KE;
					}
					// print_r($ArrGroup); exit;
					$explode_spo = implode('<br>',$ArrNo_Spool);
					$explode_ipp = implode('<br>',$ArrNo_IPP);
					$explode_spk = implode('<br>',$ArrNo_SPK);
					$explode_id = implode('<br>',$ArrNo_ID);
					
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='left'>".$value['spool_induk']."-".$value['kode_spool']."<br>".$value['no_drawing']."</td>";
                        echo "<td align='left'>".$explode_ipp."</td>";
                        echo "<td align='left'>".$explode_spo."</td>";
                        echo "<td align='left'>".$explode_id."</td>";
                        echo "<td align='left'>".$explode_spk."</td>";
                        echo "<td align='center'><input type='checkbox' name='check2[".$value['id_uniq']."]' class='chk_personal' value='".$value['spool_induk']."&".$value['kode_spool']."' ></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
		<?php } ?>
		<?php if(!empty($result3)){?>
        <h4>SO MATERIAL</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Material Name</th>
                    <th class="text-center">Berat (kg)</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result3 as $key => $value) { $key++;
					$PRODUCT = strtoupper(get_name('raw_materials','nm_material','id_material',$value['product']));
					$SPEC = number_format($value['berat'],2);
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                        echo "<td align='left'>".$PRODUCT."</td>";
                        echo "<td align='center'>".$SPEC."</td>";
						echo "<td align='center'><input type='checkbox' name='check3[".$value['id_uniq']."]' class='chk_personal' value='".$value['id_uniq']."&".$value['id']."'></td>";
					echo "</tr>";
                }
                ?>
            </tbody>
        </table>
		<?php } ?>
		<?php if(!empty($result4)){?>
        <h4>MATERIAL FIELD JOINT</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Material Name</th>
                    <th class="text-center">Berat (kg)</th>
                    <th class="text-center no-sort">#</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result4 as $key => $value) { $key++;
					$PRODUCT = strtoupper(get_name('raw_materials','nm_material','id_material',$value['product']));
					$SPEC = number_format($value['berat'],2);
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                        echo "<td align='left'>".$PRODUCT."</td>";
                        echo "<td align='center'>".$SPEC."</td>";
						echo "<td align='center'><input type='checkbox' name='check4[".$value['id_uniq']."]' class='chk_personal' value='".$value['id_uniq']."&".$value['id']."'></td>";
					echo "</tr>";
                }
                ?>
            </tbody>
        </table>
		<?php } ?>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$('#delivery_date').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true
		});

		$(document).on('click', '#delete_spool', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist product minimal 1',
					type	: "warning"
				});
				return false;
			}
			// return false;
			swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/delete_delivery',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
								swal({
									title	: "Success!",
									text	: 'Succcess Process!',
									type	: "success",
									timer	: 3000
								});
								window.location.href = base_url + active_controller;
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
									type	: "warning",
									timer	: 3000
								});
							}
						},
						error: function() {
							swal({
							title		: "Error Message !",
							text		: 'An Error Occured During Process. Please try again..',						
							type		: "warning",								  
							timer		: 3000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		$(document).on('click', '#update_print', function(){
			var nomor_sj 		= $('#nomor_sj').val()
			var delivery_date 	= $('#delivery_date').val()
			var project 		= $('#project').val()
			var alamat 			= $('#alamat').val()

			if(nomor_sj == ''){
				swal({
					title	: "Error Message!",
					text	: 'Nomor surat jalan empty, select first ...',
					type	: "warning"
				});
				return false;
			}

			if(delivery_date == ''){
				swal({
					title	: "Error Message!",
					text	: 'Delivery Date empty, select first ...',
					type	: "warning"
				});
				return false;
			}

			if(project == ''){
				swal({
					title	: "Error Message!",
					text	: 'Project Name empty, select first ...',
					type	: "warning"
				});
				return false;
			}

			if(alamat == ''){
				swal({
					title	: "Error Message!",
					text	: 'Address delivery empty, select first ...',
					type	: "warning"
				});
				return false;
			}

			swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/update_print',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
								swal({
									title	: "Success!",
									text	: 'Succcess Process!',
									type	: "success",
									timer	: 3000
								});
								window.location.href = base_url + active_controller;
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
									type	: "warning",
									timer	: 3000
								});
							}
						},
						error: function() {
							swal({
							title		: "Error Message !",
							text		: 'An Error Occured During Process. Please try again..',						
							type		: "warning",								  
							timer		: 3000
							});
						}
					});
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
	});
</script>
