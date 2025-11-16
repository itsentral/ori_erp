<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<!-- <label>Search : &nbsp;&nbsp;&nbsp;</label>
			<input type="text" name="date_range" id="date_range" class="form-control input-md datepicker" style='margin-bottom:5px;' readonly="readonly" placeholder="Select Date">
			<button type='button'class="btn btn-sm btn-success" id='pdf_report' style='float:right;'>
				<i class="fa fa-pdf"></i> Print PDF
			</button> -->
		</div>
		<br><br>
		<div class="box-tool pull-left">
		<?php
			if($akses_menu['create']=='1'){ 
			?>
				<a href="<?php echo site_url('con_nonmat/warehouse_rutin') ?>" class="btn btn-sm btn-success" id='btn-add'>
					<i class="fa fa-plus"></i> &nbsp;&nbsp;Add Pengajuan
				</a>
			<?php
			}
		?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<input type='hidden' id='tanda' value='<?=$tanda;?>'>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">No</th>
					<th class="text-center">Kode</th>
					<th class="text-center">Asal Permintaan</th>
					<th class="text-center">No PR</th>
					<th class="text-center">Untuk Kebutuhan</th>
					<th class="text-center">Req. By</th>
					<th class="text-center">Req. Date</th>
					<th class="text-center">Status</th>
					<th class="text-center no-sort">#</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
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
	
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		var tanda = $('#tanda').val();
        DataTables(tanda);
        $('.maskM').maskMoney();
		$('.tgl').datepicker();
    });
	
	$(document).on('click', '.view_pr', function(e){ 
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL PR ["+$(this).data('pengajuangroup')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_detail_pr/'+$(this).data('no_ipp')+'/'+$(this).data('sts_app')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 	: $(this).data('user'),
				"pengajuangroup" 	: $(this).data('pengajuangroup'),
			},
			cache		: false,
			// dataType	: 'json',
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

	$(document).on('click', '.edit_pr', function(e){ 
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>EDIT PR ["+$(this).data('pengajuangroup')+"]</b>");
		$.ajax({
			url: base_url + active_controller+'/modal_detail_pr_rutin_edit/'+$(this).data('no_ipp')+'/'+$(this).data('sts_app')+'/'+$(this).data('tanda'),
			type: "POST",
			data: {
				"id_user" 	: $(this).data('user'),
				"pengajuangroup" 	: $(this).data('pengajuangroup'),
			},
			cache		: false,
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
	});
	
    $(document).on('click', '.print_pr', function(e){ 
		e.preventDefault();
        var Link	= base_url + active_controller +'/print_detail_pr/'+$(this).data('user')+'/'+$(this).data('no_ipp')+'/'+$(this).data('sts_app')+'/'+$(this).data('pengajuangroup')+'/'+$(this).data('tanda');
			window.open(Link)
	});
	
	$(document).on('click', '#save_edit_pr', function(e){
		e.preventDefault();
		$('#save_edit_pr').prop('disabled',true);
		
		swal({ 
			title: "Are you sure?",
			text: "You will save be able to process again this data!",
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
					url			: base_url + active_controller+'/modal_detail_pr_rutin_edit_save',
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
							window.location.href = base_url + active_controller+'/pr_rutin';
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000,
							});
							$('#save_edit_pr').prop('disabled',false);
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
						});
						$('#save_edit_pr').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save_edit_pr').prop('disabled',false);
			return false;
			}
		});
	});
	
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		var split_id	= get_id.split('_');
		var id 			= parseInt(split_id[1])+1;
		var id_bef 		= split_id[1];
		var id_category = $(this).data('category')

		$.ajax({
			url: base_url + active_controller+'/get_add/'+id+'/'+id_category,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$(".numberOnly2").autoNumeric('init', {mDec: '2', aPad: false});
				$('.datepicker').datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true
				});
				swal.close();
			},
			error: function() {
				swal({
					title	: "Error Message !",
					text	: 'Connection Time Out. Please try again..',
					type	: "warning",
					timer	: 3000
				});
			}
		});
	});

	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent().attr('class');
		$("."+get_id).remove();

		let SUM = 0
		$('.cal_tot_budget').each(function(){
			var budget	= getNum($(this).text().split(",").join(""))
			SUM += budget
		})
		$('#cal_tot_budget').text(number_format(SUM,2))
	});
		
	function DataTables(tanda = null){
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
				url : base_url + active_controller+'/server_side_pr_rutin',
				type: "post",
				data: function(d){
					d.tanda = tanda
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
