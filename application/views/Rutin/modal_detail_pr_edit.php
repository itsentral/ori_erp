<div class="box box-primary">
    <input type="hidden" name='pengajuangroup' value='<?=$pengajuangroup;?>'>
    <div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='3%'>#</th>
					<th class="text-left">Nama Barang</th>
					<th class="text-left">Category</th>
					<th class="text-left">Brand</th>
					<th class="text-center">Keb.1 Bln</th>
					<th class="text-center">Max Stock</th>
					<th class="text-center" width='7%'>Qty</th>
					<th class="text-center">Unit</th>
					<th class="text-center">Dibutuhkan</th>
					<th class="text-center">Spec PR</th>
					<th class="text-center">Info PR</th>
					<th class="text-center">Status</th>
					<th class="text-right" width='7%'>Price From Supplier</th>
					<th class="text-right" width='7%'>Total Budget</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				$SUM_QTY = 0;
				$SUM_BUDGET = 0;
				foreach($result AS $val => $valx){ $no++;
					$SPEC 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['spec']))?' - '.$GET_COMSUMABLE[$valx['id_material']]['spec']:'';
					$BRAND 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['brand']))?$GET_COMSUMABLE[$valx['id_material']]['brand']:'';
					$CATEGORY 	= get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']);
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);

					$kebutuhnMonth 	= (!empty($GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']))?$GET_KEBUTUHAN_PER_MONTH[$valx['id_material']]['kebutuhan']:0;
					$maxStock 		= $kebutuhnMonth * 1.5;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material'].$SPEC."</td>";
						echo "<td align='left'>".strtoupper($CATEGORY)."</td>";
						echo "<td align='left'>".strtoupper($BRAND)."</td>";
						echo "<td align='center'>".number_format($kebutuhnMonth)."</td>";
						echo "<td align='center'>".number_format($maxStock)."</td>";
                        if($valx['sts_app'] == 'N'){
						    echo "<td align='center'>";
                                echo "<input type='text' name='update_data[".$valx['id']."][qty]' class='form-control input-md numberOnly2 text-center kebutuhan_month' value='".$valx['purchase']."'>";
                                echo "<input type='hidden' name='update_data[".$valx['id']."][tanggal]' value='".$valx['tanggal']."'>";
                            echo "</td>";
                        }
                        else{
                            echo "<td align='center'>".number_format($valx['purchase'],2)."</td>";
                        }
						echo "<td align='center'>".$SATUAN."</td>";
						echo "<td align='center'>".date('d-M-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='left'>".$valx['spec_pr']."</td>";
						echo "<td align='left'>".$valx['info_pr']."</td>";
						
						if($valx['sts_app'] == 'N'){
							$sts_name = 'Waiting Approval';
							$warna	= 'blue';
						}
						elseif($valx['sts_app'] == 'Y'){
							$sts_name = 'Approved';
							$warna	= 'green';
						}
						elseif($valx['sts_app'] == 'D'){
							$sts_name = 'Rejected';
							$warna	= 'red';
						}
						
						echo "<td align='center'><span class='badge bg-".$warna."'>".$sts_name."</span></td>";
						$price_from_supplier = (!empty($valx['price_from_supplier']))?$valx['price_from_supplier']:0;
						$total_budget = $price_from_supplier * $valx['purchase'];
						echo "<td align='left'>";
							echo "<input name='update_data[".$valx['id']."][price_from_supplier]' class='form-control text-right input-md numberOnly2 price_from_supplier' readonly value='".$price_from_supplier."'>";
						echo "</td>";
						echo "<td align='right' class='cal_tot_budget'>".number_format($total_budget,2)."</td>";
					echo "</tr>";

					$SUM_QTY += $valx['purchase'];
					$SUM_BUDGET += $total_budget;
				}
				?>
				<tr id='add_<?=$no;?>'>
					<td align='center'></td>
					<td align='left'><button type='button' data-category='<?=$valx['category_awal'];?>' class='btn btn-sm btn-success addPart' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>
					<td align='center' colspan='12'></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th align='center'></th>
					<th align='center' colspan='11'>TOTAL BUDGET</th>
					<th class='text-right'></th>
					<th class='text-right' id='cal_tot_budget'><?=number_format($SUM_BUDGET,2);?></th>
				</tr>
			</tfoot>
		</table>
        <br>
        <div class='form-group row'>
            <div class='col-sm-12 text-right'>
            <?php
                echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px;','content'=>'Save','id'=>'save_edit_pr'));
            ?>
            </div>
        </div>
	</div>
</div>
<style>
	.datepicker{
		cursor: pointer;
	}
</style>
<script>
    swal.close();
    $(".numberOnly2").autoNumeric('init', {mDec: '2', aPad: false});

	$(document).on('keyup', '.kebutuhan_month', function(){
		var qty			= getNum($(this).val().split(",").join(""))
		var HTML 		= $(this).parent().parent()
		var price_sup 	= getNum(HTML.find('.price_from_supplier').val().split(",").join(""))
		var budget 		= HTML.find('.cal_tot_budget')
		console.log(qty)
		console.log(price_sup)
		budget.text(number_format(price_sup*qty,2))

		let SUM = 0
		$('.cal_tot_budget').each(function(){
			var budget	= getNum($(this).text().split(",").join(""))
			SUM += budget
		})
		$('#cal_tot_budget').text(number_format(SUM,2))
	});

	$(document).on('change', '.getSpec2', function(){
		var price_sup 		= $(this).find(':selected').data('price_sup');
		var brand 			= $(this).find(':selected').data('brand');
		var kebutuhnMonth 	= $(this).find(':selected').data('kebutuhnmonth');
		var maxStock 		= $(this).find(':selected').data('maxstock');
		// console.log(price_sup)
		var HTML = $(this).parent().parent()
		var getPSub = HTML.find('.price_from_supplier')
		getPSub.val(number_format(price_sup))

		HTML.find('.brand_category').text(brand)
		HTML.find('.keb1bln_category').text(number_format(kebutuhnMonth))
		HTML.find('.maxstock_category').text(number_format(maxStock))
	});
</script>