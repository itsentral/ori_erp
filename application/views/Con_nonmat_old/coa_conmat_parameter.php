<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_ct">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
			<div class="box-tool pull-right">
			<?php if($akses_menu['create']=='1'){ ?>
			  <button type='button' class="btn btn-md btn-info" id='add'><i class="fa fa-plus"></i> Add Parameter</button>
			  <?php } ?>
			</div>
		</div>
		<div class="box-body">
			<div class="table-responsive col-lg-12">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead>
					<tr class='bg-blue'>
						<th class="text-center">Costcenter</th>
						<th class="text-center">Category</th>
						<th class="text-center">COA</th>
						<th class="text-center">Option</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if(!empty($data_row)){
					$i=0;
					foreach ($data_row as $val){
						$i++;
						echo "<tr><td>".strtoupper($val->nm_costcenter)."</td><td>".strtoupper($val->nm_category)."</td><td>".strtoupper($val->coa_biaya)." / ".strtoupper($val->nama)."</td><td>
						<button data-id='".$val->id."' data-coa_biaya='".$val->coa_biaya."' data-costcenter='".$val->costcenter."' data-category='".$val->category."' class='btn btn-sm btn-primary btnedit' title='Edit Data' type='button'><i class='fa fa-edit'></i></button> 
						<button data-id='".$val->id."' class='btn btn-sm btn-danger btndel' title='Delete Data' type='button'><i class='fa fa-trash'></i></button>
						</td></tr>";
					}
				}
				?>
				</tbody>
			</table>
			</div>
		</div>
	 </div>
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
				</div>
				<div class="modal-body" id="view">			
					<input type="hidden" class="form-control" id="id" name="id" value=''>
					<div class="box box-primary"><br>
						<div class="box-body">
							<div class="form-group row">
								<div class="col-md-4">
									<label>Costcenter</label>
								</div>
								<div class="col-md-8">
									<select id="costcenter" name="costcenter" class="form-control input-md chosen-select" required>
									<?php
									echo "<option value=''>Select an option</option>";
									foreach ($data_costcenter as $val){
										echo "<option value='".$val->id."'>".strtoupper($val->nm_costcenter)."</option>";
									}
									?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-4">
									<label>Category</label>
								</div>
								<div class="col-md-8">
									<select id="category" name="category" class="form-control input-md chosen-select" required>
									<?php
									echo "<option value=''>Select an option</option>";
									foreach ($data_category as $val){
										echo "<option value='".$val->id."'>".strtoupper($val->category)."</option>";
									}									
									?>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<div class="col-md-4">
									<label>COA</label>
								</div>
								<div class="col-md-8">
									<?php
									echo form_dropdown('coa_biaya',$data_coa, '',array('id'=>'coa_biaya','class'=>'form-control input-md chosen-select','required'=>'required'));
									?>
								</div>
							</div>
						</div>
					</div>
				
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" name="save" id="save"><i class="fa fa-save"></i> Save</button>
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</form>
<?php $this->load->view('include/footer'); ?>
<style>
.chosen-container.chosen-container-single {
    width: 400px !important;
}
</style>
<script>
	$(document).ready(function(){
		$('#my-grid').DataTable({
			order: [[0, 'asc']]
		});
	});
	$(document).on('click', '#add', function(e){
		e.preventDefault();
		loading_spinner();
		$("#form_ct")[0].reset();
		$("#id").val("");		
		$('select').trigger("chosen:updated");
		$("#head_title").html("<b>ADD PARAMETER</b>");
		$("#ModalView").modal();
		swal.close();
	});
	$(document).on('click', '.btnedit', function(e){
		e.preventDefault();
		loading_spinner();
		$("#form_ct")[0].reset();
		var id = $(this).data('id');
		var costcenter = $(this).data('costcenter');
		var category = $(this).data('category');
		var coa_biaya = $(this).data('coa_biaya');
		$("#id").val(id);
		$("#costcenter").val(costcenter);
		$("#category").val(category);
		$("#coa_biaya").val(coa_biaya);
		$('select').trigger("chosen:updated");
		$("#head_title").html("<b>EDIT PARAMETER</b>");
		$("#ModalView").modal();
		swal.close();
	});

	$(document).on('click', '#save', function(){
		var costcenter	= $("#costcenter").val();
		var category= $("#category").val();
		var coa_biaya	= $("#coa_biaya").val();
		if(costcenter==''){
			swal({title:"Error Message!", text:'Costcenter kosong', type:"warning"});
			$('#save').prop('disabled',false);
			return false;
		}
		if(category==''){
			swal({title:"Error Message!", text:'Category kosong', type:"warning"});
			$('#save').prop('disabled',false);
			return false;
		}
		if(coa_biaya=='0'){
			swal({title:"Error Message!", text:'COA kosong', type:"warning"});
			$('#save').prop('disabled',false);
			return false;
		}
		swal({
		  title: "Are you sure?",
		  text: "Save this data ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData 	= new FormData($('#form_ct')[0]);
				var baseurl		= base_url + active_controller +'/add_data';
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							window.location.reload(true);
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',
						  type				: "warning",
						  timer				: 7000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again", "error");
			return false;
			}
		});
	});

    $(document).on('click', '.btndel', function(){
		var id	= $(this).data('id');
		swal({
		  title: "Are you sure?",
		  text: "Delete this data ?",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Yes, Process it!",
		  cancelButtonText: "No, cancel process!",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/hapus_data/'+id,
					type		: "POST",
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
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							window.location.reload(true);
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',
						  type				: "warning",
						  timer				: 7000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
</script>
