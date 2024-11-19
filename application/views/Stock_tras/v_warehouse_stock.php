<?php
$this->load->view('include/side_menu');
$gudang = $this->uri->segment(3);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class='form-group row'>
			<div class='col-sm-8 text-right'><b>Search:</b></div>
			<div class='col-sm-2'>
				<select id='no_perkiraan' name='no_perkiraan' class='form-control input-sm' style='min-width:200px;'>
					<option value="">All Data</option>
					<?php
					if($rows_coa){
						foreach($rows_coa AS $val => $valCoa){
							$Code_Coa	= $valCoa->coa_1;
							$Query_COA		= "SELECT nama FROM COA WHERE no_perkiraan = '".$Code_Coa."' ORDER BY id DESC LIMIT 1";
							$rows_COA		= $this->accounting->query($Query_COA)->row();
							$Nama_COA		= '-';
							if($rows_COA){
								$Nama_COA	=$rows_COA->nama;
							}
							echo "<option value='".$Code_Coa."'>".$Code_Coa.' '. strtoupper($Nama_COA)."</option>";
						}
					}
					?>
				</select>
			</div>
			<div class='col-sm-2'>
				<input type="text" name='date_filter' id='date_filter' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Change Date'>
			</div>
		</div>
		<button type='button' class='btn btn-sm btn-success' id='download_excel'><i class='fa fa-file-excel-o'></i> Download</button>
		&nbsp;&nbsp;
		<button type='button' class='btn btn-sm btn-primary' id='download_excel_compare'><i class='fa fa-file-excel-o'></i> Download Tras vs Stock</button>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class="row col-md-2 col-md-offset-5" id="loader_proses">
			<div class="loader">
				<span></span>
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Id Material</th>
					<th class="text-center">Material</th>
					<th class="text-center">Category</th>
					<th class="text-center">Warehouse</th>
					<th class="text-center">Stock</th>
                    <th class="text-center">Price Book</th>
					<th class="text-center">Total Value</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
			
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
</form>
<!-- MODAL DETAIL UTAMA -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" id="data_modal">
		</div>
	</div>
</div>


<!-- MODAL DETAIL SECOND -->
<div class="modal fade" id="modalPreview" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" id="data_modal_preview">
		</div>
	</div>
</div>

<!-- MODAL VIEW JURNAL -->
<div class="modal fade" id="modalViewJrnl" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content" id="data_view_jurnal">
		</div>
	</div>
</div>
<?php $this->load->view('include/footer'); ?>
<style>
	/* LOADER */
	.loader span{
	  display: inline-block;
	  width: 12px;
	  height: 12px;
	  border-radius: 100%;
	  background-color: #3498db;
	  margin: 35px 5px;
	  opacity: 0;
	}

	.loader span:nth-child(1){
		background: #4285F4;
	  	animation: opacitychange 1s ease-in-out infinite;
	}

	.loader span:nth-child(2){
  		background: #DB4437;
	 	animation: opacitychange 1s ease-in-out 0.11s infinite;
	}

	.loader span:nth-child(3){
  		background: #F4B400;
	  	animation: opacitychange 1s ease-in-out 0.22s infinite;
	}
	.loader span:nth-child(4){
  		background: #0F9D58;
	  	animation: opacitychange 1s ease-in-out 0.44s infinite;
	}

	@keyframes opacitychange{
	  0%, 100%{
		opacity: 0;
	  }

	  60%{
		opacity: 1;
	  }
	}
	.lebar_col{
		white-space:nowrap;
	}
	.table-bordered {
		border : 1px solid#ccc;
	}
	.table-bordered tbody tr td {
		border : 1px solid#ccc;
		vertical-align:middle;
	}
	.table-bordered thead tr th, .table-bordered thead tr td {
		border : 1px solid#ccc;
		vertical-align:middle;
	}
	.table-bordered tfoot tr td, .table-bordered tfoot tr th {
		border : 1px solid#ccc;
		vertical-align:middle;
	}
	
	.blue-navy{
		background-color : #16697A !important;
		color : #ffffff !important;
	}
	
	.blue-grey{
		background-color : #546e7a !important;
		color : #ffffff !important;
	}
	
	 
	.text-center{
		text-align : center !important;
		vertical-align	: middle !important;
	}
	
	.text-left{
		text-align : left !important;
		vertical-align	: middle !important;
	}
	
	.text-right{
		text-align : right !important;
		vertical-align	: middle !important;
	}
	
	.bg_selisih{
		background-color:#FDF9CF;
	}
	.modal { overflow-y: auto !important; }
	
	.text-amber{
		color : #ff6f00 !important;
	}
	
	.text-yellow{
		color : #f9a825  !important;
	}
	
	.text-brown{
		color : #5d4037 !important;
	}
	
	.text-blue-grey{
		color : #37474f !important;
	}
	
	.text-green{
		color : #1b5e20 !important;
	}
	.text-blue{
		color : #01579b !important;
	}
	
	.text-teal{
		color : #00695c !important;
	}
	
	.text-red{
		color : #c62828 !important;
	}
	
	.text-purple{
		color : #7b1fa2 !important;
	}
	
	.text-maroon{
		color : #c2185b !important;
	}
	 
	 .datepicker{
		cursor:pointer;
	}
	
	.modal {
		position: fixed;
		top: 0;
		left: 0;
		z-index: 1050;
		display: none;
		width: 100%;
		height: 100%;
		overflow: hidden;
		outline: 0
	} 
	
	@media (min-width: 768px) {
		.modal-lg,.modal-xl {
			min-width:980px
		}
	}
	
	@media (min-width: 992px) {
		.modal-lg,.modal-xl {
			min-width:986px
		}
	}

	@media (min-width: 1200px) {
		.modal-lg, .modal-xl {
			min-width:1140px
		}
	}
</style>

<script>
	var base_url			= '<?php echo site_url(); ?>';
	var active_controller	= '<?php echo($this->uri->segment(1)); ?>';	
	$(document).ready(function() {
		//data_ar();
		$("#loader_proses").hide();
		
		$('#date_filter').datepicker({
			dateFormat		: 'yy-mm-dd',
			changeMonth		: true,
			changeYear		: true,
			maxDate			: '-1d',
			showButtonPanel: true,
			closeText		: 'Clear',
			onClose			: function (dateText, inst) {
				GetDisplayData();
				
			}
		});
		
		GetDisplayData();
		
	});
	
	$(document).on('change','#no_perkiraan',GetDisplayData);
	
	function GetDisplayData(){
		let CoaChosen 	= $('#no_perkiraan').val();
		let DateChosen 	= $('#date_filter').val();
		
		let table_data 		= $('#my-grid').DataTable({
			"serverSide": true,
			"destroy"	: true,
			"stateSave" : false,
			"bAutoWidth": false,
			"oLanguage": {
				"sSearch": "<b>Live Search : </b>",
				"sLengthMenu": "_MENU_ &nbsp;&nbsp;<b>Records Per Page</b>&nbsp;&nbsp;",
				"sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
				"sInfoFiltered": "(filtered from _MAX_ total entries)", 
				"sZeroRecords": "No matching records found", 
				"sEmptyTable": "No data available in table", 
				"sLoadingRecords": "Please wait - loading...", 
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"aaSorting": [[ 2, "asc" ]],
			"columnDefs": [
					{"targets":0,"sClass":"text-center","searchable":false,"orderable":false},
					{"targets":1,"sClass":"text-center"},
					{"targets":2,"sClass":"text-left"},
					{"targets":3,"sClass":"text-center"},
					{"targets":4,"sClass":"text-left text-wrap"},
					{"targets":5,"sClass":"text-center"},
					{"targets":6,"sClass":"text-right"},
					{"targets":7,"sClass":"text-right"}
				],
				
			"sPaginationType": "simple_numbers", 
			"iDisplayLength": 50,
			"aLengthMenu": [[10, 50, 100, 500, -1], [10, 50, 100, 500, 'ALL']],
			"ajax":{
				url 	: base_url +'/'+ active_controller+'/display_data',
				type	: "post",
				data	:{'no_perkiraan':CoaChosen,'tanggal':DateChosen},
				cache	: false,
				beforeSend: function() {
					$('#loader_proses').show();
				}, 
				complete: function() {
					$('#loader_proses').hide();
				},
				error	: function(){ 
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
	
	
	$(document).on('click', '#download_excel_compare', function(e){
		e.preventDefault();
		let CoaChosen 	= $('#no_perkiraan').val();
		let DateChosen 	= $('#date_filter').val();
		
		
		var Links		= base_url + active_controller+'/ExcelStockCompare?coa='+encodeURIComponent(CoaChosen)+'&tgl='+encodeURIComponent(DateChosen);
		window.open(Links,'_blank');
	});
	
	
	
	$(document).on('click', '#download_excel', function(e){
		e.preventDefault();
		let CoaChosen 	= $('#no_perkiraan').val();
		let DateChosen 	= $('#date_filter').val();
		
		
		var Links		= base_url + active_controller+'/ExcelGudangStok?coa='+encodeURIComponent(CoaChosen)+'&tgl='+encodeURIComponent(DateChosen);
		window.open(Links,'_blank');
	});

	const ActionPreviewDetail = (ParamPreview)=>{
		let ParamAction	= ParamPreview.action;
		let ParamCode	= ParamPreview.code;		
		let ParamURL	= base_url+'/'+active_controller+'/'+ParamAction;
		loading_spinner();
		$.post(ParamURL,{'code':ParamCode},function(response) {
			swal.close();
			$("#modalPreview").modal('show');
			$("#data_modal_preview").html(response);	
		  
		});
	}
	
	const PreviewDetailHistory = (ParamPreview)=>{
		let ParamAction	= ParamPreview.action;
		let ParamCode	= ParamPreview.code;
		let ParamURL	= base_url+'/'+active_controller+'/'+ParamAction;
		loading_spinner();
		$.post(ParamURL,{'code':ParamCode},function(response) {
			swal.close();
			$("#modalViewJrnl").modal('show');
			$("#data_view_jurnal").html(response);	
		  
		});
	}
	
</script>
