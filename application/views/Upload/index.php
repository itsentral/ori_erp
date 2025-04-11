<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
        <div class="box-tool pull-right">
            <a href="<?php echo site_url('upload/download_template') ?>" class="btn btn-sm btn-warning" style='float:right; margin-right:5px;'>Download Template</a>
        </div>
	</div>
    <div class="box-body">
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Type <span class='text-red'>*</span></b></label>
            <div class='col-sm-2'>              
                <select name="tipe" id="tipe" class='form-control'>
                    <option value="0">Pilih Type</option>
                    <option value="stock_barang_jadi_per_day">Finish Good</option>
                    <option value="stock_barang_wip_per_day">WIP</option>
                </select>
            </div>
        </div>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Product <span class='text-red'>*</span></b></label>
            <div class='col-sm-2'>              
                <select name="product" id="product" class='form-control'>
                    <option value="0">Pilih Product</option>
                    <option value="pipe">Pipe</option>
                    <option value="fitting">Fitting</option>
                    <option value="field joint">Field Joint</option>
                    <option value="spool">Spool</option>
                </select>
            </div>
        </div>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Tanggal <span class='text-red'>*</span></b></label>
            <div class='col-sm-2'>              
                <input type="text" name='tanggal' id='tanggal' class='form-control datepicker text-center' data-role="datepicker2" readonly placeholder='Select Date'>
            </div>
        </div>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Upload File <span class='text-red'>*</span></b></label>
            <div class='col-sm-2'>              
                <?php
                    echo form_input(array('type'=>'file', 'id'=>'excel_file','name'=>'excel_file','class'=>'form-control-file','autocomplete'=>'off','placeholder'=>'Supplier Name'));											
                ?>
            </div>
        </div>
        <div class='form-group row'>		 	 
            <label class='label-control col-sm-2'></label>
            <div class='col-sm-2'>
                <button type='button' id='uploadEx' class='btn btn-primary'>Upload</button>	
            </div>
        </div>
    </div>
 </div>
  <!-- /.box -->
</form>
<?php $this->load->view('include/footer'); ?>
<style>
    #tanggal{
        cursor: pointer;
    }
</style>
<script>
	$(document).ready(function(){
        $('input[type="text"][data-role="datepicker2"]').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth:true,
            changeYear:true
        });
	});

    $(document).on('click', '#uploadEx', function(){
        var tipe = $('#tipe').val();
        var product = $('#product').val();
        var excel_file = $('#excel_file').val();
        var tanggal = $('#tanggal').val();

        if(tipe == '0' ){
            swal({
                title	: "Error Message!",
                text	: 'Tipe is Empty, please choose file first...',
                type	: "warning"
            });
            return false;
        }

        if(product == '0' ){
            swal({
                title	: "Error Message!",
                text	: 'Product is Empty, please choose file first...',
                type	: "warning"
            });
            return false;
        }

        if(tanggal == '' || tanggal == null){
            swal({
                title	: "Error Message!",
                text	: 'Date upload is Empty, please choose file first...',
                type	: "warning"
            });
            return false;
        }

        if(excel_file == '' || excel_file == null){
            swal({
                title	: "Error Message!",
                text	: 'File upload is Empty, please choose file first...',
                type	: "warning"
            });
            return false;
        }

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
                loading_spinner();
                var formData  	= new FormData($('#form_proses')[0]);
                var baseurl		= base_url + active_controller +'/upload';
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
                                timer	: 7000
                                });
                            window.location.href = base_url + active_controller;
                        }
                        if(data.status == 2){
                            swal({
                                title	: "Save Failed!",
                                text	: data.pesan,
                                type	: "warning",
                                timer	: 5000
                            });
                        }
                        if(data.status == 3){
                            swal({
                                title	: "Save Failed!",
                                text	: data.pesan,
                                type	: "warning",
                                timer	: 5000
                            });
                        }
                        $('#uploadEx').prop('disabled',false);
                    },
                    error: function() {
                        swal({
                            title				: "Error Message !",
                            text				: 'An Error Occured During Process. Please try again..',						
                            type				: "warning",								  
                            timer				: 5000,
                        });
                        $('#uploadEx').prop('disabled',false);
                    }
                });
            } else {
            swal("Cancelled", "Data can be process again :)", "error");
            $('#uploadEx').prop('disabled',false);
            return false;
            }
        });
    });

</script>
