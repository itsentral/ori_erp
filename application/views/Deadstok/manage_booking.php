<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
        
    </div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<!-- <div class='tableFixHead' style="height:700px;"> -->
			<table class="table table-bordered table-striped" width='100%'>
				<thead class='thead'>
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">Spec</th>
						<th class="text-center th">Product</th>
						<th class="text-center th">No SPK</th>
						<th class="text-center th">No SO</th>
						<th class="text-center th" hidden>ID Deadstok dipilih</th>
						<th class="text-center th" width='12%'>Proses Next</th>
						<th class="text-center th" width='17%'>Proses For Modifikasi</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    foreach ($listBooking as $key => $value) { $key++;
                        $no_ipp 	= str_replace('PRO-','',$value['id_produksi']);
						$id_milik 	= $value['id_milik'];
						$ID			= $value['id'];

						$tandaTanki	= substr($no_ipp,0,4);
						if($tandaTanki == 'IPPT'){
							$no_spk 	= spec_deadstok_tanki($id_milik)['no_spk'];
                        	$no_so 		= spec_deadstok_tanki($id_milik)['no_so'];
							$nm_product = strtoupper($value['id_product']);
							$spec		= '';
							$nm_comp	= '';
						}else{
							$no_spk 	= (!empty($GET_NO_SPK[$id_milik]['no_spk']))?$GET_NO_SPK[$id_milik]['no_spk']:'';
                        	$no_so 		= (!empty($GET_DET_IPP[$no_ipp]['so_number']))?$GET_DET_IPP[$no_ipp]['so_number']:'';
							$nm_product = strtoupper($value['id_category']);
							$spec		= spec_bq2($id_milik);
							$nm_comp	= $value['id_product'];
						}
                       
                        echo "<tr>";
                            echo "<td align='center'>".$key."</td>";
                            echo "<td>".$nm_product."</td>";
                            echo "<td>".$spec."</td>";
                            echo "<td>".$nm_comp."</td>";
                            echo "<td align='center'>".$no_spk."</td>";
                            echo "<td align='center'>".$no_so."</td>";
                            echo "<td hidden>";
                                echo "<input type='hidden' name='detail[".$ID."][id]' value='".$ID."'>";
                                echo "<input type='hidden' name='detail[".$ID."][id_product]' id='id_deadstok_".$ID."' data-id='".$ID."' class='form-control text-center checkID' value='".$id_product."'>";
                                echo "<span class='ket_id text-bold text-primary'></span>";
                            echo "</td>";
                            echo "<td>";
                                echo "<select name='detail[".$ID."][proses_next]' class='form-control'>";
                                echo "<option value='1'>Delivery</option>";
                                echo "<option value='2'>Spool</option>";
                                echo "<option value='3'>Cutting</option>";
                                echo "<option value='4'>Modifikasi</option>";
                                echo "</select>";
                            echo "</td>";
							echo "<td>";
								echo "<input type='text' name='detail[".$ID."][proses]' class='form-control' placeholder='Proses'>";
							echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
			</table>
		<!-- </div> -->
	</div>
	<!-- /.box-body -->
    <div class='box-footer'>
        <?php
        echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Process','id'=>'simpan'));
        echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','id'=>'kembali'));
        ?>
    </div>
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<script>
	$(document).ready(function(){

        $(document).on('click','#kembali', function(){
			window.location.href = base_url + active_controller;
		});

		$(document).on('keyup', '.checkID', function(){
            var id_product	= $(this).val();
            var thisHTML    = $(this).parent().parent()

            $.ajax({
                url	: base_url+active_controller+'/check_product',
                type: "POST",
                data: {
                    'id_product' : id_product
                },
                dataType: 'json',				
                success		: function(data){	
                    if(data.status == '1'){							
                        thisHTML.find('.ket_id').text(`Qty (${data.qty_product}) : ${data.product_name}, Spec ${data.product_spec}`)
                    }
                    else{
                        thisHTML.find('.ket_id').text(`Tidak ditemukan !`)
                    }
                },
                error: function() {
                    thisHTML.find('.ket_id').text('Error !!!')
                }
            });
                
        });
	});

    $(document).on('click', '.delete', function(){
		var bF	= $(this).data('id');
		// alert(bF);
		// return false;
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
					url			: base_url+active_controller+'/delete/'+bF,
					type		: "POST",
					data		: "id="+bF,
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
								  timer	: 3000
								});
								DataTables(); 
						}
						else if(data.status == 0){
							swal({
							  title	: "Save Failed!",
							  text	: data.pesan,
							  type	: "warning",
							  timer	: 7000
							});
						}
					},
					error: function() {
						swal({
						  title				: "Error Message !",
						  text				: 'An Error Occured During Process. Please try again..',						
						  type				: "warning",								  
						  timer				: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	$(document).on('click', '#simpan', function(){
			
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
				var formData  	= new FormData($('#form_proses')[0]);
				$.ajax({
					url			: base_url + active_controller+'/booking_deadstok',  
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
							window.location.href = base_url + active_controller;
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
</script>
