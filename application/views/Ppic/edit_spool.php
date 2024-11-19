<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data" autocomplete='off'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <a href="<?php echo site_url('ppic/spool') ?>" class="btn btn-sm btn-default" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <button type='button' class='btn btn-sm btn-danger' style='float:right; margin-bottom:10px;' id='delete_spool'><i class='fa fa-puzzle-piece'></i>&nbsp;Delete Spool</button>
        <button type='button' class='btn btn-sm btn-primary' style='float:right; margin-bottom:10px; margin-right:5px;' id='update_drawing'><i class='fa fa-edit'></i>&nbsp;Update Drawing Number</button>
        <input type="text" class='form-control' name='no_drawing' id='no_drawing' value='<?=$no_drawing;?>' placeholder='No Drawing' style='float:right; margin-bottom:10px; margin-right:5px; width:200px;'>
		<label style='float:right; margin-right:5px;'>No Drawing : </label>
		<input type="hidden" name='kd_spoolx' id='kd_spoolx' value='<?=$spool_induk;?>'>
		<br>
        <?php
        foreach ($result as $key2 => $value2) { $key2++;
		    $result2 = $this->db->get_where('spool_group', array('spool_induk'=>$spool_induk,'kode_spool'=>$value2['kode_spool']))->result_array();

            ?>  
                <h4><?=$key2?>. Kode Spool : <?=$value2['kode_spool'];?></h4>
                <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
						<th class="text-center">#</th>
                            <th class="text-center">IPP</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Spec</th>
                            <th class="text-center">Length</th>
                            <th class="text-center">Code</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">No Drawing</th>
                            <th class="text-center no-sort">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result2 as $key => $value) { $key++;
							$SPEC = $value['kode_spk'];
                            $product_code = $value['product_code'];

							$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
                            $LENGTH = (!empty($value['length']))?number_format($value['length']):'';

                            if($value['sts'] == 'loose' OR $value['sts'] == 'cut'){
                                $SPEC = spec_bq2($value['id_milik']);
                                $IMPLODE = explode('.', $value['product_code']);
                                $product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE;
                            }

                            echo "<tr>";
                                echo "<td align='center'>".$key."</td>";
                                echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                                echo "<td align='left'>".strtoupper($value['id_category'])."</td>";
								echo "<td align='left'>".$SPEC."</td>";
                                echo "<td align='right'>".$LENGTH."</td>";
								echo "<td align='center'>".$product_code."</td>";
                                echo "<td align='center'>".$value['no_spk']."</td>";
                                echo "<td align='left'>".$value['no_drawing']."</td>";
								echo "<td align='center'><input type='checkbox' name='check[".$value['id']."]' class='chk_personal' value='".$value['id']."-".$value['sts']."' ></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table><br>
            <?php
        }
        ?>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){
		
		$(document).on('click', '#delete_spool', function(){
			
			if($('.chk_personal:checked').length == 0){
				swal({
					title	: "Error Message!",
					text	: 'Checklist product minimal 1',
					type	: "warning"
				});
				return false;
			}
			// return false;
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
					// loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					$.ajax({
						url			: base_url + active_controller+'/delete_spool',  
						type		: "POST",
						data		: formData,
						cache		: false,
						dataType	: 'json',
						processData	: false, 
						contentType	: false,				
						success		: function(data){								
							if(data.status == 1){
								swal({
									title	: "Success!",
									text	: 'Succcess Process!',
									type	: "success",
									timer	: 3000
								});
								window.location.href = base_url + active_controller + '/spool';
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
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
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});

		$(document).on('click', '#update_drawing', function(){
			
			let no_drawing = $('#no_drawing').val();
			let kd_spoolx = $('#kd_spoolx').val();
			// return false;
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
					// loading_spinner();
					$.ajax({
						url			: base_url + active_controller+'/update_no_drawing',  
						type		: "POST",
						data		: {
							'no_drawing' : no_drawing,
							'kd_spool' : kd_spoolx
						},
						cache		: false,
						dataType	: 'json',				
						success		: function(data){								
							if(data.status == 1){
								swal({
									title	: "Success!",
									text	: 'Succcess Process!',
									type	: "success",
									timer	: 3000
								});
								window.location.href = base_url + active_controller + '/spool';
							}
							else{
								swal({
									title	: "Failed!",
									text	: 'Failed Process!',
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
				} else {
				swal("Cancelled", "Data can be process again :)", "error");
				return false;
				}
			});
		});
		
	});
</script>
