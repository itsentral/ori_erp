<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary" style='margin-right: 17px;'>
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
			<a href="<?php echo site_url('budget/excel_kompilasi') ?>"  target='_blank' title="Download Excel" class="btn btn-md btn-success">
				<i class="fa fa-file-excel-o"></i> &nbsp;Download Excel
			</a>
		</div>
	</div>
	<div class="box-body">
		<div class="tableFixHead" style="height:600px;">
			<table class="table table-bordered table-striped" id="my-grid" width='100%'>
				<thead  class="thead">
					<tr class='bg-blue'>
						<th class="text-center th">#</th>
						<th class="text-left th">KATEGORI</th> 
						<th class="text-left th">NAMA BARANG</th>
						<th class="text-left th">SPESIFIKASI</th>
						<th class="text-left th">BRAND</th>
						<th class="text-left th">TOTAL</th>
						<th class="text-left th">UNIT</th>
						<?php
						foreach($group_header AS $val => $valx){
							$cc = '';
							if($valx['costcenter'] <> '0'){
								$cc = strtoupper(get_name('costcenter', 'nm_costcenter', 'id_costcenter', $valx['costcenter']));
							}
							echo "<th class='text-left th'><u>".strtoupper(get_name('department', 'nm_dept', 'id', $valx['department']))."</u><i><br>".$cc."</i></th>";
						}
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($group_barang AS $val => $valx){ $val++;
						echo "<tr>";
							echo "<td class='text-center'>".$val."</td>";
							echo "<td class='text-left'>".strtoupper(get_name('con_nonmat_category_awal', 'category', 'id', $valx['jenis_barang']))."</td>";
							echo "<td class='text-left'>".strtoupper(get_name('con_nonmat_new', 'material_name', 'code_group', $valx['id_barang']))."</td>";
							echo "<td class='text-left'>".strtoupper(get_name('con_nonmat_new', 'spec', 'code_group', $valx['id_barang']))."</td>";
							echo "<td class='text-left'>".strtoupper(get_name('con_nonmat_new', 'brand', 'code_group', $valx['id_barang']))."</td>";
							$total_kebutuhan = 0;
							foreach($group_header AS $val2 => $valx2){
								$get_qty = $this->db->query("SELECT a.kebutuhan_month FROM budget_rutin_detail a LEFT JOIN budget_rutin_header b ON a.code_budget=b.code_budget WHERE a.id_barang='".$valx['id_barang']."' AND b.department='".$valx2['department']."' AND b.costcenter='".$valx2['costcenter']."' ")->result();
								$total_kebutuhan += (!empty($get_qty))?$get_qty[0]->kebutuhan_month:0;
							}
							echo "<td class='text-right'>".number_format($total_kebutuhan)."</td>";
							echo "<td class='text-left'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
							foreach($group_header AS $val2 => $valx2){
								$get_qty = $this->db->query("SELECT a.kebutuhan_month FROM budget_rutin_detail a LEFT JOIN budget_rutin_header b ON a.code_budget=b.code_budget WHERE a.id_barang='".$valx['id_barang']."' AND b.department='".$valx2['department']."' AND b.costcenter='".$valx2['costcenter']."' ")->result();
								$qty = (!empty($get_qty))?number_format($get_qty[0]->kebutuhan_month):'-';
								echo "<td class='text-center'>".$qty."</td>";
							}
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Back','id'=>'back')).' ';
		?>
	</div>
 </div>
<?php $this->load->view('include/footer'); ?>
<style media="screen">
  /* JUST COMMON TABLE STYLES... */
  .table { border-collapse: collapse; width: 100%; }
  .td { background: #fff; padding: 8px 16px; }

  .tableFixHead {
    overflow: auto;
    height: 300px;
    position: sticky;
    top: 0;
  }

  .thead .th {
    position: sticky;
    top: 0;
    z-index: 9999;
  	background: #0073b7;
	vertical-align: top;
  }
</style>
<script>
	$(document).ready(function(){
		$('.maskM').maskMoney();
		$('.chosen_select').chosen();
	});
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller+'/index_rutin';
	});
</script>
