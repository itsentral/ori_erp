<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-left">
           
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <div class='form-group row'>
            <label class='label-control col-sm-2'><b>IPP Number</b></label>
            <div class='col-sm-4'>
                <?=$no_ipp;?>
                <input type="hidden" name='no_ipp' id='no_ipp' value='<?=$no_ipp;?>'>				
                <input type="hidden" name='id_milik' id='id_milik' value='<?=$id_milik;?>'>				
                <input type="hidden" name='max_booking' id='max_booking' value='<?=$max_booking;?>'>				
            </div>
        </div>
        <div class='form-group row'>
            <label class='label-control col-sm-2'><b>Product</b></label>
            <div class='col-sm-4'>
                <?=spec_deadstok($id_milik);?>			
            </div>
        </div>
		<div class='form-group row'>
            <label class='label-control col-sm-2'><b>No SPK</b></label>
            <div class='col-sm-4'>
                <?=(!empty($GET_NO_SPK[$id_milik]['no_spk']))?$GET_NO_SPK[$id_milik]['no_spk']:'-';?>			
            </div>
        </div>
        <div class='form-group row'>
            <label class='label-control col-sm-2'><b>Maksimal Booking</b></label>
            <div class='col-sm-4'>
                <?=$max_booking;?>	<span id='booking_label'></span>		
            </div>
        </div>

        <a href="<?php echo site_url('produksi/index_loose') ?>" class="btn btn-sm btn-danger" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <button type='button' class='btn btn-sm btn-success' style='float:right; margin-bottom:10px;' id='booking_deadstok'><i class='fa fa-hand-pointer-o'></i>&nbsp;Booking</button>
        <br>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Category</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">Resin</th>
                    <th class="text-center">Length</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Qty Booking</th>
                    <!-- <th class="text-center no-sort">#</th> -->
                </tr>
            </thead>
            <tbody></tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
        let no_ipp = $('#no_ipp').val()
        let id_milik = $('#id_milik').val()
        let max_booking = $('#max_booking').val()
		DataTables(no_ipp, id_milik, max_booking);

		$(document).on('keyup','.qty_booking', function(){
			let no_ipp = $('#no_ipp').val()
            let id_milik = $('#id_milik').val()
            let max_booking = $('#max_booking').val()
            let id_product = $(this).data('id_product')
            let qty_booking = $(this).val()
			
            $.ajax({
                url: base_url + active_controller+'/temporerBookingDeadstok',
                cache: false,
                type: "POST",
                data: {
                    'no_ipp'	    : no_ipp,
                    'id_milik'	    : id_milik,
                    'max_booking'	: max_booking,
                    'id_product'	: id_product,
                    'qty_booking'	: qty_booking,
                },
                dataType: "json",
                success: function(data){
                    if(data.status == '1'){
                        if(getNum(data.qty_booking) <= getNum(data.max_booking)){
                            $('#booking_label').html(`<span class='text-success text-bold'>/ Kamu sudah booking ${data.qty_booking} qty</span>`)
                            $('#booking_deadstok').show()
                        }
                        if(getNum(data.qty_booking) > getNum(data.max_booking)){
                            $('#booking_label').html(`<span class='text-danger text-bold'>/ Maksimal booking ${data.max_booking}, kamu booking ${data.qty_booking} qty</span>`)
                            $('#booking_deadstok').hide()
                        }
                    }
                }
            });
		});

		$(document).on('click', '#booking_deadstok', function(){
			
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
					var formData  	= new FormData($('#form_proses')[0]);
					$.ajax({
						url			: base_url + active_controller+'/process_booking_deadstok',  
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
								window.open(base_url + active_controller+'/print_booking_deadstok/'+data.kode_booking_deadstok,'_blank');

								window.location.href = base_url + active_controller + '/index_loose';
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

	function DataTables(no_ipp=null, id_milik=null, max_booking=null){
		var dataTable = $('#my-grid').DataTable({
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"processing": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_deadstok',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp,
					d.id_milik = id_milik,
					d.max_booking = max_booking
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
