<?php
$id			= (!empty($data[0]->id))?$data[0]->id:'';
$no_do		= (!empty($data[0]->no_do))?$data[0]->no_do:'';
$tanggal	= (!empty($data[0]->tanggal))?$data[0]->tanggal:'';
$keterangan		= (!empty($data[0]->keterangan))?$data[0]->keterangan:'';
if($no_do!=""){
}
?>
<link rel="stylesheet" href="<?=base_url()?>/chosen/chosen.min.css">
<link rel="stylesheet" href="<?=base_url()?>/adminlte/plugins/daterangepicker/daterangepicker.css">
<script src="<?=base_url()?>/chosen/chosen.jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="<?=base_url()?>/adminlte/plugins/daterangepicker/daterangepicker.js"></script>
<form action="#" method="POST" id="form_ct">
<input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
<div class="box box-success"><br>
    <div class="box-body">
        <div class="form-group row">
            <div class="col-md-4">
                <label>No Delivery</label>
            </div>
            <div class="col-md-8">
				<select name='no_do' id='no_do' class='form-control input-md chosen-select' required>
						<option value='0'>Pilih No Delivery</option>
					 <?php
						foreach($dt_delivery AS $valx){
							$selected="";
							if($no_do==$valx->kode_delivery) $selected=" selected";
							echo "<option value='".$valx->kode_delivery."' ".$selected.">".$valx->nomor_sj." (".($valx->kode_delivery).")</option>";
						}
					 ?>
			    </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Tanggal</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Tanggal" value='<?=$tanggal;?>' required>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4">
                <label>Keterangan</label>
            </div>
            <div class="col-md-8">
                <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value='<?=$keterangan;?>'>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4"></div>
            <div class="col-md-8">
				<button type="button" class="btn btn-primary preview" name="save" id="save"><i class="fa fa-save"></i> Save</button>
                <a href="#" class="btn btn-success preview update <?=(($id=="")?"hidden":"");?>" name="update" id="update"><i class="fa fa-check"></i> Update</a>
            </div>
        </div>
    </div>
</div>
</form>
<style>
    .datepicker{
        cursor:pointer;
    }
	.chosen-container{
		min-width: 200px !important;
		text-align : left !important;
	}
</style> 
<script>
    swal.close();
	$(document).on('click', '#update', function(){
		swal({
		  title: "Are you sure?",
		  text: "Update this data ?",
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
				var baseurl		= base_url + active_controller +'/update_data/<?=$id;?>';
				$.ajax({
					url			: baseurl,
					cache		: false,
					dataType	: 'json',
					processData	: false,
					contentType	: false,
					success		: function(data){
						console.log(data);
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
							window.location.href = base_url + active_controller+'/index';
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
	$(document).on('click', '#save', function(){
		var no_do	= $("#no_do").val();
		var tanggal= $("#tanggal").val();
		var nama	= $("#nama").val();
		if(no_do==''){
			swal({title:"Error Message!", text:'No Delivery', type:"warning"});
			$('#save').prop('disabled',false);
			return false;
		}
		if(tanggal==''){
			swal({title:"Error Message!", text:'Tanggal kosong', type:"warning"});
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
						console.log(data);
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
							window.location.href = base_url + active_controller+'/index';
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
		$('.chosen-select').chosen();
        $('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth:true,
            changeYear:true
        });
	
	<?php
	if($tipe=='view'){
		echo '$("#form_ct :input").prop("disabled", true);
		$(".preview").addClass("hidden");';
	}
	if($tipe=='update'){
		echo '$("#form_ct :input").prop("disabled", true);
		$(".preview").addClass("hidden");
		$(".update").removeClass("hidden");';
	}
	?>
</script>