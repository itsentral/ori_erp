<?php
$this->load->view('include/side_menu'); 
?>

<?= form_open($this->uri->uri_string(),array('id'=>'frm_data','name'=>'frm_data','role'=>'form','class'=>'form-horizontal')) ?>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br><br>
	</div>
	<div class="box-body">
        <div class="row table-responsive">
            <div class="col-md-12 tableFixHead">
                <table class="table table-bordered table-condensed">
					<thead>
					<tr>
						<th>COA</th>
						<th>No Perkiraan</th>
						<th>Budget Bulan <?=date('F', strtotime('2020-'.$bulan.'-01'))?></th>
						<th>Year To Date <?=date('F', strtotime('2020-'.$bulan.'-01'))?> <?=$tahun?></th>
						<th>Budget Tahun <?=$tahun?></th>
					</tr>
					</thead>
					<tbody>
					<?php $i=0;
					foreach($data as $record) {
						$i++;?>
						<tr>
							<td><?=$record->no_perkiraan.'</td><td>'.$record->nama_perkiraan; ?></td>
							<td align=right><?= number_format($record->{"bulan_".$bulan});?></td>
							<?php
							$ytd=0;
							for($bln=1;$bln<=$bulan;$bln++){
								$ytd=($ytd+$record->{"bulan_".$bln});
							}
							?>
							<td align=right><?php echo number_format($ytd); ?></td>
							<td align=right><?=number_format($record->total); ?></td>
					<?php } ?>
					</tbody>
				</table>
            </div>
        </div>
        <div class="box-footer">
            <button type='button' class="btn btn-danger" onclick="cancel()">Back</button>
        </div>
    </div>
</div>
<?= form_close() ?>

<?php $this->load->view('include/footer'); ?>

<style>
    .tableFixHead          { overflow: auto; height: 500px; }
    .tableFixHead thead th { position: sticky; top: 0; z-index: 1; background-color:#dadada; }
</style>
<script>
	 $(document).ready(function() {
		$("#tahunform").val('<?=$tahun?>');
    });

    function cancel(){
        window.open(base_url+'budget_coa',"_self");
    }
</script>
