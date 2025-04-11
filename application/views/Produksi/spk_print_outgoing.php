<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
            <a href="<?php echo site_url('produksi/index_loose') ?>" class="btn btn-sm btn-danger" style='float:right; margin-right:5px;'>Back</a>
        </div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">IPP</th>
						<th class="text-center th">SO</th>
						<th class="text-center th">Customer</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">No SPK</th>
						<th class="text-center th">Spec</th>
						<th class="text-center th">Qty</th>
						<th class="text-center th">Print By</th>
						<th class="text-center th">Print Date</th>
						<th class="text-center th">Option</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    // foreach ($result as $key => $value) { $key++;
                    //     echo "<tr>";
                    //         echo "<td align='center'>".$key."</td>";
                    //         echo "<td align='left'>".$value['no_ipp']."</td>";
                    //         echo "<td align='center'>".$value['so_number']."</td>";
                    //         echo "<td align='left'>".$value['nm_customer']."</td>";
                    //         echo "<td align='left'>".$value['product']."</td>";
                    //         echo "<td align='center'>".$value['no_spk']."</td>";
                    //         echo "<td align='left'>".spec_bq2($value['id_milik'])."</td>";
                    //         echo "<td align='center'>".number_format($value['qty'])."</td>";
                    //         echo "<td align='left'>".$value['created_by']."</td>";
                    //         echo "<td align='center'>".date('d-M-Y H:i:s',strtotime($value['created_date']))."</td>";
                    //         echo "<td align='center'>";
                    //         echo "<a href='".site_url('produksi/spk_baru/').$value['kode_spk']."' target='_blank'>Print</a>";
                    //         echo "</td>";
                    //     echo "</tr>";
                    // }
                    ?>
                </tbody>
			</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		DataTables();
	});

    function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"stateSave" : true,
			"autoWidth": false,
			"destroy": true,
			"responsive": true,
			"fixedHeader": {
				"header": true,
				"footer": true
			},
			"aaSorting": [[ 0, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/server_side_spk_print',
				type: "post",
				// data: function(d){
				// 	d.no_ipp = no_ipp
				// },
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			},
			"processing": true,
			"search": {
				return: true
			},
			"serverSide": true,
		});
	}
</script>
