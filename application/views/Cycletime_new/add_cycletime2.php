<?php
$this->load->view('include/side_menu');

//Liner
$ArrCostcenter = array();
$ArrCostcenter[0]	= 'Select An Costcenter';
foreach($costcenter AS $val => $valx){
	$ArrCostcenter[$valx['id']] = strtoupper(strtolower($valx['nm_costcenter']));
}

$id_costcenter 	= (!empty($get))?$get[0]->id_costcenter:'0';
$disabled 		= (!empty($view))?'disabled':'';
?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<input type='hidden' name='uri' id='uri' value='<?=$uri;?>'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <div class="form-group row">
            <div class="col-md-2">
                <label for="customer">Costcenter <span class='text-red'>*</span></label>
            </div>
            <div class="col-md-4">
                <?php
                    echo form_dropdown('id_costcenter', $ArrCostcenter, $id_costcenter, array('id'=>'id_costcenter','class'=>'form-control input-md chosen-select'));
                ?>
            </div>
        </div>
        
        <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid' width='22%'>Product</th>
                    <th class='text-center mid' width='6%'>DN1</th>
                    <th class='text-center mid' width='6%'>DN2</th>
                    <th class='text-center mid' width='6%'>Sudut</th>
                    <th class='text-center mid' width='6%'>SR/LR</th>
					<th class='text-center mid' width='6%'>PN</th>
					<th class='text-center mid' width='6%'>Liner</th>
                    <th class='text-center mid' width='6%'>MP</th>
                    <th class='text-center mid' width='16%'>Mesin</th>
                    <th class='text-center mid' width='6%'>Time Process</th>
					<th class='text-center mid' width='6%'>Curing Time</th>
					<?php if(empty($view)){ ?>
                    <th class='text-center mid' width='4%'>#</th>
					<?php } ?>
                </tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				if(!empty($data)){
					foreach($data AS $val2 => $valx2){ $id++;
						$dn1 			= (!empty($valx2['dn1']))?number_format($valx2['dn1']):'';
						$dn2 			= (!empty($valx2['dn2']))?number_format($valx2['dn2']):'';
						$sudut 			= (!empty($valx2['sudut']))?number_format($valx2['sudut']):'';
						$man_power 		= (!empty($valx2['man_power']))?number_format($valx2['man_power']):'';
						$time_process 	= (!empty($valx2['time_process']))?number_format($valx2['time_process'],2):'';
						$curing_time 	= (!empty($valx2['curing_time']))?number_format($valx2['curing_time'],2):'';
						$total_time 	= (!empty($valx2['total_time']))?number_format($valx2['total_time'],2):'';
						$man_hours 		= (!empty($valx2['man_hours']))?number_format($valx2['man_hours'],2):'';
						
						$sr 			= ($valx2['srlr'] == 'SR')?'selected':'';
						$lr 			= ($valx2['srlr'] == 'SR')?'selected':'';
						echo "<tr class='header_".$id."'>";
							echo "<td align='left'>";
								echo "<input type='hidden' name='detail[".$id."][id]' value='".$valx2['id']."'>";
								echo "<select name='detail[".$id."][product]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select Product</option>";
								foreach($product AS $val => $valx){
									$sel = ($valx2['product'] == $valx['product_parent'])?'selected':'';
									echo "<option value='".$valx['product_parent']."' ".$sel.">".strtoupper($valx['product_parent'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							
							echo "<td align='left'><input type='text' name='detail[".$id."][dn1]' value='".$dn1."' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='left'><input type='text' name='detail[".$id."][dn2]' value='".$dn2."' class='form-control input-md text-right maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='left'><input type='text' name='detail[".$id."][sudut]' value='".$sudut."' class='form-control input-md text-center autoNumeric'></td>";
							echo "<td align='left'>";
								echo "<select name='detail[".$id."][srlr]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select</option>";
								echo "<option value='SR' ".$sr.">SR</option>";
								echo "<option value='LR' ".$lr.">LR</option>";
								echo "</select>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<select name='detail[".$id."][pn]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select</option>";
								foreach($pn AS $val => $valx){
									$sel = ($valx2['pn'] == $valx['name'])?'selected':'';
									echo "<option value='".$valx['name']."' ".$sel.">PN ".strtoupper($valx['name'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							echo "<td align='left'>";
								echo "<select name='detail[".$id."][liner]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select</option>";
								foreach($liner AS $val => $valx){
									$sel = ($valx2['liner'] == $valx['name'])?'selected':'';
									echo "<option value='".$valx['name']."' ".$sel.">".strtoupper($valx['name'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							echo "<td align='left'><input type='text' name='detail[".$id."][man_power]' value='".$man_power."' class='form-control input-md text-center maskMoney' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
							echo "<td align='left'>";
								echo "<select name='detail[".$id."][mesin]' class='chosen_select form-control input-sm inline-blockd'>";
								echo "<option value='0'>Select</option>";
								foreach($mesin AS $val => $valx){
									$sel = ($valx2['mesin'] == $valx['no_mesin'])?'selected':'';
									echo "<option value='".$valx['no_mesin']."' ".$sel.">".strtoupper($valx['nm_mesin'])."</option>";
								}
								echo "</select>";
							echo "</td>";
							echo "<td align='left'><input type='text' name='detail[".$id."][time_process]' value='".$time_process."' class='form-control input-sm text-right autoNumeric'></td>";
							echo "<td align='left'><input type='text' name='detail[".$id."][curing_time]' value='".$curing_time."' class='form-control input-sm text-right autoNumeric'></td>";
							if(empty($view)){
							echo "<td align='center'>";
								echo "<button type='button' class='btn btn-sm btn-danger delPartPermanen' ".$disabled." title='Delete Part' data-id='".$valx2['id']."' data-id_costcenter='".$valx2['id_costcenter']."' data-liner='".$valx2['liner']."' data-pn='".$valx2['pn']."' data-product='".$valx2['product']."'><i class='fa fa-close'></i></button>";
							echo "</td>";
							}
						echo "</tr>";
					}
				}
				if(empty($view)){
				?>
                <tr id='add_<?=$id;?>'>
                    <td align='left'><button type='button' class='btn btn-sm btn-warning addPart' title='Add' <?=$disabled;?>><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Product</button></td>
                    <td align='center' colspan='11'></td>
                </tr>
				<?php } ?>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
            
			if(empty($view)){
				echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'float:right;','value'=>'save','content'=>'Save','id'=>'save')).' ';
            }
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
		window.location.href = base_url + active_controller;
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
				$('.autoNumeric').autoNumeric();
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
		var id_costcenter 	= $(this).data('id_costcenter');
		var liner 			= $(this).data('liner');
		var pn 				= $(this).data('pn');
		var product 		= $(this).data('product');
		
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
				$.ajax({
					type:'POST',
					url: base_url + active_controller+'/delete_permanent',
					data: {
						"id" 			: id,
						"id_costcenter" : id_costcenter,
						"liner" 		: liner,
						"pn" 			: pn,
						"product" 		: product
					},
					success:function(data){
						window.location.href = base_url + active_controller +'/add_cycletime2/'+data.uri;
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'Connection Timed Out ...',
						  type				: "warning",
						  timer				: 5000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
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
				var baseurl		= base_url + active_controller +'/add_cycletime2';
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
							window.location.href = base_url + active_controller;
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
