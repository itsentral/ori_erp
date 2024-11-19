<?php
$this->load->view('include/side_menu'); 
//echo"<pre>";print_r($data_menu);
?> 
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>		
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>		 	 
				<label class='label-control col-sm-2'><b>Komponen <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='product_parent' id='product_parent' class='form-control input-md'>
						<option value='0'>Pilih Komponent</option>
						<?php
							foreach($product AS $val => $valx){
								echo "<option value='".strtolower($valx['product_parent'])."'>".strtoupper($valx['product_parent'])."</option>";
							}
						?>
					</select>
				</div>
				<label class='label-control col-sm-2'><b>Standart <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>            
					<select name='standart_code' id='standart_code' class='form-control input-md'>
						<option value='0'>Pilih Standart</option>
						<?php
							foreach($standart AS $val => $valx){
								echo "<option value='".strtoupper($valx['nm_default'])."'>".strtoupper($valx['nm_default'])."</option>";
							}
						?>
					</select> 
				</div>
			</div>		
		</div>
		<br>
		<button type='button' name='add_liner' id='add_liner' class='btn btn-success btn-md' style='width:100px; margin-left: 10px;'>Add Step</button>
		<button type='button' name='add_list' id='add_list' class='btn btn-primary btn-md' style='width:100px; margin-left: 10px;'>Add Step List</button>
		
		<div class="box-body" style="">
			<table id="my-grid_liner" class="table table-striped table-bordered table-hover table-condensed" width="100%">
				<tbody id='detail_body_liner'>
				</tbody>
			</table>
		</div>
		<div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back', 'id'=>'kembali'));
			?>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
  
  <div class="modal fade" id="ModalView2">
		<div class="modal-dialog"  style='width:30%; '>
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

<style type="text/css">
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
	#unit_chosen{
		width: 100% !important;
	}
</style>

<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		$(".numberOnly").on("keypress keyup blur",function (event) {
			if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
				event.preventDefault();
			}
		});
		
		$(document).on('click', '#add_list', function(e){
			e.preventDefault();
			$("#head_title2").html("<b>ADD LIST STEP</b>");
			$("#view2").load(base_url +'index.php/'+ active_controller+'/modalAddStep_Master/');
			$("#ModalView2").modal();
		});
		
		$("#harga, #est_pakai").keyup(function(){
			var harga 		= $("#harga").val();
			var est_pakai	= $("#est_pakai").val();
			
			var dep_mo		= parseFloat(harga) / parseFloat(est_pakai);
			
			$("#biaya_per_pcs").val(dep_mo.toFixed(2));
		});
		
		
		$('#simpan-bro').click(function(e){
			e.preventDefault();
			$(this).prop('disabled',true);
			var product_parent	= $('#product_parent').val();
			var standart_code	= $('#standart_code').val();
			
			if(product_parent=='0' || product_parent==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Component is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}
			if(standart_code=='0' || standart_code==null){
				swal({
				  title	: "Error Message!",
				  text	: 'Standart is Empty, please input first ...',
				  type	: "warning"
				});
				$('#simpan-bro').prop('disabled',false);
				return false;	
			}

			
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
						var formData  	= new FormData($('#form_proses_bro')[0]);
						var baseurl		= base_url + active_controller +'/add_step';
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
									window.location.href = base_url + active_controller +'/step';
								}
								else{ 
									if(data.status == 2){
										swal({
										  title	: "Save Failed!",
										  text	: data.pesan,
										  type	: "warning",
										  timer	: 7000
										});
									}
									else{
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
									$('#simpan-bro').prop('disabled',false);
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
								$('#simpan-bro').prop('disabled',false);
							}
						});
				  } else {
					swal("Cancelled", "Data can be process again :)", "error");
					$('#simpan-bro').prop('disabled',false);
					return false;
				  }
			});
		});
		
		
		$(document).on('click','#kembali', function(){
			window.location.href = base_url + active_controller + "/step";
		});
		
		
		var nomor	= 1;
		

		$('#add_liner').click(function(e){
			e.preventDefault();
			AppendBaris_Liner(nomor);
		});
	
		
		$(document).on('click','.del_record', function(){
			$('#trliner_'+$(this).data('nomor')).remove();
		});
			
		
		function AppendBaris_Liner(intd){
			var nomor	= 1;
			var valuex	= $('#detail_body_liner').find('tr').length;
			if(valuex > 0){
				var akhir	= $('#detail_body_liner tr:last').attr('id');
				var det_id	= akhir.split('_');
				var nomor	= parseInt(det_id[1])+1;
			}

			var Rows	 = "<tr id='trliner_"+nomor+"'>";
				Rows 	+= 	"<td align='center' width='10%'>";
				Rows 	+=		"<div><button type='button' class='btn btn-danger btn-sm del_record ' data-nomor='"+nomor+"' title='Delete Record'>Delete Record</button></div>";
				Rows 	+= 	"</td>";
				Rows	+= 	"<td width='20%' style='vertical-align: middle;'>";
				Rows	+=		"STEP NAME";
				Rows	+= 	"</td>";
				Rows	+= 	"<td align='left'>";
				Rows	+=		"<select name='ListStep["+nomor+"][step]' id='step_"+nomor+"' class='chosen_select form-control inline-block' required><option value=''>List Empty</option></select>";
				Rows	+= 	"</td>";
				Rows	+= "</tr>";

			$('#detail_body_liner').append(Rows);
			var step 	= "#step_"+nomor;
			
			$.ajax({
				url: base_url +'index.php/'+active_controller+'/getCategory',
				cache: false,
				type: "POST",
				dataType: "json",
				success: function(data){
					$(step).html(data.option).trigger("chosen:updated");
				}
			});
			
			nomor++;
		}
		
		
	});
</script>
