<?php
$this->load->view('include/side_menu'); 
?>

<div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><?php echo $title;?></h3><br><br>
            <div class="box-tool pull-left">
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
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body"><div class="table-responsive">
		<table id="mytabledata" class="table table-bordered table-striped">
		<thead>
		<tr>
			<th width="5">#</th>
			<th>Departement</th>
			<th>Nomor</th>
			<th>Tanggal</th>
			<th width="150">
				Action
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($results)){
			$numb=0; foreach($results AS $record){ $numb++; ?>
		<tr>
		    <td><?= $numb; ?></td>
			<td><?= $record->nm_dept ?></td>
			<td><?= $record->no_doc?></td>
			<td><?= $record->tanggal_doc?></td>
			<td>
			<?php if($ENABLE_VIEW) : ?>
				<a class="btn btn-warning btn-sm view" href="javascript:void(0)" title="View" onclick="data_view('<?=$record->id?>')"><i class="fa fa-eye"></i></a>
			<?php endif;
			if($record->status==1){
				if($ENABLE_MANAGE) : ?>
					<a class="btn btn-success btn-sm edit" href="javascript:void(0)" title="Edit" onclick="data_edit('<?=$record->id?>')"><i class="fa fa-edit"></i></a>
				<?php endif;
			} ?>
			
			
			</td>
		</tr>
		<?php
			}
		}  ?>
		</tbody>
		</table>
		</div>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>
<script type="text/javascript">
	var url_add = "";
	var url_add_def = base_url+'pembayaran_rutin/create/';
	var url_edit = base_url+'pembayaran_rutin/edit/';
	var url_view = base_url+'pembayaran_rutin/view/'; 

	function new_data(key){
		url_add = url_add_def+key;
		data_add();
	}
</script>
<script src="<?= base_url('assets/js/basic.js')?>"></script>