<?php
$this->load->view('include/side_menu'); 
?>

<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-left">
                
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
		<!-- <div class="table-responsive"> -->
		<table id="mytabledata" class="table table-bordered table-striped">
		<thead>
		<tr>
			<th class='text-center'>#</th>
			<th class='text-center'>Departement</th>
			<th class='text-center'>Nomor</th>
			<th class='text-center'>Detail</th>
			<th class='text-center'>Tanggal</th>
			<th class='text-center'>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($results)){
			$numb=0; 
			foreach($results AS $record){ 
				$check2 = $this->db->select('*')->from('tr_pengajuan_rutin_detail')->where('no_doc',$record->no_doc)->where("(status='A' OR status='P')")->get()->result_array();
				if(!empty($check2)){
					$numb++; 
					$check_action = $this->db->select('*')->from('tr_pengajuan_rutin_detail')->where('no_doc',$record->no_doc)->where("(status='A')")->get()->num_rows();
					?>
					<tr>
						<td><?= $numb; ?></td>
						<td><?= $record->nm_dept ?></td>
						<td><?= $record->no_doc?></td>
						<td>
							<?php
							foreach ($get_detail[$record->no_doc] as $key => $value) { $key++;
								$status = getStatusPeriodik($value['status']);
								$EXPLODE = explode('-',$status);

								$APP = '';
								if($value['status'] == 'A'){
									$APP = '('.ucwords(strtolower(get_nama_user($value['status_by']))).' - '.$value['status_date'].')';
								}
								if($value['status'] == 'P'){
									$APP = '('.ucwords(strtolower(get_nama_user($value['payment_by']))).' - '.$value['payment_date'].')';
								}

								echo $key.'. '.strtoupper($value['nama']).' <span class="text-bold text-'.$EXPLODE[1].'">('.$EXPLODE[0].') '.$APP.'</span><br>';
							}
							?>
						</td>
						<td><?= $record->tanggal_doc?></td>
						<td><a class="btn btn-warning btn-sm view" href=<?=site_url('pembayaran_periodik/edit/'.$record->id.'/view');?> title="View"><i class="fa fa-eye"></i></a>
						
						<a class='btn btn-primary btn-sm view hidden' href='javascript:void(0)' title='View Jurnal Pembayaran Periodik' data-id_material=<?=$record->no_doc?> data-id_total=<?=$record->id?> ><i class='fa fa-list'></i>
						</a>
				
							<?php
							if($check_action > 0){
								?>
								<a class="btn btn-success btn-sm edit" href=<?=site_url('pembayaran_periodik/edit/'.$record->id);?> title="Payment"><i class="fa fa-credit-card"></i></a>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
			}
		}
		?>
		</tbody>
		</table>
		<!-- </div> -->
	</div>
</div>

<!-- modal -->
 <div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:100%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal --> 
	
	
	<div class="modal modal-default fade" id="dialog-popup" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:hidden;">
	  <div class="modal-dialog modal-lg" style='width:80%;'>
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel"><span class="fa fa-users"></span>&nbsp;Detail Jurnal</h4>
		  </div>
		  <div class="modal-body" id="ModalView">
			...
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">
			<span class="glyphicon glyphicon-remove"></span>  Close</button>
			 
		 </div>
	    </div>
	  </div>
	
	</div>


<?php $this->load->view('include/footer'); ?>
<script type="text/javascript">
	var url_add = "";
	var url_add_def = base_url+'pembayaran_periodik/create/';
	var url_edit = base_url+'pembayaran_periodik/edit/';
	var url_view = base_url+'pembayaran_periodik/view/';

	function new_data(key){
		url_add = url_add_def+key;
		data_add();
	}
	
	
	$(document).on('click', '.view2', function(){
		var id = $(this).data('id_material');
		var total = $(this).data('id_total');
		var id_vendor = $(this).data('id_vendor');
		var nm_vendor = $(this).data('nm_vendor');
		var pp = 'pononstok';
		var kd ='JV002'
		var akses = 'approval_jurnal_po_nonstok';
		var ket ='GD PUSAT - SUB GUDANG'
		// alert(id);
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Detail Jurnal</b>");
		$.ajax({
			type:'POST',
			url:base_url+'jurnal_nomor/view_jurnal_periodik/'+id,
			data:{'id':id},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
				
			}
		})
	});
	
</script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>