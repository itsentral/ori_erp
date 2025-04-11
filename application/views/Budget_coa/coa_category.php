<?php
$this->load->view('include/side_menu'); 
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<a class="btn btn-success" href="javascript:void(0)" title="Add" onclick="add_data()"><i class="fa fa-plus">&nbsp;</i>New</a>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<div class="box-body table-responsive">
			<table id="example1" class="table table-bordered table-striped">
			<thead>
			<tr>
				<th width="50">
				Action
				</th>
				<th>COA</th>
				<th>Keterangan</th>
				<th>Tipe</th>
			</tr>
			</thead>
			<tbody>
			<?php if(empty($results)){
			}else{
				$numb=0; foreach($results AS $record){ $numb++; ?>
			<tr>
				<td>
					<a class="text-green" href="javascript:void(0)" title="Edit" onclick="edit_data('<?=$record->id?>','<?=$record->coa?>','<?=$record->nama?>','<?=$record->tipe?>')"><i class="fa fa-pencil"></i>
					</a>
					<a class="text-red" href="javascript:void(0)" title="Delete" onclick="delete_data('<?=$record->id?>')"><i class="fa fa-trash"></i>
					</a>
				</td>
				<td><?= $record->coa.' - '.$record->nama_perkiraan  ?></td>
				<td><?= $record->nama ?></td>
				<td><?= $record->tipe ?></td>
			</tr>
			<?php }
			} ?>
			</tbody>
			</table>
		</div>
	</div>
</div>
<div class="modal " id="dialog-popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
	  <form id="frm">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Kategori COA</h4>
      </div>
      <div class="modal-body" id="MyModalBody">
	  <div>
	  COA :<br />
		<?php
			$datcoa[0]	= 'Select An Option';
			echo form_dropdown('coa',$datcoa, '0', array('id'=>'coa' ));
		?>
	  </div>
	  Keterangan :<br /><input type="text" class="form-control" id="nama" name="nama">
	  Tipe :<br />
		<?php
			$datatipe[0]	= 'Select An Option';
			echo form_dropdown('tipe',$datatipe, '0', array('id'=>'tipe' ));
		?>	  
	  <input type="hidden" id="id" name="id">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		<button type="submit" class="btn btn-info">Simpan</button>
      </div>
	  </form>
    </div>
  </div>
</div>
<?php $this->load->view('include/footer'); ?>
<style>
.chosen-container.chosen-container-single {
    width: 400px !important; /* or any value that fits your needs */
}
</style>
<script type="text/javascript">

    $(document).ready(function() {

    });

  	function add_data(){
		$("#id").val('');
		$("#nama").val('');
		$("#coa").val('0').trigger("chosen:updated");
		$("#tipe").val('').trigger("chosen:updated");
		$("#dialog-popup").modal('show');
		$("#nm_divisi").focus();
	}

  	function edit_data(id,coa,nama,tipe){
		if(id!=""){
			$("#id").val(id);
			$("#coa").val(coa).trigger("chosen:updated");
			$("#nama").val(nama);
			$("#tipe").val(tipe).trigger("chosen:updated");
			$("#dialog-popup").modal('show');
			$("#nm_divisi").focus();
		}
	}
	
    $('#frm').on('submit', function(e){
        e.preventDefault();
        var formdata = $("#frm").serialize();
        $.ajax({
            url: base_url+"budget_coa/save_category",
            dataType : "json",
            type: 'POST',
            data: formdata,
            success: function(msg){
                if(msg['save']=='1'){
                    swal({
                        title: "Sukses!",
                        text: "Data Berhasil Di Simpan",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    window.location.reload();
                } else {
                    swal({
                        title: "Gagal!",
                        text: "Data Gagal Di Simpan",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                };//alert(msg);
            },
            error: function(msg){
                swal({
                    title: "Gagal!",
                    text: "Ajax Data Gagal Di Proses",
                    type: "error",
                    timer: 1500,
                    showConfirmButton: false
                });
				console.log(msg);
            }
        });
    });

	//Delete
	function delete_data(id){
		//alert(id);
		swal({
		  title: "Anda Yakin?",
		  text: "Data Akan Terhapus secara Permanen!",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Ya, delete!",
		  cancelButtonText: "Tidak!",
		  closeOnConfirm: false,
		  closeOnCancel: true
		},
		function(isConfirm){
		  if (isConfirm) {
		  	$.ajax({
		            url: base_url+'budget_coa/hapus_category/'+id,
		            dataType : "json",
		            type: 'POST',
		            success: function(msg){
		                if(msg['delete']=='1'){		                	
		                    swal({
		                      title: "Terhapus!",
		                      text: "Data berhasil dihapus",
		                      type: "success",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                    window.location.reload();
		                } else {
		                    swal({
		                      title: "Gagal!",
		                      text: "Data gagal dihapus",
		                      type: "error",
		                      timer: 1500,
		                      showConfirmButton: false
		                    });
		                };
		            },
		            error: function(msg){
		                swal({
	                      title: "Gagal!",
	                      text: "Gagal Eksekusi Ajax",
	                      type: "error",
	                      timer: 1500,
	                      showConfirmButton: false
	                    });
						console.log(msg);
		            }
		        });
		  } else {
		    //cancel();
		  }
		});
	}

</script>