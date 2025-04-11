<?php
$this->load->view('include/side_menu');

$type           = (!empty($data))?$data[0]->adjustment_type:'';
$gudang_dari    = (!empty($data))?$data[0]->id_gudang_dari:'';
$gudang_ke      = (!empty($data))?$data[0]->id_gudang_ke:'';
$pic            = (!empty($data))?strtoupper($data[0]->pic):'';
$no_ba          = (!empty($data))?strtoupper($data[0]->no_ba):'';

$MUTASI = ($type == 'mutasi')?'selected':'';

?> 
<form action="#" method="POST" id="form_proses_bro" autocomplete='off'>
	<input type='hidden' name='kode_trans' value='<?=$kode_trans;?>'>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Adjustment Type</b></label>
				<div class='col-sm-4'>              
					<select name='adjustment_type' id='adjustment_type' class='form-control input-sm' disabled>
						<option value='0'>Select Type</option>
						<!-- <option value='plus'>PLUS</option>
						<option value='minus'>MINUS</option> -->
						<option value='mutasi' <?=$MUTASI;?>>RETUR</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Dari Gudang</b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_dari_m' id='id_gudang_dari_m' class='form-control input-sm' disabled>
						<option value='0'>Select Gudang</option>
						<?php
						foreach($gudang AS $val => $valx){
                            $sele = ($valx['id'] == $gudang_dari)?'selected':'';
							echo "<option value='".$valx['id']."' ".$sele.">".strtoupper($valx['nm_gudang'])."</option>";
						}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Ke Gudang</b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_ke_m' id='id_gudang_ke_m' class='form-control input-sm' disabled>
						<?php
                            if(empty($kode_trans)){
                                echo "<option value='0'>List Empty</option>";
                            }
                            else{
                                echo "<option value='".$gudang_ke."'>".strtoupper(get_name('warehouse','nm_gudang','id',$gudang_ke))."</option>";
                            }
                        ?>
					</select>
				</div>
			</div>
			<div class='form-group row'>		 	 
                <label class='label-control col-sm-2'><b>PIC</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'pic_m','name'=>'pic_m','class'=>'form-control input-md','placeholder'=>'PIC','disabled'=>true),$pic);
					?>
				</div>
				<label class='label-control col-sm-2'><b>No BA</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'no_ba','name'=>'no_ba','class'=>'form-control input-md','placeholder'=>'No BA','disabled'=>true),$no_ba);
					?>
				</div>
			</div>
            <div class='form-group row'>
                <div class='col-sm-12'>
                    <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
                        <thead>
                            <tr class='bg-blue'>
                                <th class='text-center mid'>Material Name</th>
                                <th class='text-center mid' width='15%'>Lot Number</th>
                                <th class='text-center mid' width='10%'>Expired Date</th>
                                <th class='text-center mid' width='8%'>Qty</th>
                                <th class='text-center mid' width='17%'>Reason</th>
                                <th class='text-center mid' width='8%'>Aktual</th>
                                <th class='text-center mid' width='17%'>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id = 0;
                            if(!empty($data_detail)){
                                foreach($data_detail AS $val2 => $valx2){ $id++;
                                    $materialx 			= (!empty($valx2['id_material']))?$valx2['id_material']:'';
                                    $lot_number 		= (!empty($valx2['lot_number']))?strtoupper($valx2['lot_number']):'';
                                    $keterangan 		= (!empty($valx2['keterangan']))?strtoupper($valx2['keterangan']):'';
                                    $expired_date 		= (!empty($valx2['expired_date']) AND $valx2['expired_date'] != '0000-00-00')?$valx2['expired_date']:'';
                                    $qty_oke 	        = (!empty($valx2['qty_oke']))?$valx2['qty_oke']:'';

                                    echo "<tr class='header_".$id."'>";
                                        echo "<td align='left'>";
                                            echo "<select name='detail[".$id."][material]' class='chosen_select form-control input-sm material' disabled>";
                                            echo "<option value='0'>Select Material</option>";
                                            foreach($material AS $row){
                                                $SEL = ($row->id_material == $materialx)?'selected':'';
                                                echo "<option value='".$row->id_material."' ".$SEL.">".strtoupper($row->nm_material)."</option>";
                                            }
                                            echo "</select>";
                                        echo "</td>";
                                        echo "<td align='left'>
                                                <input type='hidden' name='detail[".$id."][id]' value='".$valx2['id']."'>
                                                <input type='text' name='detail[".$id."][lot_number]' value='".$lot_number."' disabled placeholder='Lot Number/Received Dat' class='form-control input-md text-left'>
                                            </td>";
                                        echo "<td align='left'>";
                                            echo "<select name='detail[".$id."][expired]' class='chosen_select form-control input-sm expired' disabled>";
                                                if(empty($expired_date)){
                                                    echo "<option value='0'>List Empty</option>";
                                                }
                                                else{
                                                    echo "<option value='".$expired_date."'>".$expired_date."</option>";
                                                }
                                            echo "</select>";
                                        echo "</td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][qty]' value='".$qty_oke."' disabled placeholder='Qty' class='form-control input-md text-center autoNumeric4'></td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][reason]' value='".$keterangan."' disabled placeholder='Reason' class='form-control input-md text-left'></td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][qty_check]' value='".$qty_oke."' placeholder='Qty Aktual' class='form-control input-md text-center autoNumeric4'></td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][reason_check]' placeholder='Note' class='form-control input-md text-left'></td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-10 text-right'>              
					<?php
                        if(empty($view)){
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Approve','id'=>'simpan-bro')).' ';
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'save','content'=>'Reject','id'=>'reject')).' ';
                        }
                        echo form_button(array('type'=>'button','class'=>'btn btn-md btn-default','id'=>'back','content'=>'Back'));
					?>
				</div>
			</div>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>

<?php $this->load->view('include/footer'); ?>
<style>
	.datepicker{
		cursor: pointer;
	}
</style>
<script>
	$(document).ready(function(){
        $(".autoNumeric4").autoNumeric('init', {mDec: '4', aPad: false});
		$(document).on('click', '#back', function(e){
			window.location.href = base_url + active_controller + '/index/app'
		});
		
		$(document).on('click','#simpan-bro', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			
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
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/approve';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: formData,
							cache		: false,
							dataType	: 'json',
							processData	: false, 
							contentType	: false,				
							success		: function(data){								
								if(data.status == 1){											
									swal({
										  title	: "Save Success!",
										  text	: data.pesan,
										  type	: "success",
										  timer	: 5000
										});
									window.location.href = base_url + active_controller;
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 5000
									});
								}
								$('#simpan-bro').prop('disabled',false);
							
							},
							error: function() {
								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',						
								  type				: "warning",								  
								  timer				: 5000
								});
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});

		$(document).on('click','#reject', function(e){
			e.preventDefault();
			
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
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/reject';
						$.ajax({
							url			: baseurl,
							type		: "POST",
							data		: formData,
							cache		: false,
							dataType	: 'json',
							processData	: false, 
							contentType	: false,				
							success		: function(data){								
								if(data.status == 1){											
									swal({
										  title	: "Save Success!",
										  text	: data.pesan,
										  type	: "success",
										  timer	: 5000
										});
									window.location.href = base_url + active_controller;
								}
								else{
									swal({
									  title	: "Save Failed!",
									  text	: data.pesan,
									  type	: "warning",
									  timer	: 5000
									});
								}
							
							},
							error: function() {
								swal({
								  title				: "Error Message !",
								  text				: 'An Error Occured During Process. Please try again..',						
								  type				: "warning",								  
								  timer				: 5000
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
