<?php
$product_name = '';
$product_qty = '';
if($DMF_TANDA == 'DMF'){
	$product_name = $get_no_spk[0]['product_name'];
	$product_qty = $get_no_spk[0]['qty'];
}
?>
<form action="#" method="POST" id="form_adjustment" enctype="multipart/form-data" autocomplete='off'> 
	<div class="box-body">
	<?php
	if($no_ipp != 'ancuran' AND $no_ipp != 'internal' AND $no_ipp != 'reqnonso'){
	?>
	<h4><b>Nomor SO : <?= get_name('so_number','so_number','id_bq','BQ-'.$no_ipp);?></b></h4>
	<?php } ?>
	<input type="hidden" name='IPP_TANDA' id='IPP_TANDA' value='<?= $IPP_TANDA;?>'>
	<input type="hidden" name='no_ipp' id='no_ipp' value='<?= $no_ipp;?>'>
	<input type="hidden" name='id_milik' id='id_milik'>
    <input type="hidden" name='gudang_before' id='gudang_before' value='<?= $gudang_before;?>'>
	<input type="hidden" name='gudang_after' id='gudang_after' value='<?= $gudang_after;?>'>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>No SPK *</b></label>
		<div class='col-sm-4'>              
			<select name="no_spk" id="no_spk" class='form-control chosen-select'>
				<?php if($DMF_TANDA != 'DMF'){ ?>
				<option value="0">Pilih No SPK</option>
				<option value="INT">INTERNAL</option>
				<?php
				}
				foreach ($get_no_spk as $key => $value) {
					if($IPP_TANDA == 'IPPT'){
						echo "<option value='".$value['no_spk']."'>".$value['no_spk']." - ".$value['product_name']."</option>";
					}
					elseif($DMF_TANDA == 'DMF'){
						echo "<option value='".$value['code_est']."'>".$value['no_spk']." - ".$value['product_name']."</option>";
					}
					else{
						echo "<option value='".$value['no_spk']."'>".$value['no_spk']." - ".$value['product_name'].", ".spec_bq2($value['id'])."</option>";
					}
				}
				?>
			</select>
		</div>
		<label class='label-control col-sm-2'><b>Product</b></label>
		<div class='col-sm-4'>              
			<input type='text' name="product_text" id="product_text" class='form-control' readonly value='<?=$product_name;?>'>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Category</b></label>
		<div class='col-sm-4'>              
			<select name="category_mat" id="category_mat" class='form-control chosen-select'>
				<option value="0">All Category</option>
				<?php
				foreach ($get_category as $key => $value) {
					echo "<option value='".$value['id_category']."'>".$value['category']."</option>";
				}
				?>
			</select>
		</div>
		<label class='label-control col-sm-2'><b>Tgl Planning *</b></label>
		<div class='col-sm-4'>              
			<input type='text' name="tanggal" id="tanggal" class='form-control tanggal' value='<?=date('Y-m-d');?>' readonly>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Keterangan</b></label>
		<div class='col-sm-4'>              
			<textarea name="keterangan" id="keterangan" rows="3" class='form-control'></textarea>
		</div>
		<label class='label-control col-sm-2'><b>Qty</b></label>
		<div class='col-sm-4'>              
			<input type='text' name="qty_spk" id="qty_spk" class='form-control' readonly value='<?=$product_qty;?>'>
		</div>
	</div>
	<?php
	if($no_ipp != 'ancuran' AND $no_ipp != 'internal' AND $no_ipp != 'reqnonso'){
		if(empty($get_planning)){
		?>
		<button type='button' id='buat_request' class='btn btn-md btn-danger' data-no_ipp='<?=$no_ipp;?>'>Buat Request !!!</button>
		<?php 
		} 
	?>
	<h4 class='text-bold'>Daftar Budget Material <?=$no_ipp;?></h4>
	<table id="my-grid3" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Est (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Sisa Request (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Total Request (Kg)</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Request (Kg)</th>
				<th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th> 
			</tr>
		</thead>
		<tbody id='budget_material_replace'>
			<?php
			if(!empty($get_planning)){
				foreach ($get_planning as $key => $value) { $key++;
					$sisa_req 		= $value['berat']-$value['total_request'];
					$color = 'text-green text-bold';;
					if($sisa_req < 0){
						$color = 'text-red text-bold';
					}
					echo "<tr>";
						echo "<td class='text-center'>".$key."
								<input type='hidden' name='detail2[999".$key."][id]' value='".$value['id']."'>
								<input type='hidden' name='detail2[999".$key."][berat_est]' value='".$value['berat']."'>
								<input type='hidden' name='detail2[999".$key."][qty_sisa]' value='".$sisa_req."'>
								<input type='hidden' name='detail2[999".$key."][qty_total_req]' value='".$value['total_request']."'>
						</td>";
						echo "<td>".strtoupper($value['nm_material'])."</td>";
						echo "<td class='text-right text-bold'>".number_format($value['berat'],3)."</td>";
						echo "<td class='text-right ".$color." sisaRequest'>".number_format($sisa_req,3)."</td>";
						echo "<td class='text-right text-bold'>".number_format($value['total_request'],3)."</td>";
						echo "<td><input type='text' style='width:100%' name='detail2[999".$key."][sudah_request]' data-no='".$key."' class='form-control text-bold input-sm text-right autoNumeric requestBlock' placeholder='Request (kg)'></td>";
						echo "<td><input type='text' style='width:100%' name='detail2[999".$key."][ket_request]' data-no='".$key."' class='form-control input-sm text-left' placeholder='Keterangan'></td>";
					echo "</tr>";
				}
			}
			else{
				echo "<tr>";
					echo "<td colspan='7' class='text-red'><b>Buat request terlebih dahulu !!!</b></td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<br>
	<?php } ?>
	<h4 class='text-bold'>Daftar permintaan diluar material budget</h4>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='15%'>Stock Sub Gudang</th>
                <th class="text-center no-sort" style='vertical-align:middle;' width='10%'>Request</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='20%'>Keterangan</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='10%'>Hapus Material</th>
			</tr>
		</thead>
		<tbody id='body_req'>
			<tr>
				<td colspan='6'>Empty request material.</td>
			</tr>
		</tbody>
	</table>
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'float:right; margin: 10px 0px 5px 0px;','value'=>'Save','content'=>'Process','id'=>'request_material'));
	?>
	
	<br><br>
	<h4 class='text-bold'>Daftar Material</h4>
	<table id="my-grid2" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='15%'>Stock Sub Gudang</th>
				<!-- <th class="text-center no-sort" style='vertical-align:middle;' width='15%'>Stock Produksi</th> -->
                <th class="text-center no-sort" style='vertical-align:middle;' width='10%'>Request</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='20%'>Keterangan</th>
				<th class="text-center no-sort" style='vertical-align:middle;' width='10%'>Pilih Material</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>
</form>
<style>
	.tanggal{
		cursor: pointer;
	}
</style> 
<script>
	$(document).ready(function(){
        swal.close();
		$('.autoNumeric').autoNumeric('init', {mDec: '3', aPad: false});
		$('.chosen-select').chosen({
			width : '100%'
		});
		$('.tanggal').datepicker({
			dateFormat: 'yy-mm-dd',
			changeMonth:true,
			changeYear:true
		});
		var no_ipp 		= $('#no_ipp').val();
		var pusat 		= $('#gudang_before').val();
		DataTables2(no_ipp, pusat);
    });

	let arrayRequest = [];
	let arrayDataCheck = [];

	arrayDataCheck.splice(0,arrayDataCheck.length)

	// console.log(arrayRequest);
	// console.log(arrayDataCheck);

	$(document).on('click','.pindahkan', function(){
		let id = $(this).parent().parent().parent().find('.id').val();
		let nm_material = $(this).parent().parent().parent().find('.nm_material').val();
		let qty_stock = $(this).parent().parent().parent().find('.qty_stock').val();
		let sudah_request = $(this).parent().parent().parent().find('.sudah_request').val().split(',').join('');
		let ket_request = $(this).parent().parent().parent().find('.ket_request').val();

		let check = arrayDataCheck.includes(id);
		// console.log(arrayDataCheck);
		// console.log(check);
		if(check === false){
			let dataArr = {
				'id' : id,
				'nm_material' : nm_material,
				'qty_stock' : qty_stock,
				'sudah_request' : sudah_request,
				'ket_request' : ket_request
			}
			// console.log(dataArr);
			arrayRequest.push(dataArr);
			arrayDataCheck.push(id);
			// console.log(arrayDataCheck);
			viewRequest();
		}
		// else{
		// 	alert('Material sudah ada dalam daftar !!!')
		// }
	});

	$(document).on('click', '.hapus_req', function(){
		let id = $(this).data('id');
		delete arrayRequest[id]
		delete arrayDataCheck[id]
		viewRequest();
	});

	const viewRequest = () => {
		let DataAppend = "";
		let nomor = 0;
		// console.log(arrayRequest)
		arrayRequest.map((row,idx)=>{
				nomor++

				DataAppend += "<tr>"
					DataAppend += "<td class='text-center'>"+nomor+"</td>"
					DataAppend += "<td>"+row.nm_material+"</td>"
					DataAppend += "<td class='text-right'>"+number_format(row.qty_stock,2)+" kg</td>"
					DataAppend += "<td>"
						DataAppend += "<input type='hidden' name='detail["+idx+"][id]' value='"+row.id+"'>"
						DataAppend += "<input type='text' name='detail["+idx+"][sudah_request]' class='form-control input-sm text-right autoNumeric' value='"+row.sudah_request+"'>"
					DataAppend += "</td>"
					DataAppend += "<td>"
						DataAppend += "<input type='text' name='detail["+idx+"][ket_request]' class='form-control input-sm' value='"+row.ket_request+"'>"
					DataAppend += "</td>"
					DataAppend += "<td class='text-center'><button type='button' class='btn btn-danger btn-sm hapus_req' data-id='"+idx+"' title='Delete'><i class='fa fa-trash'></i></button></td>"
				DataAppend += "</tr>"
		})

		$('#body_req').html(DataAppend)
		$('.autoNumeric').autoNumeric('init', {mDec: '3', aPad: false});
	}
</script>