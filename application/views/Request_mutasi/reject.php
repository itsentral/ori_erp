 <div class="box box-primary">
    <div class="box-body">
		<form id="form-header-alasan" method="post">
		<?php	
		$nomor = $results;
		?>
					   <div class="row">
                          <div class="form-group ">
                          <label for="tgl_bayar" class="col-sm-4 control-label">No Request :</label>
                            <div class="col-sm-6">
                                <input type="text" name="no_request" id="no_request"  value="<?=$nomor?>"placeholder="Automatic" class="form-control input-sm" readonly>
                            </div>
                          </div>
                        </div>
						 <div class="row">
						  <div class="form-group">
                            <label for="ket_bayar" class="col-sm-4 control-label">Alasan Reject </font></label>
                            <div class="col-sm-6">
                              <textarea name="alasan" class="form-control input-sm" id="alasan"></textarea>
                            </div>
                          </div>
						</div>
						
						<div class="row">
							<div class="col-lg-12">
							
											
								<button class="btn btn-primary" type="button" onclick="savemutasi()">
									<i class="fa fa-save"></i><b>Save</b>
								</button>
								
								
							</div>
						</div>
			</div>
				 </div>
			</div>
		</form>		  
	</div>
</div>	
	
				  
				  

	
<script>
	$(document).ready(function(){
		//$('.select2').select2();
		swal.close();
		$('#incomplete').hide();
		$('#pakailebihbayar').hide();
		$("#list_item_unlocated").DataTable({lengthMenu:[10,15,25,30]}).draw();
		$(".divide").divide();
	});
	 function savemutasi(){
		
	    if ($('#alasan').val() == "") {
          swal({
            title	: "Alasan Tidak Boleh Kosong!",
            text	: "Isi Alasan Terlebih Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else {
        swal({
          title: "Peringatan !",
          text: "Pastikan data sudah lengkap dan benar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Ya, simpan!",
          cancelButtonText: "Batal!",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
			if(isConfirm) {
				//$('#simpanpenerimaan').hide();
				var formdata = $("#form-header-alasan").serialize();
				$.ajax({
					url: base_url+"approval_mutasi/save_reject",
					dataType : "json",
					type: 'POST',
					data: formdata,
					success: function(data){
						if(data.status == 1){
						swal({
						  title	: "Save Success!",
						  text	: data.pesan,
						  type	: "success",
						  timer	: 15000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
						window.location.href = base_url + active_controller;
					  }else{

						if(data.status == 2){
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}else{
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}

					  }
					},
					error: function(){
						swal({
							title: "Gagal!",
							text: "Batal Proses, Data bisa diproses nanti",
							type: "error",
							timer: 1500,
							showConfirmButton: false
						});
					}
				});
			}
        });
		}
    }
	
</script>