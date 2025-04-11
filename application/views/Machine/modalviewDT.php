<?php
$id_bq = $this->uri->segment(3);

$qBQdetailHeader 	= "SELECT a.*, b.sum_mat FROM bq_detail_header a LEFT JOIN estimasi_cost_and_mat b ON a.id=b.id_milik WHERE a.id_bq = '".$id_bq."' AND b.id_bq = '".$id_bq."' AND b.parent_product <> 'pipe slongsong' ORDER BY a.id ASC";
$qBQdetailRest		= $this->db->query($qBQdetailHeader)->result_array();
// echo $qBQdetailHeader;
$detail 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'acc'))->result_array();
$detail2 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'mat'))->result_array();
$detail3 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'baut'))->result_array();
$detail4 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'plate'))->result_array();
$detail4g 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'gasket'))->result_array();
$detail5 		= $this->db->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'category'=>'lainnya'))->result_array();

$GET_DET_ACC = get_detail_accessories();
?>
<div class="box box-primary">
	<div class="box-header">
		<label>A. PIPA FITTING</label>
	</div>
	<div class="box-body">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
					<th class="text-center" style='vertical-align:middle;' width='16%'>Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>No Component</th>
					<th class="text-center" style='vertical-align:middle;' width='10%'>Dimensi</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Qty</th>
					<th class="text-center" style='vertical-align:middle;' width='29%'>Product ID</th>
					<th class="text-center" style='vertical-align:middle;' width='14%'>Material</th>
					<th class="text-center" style='vertical-align:middle;' width='8%'>Detail</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$Sum = 0;
					$no = 0;
					if(!empty($qBQdetailRest)){
						foreach($qBQdetailRest AS $val => $valx){ $no++;
							$spaces = "";
							$id_delivery = strtoupper($valx['id_delivery']);
							$bgwarna	= "bg-blue";
								
							$SumQty	= $valx['sum_mat'] * $valx['qty'];
							$Sum += $SumQty;
							if($valx['sts_delivery'] == 'CHILD'){
								$spaces = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								$id_delivery = strtoupper($valx['sub_delivery']);
								$bgwarna	= "bg-green";
							}
							echo "<tr>";
								echo "<td align='center'>".$no."</span></td>";
								echo "<td align='left' style='padding-left:20px;'>".$spaces."".strtoupper($valx['id_category'])."</td>";
								echo "<td align='left'>".$valx['no_komponen']."</span></td>";
								echo "<td align='left' style='padding-left:20px;'>".spec_bq($valx['id'])."</td>";
								echo "<td align='center'><span class='badge ".$bgwarna."'>".$valx['qty']."</span></td>";
								echo "<td align='left'>".$valx['id_product']."</span></td>";
								echo "<td align='right' style='padding-right:20px;'>".number_format($SumQty, 3)." Kg</span></td>";
								echo "<td align='center'>"; 
									echo "<button class='btn btn-sm btn-warning' id='detailDT' title='Detail Material' data-id_product='".$valx['id_product']."' data-id_milik='".$valx['id']."' data-qty='".$valx['qty']."' data-length='".floatval($valx['length'])."'><i class='fa fa-eye'></i></button>"; 
									if(!empty($valx['id_product'])){
										echo "&nbsp;<button type='button' class='btn btn-sm btn-primary detailX' title='Lihat Estimasi' data-id_bq='".$valx['id_bq']."' data-id_milik='".$valx['id']."'><i class='fa fa-eye'></i></button>";
									}
								echo "</td>";						
							echo "</tr>";
						}
					}
					else{
						echo "<tr>";
							echo "<td colspan='8'>Tidak ada product yang ditampilkan atau kemungkinan product kosong.</td>";
						echo "</tr>";
					}
				?>
				<tr>
					<th class="text-center" colspan='6' style='vertical-align:middle;'>Total</th> 
					<th class="text-right" style='padding-right:20px;'><?= number_format($Sum, 3);?> Kg</th>
					<th class="text-center" style='vertical-align:middle;'></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>B. MUR BAUT</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='19%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail3)){
						foreach($detail3 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							echo "<tr class='header3_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='6'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>C. PLATE</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='9%'>Lebar (mm)</th>
					<th class="text-center" width='9%'>Panjang (mm)</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Berat (kg)</th>
					<th class="text-center" width='9%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4)){
						foreach($detail4 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							echo "<tr class='header4_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='right'>".number_format($valx['berat'],3)."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='10'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>D. GASKET</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='9%'>Lebar (mm)</th>
					<th class="text-center" width='9%'>Panjang (mm)</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='9%'>Sheet</th>
					<th class="text-center" width='10%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail4g)){
						foreach($detail4g AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							echo "<tr class='header4g_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='right'>".number_format($valx['lebar'],2)."</td>";
								echo "<td align='right'>".number_format($valx['panjang'],2)."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='right'>".number_format($valx['sheet'],2)."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='8'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-success">
	<div class="box-header">
		<label>E. LAINNYA</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='19%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail5)){
						foreach($detail5 AS $val => $valx){ $id++;
							$spec = (!empty($GET_DET_ACC[$valx['id_material']]['spec']))?$GET_DET_ACC[$valx['id_material']]['spec']:'';
							echo "<tr class='header5_".$id."'>";
								echo "<td align='center'>".$id."</td>";
								echo "<td align='left'>".$spec."</td>";
								echo "<td align='center'>".number_format($valx['qty'])."</td>";
								echo "<td align='center'>".strtoupper(get_name('raw_pieces','kode_satuan','id_satuan',$valx['satuan']))."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='7'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<div class="box box-info">
	<div class="box-header">
		<label>F. MATERIAL</label>
	</div>
	<div class="box-body">
		<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<thead id='head_table'>
				<tr class='bg-blue'>
					<th class="text-center" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='9%'>Qty</th>
					<th class="text-center" width='9%'>Unit</th>
					<th class="text-center" width='19%'>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$id = 0;
					if(!empty($detail2)){
						foreach($detail2 AS $val => $valx){ $id++;
		
							echo "<tr class='header_".$id."'>";
								echo "<td align='center'>".$id."</td>"; 
								echo "<td align='left'>".strtoupper(get_name('raw_materials','nm_material','id_material',$valx['id_material']))."</td>";
								echo "<td align='right'>".number_format($valx['qty'],2)."</td>";
								echo "<td align='center'>".get_name('raw_pieces','kode_satuan','id_satuan','1')."</td>";
								echo "<td align='left'>".strtoupper($valx['note'])."</td>";
							echo "</tr>";
						}
					}else{
						echo "<tr>";
							echo "<td colspan='5'>Tidak ada data yang ditampilkan.</td>";
						echo "</tr>";
					}
				?>
            </tbody>
		</table>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.chosen-select').chosen({width: '100%'});
		swal.close();
	});
	
	$(document).on('click', '#detailDT', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL DATA BQ ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailMat/'+$(this).data('id_product')+'/'+$(this).data('id_milik')+'/'+$(this).data('qty')+'/'+$(this).data('length'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '.detailX', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title2").html("<b>DETAIL ESTIMATION</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalDetailX/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'));
		$("#ModalView2").modal();
	});

</script>