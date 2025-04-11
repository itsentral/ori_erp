<?php
$this->load->view('include/side_menu');
$status = $tanda;
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
<input type="hidden" id='status' name='status' value='<?=$status;?>'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<button type='button' class="btn btn-md btn-success" id='loading_excel'>
				<i class="fa fa-file-excel"></i> &nbsp;Download
			</button>
			<a href="<?php echo site_url('qc/report_spool') ?>" class="btn btn-md btn-warning">
                <i class="fa fa-replay"></i> TO - QC SPOOL
            </a>
		</div><br><br>
			<div class='form-group row'>
				<div class='col-sm-4'>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="far fa-calendar-alt"></i>
							</span>
						</div>
						<input type="text" class="form-control float-right" id="range_picker" placeholder='Select range date' readonly value=''>
					</div>
				</div>	
				<div class='col-sm-3'>
					<select name='no_ipp' id='no_ipp' class='form-control input-sm chosen-select'>
						<option value='0'>ALL SO & PROJECT</option>
						<?php
						foreach($IPP as $val => $valx)
						{
							$EXPLODE = explode('-',$valx['product_code']);
							$PROJECT = strtoupper(get_name('production','project','no_ipp',str_replace('PRO-','',$valx['id_produksi'])));
							echo "<option value='".$valx['id_produksi']."'>".$EXPLODE[0]." - ".$PROJECT."</option>";
						}
						?>
					</select>
				</div>
				<div class='col-sm-2'>
					<select name='no_spk' id='no_spk' class='form-control input-sm chosen-select'>
						<option value='0'>ALL NO SPK</option>
						<?php
						foreach($NO_SPK as $val => $valx)
						{
							echo "<option value='".$valx['no_spk']."'>".$valx['no_spk']."</option>";
						}
						?>
					</select>
				</div>
				<div class='col-sm-3'>
					<select name='product' id='product' class='form-control input-sm chosen-select'>
						<option value='0'>ALL PRODUCT</option>
						<?php
						foreach($PRODUCT as $val => $valx)
						{
							echo "<option value='".$valx['id_category']."'>".strtoupper($valx['id_category'])."</option>";
						}
						?>
					</select>
				</div>
				
			<!-- </div> -->
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table class="table table-sm table-bordered table-striped" id="my-grid2" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Customer</th>
                    <th class="text-center">Project</th>
                    <th class="text-center">No SO</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">No PO</th>
                    <th class="text-center">Product</th>
                    <th class="text-center no-sort">Spec</th>
                    <th class="text-center">Daycode</th>
                    <th class="text-center">QC Pass Date</th>
                    <th class="text-center">QC Release</th>
                    <th class="text-center no-sort">Ket</th>
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
 <div class="modal fade" id="ModalView2">
    <div class="modal-dialog"  style='width:50%; '>
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
<script>
	$(document).ready(function(){
		$('#range_picker').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

		$(document).on('click', '.edit', function(e){
            e.preventDefault();
            loading_spinner();
            $("#head_title2").html("<b>Edit Report</b>");
            $("#view2").load(base_url + active_controller + '/modalEditReport/'+$(this).data('id'));
            $("#ModalView2").modal();
        });

		$('#range_picker').val('');

		let status = $('#status').val();
		let range = $('#range_picker').val();
		let no_ipp = $('#no_ipp').val();
		let no_spk = $('#no_spk').val();
		let product = $('#product').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
		var sPLT 		= range.split(' - ');
		var tgl_awal 	= sPLT[0];
		var tgl_akhir 	= sPLT[1];
		}
		DataTables2(status,no_ipp,no_spk,product,tgl_awal,tgl_akhir);
	});

	$('#range_picker').on('apply.daterangepicker', function(ev, picker) {
		let status = $('#status').val();
		let range = $('#range_picker').val();
		let no_ipp = $('#no_ipp').val();
		let no_spk = $('#no_spk').val();
		let product = $('#product').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
		var sPLT 		= range.split(' - ');
		var tgl_awal 	= sPLT[0];
		var tgl_akhir 	= sPLT[1];
		}
		DataTables2(status,no_ipp,no_spk,product,tgl_awal,tgl_akhir);
	});

	$('#range_picker').on('cancel.daterangepicker', function(ev, picker) {
		$('#range_picker').val('');
		let status = $('#status').val();
		let range = $('#range_picker').val();
		let no_ipp = $('#no_ipp').val();
		let no_spk = $('#no_spk').val();
		let product = $('#product').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
		var sPLT 		= range.split(' - ');
		var tgl_awal 	= sPLT[0];
		var tgl_akhir 	= sPLT[1];
		}
		DataTables2(status,no_ipp,no_spk,product,tgl_awal,tgl_akhir);
	});

	$(document).on('change','#no_ipp, #no_spk, #product', function(){
		let status = $('#status').val();
		let range = $('#range_picker').val();
		let no_ipp = $('#no_ipp').val();
		let no_spk = $('#no_spk').val();
		let product = $('#product').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
			var sPLT 		= range.split(' - ');
			var tgl_awal 	= sPLT[0];
			var tgl_akhir 	= sPLT[1];
		}
		DataTables2(status,no_ipp,no_spk,product,tgl_awal,tgl_akhir);
	});

	$(document).on('click', '#loading_excel', function(e){
		let range = $('#range_picker').val();

		if(range=='' || range==null){
			swal({
				title	: "Error Message!",
				text	: 'Range Date wajib diisi ...',
				type	: "warning"
			});
			return false;
		}
		let no_ipp = $('#no_ipp').val();
		let no_spk = $('#no_spk').val();
		let product = $('#product').val();
		var tgl_awal 	= '0';
		var tgl_akhir 	= '0';
		if(range != ''){
		var sPLT 		= range.split(' - ');
		var tgl_awal 	= sPLT[0];
		var tgl_akhir 	= sPLT[1];
		}
		var Link	= base_url + active_controller +'/download_excel/'+no_ipp+'/'+no_spk+'/'+product+'/'+tgl_awal+'/'+tgl_akhir;
		window.open(Link);
	});

	$(document).on('click', '#edit_report', function(e){
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
					var formData 	=new FormData($('#form_proses_bro')[0]);
					var baseurl=base_url + active_controller +'/modalEditReport';
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
									  timer	: 7000
									});
								window.location.href = base_url + active_controller + '/report';
							}
							else{
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000
								});
							}
							$('#edit_report').prop('disabled',false);
						},
						error: function() {

							swal({
							  title	: "Error Message !",
							  text	: 'An Error Occured During Process. Please try again..',
							  type	: "warning",
							  timer	: 7000
							});
							$('#edit_report').prop('disabled',false);
						}
					});
			  } else {
				swal("Cancelled", "Data can be process again :)", "error");
				$('#edit_report').prop('disabled',false);
				return false;
			  }
		});
	});


	function DataTables2(status=null,no_ipp=null,no_spk=null,product=null,tgl_awal=null,tgl_akhir=null){
		var dataTable = $('#my-grid2').DataTable({
			"serverSide": true,
			"stateSave" : true,
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
				url : base_url + active_controller+'/server_side_qc_report',
				type: "post",
				data: function(d){
					d.status = status,
					d.no_ipp = no_ipp,
					d.no_spk = no_spk,
					d.product = product,
					d.tgl_awal = tgl_awal,
					d.tgl_akhir = tgl_akhir
				},
				cache: false,
				error: function(){
					$(".my-grid2-error").html("");
					$("#my-grid2").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid2_processing").css("display","none");
				}
			}
		});
	}
</script>
