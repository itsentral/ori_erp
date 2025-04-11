<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<input type='hidden' id='uri_tanda' value='<?=$uri_tanda;?>'>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<?php
		if(empty($uri_tanda)){
			?>
		<div hidden>
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Type Request</b></label>
				<div class='col-sm-4'>              
					<select id='tipe' name='tipe' class='form-control input-sm'>
						<option value='change'>REQUEST MAERIAL CHANGE</option>
					</select>
				</div>
			</div>	
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>No SO</b></label>
			<div class='col-sm-4'>              
				<select id='no_ipp' name='no_ipp' class='form-control input-sm'>
					<option value='0'>Select No SO</option>
					<?php
						foreach($no_ipp AS $val => $valx){
                            $IMPLODE = substr($valx['no_ipp'],0,4);
                            $nomor_so = $valx['nomor_so'];
                            if($IMPLODE == 'IPPT'){
                                $nomor_so = $valx['nomor_so_tanki'];
                            }
							echo "<option value='".$valx['no_ipp']."'>".strtoupper($nomor_so)."</option>";
						}
					?>
				</select>
			</div>
		</div>
		<div  class='form-group row'>	 	 
			<label class='label-control col-sm-2'><b>No SPK</b></label>
			<div class='col-sm-4'>              
				<select id='no_spk' name='no_spk' class='form-control input-sm'>
					<option value='0'>List Empty</option>
				</select>
			</div>
		</div>
		<?php
			if($akses_menu['create']=='1'){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'request'));
			}
		?>
		<br><br><br>
		<?php
		}
		?>
        <div class='form-group row'>
            <div class='col-sm-10'></div>
            <div class='col-sm-2'>
            <select id='no_ipp2' name='no_ipp2' class='form-control input-sm chosen_select'>
                <option value='0'>All IPP Number</option>
                <?php
                    foreach($list_ipp_req AS $val => $valx){
                        $IMPLODE = substr($valx['no_ipp'],0,4);
                        $nomor_so = $valx['nomor_so'];
                        if($IMPLODE == 'IPPT'){
                            $nomor_so = $valx['nomor_so_tanki'];
                        }
                        echo "<option value='".$valx['no_ipp']."'>".strtoupper($nomor_so)."</option>";
                    }
                ?>
            </select>
            </div>
        </div>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th> 
					<th class="text-center">No Trans</th>
					<th class="text-center">No SO</th>
					<th class="text-center">No SPK</th>
					<th class="text-center">Request By</th>
					<th class="text-center">Request Date</th>
                    <th class="text-center">Approve By</th>
					<th class="text-center">Approve Date</th>
					<th class="text-center no-sort">Status</th>
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
		<div class="modal-dialog"  style='width:85%; '>
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
	.chosen-container{
		width: 100% !important;
		text-align : left !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.chosen_select').chosen();
		var no_ipp 	= $('#no_ipp2').val();
		DataTables(no_ipp);
		
		$(document).on('change','#no_ipp2', function(e){
			e.preventDefault();
			var no_ipp 	= $('#no_ipp2').val();
			DataTables(no_ipp);
		});

        $(document).on('change', '#no_ipp', function(e){
            e.preventDefault();
            let no_ipp 			= $('#no_ipp').val()

            if(no_ipp != '0'){
                $.ajax({
                    type:'POST',
                    url: base_url + active_controller+'/list_spk',
                    data: {
                        "no_ipp" 		: no_ipp,
                    },
                    cache		: false,
                    dataType	: 'json',
                    beforeSend: function(){
                        loading_spinner();
                    },
                    success:function(data){
                        $("#no_spk").html(data.option).trigger("chosen:updated");
                        swal.close()
                    },
                    error: function() {
                        swal({
                        title		: "Error Message !",
                        text		: 'Connection Timed Out ...',
                        type		: "warning",
                        timer		: 5000
                        });
                    }
                });
            }
            else{
                $("#no_spk").html('');
            }
        });

        $(document).on('click', '#request', function(e){
            e.preventDefault();
            var no_spk 	    = $('#no_spk').val();
            var no_ipp 		= $('#no_ipp').val();

            if( no_ipp == '0'){
                swal({
                title	: "Error Message!",
                text	: 'SO Number Not Select, please input first ...',
                type	: "warning"
                });
                $('#request').prop('disabled',false);
                return false;
            }

            if( no_spk == '0'){
                swal({
                title	: "Error Message!",
                text	: 'SPK Number Not Select, please input first ...',
                type	: "warning"
                });
                $('#request').prop('disabled',false);
                return false;
            }

            loading_spinner();
            $("#head_title2").html("<b>REQUEST MATERIAL CHANGE "+no_ipp.toUpperCase()+" / "+no_spk+"</b>");
            $.ajax({
                type:'POST',
                url: base_url + active_controller+'/modal_request',
                data: {
                    "no_ipp": no_ipp,
                    "no_spk": no_spk,
                },
                cache		: false,
                // dataType	: 'json',
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
                    timer   : 5000
                    });
                }
            });
        });

        $(document).on('click', '#request_material', function(){
            var upload_spk 	    = $('#upload_spk').val();
            var ket_request 		= $('#ket_request').val();

            if( upload_spk == ''){
                swal({
                title	: "Error Message!",
                text	: 'File wajib di upload ...',
                type	: "warning"
                });
                return false;
            }

            if( ket_request == ''){
                swal({
                title	: "Error Message!",
                text	: 'Keterangan masih kosong ...',
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
                        url			: base_url + active_controller+'/process_request',
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
                                title	: "Error Message !",
                                text	: 'An Error Occured During Process. Please try again..',						
                                type	: "warning",								  
                                timer	: 7000
                            });
                        }
                    });
                } else {
                swal("Cancelled", "Data can be process again :)", "error");
                return false;
                }
            });
        });

		$(document).on('click', '.detail', function(e){
            e.preventDefault();
            var kode_trans 	    = $(this).data('kode_trans');

            loading_spinner();
            $("#head_title2").html("<b>DETAIL MATERIAL CHANGE "+kode_trans+"</b>");
            $.ajax({
                type:'POST',
                url: base_url + active_controller+'/modal_detail',
                data: {
                    "kode_trans": kode_trans
                },
                cache		: false,
                // dataType	: 'json',
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
                    timer   : 5000
                    });
                }
            });
        });

		$(document).on('click', '.delete', function(){
			var bF	= $(this).data('kode_trans');
			// alert(bF);
			// return false;
			swal({
			  title: "Are you sure?",
			  text: "Delete this data ?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Yes, Process it!",
			  cancelButtonText: "No, cancel process!",
			  closeOnConfirm: true,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					$.ajax({
						url			: base_url+active_controller+'/hapus/'+bF,
						type		: "POST",
						data		: "id="+bF,
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
								window.location.href = base_url + active_controller;
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
		
	});

	function DataTables(no_ipp=null){
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
				url : base_url + active_controller+'/get_data_json_material_change',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp
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
