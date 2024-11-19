<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">		
		</div><br><br>
		<?php
			if($akses_menu['create']=='1'){ 
			?>
			<div class="dropdown">
			  <button class="btn btn-success btn-sm" type="button" onclick="data_add()">
				<i class="fa fa-plus">&nbsp;</i> Tambah
			  </button>
			</div>
			<?php
			}
		?>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
            <thead>
			<tr>
				<th width="5">#</th>
				<th>No</th>
				<th>Tanggal</th>
				<th>Nama</th>
				<th>Keperluan</th>
				<th>No. Polisi</th>
				<th>Status</th>
				<th width="120">Action</th>
			</tr>
            </thead>
            <tbody>
            <?php
            if(!empty($results)){
                $numb=0; foreach($results AS $record){ $numb++; ?>
            <tr>
                <td><?= $numb; ?></td>
				<td><?= $record->no_doc ?></td>
				<td><?= $record->tgl_doc ?></td>
				<td><?= $record->nmuser ?></td>
				<td><?= $record->keperluan ?></td>
				<td><?= $record->nopol?></td>
				<td><?= $status[$record->status] ?></td>
				<td>
                <?php if($akses_menu['read']=='1'){ ?>
                   <a class="btn btn-warning btn-sm view" href="javascript:void(0)" title="View" onclick="data_view('<?=$record->id?>')"><i class="fa fa-eye"></i></a>
                <?php }
                if($akses_menu['update']=='1'){
                    if ($record->status==0) {?>
                   <a class="btn btn-success btn-sm edit" href="javascript:void(0)" title="Edit" onclick="data_edit('<?=$record->id?>')"><i class="fa fa-edit"></i></a>
                    <?php }
                }
                if($akses_menu['delete']=='1'){
                    if ($record->status==0) {?>
                    <a class="btn btn-danger btn-sm delete" href="javascript:void(0)" title="Hapus" onclick="data_delete('<?=$record->id?>')"><i class="fa fa-trash"></i></a>
                    <?php }
                }
                ?>
                </td>
            </tr>
            <?php
                }
            }  ?>
            </tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
<div id="form-data"></div>
<?php $this->load->view('include/footer'); ?>
<script>
	var url_add = base_url+'expense/transport_create/';
	var url_edit = base_url+'expense/transport_edit/';
	var url_delete = base_url+'expense/transport_delete/';
	var url_view = base_url+'expense/transport_view/';
</script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>
