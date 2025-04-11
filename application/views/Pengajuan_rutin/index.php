<?php
$this->load->view('include/side_menu'); 
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
		<div class="box-tool pull-left">
			<?php if($tanda != 'approval'){ ?>
			<div class="dropdown">
				<button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<i class="fa fa-plus">&nbsp;</i> New
				</button>
				<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					<?php
					echo "<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>DEPARTEMEN</b></li>";
					foreach (get_list_dept() as $key => $val){
					echo "<li><a href=".site_url('pengajuan_rutin/create/'.$val['id'])."><i class='fa fa-sun-o'></i> &nbsp;&nbsp;".$val['nm_dept']."</a></li>";
					}
					?>
				</ul>
			</div>
			<?php } ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="mytabledata" class="table table-bordered table-striped">
			<thead>
				<tr>
					<th class='text-center'>#</th>
					<th class='text-center'>Department</th>
					<th class='text-center'>Nomor</th>
					<th class='text-center'>Detail</th>
					<th class='text-center'>Tanggal</th>
					<th class='text-center'>By</th>
					<th class='text-center'>Date</th>
					<th class='text-center'>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($results)){
					$numb = 0; 
					foreach($results AS $record){ $numb++; 
						$last_by 	= (!empty($record->modified_by))?$record->modified_by:$record->created_by;
						$last_date = (!empty($record->modified_on))?$record->modified_on:$record->created_on;
						$check_action = $this->db->select('*')->from('tr_pengajuan_rutin_detail')->where('no_doc',$record->no_doc)->where("(status='N' OR status='R')")->get()->num_rows();
						?>
						<tr>
							<td class='text-center'><?= $numb; ?></td>
							<td><?= $record->nm_dept ?></td>
							<td class='text-center'><?= $record->no_doc?></td>
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
									if($value['status'] == 'R'){
										$APP = '(Reason: '.$value['reason'].')';
									}

									echo $key.'. '.strtoupper($value['nama']).' <span class="text-bold text-'.$EXPLODE[1].'">('.$EXPLODE[0].') '.$APP.'</span><br>';
								}
								?>
							</td>
							<td class='text-center'><?=date('d-M-Y',strtotime( $record->tanggal_doc));?></td>
							<td class='text-center'><?= strtoupper($last_by);?></td>
							<td class='text-center'><?= date('d-M-Y H:i:s',strtotime($last_date));?></td>
							<td>
								<a class="btn btn-warning btn-sm view" href=<?=site_url('pengajuan_rutin/view/'.$record->id);?> title="View"><i class="fa fa-eye"></i></a>
								<?php
								// if ($record->status==0) {
									if($check_action > 0 AND $tanda != 'approval'){
									?>
										<a class="btn btn-success btn-sm" href=<?=site_url('pengajuan_rutin/edit/'.$record->id);?> title="Edit"><i class="fa fa-edit"></i></a>
										<a class="btn btn-info btn-sm" href=<?=site_url('pengajuan_rutin/request/'.$record->id);?> title="Request"><i class="fa fa-paper-plane"></i></a>
										<a class="btn btn-primary btn-sm" href=<?=site_url('pengajuan_rutin/print_request/'.$record->id);?> target='_blank' title="Print"><i class="fa fa-print"></i></a>
										<!-- <a class="btn btn-danger btn-sm delete" href="javascript:void(0)" title="Hapus" onclick="data_delete('<?=$record->no_doc?>')"><i class="fa fa-trash"></i></a> -->
									<?php
									}
									if($tanda == 'approval'){
										?>
										<a class="btn btn-success btn-sm" href=<?=site_url('pengajuan_rutin/approval/'.$record->id);?> title="Approval"><i class="fa fa-check"></i></a>
									<?php
									}
								// }
								?>
							</td>
						</tr>
					<?php
					}
				}  
				?>
			</tbody>
		</table>
	</div>  
</div>

<?php $this->load->view('include/footer'); ?>
<script type="text/javascript">
	var url_add 	= "";
	var url_add_def = base_url+'pengajuan_rutin/create/';
	var url_edit 	= base_url+'pengajuan_rutin/edit/';
	var url_delete 	= base_url+'pengajuan_rutin/hapus_data/';
	var url_view 	= base_url+'pengajuan_rutin/view/';

	function new_data(key){
		url_add = url_add_def+key;
		data_add();
	}
</script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>