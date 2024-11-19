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
			<!-- <button type='button' class="btn btn-sm btn-success" id='loading_excel'>
				<i class="fa fa-file-excel"></i> &nbsp;Download
			</button> -->
		</div><br><br>
        <div class='form-group row'>
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
    <div class="modal-dialog"  style='width:80%;'>
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
		let no_ipp = $('#no_ipp').val();
		DataTables2(no_ipp);
	});

	$(document).on('change','#no_ipp', function(){
		let no_ipp = $('#no_ipp').val();
		DataTables2(no_ipp);
	});

    $(document).on('click', '.detail', function(e){
        e.preventDefault();
        loading_spinner();
        $("#head_title2").html("<b>Progress QC</b>");
        $("#view2").load(base_url + active_controller + '/modalDetailQCIPP/'+$(this).data('id'));
        $("#ModalView2").modal();
    });

	// $(document).on('click', '#loading_excel', function(e){
	// 	let range = $('#range_picker').val();
	// 	let no_ipp = $('#no_ipp').val();
	// 	let no_spk = $('#no_spk').val();
	// 	let product = $('#product').val();
	// 	var tgl_awal 	= '0';
	// 	var tgl_akhir 	= '0';
	// 	if(range != ''){
	// 	var sPLT 		= range.split(' - ');
	// 	var tgl_awal 	= sPLT[0];
	// 	var tgl_akhir 	= sPLT[1];
	// 	}
	// 	var Link	= base_url + active_controller +'/download_excel/'+no_ipp+'/'+no_spk+'/'+product+'/'+tgl_awal+'/'+tgl_akhir;
	// 	window.open(Link);
	// });


	function DataTables2(no_ipp=null){
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
				url : base_url + active_controller+'/server_side_qc_progress',
				type: "post",
				data: function(d){
					d.no_ipp = no_ipp
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
