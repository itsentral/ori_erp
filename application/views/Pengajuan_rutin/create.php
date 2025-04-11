<?php
$this->load->view('include/side_menu');
$tanda = $this->uri->segment(2);
// echo $tanda;
$disabled = '';
if($tanda == 'approval' OR $tanda == 'request'){
	$disabled = 'disabled';
}
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $title;?></h3>
    </div>
	<?=form_open($this->uri->uri_string(),array('id'=>'frm_data','name'=>'frm_data','role'=>'form','class'=>'form-horizontal','enctype'=>"multipart/form-data"));?>
	<div class="box-body">
		<input type="hidden" name="id" id="id" value="<?php echo (isset($data->id) ? $data->id: ''); ?>">
		<div class="form-group ">
			<label for="divisi" class="col-sm-2 control-label">Department</label>
			<div class="col-sm-3">
				<div class="input-group">
					<?php
					echo form_dropdown('departement',$datdept, set_value('departement', isset($departement) ? $departement: '0'), array('id'=>'departement','class'=>'form-control','required'=>'required'));
					?>
				</div>
			</div>
			<?php if($tanda == 'approval'){?>
			<label for="divisi" class="col-sm-2 control-label">Approval ?</label>
			<div class="col-sm-3">
				<div class="input-group">
					<select name="app_action" id="app_action" class='form-control'>
						<option value="0">Select Approval</option>
						<option value="A">Approve</option>
						<option value="R">Reject</option>
					</select>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="form-group ">
			<label for="divisi" class="col-sm-2 control-label">No Dokumen</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input type="text" class="form-control" id="no_doc" name="no_doc" value="<?php echo (isset($data->no_doc) ? $data->no_doc: ''); ?>" placeholder="Auto" required readonly>
				</div>
			</div>
			<?php if($tanda == 'approval'){?>
			<label for="divisi" class="col-sm-2 control-label">Reject Reason</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input type="text" name='app_reason' id='app_reason' class='form-control' placeholder='Approve Reason'>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="form-group ">
			<label for="divisi" class="col-sm-2 control-label">Tanggal</label>
			<div class="col-sm-3">
				<div class="input-group">
					<input type="text" class="form-control tanggal" id="tanggal_doc" name="tanggal_doc" value="<?php echo (isset($data->tanggal_doc) ? $data->tanggal_doc: date("Y-m-d")); ?>" placeholder="Tanggal" required readonly <?=$disabled;?>>
				</div>
			</div>

		</div>
		<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<?php if($tanda != 'view'){?>
					<th><input type="checkbox" name="chk_all" id="chk_all"></th>
					<?php } ?>
					<th>Nama Barang /Jasa</th>
					<th>Jadwal Pembayaran</th>
					<th>Budget</th>
					<th hidden>Perkiraan Biaya</th>
					<th hidden>Keterangan</th>
					<th hidden>Dokumen</th>
					<?php if($tanda == 'edit'){?>
					<th>Reason</th>
					<?php } ?>
					<th class="hidden"><div class="pull-right"><button class="btn btn-success btn-xs" onclick="add_detail()" id="add-material" type="button"><i class="fa fa-plus"></i> Tambah</button></div></th>
					<?php if($tanda == 'approval'){?>
					<th>Tanggal Bayar</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody id="detail_body">
			<?php $total=0; $idd=1;
			if(!empty($data_detail)){
				foreach($data_detail AS $record){ ?>
				<tr id='tr1_<?=$idd?>' class='delAll'>
					<?php if($tanda != 'view'){?>
					<td>
						<input type="checkbox" name="check[<?=$record->id;?>]" class="chk_personal" data-nomor="1" value="<?=$record->id;?>">
						<input type="hidden" name="detail_id[]" id="raw_id_<?=$idd?>" value="<?=$record->id;?>">
						<input type="hidden" name="id_budget[]" id="id_budget_<?=$idd;?>" value="<?=$record->id_budget?>" class='budget'>
						<input type="hidden" name="coa[]" id="coa_<?=$idd;?>" value="<?=$record->coa?>">
					</td>
					<?php } ?>
					<td><input type="text" class="form-control" name="nama[]" id="nama_<?=$idd;?>" value="<?=$record->nama;?>" <?=$disabled;?>></td>
					<td>
						<input type="text" class="form-control text-center" name="tanggal[]" id="tanggal_<?=$idd;?>" value="<?=$record->tanggal;?>" readonly data-role="datepicker_lost" style="cursor: pointer;" readonly <?=$disabled;?>>
					</td>
					<td><input type="text" class="form-control divide text-right" name="budget[]" id="budget<?=$idd;?>" value="<?=($record->budget);?>" readonly tabindex="-1" <?=$disabled;?>></td>
					<td hidden><input type="text" class="form-control divide text-right" name="nilai[]" id="nilai_<?=$idd;?>" value="<?=($record->nilai);?>" <?=$disabled;?>></td>
					<td hidden><input type="text" class="form-control" name="keterangan[]" id="keterangan_<?=$idd;?>" value="<?=$record->keterangan;?>" <?=$disabled;?>></td>
					<td hidden>
					<?php if($tanda == 'edit'){?><input type="file" name="doc_file_<?=$idd?>" id="doc_file<?=$idd?>"><?php } ?>
					<?=($record->doc_file!=''?'<a href="'.base_url('assets/bayar_rutin/'.$record->doc_file).'" target="_blank">Download</a>':'')?></td>
					<?php if($tanda == 'edit'){?>
					<td><?=$record->reason;?></td>
					<?php } ?>
					<td align='center' class="hidden"><button type='button' class='btn btn-danger btn-xs' data-toggle='tooltip' onClick='delDetail(<?=$idd?>)' title='Hapus data'><i class='fa fa-close'></i> Hapus</button></td>
					<?php if($tanda == 'approval'){?>
						<td><input type="text" class="form-control text-center" name="tanggalbayar[]" id="tanggalbayar_<?=$idd;?>" value="<?=$record->tgl_bayar;?>" readonly data-role="datepicker_lost" style="cursor: pointer;" readonly <?=$disabled;?>></td>
					<?php } ?>
				</tr>
				<?php
					$idd++;
				}
			}?>
			</tbody>
		</table>
		</div>
		<div class="form-group">
			<div class="col-sm-12 text-right">
				<?php if($tanda == 'create' OR $tanda == 'edit'){?>
				<button type="submit" name="save" class="btn btn-success btn-md" id="submit">Save</button>
				<?php } ?>
				<?php if($tanda == 'request'){?>
				<button type="button" name="save" class="btn btn-success btn-md" id="request">Submit</button>
				<?php } ?>
				<?php if($tanda == 'approval'){?>
				<button type="button" name="save" class="btn btn-success btn-md" id="approval">Approved</button>
				<a class="btn btn-danger btn-md" href="<?=base_url('pengajuan_rutin/list_approve')?>">Back</a>
				<?php }else{ ?>
				<a class="btn btn-danger btn-md" href="<?=base_url('pengajuan_rutin')?>">Back</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?= form_close() ?> 
</div>
<?php $this->load->view('include/footer'); ?>
<style>
	.tanggal{
		cursor: pointer;
	}
</style>
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>
<script type="text/javascript">
	var url_save = base_url+'pengajuan_rutin/save_data_new/';
	var url_approve = base_url+'pengajuan_rutin/approve/';
	var nomor=<?=$idd?>;

	$(function () {
		$('.divide').divide();
		$(".select2").chosen({
			width:'100%'
		});
		$(".tanggal").datepicker({
			todayHighlight: true,
			format : "yyyy-mm-dd",
			showInputs: true,
			autoclose:true
		});
	});

	$("#chk_all").click(function(){
		$('input:checkbox').not(this).prop('checked', this.checked);
	});
	
    $('#frm_data').on('submit', function(e){
        e.preventDefault();
		var errors="";
		if(errors==""){
		swal({
		  title: "Anda Yakin?",
		  text: "Data Akan Disimpan!",
		  type: "info",
		  showCancelButton: true,
		  confirmButtonText: "Ya, simpan!",
		  cancelButtonText: "Tidak!",
		  closeOnConfirm: false,
		  closeOnCancel: true
		},
		function(isConfirm){
		  if (isConfirm) {
			var formdata = new FormData($('#frm_data')[0]);
			$.ajax({
				url: url_save,
				dataType : "json",
				type: 'POST',
				data: formdata,
				processData	: false,
				contentType	: false,
				success: function(msg){
					if(msg['status']=='1'){
						swal({
							title: "Sukses!",
							text: "Data Berhasil Di Simpan",
							type: "success",
							timer: 7000,
							showConfirmButton: false
						});
						cancel();
						window.location.reload();
					} else {
						swal({
							title: "Gagal!",
							text: "Data Gagal Di Simpan",
							type: "error",
							timer: 7000,
							showConfirmButton: false
						});
					};
					console.log(msg);
				},
				error: function(msg){
					swal({
						title: "Gagal!",
						text: "Ajax Data Gagal Di Proses",
						type: "error",
						timer: 7000,
						showConfirmButton: false
					});
					console.log(msg);
				}
			});
		  }
		});
		}else{
			swal(errors);
			return false;
		}
	});

	//mengajukan
	$(document).on('click', '#request', function(){

		if($('.chk_personal:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist Minimal One !!!',
				type	: "warning"
			});
			return false;
		}

		swal({ 
			title: "Are you sure?",
			text: "You will be able to process again this data!",
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
				var formData  	= new FormData($('#frm_data')[0]);
				$.ajax({
					url			: base_url + active_controller+'/save_request',
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
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	//mengajukan
	$(document).on('click', '#approval', function(){
		let app_action = $('#app_action').val();
		let app_reason = $('#app_reason').val();

		if(app_action == '0'){
			swal({
				title	: "Error Message!",
				text	: 'Aprove action not action !!!',
				type	: "warning"
			});
			return false;
		}

		if(app_action == 'N' && app_reason == ''){
			swal({
				title	: "Error Message!",
				text	: 'Reject reason is empty !!!',
				type	: "warning"
			});
			return false;
		}

		if($('.chk_personal:checked').length == 0){
			swal({
				title	: "Error Message!",
				text	: 'Checklist Minimal One !!!',
				type	: "warning"
			});
			return false;
		}

		swal({ 
			title: "Are you sure?",
			text: "You will be able to process again this data!",
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
				var formData  	= new FormData($('#frm_data')[0]);
				$.ajax({
					url			: base_url + active_controller+'/save_approval',
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
							window.location.href = base_url + active_controller+'/approval/'+data.id;
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title	: "Error Message !",
							text	: 'An Error Occured During Process. Please try again..',						
							type	: "warning",								  
							timer	: 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

	function add_detail(){
		var idbudget = [];
		var departement = $("#departement").val();
		var tanggal_doc = $("#tanggal_doc").val();
		$('.budget').each(function() {
			idbudget.push($(this).val());
		});
        $.ajax({
            url: base_url+"pengajuan_rutin/get_data",
            dataType : "json",
            type: 'POST',
            data: { allbudget: idbudget, dept: departement, tanggal: tanggal_doc },
            success: function(msg){
                if(msg['save']=='1'){
					$.each(msg['data'], function(index, element) {
						var Rows	 = 	"<tr id='tr1_"+nomor+"' class='delAll'>";
							Rows	+= 		"<td>";
							Rows	+=			"<input type='hidden' name='detail_id[]' id=raw_id_"+nomor+"' value=''>";
							Rows	+=			"<input type='hidden' name='id_budget[]' id='id_budget_"+nomor+"' value='"+element.id+"' class='budget'>";
							Rows	+=			"<input type='hidden' name='coa[]' id='coa_"+nomor+"' value='"+element.coa+"'></td>";
							Rows	+= 		"</td>";
							Rows	+= 		"<td>";
							Rows	+=			"<input type='text' class='form-control' name='nama[]' id='nama_"+nomor+"' value='"+element.nama+"' />";
							Rows	+= 		"</td>";
							var jadwal='';
							if(element.tipe=='tahun') jadwal=msg['tahun']+'-'+element.tanggal;
							if(element.tipe=='bulan') jadwal=msg['tahun']+'-'+msg['bulan']+'-'+element.tanggal;
							Rows	+= 		"<td>";
							Rows	+=			"<input type='text' class='form-control tanggal text-center' name='tanggal[]' id='tanggal_"+nomor+"' value='"+jadwal+"' />";
							Rows	+= 		"</td>";
							Rows	+= 		"<td>";
							Rows	+=			"<input type='text' class='form-control divide text-right' name='budget[]' value='"+element.nilai+"' id='budget_"+nomor+"' />";
							Rows	+= 		"</td>";
							Rows	+= 		"<td hidden>";
							Rows	+=			"<input type='text' class='form-control divide text-right' name='nilai[]' value='0' id='nilai_"+nomor+"' />";
							Rows	+= 		"</td>";
							Rows	+= 		"<td hidden>";
							Rows	+=			"<input type='text' class='form-control' name='keterangan[]' id='keterangan_"+nomor+"' value='' />";
							Rows	+= 		"</td>";
							Rows	+= 		"<td hidden>";
							Rows	+=			"<input type='file' name='doc_file_"+nomor+"' id='doc_file"+nomor+"'>";
							Rows	+= 		"</td>";
							Rows	+= 		"<td></td>";
							
							Rows	+= 		"<td align='center' class='hidden'>";
							Rows 	+=			"<button type='button' class='btn btn-danger btn-xs' data-toggle='tooltip' onClick='delDetail("+nomor+")' title='Hapus data'><i class='fa fa-close'></i> Hapus</button>";
							Rows	+= 		"</td>";
							Rows	+= 	"</tr>";
							nomor++;
							$('#detail_body').append(Rows);
					});
					$(".divide").divide();
					$(".tanggal").datepicker({
						dateFormat: 'yy-mm-dd',
						changeMonth:true,
						changeYear:true
					});

				} else {
                    swal({
                        title: "Gagal!",
                        text: "Data gagal diambil",
                        type: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                };
				console.log(msg);
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

	}

	function delDetail(row){
		$('#tr1_'+row).remove();
	}
	<?php
	if($idd==1 AND $tanda =='create') echo "add_detail();";
	?>
</script>