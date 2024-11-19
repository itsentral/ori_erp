<?php
$this->load->view('include/side_menu');
$gudang = $this->uri->segment(3);
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-left">
			
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Material</b></label>
			<div class='col-sm-4'>
				<select name='material2' id='material2' class='form-control input-md'>
					<option value='0'>SELECT MATERIAL</option>
				<?php
					foreach($data_material AS $val => $valx){
						echo "<option value='".$valx['code_group']."'>".strtoupper($valx['code_group'].' - '.$valx['material_name'])." - ".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $valx['code_group']))."</option>";
					}
					?>
				</select>
			</div>
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'><b>Range Date</b></label>
			<div class='col-sm-4'>
				<div class="input-group" style='width: -webkit-fill-available !important;'>
					<div class="input-group-prepend">
						<span class="input-group-text">
							<i class="far fa-calendar-alt"></i>
						</span>
					</div>
					<input type="text" class="form-control float-right" id="range_picker2" placeholder='Select range date' readonly>
				</div>
			</div>	
		</div>
		<div class='form-group row'>
			<label class='label-control col-sm-2'></label>
			<div class='col-sm-4'>
				<?php
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Show','content'=>'Show History','id'=>'showHistory'));
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','value'=>'save','content'=>'Download Excel','id'=>'download_excel_'));
				?>
			</div>	
		</div>
		<div id='show_history_view'></div>
		<hr>
	</div>
	<div class="box-body">
		Filter:
		<div class='form-group row'>
			<div class='col-sm-3'>
				<div class="input-group" style='width: -webkit-fill-available !important;'>
					<div class="input-group-prepend">
						<span class="input-group-text">
							<i class="far fa-calendar-alt"></i>
						</span>
					</div>
					<input type="text" class="form-control float-right" id="range_picker" placeholder='Select range date' readonly value=''>
				</div>
			</div>
			
			<div class='col-sm-3'>
				<select id='material' name='material' class='form-control input-sm' style='min-width:200px;'>
					<option value='0'>All Nama Barang</option>
					<?php
						foreach($data_material AS $val => $valx){
							echo "<option value='".$valx['code_group']."'>".strtoupper($valx['code_group'].' - '.$valx['material_name'])." - ".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $valx['code_group']))."</option>";
						}
					?>
				</select>
			</div>
			<div class='col-sm-5'>
				<?php
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Filter','content'=>'Filter','id'=>'filter_'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'Reset','content'=>'Reset','id'=>'reset_'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','value'=>'save','content'=>'Excel','id'=>'download_excel'));
                ?>
			</div>
		</div>
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">In/Out</th>
					<th class="text-center">Tgl Trans</th>
					<th class="text-center">No Trans</th>
					<th class="text-center">Costcenter</th>
					<th class="text-center">Category</th>
					<th class="text-center">Code</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center no-sort">Spesifikasi</th>
					<th class="text-center no-sort">Qty</th>
                    <th class="text-center no-sort">Keterangan</th>
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
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<style>
	#range_picker, #range_picker2{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
		$('#range_picker, #range_picker2').daterangepicker({
			autoUpdateInput: false,
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

		$('#range_picker, #range_picker2').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format('DD-MM-YYYY'));
		});

		DataTables(0,0,0);
        
        $(document).on('click','#filter_', function(e){
			e.preventDefault();
			var material 	= $('#material').val();
			let range       = $('#range_picker').val();
			var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
				var sPLT 		= range.split(' - ');
				var tgl_awal 	= sPLT[0];
				var tgl_akhir 	= sPLT[1];
            }
			DataTables(material,tgl_awal,tgl_akhir);
		});

		$(document).on('click','#reset_', function(e){
			e.preventDefault();
			DataTables(0,0,0);
		});

		$(document).on('click', '#download_excel', function(e){
            let range       = $('#range_picker').val();
            let material    = $('#material').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
            var Link	= base_url + active_controller +'/download_excel_stok/'+material+'/'+tgl_awal+'/'+tgl_akhir;
            window.open(Link);
        });

		$(document).on('click', '#download_excel_', function(e){
            let range       = $('#range_picker2').val();
            let material    = $('#material2').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
            var Link	= base_url + active_controller +'/download_excel_stok_hist/'+material+'/'+tgl_awal+'/'+tgl_akhir;
            window.open(Link);
        });

		$(document).on('click', '#showHistory', function(e){
            let range       = $('#range_picker2').val();
            let material    = $('#material2').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }

			// if(material == 0){
			// 	swal({
			// 	  title	: "Error Message!",
			// 	  text	: 'Material wajib dipilih ...',
			// 	  type	: "warning"
			// 	});
			// 	return false;	
			// }

            var formData 	=new FormData($('#form_proses_bro')[0]);
			var baseurl=base_url + active_controller +'/show_history';
			$.ajax({
				url			: baseurl,
				type		: "POST",
				data		: {
					'material' : material,
					'tgl_awal' : tgl_awal,
					'tgl_akhir' : tgl_akhir,	
				},
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
					$('#show_history_view').html(data.data_html);
					swal.close();
					}
					else{
						swal({
							title	: "Save Failed!",
							text	: data.pesan,
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
        });
    });
		
	function DataTables(material=null,tgl_awal=null,tgl_akhir=null){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 2, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}], 
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_detil_transaction',
				type: "post",
				data: function(d){
					d.material = material,
					d.tgl_awal = tgl_awal,
					d.tgl_akhir = tgl_akhir
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
