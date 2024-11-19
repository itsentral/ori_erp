<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">Nomor SO</th>
					<th class="text-center">Project</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Nilai SO</th>
					<th class="text-center"></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$numb=0;
			if(!empty($result)){
				foreach($result AS $val => $valx){
					$numb++;
					echo '<tr><td>'.$numb.'</td><td>'.$valx->no_ipp.'</td><td>'.$valx->project.'</td><td>'.$valx->nm_customer.'</td><td></td><td></td></tr>';
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:60%; '>
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
<script>
	$(document).ready(function(){
		$('#my-grid').DataTable();
	});

	$(document).on('click', '.view', function(e){
		e.preventDefault();
		loading_spinner();
		var id = $(this).data('code');
		$("#head_title").html("<b>View Sales Order</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/view_sales_invoice/'+id);
		$("#ModalView").modal();
	});

</script>
