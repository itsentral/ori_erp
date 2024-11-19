<?php
$this->load->view('include/side_menu');
//echo"<pre>";print_r($data_menu);
?>
<form action="#" method="POST" id="form_proses_bro">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>SALES ORDER</b></label>
				<div class='col-sm-10'>
					<select name='sales_order' id='sales_order' class='form-control input-md'>
						<option value='0'>SELECT SALES ORDER</option>
						<?php
						foreach($sales_order AS $val => $valx){
							$NO_IPP = str_replace('BQ-','',$valx['id_bq']);
							echo "<option value='".$NO_IPP."'>".strtoupper($valx['no_ipp'].' ['.$valx['so_number'].'] - '.$valx['nm_customer'].', PROJECT: '.$valx['project'])."</option>";
						}
					 	?>
					</select>
				</div>
			</div>
            <!-- <div class='form-group row'>
				<label class='label-control col-sm-2'><b>NO SPK</b></label>
				<div class='col-sm-4'>
					<select name='no_spk' id='no_spk' class='form-control input-md'>
						<option value='0'>LIST EMPTY (PILIH NO SO)</option>
					</select>
				</div>
			</div> -->
            <div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-10'>
                    <?php
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Show','content'=>'Show','id'=>'showHistory'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','value'=>'save','content'=>'Download Excel','id'=>'download_excel'));
                    ?>
				</div>	
			</div>
			<div id='show_history_view'></div>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>
<!-- modal -->
<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
	<div class="modal-dialog"  style='width:80%; '>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="head_title2"></h4>
				</div>
				<div class="modal-body" id="view2">
				</div>
				<div class="modal-footer">
				<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<!-- modal --> 

<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	#range_picker{
		cursor:pointer;
	}
</style>
<script>
    $(document).ready(function(){
        $('#range_picker').daterangepicker({
            // autoUpdateInput: false,
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

		$(document).on('click', '#showHistory', function(e){
            let sales_order   	= $('#sales_order').val();
			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

            var formData 	=new FormData($('#form_proses_bro')[0]);
			var baseurl=base_url + active_controller +'/show_history';
			$.ajax({
				url			: baseurl,
				type		: "POST",
				data		: {
					'sales_order' 	: sales_order
				},
				cache		: false,
				dataType	: 'json',
				beforeSend	: function(){
					loading_spinner()
				},
				success		: function(data){
					if(data.status == 1){
					$('#show_history_view').html(data.data_html);
					swal.close();
					}
					else{
						swal({
							title	: "Save Failed!",
							text	: data.pesan,
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
        });

		$(document).on('click', '#download_excel', function(e){
			e.preventDefault();
			var sales_order 	= $('#sales_order').val();
			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
			var Links			= base_url + active_controller+'/download_excel/'+sales_order;
			window.open(Links,'_blank');
		});
    });

</script>
