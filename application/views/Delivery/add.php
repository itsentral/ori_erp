<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-left">
            <select name='no_ipp' id='no_ipp' class='form-control input-sm chosen-select' style='width:200px; float:right;'>
                <option value='0'>PILIH SO</option>
                <?php
                foreach($list_ipp as $val => $valx)
                {
                    echo "<option value='".$valx['id_produksi']."'>".$valx['so_number']."</option>";
                }
                ?>
            </select>
		</div>
		<div class="box-tool pull-right">
			<select name='kode_delivery' id='kode_delivery' class='form-control input-sm chosen-select' style='width:250px; float:right; margin-top:10px;'>
                <option value='0'>Buat Baru Delivery</option>
            </select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <a href="<?php echo site_url('delivery') ?>" class="btn btn-sm btn-danger" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <button type='button' class='btn btn-sm btn-success' style='float:right; margin-bottom:10px;' id='make_spool'><i class='fa fa-truck'></i>&nbsp;Buat Delivery</button>
        <br>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center" width='4%'>#</th>
                    <th class="text-center" width='8%'>IPP</th>
                    <th class="text-center" width='15%'>Product</th>
                    <th class="text-center" width='8%'>No SPK</th>
                    <th class="text-center" width='12%'>Product Code</th>
                    <th class="text-center" width='12%'>Spec</th>
                    <th class="text-center">Id Product</th>
                    <th class="text-center no-sort" width='5%'>Sisa</th>
                    <th class="text-center no-sort" width='5%'>Delivery</th>
                    <th class="text-center no-sort" width='3%'>#</th>
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
		$('.chosen-select').chosen({
			width: '150px'
		})
		let no_ipp = $('#no_ipp').val();
		DataTables(no_ipp);

		// $(document).on('change','#no_ipp', function(){
		// 	let no_ipp = $('#no_ipp').val();
		// 	DataTables(no_ipp);
		// });

        $(document).on('change','#no_ipp', function(){
			let no_ipp = $(this).val();
			let kode_delivery = $('#kode_delivery');
			$.ajax({
				url: base_url + active_controller+'/getDetaildelivery',
				cache: false,
				type: "POST",
				data: {
					'no_ipp':no_ipp,
					'category'	:'loose',
				},
				dataType: "json",
				success: function(data){
					$(kode_delivery).html(data.option).trigger("chosen:updated");
					DataTables(data.no_ipp);
				}
			});
		});

		$(document).on('change','.changeTemp', function(){
			let id_milik 	= $(this).data('id_milik')
			let no_ipp 		= $('#ipp_'+id_milik).val()
			let qty 		= $('#spk_'+id_milik).val()
			let check 		= $(this).parent().parent().parent().find('.chk_personal').is(":checked")
			let sisa_spk 	= getNum($(this).parent().parent().parent().find('.sisa_spk').html().split(",").join(""))
			// let check 		= $(this).parent().parent().parent().html()
			if(qty > sisa_spk){
				$('#spk_'+id_milik).val(sisa_spk)
			}
			// console.log(check);
			// return false;
			if(check === true || check === false){
				$.ajax({
					url: base_url + active_controller+'/changeDeliveryTemp',
					cache: false,
					type: "POST",
					data: {
						'id_milik'	:id_milik,
						'no_ipp'	:no_ipp,
						'qty'		:qty,
						'check'		:check,
						'category'	:'loose',
					},
					dataType: "json",
					success: function(data){
						console.log(data.pesan)
					}
				});
			}
		});

		$(document).on('click', '#make_spool', function(){
			
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
						url			: base_url + active_controller+'/create_delivery',  
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

	function DataTables(no_ipp=null){
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
				url : base_url + active_controller+'/server_side_request',
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
