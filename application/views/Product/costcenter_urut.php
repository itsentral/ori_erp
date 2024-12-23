<?php
$this->load->view('include/side_menu');

?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <input type='hidden' name='id' id='id' value='<?=$id;?>'>
        <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid' >Costcenter</th>
                    <th class='text-center mid' width='20%'>Urut</th>
                    <th class='text-center mid' width='10%'>#</th>
                </tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				if(!empty($data)){
					foreach($data AS $val2 => $valx2){ $id++;
						$urut 			= (!empty($valx2['urut']))?number_format($valx2['urut']):'';
						
						echo "<tr class='header_".$id."'>";
							echo "<td align='left'>";
								echo "<input type='hidden' name='detail[".$id."][id]' value='".$valx2['id']."'>";
								echo "<select name='detail[".$id."][costcenter]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select Costcenter</option>";
								foreach($product AS $val => $valx){
									$sel = ($valx2['costcenter'] == $valx['id'])?'selected':'';
									echo "<option value='".$valx['id']."' ".$sel.">".strtoupper($valx['nm_costcenter'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							
							
							echo "<td align='left'><input type='text' name='detail[".$id."][urut]' class='form-control input-sm text-center maskMoney' value='".$urut."' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='center'>";
								echo "<button type='button' class='btn btn-sm btn-danger delPartPermanen' data-id='".$valx2['id']."' title='Delete Part'><i class='fa fa-close'></i></button>";
							echo "</td>";
						echo "</tr>";
					}
				}
				?>
                <tr id='add_<?=$id;?>'>
                    <td align='left' coslpan='3'><button type='button' class='btn btn-sm btn-warning addPart' title='Add'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Costcenter</button></td>
                </tr>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
            echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'save')).' ';
        ?>
        </div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

</form>
<?php $this->load->view('include/footer'); ?>
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
	.mid{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).ready(function(){
		$('.chosen_select').chosen({width: '100%'});
	});
	
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller +'/type';
	});
		
	$(document).on('click', '.addPart', function(){
		loading_spinner();
		var get_id 		= $(this).parent().parent().attr('id');
		// console.log(get_id);
		var split_id	= get_id.split('_');
		var id 		= parseInt(split_id[1])+1;
		var id_bef 	= split_id[1];

		$.ajax({
			url: base_url + active_controller+'/get_add/'+id,
			cache: false,
			type: "POST",
			dataType: "json",
			success: function(data){
				$("#add_"+id_bef).before(data.header);
				$("#add_"+id_bef).remove();
				$('.chosen_select').chosen({width: '100%'});
				$('.maskMoney').maskMoney();
				swal.close();
			},
			error: function(){
				swal({
					title				: "Error Message !",
					text				: 'Connection Time Out. Please try again..',
					type				: "warning",
					timer				: 3000,
					showCancelButton	: false,
					showConfirmButton	: false,
					allowOutsideClick	: false
				});
			}
		});
	});
	
	//delete part
	$(document).on('click', '.delPart', function(){
		var get_id 		= $(this).parent().parent().attr('class');
		$("."+get_id).remove();
	});
	
	$(document).on('click', '.delPartPermanen', function(e){
		e.preventDefault();
		var id 				= $(this).data('id');
		var product 		= $('#id').val();
		
		swal({
		  title: "Are you sure?",
		  text: "Delete urutan this data ?",
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
					url: base_url + active_controller+'/delete_permanent/'+id+'/'+product,
					type: "POST",
					// data: function(d){
						// d.id 			= id,
						// d.uri			= uri
					// },
					dataType: "json",
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
							window.location.href = base_url + active_controller +'/costcenter_urut/'+data.uri;
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 3000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save').prop('disabled',false);
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
						$('#save').prop('disabled',false);
					}
				});
		} 
		else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save').prop('disabled',false);
			return false;
			}
		});
	});

	//SAVE
	$(document).on('click', '#save', function(e){
		e.preventDefault();
		$('#save').prop('disabled',true);
		
		var id_costcenter	= $('#id_costcenter').val();
		if(id_costcenter == '0'){
			swal({
				title	: "Error Message!",
				text	: 'Costcenter name empty, select first ...',
				type	: "warning"
			});

			$('#save').prop('disabled',false);
			return false;
		}
		
		$('#save').prop('disabled',true);
		
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
				var baseurl		= base_url + active_controller +'/costcenter_urut';
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
							window.location.href = base_url + active_controller +'/type';
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 3000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
							$('#save').prop('disabled',false);
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
						$('#save').prop('disabled',false);
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			$('#save').prop('disabled',false);
			return false;
			}
		});
	});

</script>
