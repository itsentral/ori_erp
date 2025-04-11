<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">   
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right"><br><br>
            <label>Search : </label>
			<select id='bulan' name='bulan' class='form-control input-sm' style=width:120px;'>
				<option value='0'>All Month</option>
                <option value='1'>January</option>
                <option value='2'>February</option>
                <option value='3'>March</option>
                <option value='4'>April</option>
                <option value='5'>May</option>
                <option value='6'>June</option>
                <option value='7'>July</option>
                <option value='8'>August</option>
                <option value='9'>September</option>
                <option value='10'>October</option>
                <option value='11'>November</option>
                <option value='12'>December</option>
			</select>
			<select id='tahun' name='tahun' class='form-control input-sm' style='width:100px;'>
				<option value='0'>All Year</option>
                <?php
				$date = date('Y') + 5;
				for($a=2019; $a < $date; $a++){
					echo "<option value='".$a."'>".$a."</option>";
				}
				?>
			</select>
		</div><br><br>
		<div class="box-tool pull-left">
			<button type='button' target='_blank' class="btn btn-sm btn-success" id='excel_report' style='float:right;'>
				<i class="fa fa-print"></i> Print Excel
			</button>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body" style="width:100%;">
		<table class="table table-bordered table-striped" id="my-grid">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort valignmid" width='80px' rowspan='2'>#</th>
					<th class="text-center valignmid" rowspan='2'>Tanggal QC Check</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Est Material (kg)</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Est Price ($)</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Aktual Material (kg)</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Aktual Price ($)</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Revenue</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Direct Labour</th>
					<th class="text-center no-sort valignmid" rowspan='2'>Indirect Labour</th>
                    <th class="text-center no-sort valignmid" rowspan='2'>Consumable</th>
                    <th class="text-center no-sort valignmid" colspan='5'>FOH</th>
                    <th class="text-center no-sort valignmid" rowspan='2'>Sales & Marketing</th>
                    <th class="text-center no-sort valignmid" rowspan='2'>Umum & Admin</th>
				</tr>
				<tr class='bg-blue'>
					<th class="text-center no-sort valignmid">Machine Cost</th>
                    <th class="text-center no-sort valignmid">Mold mandril Cost</th>

                    <th class="text-center no-sort valignmid">Depreciation FOH</th>
                    <th class="text-center no-sort valignmid">Factory Overhead</th>
                    <th class="text-center no-sort valignmid">Salary Factory Management</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:95%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
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
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}

	/* th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        width: 800px;
        margin: 0 auto;
    } */

	.valignmid{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
        var bulan = $('#bulan').val();
        var tahun = $('#tahun').val();
		DataTables(bulan, tahun);

        $(document).on('change', '#bulan', function(e){
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();
		    DataTables(bulan, tahun);
        });

        $(document).on('change', '#tahun', function(e){
            var bulan = $('#bulan').val();
            var tahun = $('#tahun').val();
		    DataTables(bulan, tahun);
        });
	});
	
	$(document).on('click', '#excel_report', function(e){
		// loading_spinner();
		var bulan 	= $('#bulan').val();
		var tahun 	= $('#tahun').val();

		if(bulan=='0'){
			swal({
				title	: "Error Message!",
				text	: 'Bulan belum dipilih ...',
				type	: "warning"
			});
			return false;
		}

		if(tahun=='0'){
			swal({
				title	: "Error Message!",
				text	: 'Tahun belum dipilih ...',
				type	: "warning"
			});
			return false;
		}

		var Link	= base_url + active_controller +'/excel_project/'+bulan+'/'+tahun;
		window.open(Link);
	});
	
	$(document).on('click', '#detail', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL ["+$(this).data('tanggal')+"]</b>");
		$("#view").load(base_url + active_controller+'/modalDetail/'+$(this).data('tanggal'));
		$("#ModalView").modal();
	});
		
	function DataTables(bulan = null, tahun = null){
		var dataTable = $('#my-grid').DataTable({ 
            "scrollX": true,
			"scrollY": "500",
			"scrollCollapse" : true,
			"serverSide": true,
			"processing": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"fixedColumns":{
				"leftColumns": "1"
			},
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
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url +'index.php/'+active_controller+'/getDataJSON', 
				type: "post",
				data: function(d){
					d.bulan = $('#bulan').val(),
                    d.tahun = $('#tahun').val()
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
