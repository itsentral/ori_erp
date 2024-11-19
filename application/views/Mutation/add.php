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
				<label class='label-control col-sm-2'><b>Adjustment Type <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>              
					<select name='adjustment_type' id='adjustment_type' class='form-control input-sm'>
						<option value='0'>Select Type</option>
						<!-- <option value='plus'>PLUS</option>
						<option value='minus'>MINUS</option> -->
						<option value='mutasi' <?=$MUTASI;?>>RETUR</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Dari Gudang <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<select name='id_gudang_dari_m' id='id_gudang_dari_m' class='form-control input-sm'>
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
					<select name='id_gudang_ke_m' id='id_gudang_ke_m' class='form-control input-sm'>
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
                <label class='label-control col-sm-2'><b>PIC <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'pic_m','name'=>'pic_m','class'=>'form-control input-md','placeholder'=>'PIC'),$pic);
					?>
				</div>
				<label class='label-control col-sm-2'><b>No BA</b></label>
				<div class='col-sm-4'>
					<?php
					 echo form_input(array('id'=>'no_ba','name'=>'no_ba','class'=>'form-control input-md','placeholder'=>'No BA'),$no_ba);
					?>
				</div>
			</div>
            <div class='form-group row'>
                <div class='col-sm-12'>
                    <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
                        <thead>
                            <tr class='bg-blue'>
                                <th class='text-center mid'>Material Name</th>
                                <th class='text-center mid' width='20%'>Lot Number</th>
                                <th class='text-center mid' width='15%'>Expired Date</th>
                                <th class='text-center mid' width='10%'>Qty</th>
                                <th class='text-center mid' width='20%'>Reason</th>
                                <?php if(empty($view)){ ?>
                                <th class='text-center mid' width='5%'>#</th>
                                <?php } ?>
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
                                    $qty_oke 	        = (!empty($valx2['qty_oke']))?number_format($valx2['qty_oke'],2):'';

                                    echo "<tr class='header_".$id."'>";
                                        echo "<td align='left'>";
                                            echo "<select name='detail[".$id."][material]' class='chosen_select form-control input-sm material'>";
                                            echo "<option value='0'>Select Material</option>";
                                            foreach($material AS $row){
                                                $SEL = ($row->id_material == $materialx)?'selected':'';
                                                echo "<option value='".$row->id_material."' ".$SEL.">".strtoupper($row->nm_material)."</option>";
                                            }
                                            echo "</select>";
                                        echo "</td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][lot_number]' value='".$lot_number."' placeholder='Lot Number/Received Dat' class='form-control input-md text-left'></td>";
                                        echo "<td align='left'>";
                                            echo "<select name='detail[".$id."][expired]' class='chosen_select form-control input-sm expired'>";
                                                if(empty($expired_date)){
                                                    echo "<option value='0'>List Empty</option>";
                                                }
                                                else{
                                                    echo "<option value='".$expired_date."'>".$expired_date."</option>";
                                                }
                                            echo "</select>";
                                        echo "</td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][qty]' value='".$qty_oke."' placeholder='Qty' class='form-control input-md text-center autoNumeric4'></td>";
                                        echo "<td align='left'><input type='text' name='detail[".$id."][reason]' value='".$keterangan."' placeholder='Reason' class='form-control input-md text-left'></td>";
                                        if(empty($view)){
                                        echo "<td align='center'>";
                                            echo "<button type='button' class='btn btn-sm btn-danger delPart' title='Delete'><i class='fa fa-close'></i></button>";
                                        echo "</td>";
                                        }
                                    echo "</tr>";
                                }
                            }
                            if(empty($view)){
                            ?>
                            <tr id='add_<?=$id;?>'>
                                <td align='left'><button type='button' class='btn btn-sm btn-success addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Material</button></td>
                                <td align='center' colspan='5'></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-10 text-right'>              
					<?php
                        if(empty($view)){
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
                        }
                        echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','id'=>'back','content'=>'Back'));
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
			window.location.href = base_url + active_controller
		});

        $(document).on('click', '.addPart', function(){
            loading_spinner();
            var get_id 		= $(this).parent().parent().attr('id');
            // console.log(get_id);
            var split_id	= get_id.split('_');
            var id 		= parseInt(split_id[1])+1;
            var id_bef 	= split_id[1];

            $.ajax({
                url: base_url + active_controller+'/get_add/'+id,
                cache: false,
                type: "POST",
                dataType: "json",
                success: function(data){
                    $("#add_"+id_bef).before(data.header);
                    $("#add_"+id_bef).remove();
                    $('.chosen_select').chosen({width: '100%'});
                    $(".autoNumeric4").autoNumeric('init', {mDec: '4', aPad: false});
                    swal.close();
                },
                error: function(){
                    swal({
                        title				: "Error Message !",
                        text				: 'Connection Time Out. Please try again..',
                        type				: "warning",
                        timer				: 3000,
                        showCancelButton	: false,
                        showConfirmButton	: false,
                        allowOutsideClick	: false
                    });
                }
            });
        });

        $(document).on('click', '.delPart', function(){
            var get_id 		= $(this).parent().parent().attr('class');
            $("."+get_id).remove();
        });
		
		$(document).on('change', '#adjustment_type', function(e){ 
			var type = $(this).val();
			if(type != 'mutasi'){
				$('.gudang_mutasi').hide();
				$('.gudang_plus_min').show();
			}
			if(type == 'mutasi'){
				$('.gudang_mutasi').show();
				$('.gudang_plus_min').hide();
				$("#id_material").html("<option value='0'>List Empty</option>").trigger("chosen:updated");
				$("#expired_date_m").html("<option value='0'>List Empty</option>").trigger("chosen:updated");
			}
		});
		
		$(document).on('change','#id_gudang_dari_m', function(e){
			e.preventDefault();
			let id_gudang = $(this).val();
			
            if(id_gudang != '0'){
                $.ajax({
                    url: base_url + active_controller+'/list_gudang_ke',
                    cache: false,
                    type: "POST",
                    data: {
                        'gudang' : id_gudang,
                        'tandax' : 'MOVE'
                    },
                    dataType: "json",
                    success: function(data){
                        $("#id_gudang_ke_m").html(data.option).trigger("chosen:updated");
                    },
                    error: function() {
                        swal({
                        title				: "Error Message !",
                        text				: 'Connection Timed Out ...',
                        type				: "warning",
                        timer				: 5000
                        });
                    }
                });
            }
			
		});
		
		$(document).on('change','.material', function(e){
			var type = $('#adjustment_type').val();
            let thisRow = $(this).parent().parent().find('.expired')
			
            if(type=='0'){
                swal({
                    title	: "Error Message!",
                    text	: 'Adjustment type not selected, please select first ...',
                    type	: "warning"
                });
                return false;
            }
            
			if(type == 'mutasi'){
				var id_gudang_ke = $('#id_gudang_dari_m').val();
				var id_material = $(this).val();

                if(id_gudang_ke=='0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang tujuan not selected, please select first ...',
					  type	: "warning"
					});
					return false;
				}
				
				$.ajax({
					url: base_url + active_controller+'/list_expired_date',
					cache: false,
					type: "POST",
					data: {
						'id_gudang_ke' : id_gudang_ke,
						'id_material' : id_material
					},
					dataType: "json",
					success: function(data){
						thisRow.html(data.option).trigger("chosen:updated");
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'Connection Timed Out ...',
						  type				: "warning",
						  timer				: 5000
						});
					}
				});
			}
		});
		
		$(document).on('click','#simpan-bro', function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var adjustment_type		= $('#adjustment_type').val();
			var no_ba				= $('#no_ba').val();
			var id_gudang_dari_m	= $('#id_gudang_dari_m').val();
			var id_gudang_ke_m		= $('#id_gudang_ke_m').val();
			var pic_m				= $('#pic_m').val();
			
			if(adjustment_type=='0'){
				swal({
				  title	: "Error Message!",
				  text	: 'Adjustment type is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;
			}
			
			if(adjustment_type == 'mutasi'){
				if(id_gudang_dari_m == '0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang dari is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan-bro').prop('disabled',false);
					return false;
				}
				if(id_gudang_ke_m=='0'){
					swal({
					  title	: "Error Message!",
					  text	: 'Gudang ke is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan-bro').prop('disabled',false);
					return false;
				}
				if(pic_m == ''){
					swal({
					  title	: "Error Message!",
					  text	: 'PIC is Empty, please input first ...',
					  type	: "warning"
					});
					$('#simpan-bro').prop('disabled',false);
					return false;
				}
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
						loading_spinner();
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/add';
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
	});
</script>
