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
				<label class='label-control col-sm-2'><b>Warehouse</b></label>
				<div class='col-sm-4'>
					<select name='warehouse' id='warehouse' class='form-control input-md'>
						<option value='0'>ALL WAREHOUSE</option>
						<?php
						foreach($warehouse AS $val => $valx){
							echo "<option value='".$valx['id']."'>".strtoupper(strtolower($valx['nm_gudang']))."</option>";
						}
					 	?>
					 	<option value='16'>FINISH GOOD</option>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'><b>Material</b></label>
				<div class='col-sm-4'>
					<select name='material' id='material' class='form-control input-md'>
						<option value='0'>SELECT MATERIAL</option>
					<?php
						foreach($material AS $val => $valx){
							echo "<option value='".$valx['id_material']."'>".strtoupper(strtolower($valx['nm_material']))."</option>";
						}
					 ?>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'><b>Range Date</b></label>
				<div class='col-sm-4'>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								<i class="far fa-calendar-alt"></i>
							</span>
						</div>
						<input type="text" class="form-control float-right" id="range_picker" placeholder='Select range date' readonly value=''>
					</div>
				</div>	
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-4'>
                    <?php
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Show','content'=>'Show History','id'=>'showHistory'));
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

			if(material == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'Material wajib dipilih ...',
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
					'warehouse' : warehouse,
					'material' : material,
					'tgl_awal' : tgl_awal,
					'tgl_akhir' : tgl_akhir,	
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
