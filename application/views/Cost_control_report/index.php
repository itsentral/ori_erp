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
				<label class='label-control col-sm-2'><b>SALES ORDER NUMBER</b></label>
				<div class='col-sm-4'>
					<select name='sales_order' id='sales_order' class='form-control input-md'>
						<option value='0'>SELECT SALES ORDER</option>
						<?php
						foreach($sales_order AS $val => $valx){
							$NO_IPP = str_replace('BQ-','',$valx['id_bq']);
							echo "<option value='".$NO_IPP."'>".strtoupper(strtolower($valx['so_number']))."</option>";
						}
					 	?>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'><b>NO SPK</b></label>
				<div class='col-sm-4'>
					<select name='no_spk' id='no_spk' class='form-control input-md'>
						<option value='0'>LIST EMPTY (PILIH NO SO)</option>
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
				<div class='col-sm-10'>
                    <?php
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Show','content'=>'Show History','id'=>'showHistory'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','value'=>'save','content'=>'Download Excel','id'=>'download_excel'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-default','value'=>'save','content'=>'Download Excel Rekap','id'=>'download_excel_rekap'));
                    ?>
				</div>	
			</div>
			<div id='show_history_view'></div>
		</div>
		<!-- /.box-body -->
	 </div>
  <!-- /.box -->
</form>
<form action="#" method="POST" id="form_proses">
	<div class="box box-warning">
		<div class="box-header">
			<h3 class="box-title">Pengeluaran Sub Gudang</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
		<div class='form-group row'>
				<label class='label-control col-sm-2'><b>SALES ORDER NUMBER</b></label>
				<div class='col-sm-4'>
					<select name='sales_order2' id='sales_order2' class='form-control input-md'>
						<option value='0'>SELECT SALES ORDER</option>
						<?php
						foreach($sales_order AS $val => $valx){
							$NO_IPP = str_replace('BQ-','',$valx['id_bq']);
							echo "<option value='".$NO_IPP."'>".strtoupper(strtolower($valx['so_number']))."</option>";
						}
					 	?>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'><b>NO SPK</b></label>
				<div class='col-sm-4'>
					<select name='no_spk2' id='no_spk2' class='form-control input-md'>
						<option value='0'>LIST EMPTY (PILIH NO SO)</option>
					</select>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Sub Gudang</b></label>
				<div class='col-sm-4'>              
					<select id='gudang' name='gudang' class='form-control input-sm' style='min-width:200px;'>
						<?php
							foreach($pusat AS $val => $valx){
								echo "<option value='".$valx['id']."'>".strtoupper($valx['nm_gudang'])."</option>";
							}
						?>
					</select>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'><b>Periode</b></label>
				<div class='col-sm-4'>
					<select id='bulan' name='bulan' style='min-width:100px;' >
						<?php
							for($i=1;$i<=12;$i++){
								echo "<option value='".$i."' ".($i==date("n")?" selected":"").">".($i)."</option>";
							}
						?>
					</select>
					<select id='tahun' name='tahun' style='min-width:200px;' >
						<?php
							for($i=date("Y");$i>=2020;$i--){
								echo "<option value='".$i."'>".($i)."</option>";
							}
						?>
					</select>
				</div>	
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>No Transaksi</b></label>
				<div class='col-sm-4'>
					<input type="text" id='no_trans' class='form-control input-md'>
				</div>
			</div>
            <div class='form-group row'>
				<label class='label-control col-sm-2'></label>
				<div class='col-sm-4'>
                    <?php
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'Preview','content'=>'Preview','id'=>'showLog'));
                    echo form_button(array('type'=>'button','class'=>'btn btn-md btn-info','value'=>'save','content'=>'Download Excel','id'=>'download_excel_sub'));
                    ?>
				</div>	
			</div>
			<div id='show_log_view'></div>
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
            locale: {
                format: 'DD-MM-YYYY'
            }
        });

		$(document).on('click', '#showLog', function(e){
			loading_spinner();
            let sales_order	= $('#sales_order2').val();
            let no_spk		= $('#no_spk2').val();
            let tahun		= $('#tahun').val();
            let bulan		= $('#bulan').val();
            let gudang		= $('#gudang').val();
            let no_trans	= $('#no_trans').val();
            var formData	= new FormData($('#form_proses')[0]);
			var baseurl		= base_url + active_controller +'/show_log';
			$.ajax({
				url			: baseurl,
				type		: "POST",
				data		: {
					'tahun'	: tahun,
					'bulan'	: bulan,
					'gudang': gudang,
					'sales_order': sales_order,
					'no_spk': no_spk,
					'no_trans': no_trans,
				},
				cache		: false,
				dataType	: 'json',
				success		: function(data){
					if(data.status == 1){
						$('#show_log_view').html(data.data_html);						
					} else {
						swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 3000
						});
					}
					swal.close();
				},
				error: function() {
					swal.close();
					swal({
						title		: "Error Message !",
						text		: 'An Error Occured During Process. Please try again..',
						type		: "warning",
						timer		: 3000
					});
				}
			});
        });

		$(document).on('change','#sales_order', function(e){
			e.preventDefault();
			var no_so 	= $(this).val();
            let no_spk = $('#no_spk')

            if(no_so != '0'){
                $.ajax({
                    url: base_url + active_controller+'/get_no_spk',
                    type		: "POST",
                    dataType	: 'json',
                    data: {
                        "no_so":no_so,
                    },
                    cache		: false,
                    success:function(data){
                        no_spk.html(data.option).trigger("chosen:updated");
                    },
                    error: function() {
                        swal({
                        title				: "Error Message !",
                        text				: 'Connection Timed Out ...',
                        type				: "warning",
                        timer				: 5000
                        });
                    }
                });
            }
            else{
                no_spk.val('');
            }
		});

		$(document).on('change','#sales_order2', function(e){
			e.preventDefault();
			var no_so 	= $(this).val();
            let no_spk = $('#no_spk2')

            if(no_so != '0'){
                $.ajax({
                    url: base_url + active_controller+'/get_no_spk',
                    type		: "POST",
                    dataType	: 'json',
                    data: {
                        "no_so":no_so,
                    },
                    cache		: false,
                    success:function(data){
                        no_spk.html(data.option).trigger("chosen:updated");
                    },
                    error: function() {
                        swal({
                        title				: "Error Message !",
                        text				: 'Connection Timed Out ...',
                        type				: "warning",
                        timer				: 5000
                        });
                    }
                });
            }
            else{
                no_spk.val('');
            }
		});

        $(document).on('click', '#download_excel', function(e){
            let range       = $('#range_picker').val();
			let sales_order  = $('#sales_order').val();
            let no_spk    		= $('#no_spk').val().split(".").join("-");
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

			if(no_spk == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SPK wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
			
            var Link	= base_url + active_controller +'/download_excel/'+sales_order+'/'+no_spk+'/'+tgl_awal+'/'+tgl_akhir;
            window.open(Link);
        });
        $(document).on('click', '#download_excel_rekap', function(e){
            let range       = $('#range_picker').val();
			let sales_order  = $('#sales_order').val();
            let no_spk    		= $('#no_spk').val().split(".").join("-");
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

			if(no_spk == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SPK wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
			
            var Link	= base_url + active_controller +'/download_excel_rekap/'+sales_order+'/'+no_spk+'/'+tgl_awal+'/'+tgl_akhir;
            window.open(Link);
        });

		$(document).on('click', '#showHistory', function(e){
            let range       	= $('#range_picker').val();
            let sales_order   	= $('#sales_order').val();
            let no_spk    		= $('#no_spk').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }

			if(sales_order == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

			if(no_spk == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SPK wajib dipilih ...',
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
					'sales_order' 	: sales_order,
					'no_spk' 		: no_spk,
					'tgl_awal' 		: tgl_awal,
					'tgl_akhir' 	: tgl_akhir,	
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

		$(document).on('click', '#download_excel_sub', function(e){
            let bulan       = $('#bulan').val();
            let tahun       = $('#tahun').val();
			let sales_order2  = $('#sales_order2').val();
            let no_spk2    		= $('#no_spk2').val().split(".").join("-");
           
			if(sales_order2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SO wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}

			if(no_spk2 == 0){
				swal({
				  title	: "Error Message!",
				  text	: 'No SPK wajib dipilih ...',
				  type	: "warning"
				});
				return false;	
			}
			
            var Link	= base_url + active_controller +'/download_excel_sub/'+sales_order2+'/'+no_spk2+'/'+bulan+'/'+tahun;
            window.open(Link);
        });

		$(document).on('click', '.detail_material', function(){
			var kode_trans 		= $(this).data('kode_trans');
			let range       = $('#range_picker').val();
            var tgl_awal 	= '0';
            var tgl_akhir 	= '0';
            if(range != ''){
            var sPLT 		= range.split(' - ');
            var tgl_awal 	= sPLT[0];
            var tgl_akhir 	= sPLT[1];
            }
			$("#head_title2").html("<b>DETAIL TRANSKASI</b>");
			loading_spinner();
			$.ajax({
				url			: base_url + active_controller+'/show_detail_transaksi',
				type		: "POST",
				data		: {
					'kode_trans' 	: kode_trans,
					'tgl_awal' 		: tgl_awal,	
					'tgl_akhir' 	: tgl_akhir,	
				},
				cache		: false,
				dataType	: 'json',
				success:function(data){
					$("#ModalView2").modal();
					$("#view2").html(data.data_html);
				},
				error: function() {
					swal({
					title	: "Error Message !",
					text	: 'Connection Timed Out ...',
					type	: "warning",
					timer	: 5000,
					});
				}
			})
		});
    });

</script>
