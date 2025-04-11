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
				<label class='label-control col-sm-2'><b>Sales Order</b></label>
				<div class='col-sm-4'>
					<select name='sales_order' id='sales_order' class='form-control input-md'>
						<option value='0'>SELECT SALES ORDER</option>
						<?php
						foreach($sales_order AS $val => $valx){
							echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['so_number'])."</option>";
						}
					 	?>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-4'>
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

<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	#range_picker{
		cursor:pointer;
	}
</style>
<script>
    $(document).ready(function(){
        $('#range_picker').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

        $(document).on('click', '#download_excel', function(e){
            let sales_order       = $('#sales_order').val();

            if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Sales Order wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
          
            var Link	= base_url + active_controller +'/download_excel/'+sales_order
            window.open(Link);
        });

		$(document).on('click', '#showHistory', function(e){
            let sales_order   = $('#sales_order').val();

			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Sales Order wajib dipilih ...',
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
					'sales_order' : sales_order	
				},
				cache		: false,
				dataType	: 'json',
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
    });

</script>
