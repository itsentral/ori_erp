<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class="box box-success">
			<div class="box-body">
				<br>
				<input type="hidden" id='tandax' name='tandax'>
				<div class='in_ipp'>
					<div class='form-group row'>		 	 
						<label class='label-control col-sm-2'><b>Type Outgoing</b></label>
						<div class='col-sm-4'>              
							<select id='tipe_out' name='tipe_out' class='form-control input-sm' style='min-width:200px;'>
								<option value='non-so' data-field='no'>NON SALES ORDER</option>
								<?php
									foreach($no_po AS $val => $valx){
                                        $tipeX = ($valx['no_ipp'] == 'non-so')?'NON SO - ':'SO MATERIAL - ';
                                        $tipeX2 = ($valx['no_ipp'] == 'non-so')?'':' - '.get_name('so_number','so_number','id_bq',$valx['no_ipp'])." - ".get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['no_ipp']));
										echo "<option value='".$valx['kode_trans']."' data-field='no'>".$tipeX.$valx['kode_trans'].$tipeX2."</option>";
									}
									foreach($no_po2 AS $val => $valx){
										if($valx['qty_out'] < $valx['qty']){
											echo "<option value='".$valx['id_bq']."' data-field='no'>SO MATERIAL - ".get_name('so_number','so_number','id_bq',$valx['id_bq'])." - ".get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']))."</option>";
										}
									}
									foreach($no_field AS $val => $valx){
										if($valx['qty_out'] < $valx['qty']){
											$PRODUCT = strtoupper(get_name('so_detail_header','id_category','id',$valx['id_milik']));
											echo "<option value='".$valx['id_bq']."' data-field='yes'>".$PRODUCT." - ".get_name('so_number','so_number','id_bq',$valx['id_bq'])." - ".str_replace('BQ-','',$valx['id_bq'])." - ".get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']))."</option>";
										}
									}
									foreach($no_field2 AS $val => $valx){
										$PRODUCT = strtoupper(get_name('so_detail_header','id_category','id',$valx['id_milik']));
										echo "<option value='".$valx['id_bq']."' data-field='yes'>".$PRODUCT." - ".get_name('so_number','so_number','id_bq',$valx['id_bq'])." - ".str_replace('BQ-','',$valx['id_bq'])." - ".get_name('production','nm_customer','no_ipp',str_replace('BQ-','',$valx['id_bq']))."</option>";
									}
								?>
							</select>
						</div>
					</div>	
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Asal Gudang</b></label>
					<div class='col-sm-4'>              
						<select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
							<!-- <option value='0'>Select Gudang</option> -->
							<?php
								foreach($pusat AS $val => $valx){
									echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
								}
							?>
						</select>
					</div>
				</div>
				<div class='form-group row'>
					<label class='label-control col-sm-2'><b>Tujuan Outgoing</b></label>
					<div class='col-sm-4'>              
						<input type="text" name='tujuan_out' id='tujuan_out' class='form-control input-md' placeholder='Tujuan Outgoing' readonly>
					</div>
				</div>
				<div class='form-group row spk_field'>
					<label class='label-control col-sm-2'><b>No SPK</b></label>
					<div class='col-sm-4'>              
						<select id='no_spk_field' name='no_spk_field' class='form-control input-sm' style='min-width:200px;'>
							<option value='0'>Empty List</option>
						</select>
					</div>
				</div>
				
				<?php
					if($akses_menu['create']=='1'){
						echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'modalDetail')).' ';
					}
				?>
			</div>
		</div>
		<div class="box-tool pull-right">
			<select id='no_po' name='no_po' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Code</option>
				<?php
					foreach($list_po AS $val => $valx){
						echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'])."</option>";
					}
				?>
			</select>
			<select id='gudang' name='gudang' class='form-control input-sm' style='min-width:200px;'>
				<option value='0'>All Warehouse</option>
				<?php
					foreach($data_gudang AS $val => $valx){
						echo "<option value='".$valx['id_gudang_ke']."'>".strtoupper($valx['kd_gudang_ke'])."</option>";
					}
				?>
			</select>
		</div>
		<br><br>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">No SO</th>
					<th class="text-center">No SPK</th>
					<th class="text-center no-sort">Warehouse</th>
					<th class="text-center no-sort">Total Material</th>
					<th class="text-center no-sort">Receiver</th>
					<th class="text-center no-sort">Outgoing Date</th>
					<th class="text-center no-sort">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
	<!-- modal -->
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:95%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>

<?php $this->load->view('include/footer'); ?>
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.spk_field').hide();
		
		var no_po 	= $('#no_po').val();
		var gudang 	= $('#gudang').val();
		DataTables(no_po,gudang);
		
		$(document).on('change','#gudang, #no_po', function(e){
			e.preventDefault();
			var no_po 	= $('#no_po').val();
			var gudang 	= $('#gudang').val();
			DataTables(no_po,gudang);
		});

        $(document).on('change','#tipe_out', function(e){
			e.preventDefault();
			var tipe_out 	= $(this).val();
            let tujuan = $('#tujuan_out')
            let gudang_before = $('#gudang_before')
            let no_spk_field = $('#no_spk_field')
			let field_joint 	= $('#tipe_out').find(':selected').data('field');

			if(field_joint == 'yes'){
				$('.spk_field').show();
			}
			else{
				$('.spk_field').hide();
			}

            if(tipe_out != '0'){
                $.ajax({
                    url: base_url + active_controller+'/get_customer2',
                    type		: "POST",
                    dataType	: 'json',
                    data: {
                        "tipe_out":tipe_out,
                        "field_joint":field_joint,
                    },
                    cache		: false,
                    success:function(data){
                        tujuan.val(data.tujuan_out);
                        gudang_before.html(data.option).trigger("chosen:updated");
                        no_spk_field.html(data.option2).trigger("chosen:updated");
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
            else{
                tujuan.val('');
            }
		});
	});

	$(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL OUTGOING MATERIAL</b>");
		$.ajax({
			type:'POST',
			url: base_url +'warehouse/modal_detail_adjustment/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);
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
	});

    $(document).on('click', '#modalDetail', function(e){
		e.preventDefault();
		var tipe_out 	= $('#tipe_out').val();
		if( tipe_out == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Tipe Not Select, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		loading_spinner();
		var tipe_out 		= $('#tipe_out').val();
		var gudang_before 	= $('#gudang_before').val();
		var tujuan_out 	    = $('#tujuan_out').val();
		let no_spk_field 	= $('#no_spk_field').val();
		let field_joint 	= $('#tipe_out').find(':selected').data('field');

		$("#head_title2").html("<b>OUTGOING MATERIAL</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_outgoing_subgudang',
			type		: "POST",
			data: {
				"tipe_out" 		: tipe_out,
				"field_joint" 	: field_joint,
				"no_spk_field" 	: no_spk_field,
				"gudang_before" : gudang_before,
				"tujuan_out"    : tujuan_out
			},
			cache		: false,
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

				$('.autoNumeric2').autoNumeric('init', {mDec: '4', aPad: false, vMin: '-99999999999'});
				$('.chosen_select').chosen({width: '100%'});
				$('.tanggal').datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true
				});
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

	$(document).on('click', '#saveINMaterial', function(){
		
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
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_out_material_sub',
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
									timer	: 7000
								});
							window.location.href = base_url + active_controller+'/outgoing_subgudang';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '#saveFGMaterial', function(){
		let qty_kit = $('#qty_kit').val();
		$('#saveFGMaterial').prop('disabled',true);
		if( qty_kit == '0' || qty_kit == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'Qty Kit Not Empty ! ...',
			  type	: "warning"
			});
			$('#saveFGMaterial').prop('disabled',false);
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
				loading_spinner();
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_fg_material_sub_new',
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
									timer	: 7000
								});
								$('#saveFGMaterial').prop('disabled',false);
							window.location.href = base_url + active_controller+'/outgoing_subgudang';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
							$('#saveFGMaterial').prop('disabled',false);
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000
						});
						$('#saveFGMaterial').prop('disabled',false);
					}
				});
			} else {
				$('#saveFGMaterial').prop('disabled',false);
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
			}
		});
	});

	$(document).on('click', '#saveSementara', function(){
		
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
				var formData  	= new FormData($('#form_adjustment')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_save_sementara',
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
									timer	: 7000
								});
							$('#ModalView2').modal('toggle');
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

    //MODAL
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
                    timer				: 3000
                });
            }
        });
    });

    $(document).on('click', '.delPart', function(){
        var get_id 		= $(this).parent().parent().attr('class');
        $("."+get_id).remove();
    });

	$(document).on('click', '.addPartCustom', function(){
        loading_spinner();
        var get_id 		= $(this).parent().parent().attr('id');
        // console.log(get_id);
        var split_id	= get_id.split('_');
        var id 		= parseInt(split_id[1])+1;
        var id_bef 	= split_id[1];

        $.ajax({
            url: base_url + active_controller+'/get_add_custom/'+id,
            cache: false,
            type: "POST",
            dataType: "json",
            success: function(data){
                $("#add_"+id_bef).before(data.header);
                $("#add_"+id_bef).remove();
                $('.chosen_select').chosen({width: '100%'});
                $(".autoNumeric4").autoNumeric('init', {mDec: '4', aPad: false});
				$('#saveINMaterial').hide()
				$('#saveFGMaterial').hide()
                swal.close();
            },
            error: function(){
                swal({
                    title				: "Error Message !",
                    text				: 'Connection Time Out. Please try again..',
                    type				: "warning",
                    timer				: 3000
                });
            }
        });
    });

	$(document).on('change','.category', function(e){
		e.preventDefault();
		var id_category 	= $(this).val();
		let materials = $(this).parent().parent().find('.material')
		if(id_category != '0'){
			$.ajax({
				url: base_url + active_controller+'/get_raw_materials',
				type		: "POST",
				dataType	: 'json',
				data: {
					"id_category":id_category
				},
				cache		: false,
				success:function(data){
					materials.html(data.option).trigger("chosen:updated");
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
		else{
			materials.html("<option value='0'>List Empty</option>").trigger("chosen:updated");
		}
	});

	$(document).on('change','.material, .sub_gudang', function(e){
		e.preventDefault();
		// var id_material = $(this).val();
		// let gudang 	= $('#gudang_before').val()
		let id_material = $(this).parent().parent().find('.material').val()
		let gudang 		= $(this).parent().parent().find('.sub_gudang').val()
		let stockval 	= $(this).parent().parent().find('.stockval')
		let qtyval 		= $(this).parent().parent().find('.qtyval')
		if(id_material != '0'){
			$.ajax({
				url: base_url + active_controller+'/get_stock',
				type		: "POST",
				dataType	: 'json',
				data: {
					"id_material":id_material,
					"gudang":gudang,
				},
				cache		: false,
				success:function(data){
					stockval.val(data.stock);
					qtyval.val('');
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
		else{
			stockval.val('');
			qtyval.val('');
		}
	});

	$(document).on('keyup','.qtyval', function(e){
		e.preventDefault();
		let stockval 	= getNum($(this).parent().parent().find('.stockval').val().split(",").join(""))
		let qtyval 		= getNum($(this).parent().parent().find('.qtyval').val().split(",").join(""))

		if(stockval < qtyval && stockval >= 0){
			$(this).parent().parent().find('.qtyval').val(stockval)
		}

		if(stockval < 0){
			$(this).parent().parent().find('.qtyval').val(0)
		}
		
	});

	function DataTables(no_po=null,gudang=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_outgoing',
				type: "post",
				data: function(d){
					d.no_po = no_po,
					d.gudang = gudang,
					d.tipe = 'subgudang'
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}

	
</script>
