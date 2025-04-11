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
				<label class='label-control col-sm-2'><b>NO PO</b></label>
				<div class='col-sm-10'>
					<select name='sales_order' id='sales_order' class='form-control input-md'>
						<option value='0'>SELECT NO PO</option>
						<?php
						foreach($sales_order AS $val => $valx){
							$tanda = substr($valx['no_po'],0,3);
							$type = ($tanda == 'POX')?'stok':'material';
							echo "<option value='".$valx['no_po']."' data-type='".$type."'>".strtoupper($valx['no_po'].' - '.$valx['nm_supplier'])."</option>";
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
                    // echo form_button(array('type'=>'button','class'=>'btn btn-md btn-default','value'=>'save','content'=>'Download Excel Rekap','id'=>'download_excel_rekap'));
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
			let type   			= $('#sales_order').find(':selected').data('type')
			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No PO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

			var baseurl=base_url + active_controller +'/show_history';
			$.ajax({
				url			: baseurl,
				type		: "POST",
				data		: {
					'sales_order' 	: sales_order,
					'type' 	: type,
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
				  text	: 'No PO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
			var Links			= base_url + active_controller+'/download_excel/'+sales_order;
			window.open(Links,'_blank');
		});
    });

</script>
