<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-left">
            <select name='no_ipp' id='no_ipp' class='form-control input-sm chosen-select' style='width:200px; float:right;'>
                <option value='0'>ALL IPP</option>
                <?php
                foreach($list_ipp as $val => $valx)
                {
                    echo "<option value='".$valx['id_produksi']."'>".str_replace('PRO-','',$valx['id_produksi'])."</option>";
                }
                ?>
            </select>
		</div>
		<div class="box-tool pull-right">
			<select name='spool_induk' id='spool_induk' class='form-control input-sm chosen-select' style='width:250px; float:right; margin-top:10px;'>
                <option value='0'>Buat Baru Spool</option>
                <?php
                foreach($data_spool as $val => $valx)
                {
                    echo "<option value='".$valx['spool_induk']."'>Tambahkan Ke - ".$valx['spool_induk']."</option>";
                }
                ?>
            </select>

			<select name='kode_spool' id='kode_spool' class='form-control input-sm chosen-select' style='width:200px; float:right; margin-top:10px;'>
                <option value='0'>List Empty</option>
            </select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <a href="<?php echo site_url('ppic/spool') ?>" class="btn btn-sm btn-danger" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <button type='button' class='btn btn-sm btn-success' style='float:right; margin-bottom:10px;' id='make_spool'><i class='fa fa-puzzle-piece'></i>&nbsp;Buat Spool</button>
        <br>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">Id Product</th>
                    <th class="text-center no-sort">Qty Sisa</th>
                    <th class="text-center no-sort">Qty Spool</th>
                    <th class="text-center no-sort">#</th>
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

		$(document).on('change','#no_ipp', function(){
			let no_ipp = $('#no_ipp').val();
			DataTables(no_ipp);
		});

		$(document).on('change','#spool_induk', function(){
			let spool_induk = $(this).val();
			let kode_spool = $('#kode_spool');
			$.ajax({
				url: base_url + active_controller+'/getDetailSpool',
				cache: false,
				type: "POST",
				data: {
					'spool_induk':spool_induk
				},
				dataType: "json",
				success: function(data){
					$(kode_spool).html(data.option).trigger("chosen:updated");
				}
			});
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
						url			: base_url + active_controller+'/create_spool',  
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
								window.location.href = base_url + active_controller + '/add_spool';
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
