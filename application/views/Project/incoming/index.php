<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <input type="hidden" id='tandax' name='tandax'>
        <input type="hidden" id='category' name='category' value='<?=$category;?>'>
        <div class='in_ipp'>
            <div class='form-group row'>		 	 
                <label class='label-control col-sm-2'><b>Incoming Type <span class='text-red'>*</span></b></label>
                <div class='col-sm-4'>              
                    <select id='no_ipp' name='no_ipp' class='form-control input-sm' style='min-width:200px;'>
                        <option value='0'>Nomor PO</option>
                        <option value='X'>Pengembalian Barang</option>
                        <?php
                            foreach($no_po AS $val => $valx){
                                echo "<option value='".$valx['no_po']."'>".strtoupper($valx['typ'].' - '.$valx['no_po'].$valx['no_surat_jalan'].' - '.$valx['ket_name'].', create by: '.strtolower($valx['created_by']))."</option>";
                            }
                        ?>
                    </select>
                </div>
                <label class='label-control col-sm-2'><b>PIC <span class='text-red'>*</span></b></label>
                <div class='col-sm-4'>
                    <?php
                        echo form_input(array('id'=>'pic','name'=>'pic','class'=>'form-control input-md','placeholder'=>'PIC'));
                    ?>
                </div>
            </div>	
        </div>
        <div class='form-group row'>
            <label class='label-control col-sm-2'><b>Warehouse <span class='text-red'>*</span></b></label>
            <div class='col-sm-4'>              
                <select id='gudang_before' name='gudang_before' class='form-control input-sm' style='min-width:200px;'>
                    <?php
                        foreach($pusat AS $val => $valx){
                            echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
                        }
                    ?>
                </select>
            </div>
            <label class='label-control col-sm-2'><b>Note</b></label>
            <div class='col-sm-4'>
                <?php
                    echo form_textarea(array('id'=>'note','name'=>'note','class'=>'form-control input-md','rows'=>'2','cols'=>'75','placeholder'=>'Note'));
                ?>
            </div>
        </div>
        <div class='form-group row'>
            <label class='label-control col-sm-2'><b>Nomor ROS</b></label>
            <div class='col-sm-4'>              
                <select id='no_ros' name='no_ros' class='form-control input-sm' style='min-width:200px;'>
                    <option value=''>Nomor ROS</option>
                </select>
            </div>
            <label class='label-control col-sm-2'><b>Date Transaksi</b></label>
            <div class='col-sm-4'>              
                <input type="text" name="tanggal_trans" id="tanggal_trans" class='form-control input-sm' data-role="datepicker_lost" readonly value='<?=date('Y-m-d');?>'>
            </div>
        </div>
        <div class='form-group row'>
            <div class='col-sm-12'>
                <?php
                    if($akses_menu['create']=='1'){
                        echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 5px 5px 5px 5px;','value'=>'Process','content'=>'Process','id'=>'modalDetail'));
                        echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 5px 5px 5px 5px;','value'=>'Process','content'=>'Process','id'=>'modalPengembalianBarang'));
                    }
                ?>
            </div> 
        </div> 
		<div class='form-group row'>
            <div class='col-sm-8'></div> 
            <div class='col-sm-2'>   
                <select id='no_po' name='no_po' class='form-control input-sm' style='min-width:200px;'>
                    <option value='0'>All PO Number</option>
                    <?php
                        foreach($list_po AS $val => $valx){
                            echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'])."</option>";
                        }
                    ?>
                </select>
            </div>
            <div class='col-sm-2'>   
                <select id='gudang' name='gudang' class='form-control input-sm' style='min-width:200px;'>
                    <option value='0'>All Warehouse</option>
                    <?php
                        foreach($data_gudang AS $val => $valx){
                            echo "<option value='".$valx['id_gudang_ke']."'>".strtoupper($valx['kd_gudang_ke'])."</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th> 
					<th class="text-center">No Trans</th>
                    <th class="text-center">No PO</th>
					<th class="text-center no-sort">Tanggal</th>
					<th class="text-center no-sort">Warehouse</th>
					<th class="text-center no-sort">PIC</th>
					<th class="text-center no-sort">No. Surat Jalan</th>
					<th class="text-center no-sort">Receiver</th>
					<th class="text-center no-sort">Dated</th>
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
		<div class="modal-dialog"  style='width:90%; '>
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
<style>
	#tanggal_trans{
		cursor: pointer;
	}
</style>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('#modalPengembalianBarang').hide()
		var no_po 	= $('#no_po').val();
		var gudang 	= $('#gudang').val();
		var category 		= $('#gudang_before').val();
		DataTables(no_po,gudang,category);
		
		$(document).on('change','#gudang, #no_po', function(e){
			e.preventDefault();
			var no_po 	= $('#no_po').val();
			var gudang 	= $('#gudang').val();
			var category= $('#gudang_before').val();
			DataTables(no_po,gudang,category);
		});
	});

    $(document).on('click', '#modalPengembalianBarang', function(e){
		e.preventDefault();
		var tanggal_trans 	= $('#tanggal_trans').val();
        var gudang 	        = $('#gudang_before').val();
        var no_po 	        = $('#no_ipp').val();
        var pic 	        = $('#pic').val();
        var note 	        = $('#note').val();
        var no_ros 	        = $('#no_ros').val();

        if(no_po == 'X'){
            $("#head_title2").html("<b>Pengambalian Barang Project</b>");
            $.ajax({
                type:'POST',
                url: base_url + active_controller+'/modal_pengembalian_barang',
                data: {
                    'no_po' : no_po,
                    'gudang' : gudang,
                    'tanggal_trans' : tanggal_trans,
                    'pic' : pic,
                    'note' : note,
                    'no_ros' : no_ros,
                },
                beforeSend: function(){
                    loading_spinner();
                },
                success:function(data){
                    $("#ModalView2").modal();
                    $("#view2").html(data);
                },
                error: function() {
                    swal({
                    title	: "Error Message !",
                    text	: 'Connection Timed Out ...',
                    type	: "warning",
                    timer	: 5000
                    });
                }
            });
        }
        else{
            swal({
                title	: "Error Message !",
                text	: 'No PO harus Pengembalian Barang',
                type	: "warning",
                timer	: 5000
            });
        }
	});

    $(document).on('change','.qtypack, .ket', function(){
		var no 		= $(this).data('no');
		var id 		= $('#id_'+no).val();
		var qty 	= $('#qty_'+no).val();
		var ket		= $('#ket_'+no).val();

		$.ajax({
				url			: base_url + active_controller+'/save_temp_mutasi',
				type		: "POST",
				data		: {
					"id" 	: id,
					"qty"   : qty,
					"ket" 	: ket
				},
				cache		: false
		});
	});

    $(document).on('click', '#ProcessPengembalianBarang', function(){
		var no_surat_jalan 	        = $('#no_surat_jalan').val();

		if(no_surat_jalan == ''){
			swal({
				title	: "Error Message!",
				text	: 'No Surat Jalan Wajib Diisi !',
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
                loading_spinner();
                var formData  	= new FormData($('#form_adjustment')[0]);
                $.ajax({
                    url			: base_url + active_controller+'/process_pengembalian_barang',
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
                                    timer	: 7000,
                                    showCancelButton	: false,
                                    showConfirmButton	: false,
                                    allowOutsideClick	: false
                                });
                            window.location.href = base_url + active_controller;
                        }
                        else if(data.status == 0){
                            swal({
                                title	: "Save Failed!",
                                text	: data.pesan,
                                type	: "warning",
                                timer	: 7000,
                                showCancelButton	: false,
                                showConfirmButton	: false,
                                allowOutsideClick	: false
                            });
                        }
                    },
                    error: function() {
                        swal({
                            title				: "Error Message !",
                            text				: 'An Error Occured During Process. Please try again..',						
                            type				: "warning",								  
                            timer				: 7000,
                            showCancelButton	: false,
                            showConfirmButton	: false,
                            allowOutsideClick	: false
                        });
                    }
                });
            } else {
            swal("Cancelled", "Data can be process again :)", "error");
            return false;
            }
        });
    });

    $(document).on('click', '.detailAjust', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL TRANSACTION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_detail/'+$(this).data('kode_trans'),
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

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

    $(document).on('change', '#no_ipp', function(){
		var no_ipp	= $('#no_ipp').val();

		if(no_ipp == 'X'){
			$('#modalPengembalianBarang').show()
			$('#modalDetail').hide()
		}
		else{
			$('#modalPengembalianBarang').hide()
			$('#modalDetail').show()
		}
		
        if(no_ipp != '0' && no_ipp != 'X'){
            $.ajax({
                url			: base_url + 'warehouse/get_ros/'+no_ipp,
                type		: "POST",
                cache		: false,
                dataType	: 'json',
                processData	: false, 
                contentType	: false,
                beforeSend : function(){
                    $('#no_ros').html('');
                    $('#spinner').show();
                },
                success		: function(data){
                    console.log(data);
                    $('#no_ros').html(data.option).trigger("chosen:updated");
                    $('#spinner').hide();
                },
                error: function() {
                    swal({
                        title	: "Error Message !",
                        text	: 'An Error Occured During Process. Please try again..',
                        type	: "warning",
                        timer	: 7000
                    });
                }
            });
        }
        else{
            $('#no_ros').html('').trigger("chosen:updated");
        }
	});

    function DataTables(no_po=null,gudang=null,category=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/server_side_incoming',
				type: "post",
				data: function(d){
					d.no_po = no_po,
					d.gudang = gudang,
					d.category = category
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

	function DataTables2(){
		var dataTable = $('#my-grid2').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
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
				url : base_url + active_controller+'/server_side_pengembalian_barang',
				type: "post",
				// data: function(d){
				// 	d.gudang1 = gudang1
				// },
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}

	$(document).on('click', '#saveINMaterial', function(){
		var tanda = $('#tanda').val()

		var linkProcess = (tanda == 'RTR')?'confirm_retur':'process_incoming';
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
					url			: base_url + active_controller+'/'+linkProcess,
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
							window.location.href = base_url + active_controller
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



















	

    $(document).on('click', '#modalDetail', function(e){
		e.preventDefault();
		var gudang_before 	= $('#gudang_before').val();
		var no_ipp 			= $('#no_ipp').val();
		var pic 			= $('#pic').val();
		var note 			= $('#note').val();
		var no_ros 			= $('#no_ros').val();
		var tanggal_trans 	= $('#tanggal_trans').val();
		var category 		= $('#category').val();

		if( no_ipp == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'PO Number Not Select, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		if( pic == ''){
			swal({
			  title	: "Error Message!",
			  text	: 'PIC is empty, please input first ...',
			  type	: "warning"
			});
			$('#modalDetail').prop('disabled',false);
			return false;
		}

		loading_spinner();
		var no_ipp 			= $('#no_ipp').val();
		var gudang_before 	= $('#gudang_before').val();
		$("#head_title2").html("<b>INCOMING TRANSACTION</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modal_incoming',
			data: {
				"tanggal_trans" : tanggal_trans,
				"gudang_before" : gudang_before,
				"no_po" 		: no_ipp,
				"pic" 			: pic,
				"note" 			: note,
				"category" 		: category,
				"no_ros"		: no_ros
			},
			success:function(data){
				$("#ModalView2").modal();
				$("#view2").html(data);

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

	$(document).on('click', '#processAjust', function(){
		var gudang_before 	= $('#gudang_before').val();
		var no_ipp 			= $('#no_ipp').val();
		var gudang_after 	= $('#gudang_after').val();

		if( no_ipp == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'IPP Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
			return false;
		}

		if( gudang_before == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Origin Warehouse Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
			return false;
		}

		if( gudang_after == '0'){
			swal({
			  title	: "Error Message!",
			  text	: 'Destination Warehouse Not Select, please input first ...',
			  type	: "warning"
			});
			$('#processAjust').prop('disabled',false);
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
				var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url + active_controller+'/process_adjustment',
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
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/material_adjustment';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '#moveMat', function(){
		
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
				var formData  	= new FormData($('#form_move')[0]);
				$.ajax({
					url			: base_url + active_controller+'/move_material',
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
									timer	: 7000,
									showCancelButton	: false,
									showConfirmButton	: false,
									allowOutsideClick	: false
								});
							window.location.href = base_url + active_controller+'/material_adjustment';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
								showCancelButton	: false,
								showConfirmButton	: false,
								allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
							title				: "Error Message !",
							text				: 'An Error Occured During Process. Please try again..',						
							type				: "warning",								  
							timer				: 7000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
</script>
