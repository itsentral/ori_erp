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
				<label class='label-control col-sm-2'><b>IPP Number</b></label>
				<div class='col-sm-8'>
					<select name='no_ipp' id='no_ipp' class='form-control input-md'>
						<option value='0'>SELECT IPP</option>
					<?php
						foreach($list_ipp AS $val => $valx){
							echo "<option value='".$valx['no_ipp']."'>".strtoupper($valx['no_ipp'].' ['.$valx['so_number'].'] - '.$valx['nm_customer'].' ['.$valx['project'])."]</option>";
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
                    // echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','value'=>'save','content'=>'Download Excel','id'=>'download_excel'));
                    ?>
				</div>	
			</div>
			<div id='show_history_view' class="table-responsive"></div>
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
            let range       = $('#range_picker').val();
            let warehouse   = $('#warehouse').val();
            let material    = $('#material').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
            var Link	= base_url + active_controller +'/download_excel/'+warehouse+'/'+material+'/'+tgl_awal+'/'+tgl_akhir;
            window.open(Link);
        });

		$(document).on('click', '#showHistory', function(e){
            let no_ipp    = $('#no_ipp').val();

			if(no_ipp == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'IPP wajib dipilih ...',
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
					'no_ipp' : no_ipp	
				},
				cache		: false,
				dataType	: 'json',
				beforeSend	: function(){
					loading_spinner();
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
    });

</script>
