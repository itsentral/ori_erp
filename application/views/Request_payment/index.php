<?php
$this->load->view('include/side_menu');
?>
<div id="alert_edit" class="alert alert-success alert-dismissable" style="padding: 15px; display: none;"></div>
<?=form_open($this->uri->uri_string(),array('id'=>'frm_data','name'=>'frm_data','role'=>'form','class'=>'form-horizontal'));?>
<div class="box">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>		
	</div>
	<div class="box-body">
		<div class="table-responsive">
		<table id="mytabledata" class="table table-bordered">
		<thead>
		<tr>
			<th width="5">#</th>
			<th>No Dokumen</th>
			<th>Request By</th>
			<th>Tanggal</th>
			<th>Keperluan</th>
			<th>Tipe</th>
			<th>Mata Uang</th>
			<th>Nilai Pengajuan (Asing)</th>
			<th>Nilai Pengajuan (IDR)</th>
			<th>Tanggal Pembayaran</th>
			<th>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($row)){
			$numb=0; 
			foreach($row AS $record){ 
			  if($record->jumlah>0){
				$numb++;
			?>
		<tr>
		    <td><?= $numb; ?></td>
			<td><?= $record->no_doc ?></td>
			<td><?= $record->nama ?></td>
			<td><?= $record->tgl_doc ?></td>
			<td><?= $record->keperluan ?></td>
			<td><?= $record->tipe ?></td>
			<td><?= $record->matauang ?></td>
			<td><?= number_format($record->jumlah_kurs) ?></td>
			<td><?= number_format($record->jumlah) ?></td>
			<td><input type="text" class="form-control tanggal" id="tanggal_<?=$numb?>" name="tanggal_<?=$numb?>" value="" placeholder="Tanggal"></td>
			<td>
			<?php if($akses_menu['create']=='1'){ ?>
				<input type="hidden" name="no_doc_<?=$numb?>" id="no_doc_<?=$numb?>" value="<?=$record->no_doc?>">
				<input type="hidden" name="nama_<?=$numb?>" id="nama_<?=$numb?>" value="<?=$record->nama?>">
				<input type="hidden" name="tgl_doc_<?=$numb?>" id="tgl_doc_<?=$numb?>" value="<?=$record->tgl_doc?>">
				<input type="hidden" name="keperluan_<?=$numb?>" id="keperluan_<?=$numb?>" value="<?=$record->keperluan?>">
				<input type="hidden" name="tipe_<?=$numb?>" id="tipe_<?=$numb?>" value="<?=$record->tipe?>">
				<input type="hidden" name="jumlah_<?=$numb?>" id="jumlah_<?=$numb?>" value="<?=$record->jumlah?>">
				<input type="hidden" name="jumlah_kurs_<?=$numb?>" id="jumlah_kurs_<?=$numb?>" value="<?=$record->jumlah_kurs?>">
				<input type="hidden" name="matauang_<?=$numb?>" id="matauang_<?=$numb?>" value="<?=$record->matauang?>">
				<input type="hidden" name="bank_id_<?=$numb?>" id="bank_id_<?=$numb?>" value="<?=$record->bank_id?>">
				<input type="hidden" name="accnumber_<?=$numb?>" id="accnumber_<?=$numb?>" value="<?=$record->accnumber?>">
				<input type="hidden" name="accname_<?=$numb?>" id="accname_<?=$numb?>" value="<?=$record->accname?>">
				<input type="hidden" name="ids_<?=$numb?>" id="ids_<?=$numb?>" value="<?=$record->ids?>">
				<input type="checkbox" name="status[]" id="status_<?=$numb?>" value="<?=$numb?>" class="dtlloop" onclick="cektotal()">
			<?php }
			if($record->tipe=='kasbon'){?>
			<a href="<?=base_url('expense/kasbon_view/'.$record->ids)?>" target="_blank"><i class="fa fa-search pull-right"></i></a>
			<?php }
			if($record->tipe=='transportasi'){?>
			<a href="<?=base_url('expense/transport_req_view/'.$record->ids)?>" target="_blank"><i class="fa fa-search pull-right"></i></a>
			<?php }
			if($record->tipe=='expense'){?>
			<a href="<?=base_url('expense/view/'.$record->ids)?>" target="_blank"><i class="fa fa-search pull-right"></i></a>
			<?php }
			if($record->tipe=='nonpo'){?>
			<a href="<?=base_url('purchase_order/non_po/view/'.$record->ids)?>" target="_blank"><i class="fa fa-search pull-right"></i></a>
			<?php }
			if($record->tipe=='periodiks'){?>
			<a href="<?=base_url('pembayaran_rutin/view/'.$record->ids)?>" target="_blank"><i class="fa fa-search pull-right"></i></a>
			<?php }
			?>
			</td>
		</tr>
		<?php
				}
			}
		}  ?>
		</tbody>
		<tfoot>
		<tr><td colspan=7 align=right>Total</td><td colspan=2><input type="text" class="form-control divide input-sm" name="total_req" id="total_req" value="0" readonly></td></tr>
		</tfoot>
		</table>
		<div class="pull-right">
		<?php if($akses_menu['create']=='1'){ ?>
		<button type="submit" name="save" class="btn btn-success btn-sm" id="submit"><i class="fa fa-save">&nbsp;</i>Update</button>
		<?php } ?>
		</div>
		</div>
	</div>
	<!-- /.box-body -->
</div>
<?= form_close() ?>
<?php $this->load->view('include/footer'); ?>
<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
<script type="text/javascript">
	$("#mytabledata").DataTable({
	"paging": false,
	"ordering": false,
	"info": false,
	});
	$(".divide").divide();
	function cektotal(){
		var total_req=0;
		$('.dtlloop').each(function() {
			if(this.checked){
				var ids=$(this).val();
				total_req += Number($("#jumlah_"+ids).val());
			}
		});
		$("#total_req").val(total_req);
	}
	var url_save = base_url+'request_payment/save_request/';
	$(function () {
		$(".tanggal").datepicker({
			dateFormat: 'yy-mm-dd'
		});
	});
	//Save
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
			$("#submit").hide();
			$.ajax({
				url: url_save,
				dataType : "json",
				type: 'POST',
				data: formdata,
				processData	: false,
				contentType	: false,
				success: function(msg){
					if(msg['save']=='1'){
						swal({
							title: "Sukses!",
							text: "Data Berhasil Di Update",
							type: "success",
							timer: 1500,
							showConfirmButton: false
						});
						window.location.href= window.location.href;
					} else {
						swal({
							title: "Gagal!",
							text: "Data Gagal Di Update",
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
		});
		}else{
			swal(errors);
			return false;
		}
    });
</script>
