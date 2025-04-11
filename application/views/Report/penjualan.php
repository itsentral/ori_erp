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
				<label class='label-control col-sm-2'><b>Pilih Bulan</b></label>
				<div class='col-sm-2'>
                    <select id='bulan' name='bulan' class='form-control input-sm'>
                        <option value='0'>All Month</option>
                        <option value='01'>January</option>
                        <option value='02'>February</option>
                        <option value='03'>March</option>
                        <option value='04'>April</option>
                        <option value='05'>May</option>
                        <option value='06'>June</option>
                        <option value='07'>July</option>
                        <option value='08'>August</option>
                        <option value='09'>September</option>
                        <option value='10'>October</option>
                        <option value='11'>November</option>
                        <option value='12'>December</option>
                    </select>
				</div>	
                <div class='col-sm-1'>
                    <select id='tahun' name='tahun' class='form-control input-sm'>
                        <?php
                        $date = date('Y') + 5;
                        for($a=2020; $a < $date; $a++){
                            $selected = ($a == date('Y'))?'selected':'';
                            echo "<option value='".$a."' ".$selected.">".$a."</option>";
                        }
                        ?>
                    </select>
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
            let bulan       = $('#bulan').val();
            let tahun       = $('#tahun').val();

            var Link	= base_url + active_controller +'/download_excel/'+bulan+'/'+tahun;
            window.open(Link);
        });

		$(document).on('click', '#showHistory', function(e){
            let bulan       = $('#bulan').val();
            let tahun       = $('#tahun').val();
           
            var formData = new FormData($('#form_proses_bro')[0]);
			var baseurl=base_url + active_controller +'/show_history';
			$.ajax({
				url			: baseurl,
				type		: "POST",
				data		: {
					'bulan' : bulan,
					'tahun' : tahun,	
				},
				cache		: false,
				dataType	: 'json',
                beforeSend : function(){
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
                        swal.close();
					}
				},
				error: function() {

					swal({
						title		: "Error Message !",
						text		: 'An Error Occured During Process. Please try again..',
						type		: "warning",
						timer		: 3000
					});
                    swal.close();
				}
			});
        });
    });

</script>
