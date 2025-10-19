

<div class="box-body">
	<br>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Nomor IPP</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','readonly'=>'readonly'),str_replace('BQ-','',$id_bq));
				echo form_input(array('type'=>'hidden','name'=>'type','class'=>'form-control input-md','readonly'=>'readonly'),$type);
			?>
		</div>
		<label class='label-control col-sm-2'><b>Tanggal Dibutuhkan <span class='text-red'>*</span></b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'tgl_butuh','name'=>'tgl_butuh','class'=>'form-control input-md tgl','readonly'=>'readonly','placeholder'=>'Tanggal Dibutuhkan'));
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Project</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'2','cols'=>'75','readonly'=>'readonly'),strtoupper(get_name('production','project','no_ipp',str_replace('BQ-','',$id_bq))));
			?>
		</div>
	</div>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr>
				<td class='bg-blue' colspan='13'><b>MATERIAL</b></td>
			</tr>
		</thead>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' rowspan='2'>No</th>
				<th class="text-center" style='vertical-align:middle;' rowspan='2'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' colspan='4'>Ambil Dari Stock Free</th>
				<th class="text-center" style='vertical-align:middle;' colspan='4'>PR Material</th>
			</tr>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Stock Free</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Use Stock</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Sisa Stock Free</th>

				<th class="text-center" style='vertical-align:middle;' width='9%'>Min Stock</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Max Stcok</th>
				<!-- <th class="text-center" style='vertical-align:middle;' width='9%'>Min Order</th> -->
				<th class="text-center" style='vertical-align:middle;' width='9%'>Purchase</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($data_result)){
            $Total1 = 0;
            $No=0;
			foreach($data_result AS $val => $valx){
                $No++;
				
				$book_per_month = $this->db->select('purchase')->get_where('check_book_per_month', array('id_material'=>$valx['id_material'],'tahun'=>$tahun,'bulan'=>$bulan))->result();
					$b_permont = (!empty($book_per_month))?$book_per_month[0]->purchase:0;
				$acc_mat 		= $this->db->select('qty')->get_where('so_bf_acc_and_mat', array('id_material'=>$valx['id_material'],'id_bq'=>$valx['id_bq']))->result();
					$qty_accmat = (!empty($acc_mat))?$acc_mat[0]->qty:0;
				$ware_stock 	= $this->db->select('qty_stock,qty_booking,idmaterial')->from('warehouse_stock')->where('id_material',$valx['id_material'])->where("(id_gudang = '1' OR id_gudang = '2')")->get()->result();
					$qty_stock 	= (!empty($ware_stock))?$ware_stock[0]->qty_stock:0;
					$qty_booking= (!empty($ware_stock))?$ware_stock[0]->qty_booking:0;
					$idmaterial = (!empty($ware_stock))?$ware_stock[0]->idmaterial:0;
				$moq_mat 		= $this->db->select('moq')->get_where('moq_material', array('id_material'=>$valx['id_material']))->result();
					$moq 		= (!empty($moq_mat))?$moq_mat[0]->moq:0;
				
				
				$last_cost 		= $valx['last_cost'] + $qty_accmat;
				
				$Total1 += $last_cost;
				$bookpermonth 	= $b_permont;
				$leadtime 		= get_max_field('raw_material_supplier', 'lead_time_order', 'id_material', $valx['id_material']);
				$safetystock 	= get_max_field('raw_materials', 'safety_stock', 'id_material', $valx['id_material']);
				
				$max_stock 		= get_max_field('raw_materials', 'max_stock', 'id_material', $valx['id_material']);
				$kg_per_bulan 	= get_max_field('raw_materials', 'kg_per_bulan', 'id_material', $valx['id_material']);
			
				$reorder 		= ($safetystock/30) * $kg_per_bulan;
				$max_stock2 	= ($max_stock/30) * $kg_per_bulan;
				$availabel_sisa = ($qty_stock - $qty_booking) - $last_cost;
				$sisaFREE 		= ($qty_stock - $qty_booking);
				$purchasex2 = $max_stock2 - $sisaFREE;
				$purchasex = ($purchasex2 > 0)?$purchasex2:0;
				echo "<tr>";
	        echo "<td align='center'>".$No."
		            <input type='hidden' name='addMatPlanning[$No][no_ipp]' value='".str_replace('BQ-','',$valx['id_bq'])."'>
		            <input type='hidden' name='addMatPlanning[$No][idmaterial]' value='".$idmaterial."'>
		            <input type='hidden' name='addMatPlanning[$No][id_material]' value='".$valx['id_material']."'>
		            <input type='hidden' name='addMatPlanning[$No][nm_material]' value='".$valx['nm_material']."'>
		            <input type='hidden' name='addMatPlanning[$No][jumlah_mat]' value='".$last_cost."'>
		            <input type='hidden' name='addMatPlanning[$No][qty_stock]' id='stock_$No' value='".$qty_stock."'>
								<input type='hidden' name='addMatPlanning[$No][moq]' value='".$moq."'>
	              <input type='hidden' name='addMatPlanning[$No][qty_booking]' id='book_$No' value='".$qty_booking."'>
								<input type='hidden' name='addMatPlanning[$No][book_per_month]' id='book_month_$No' value='".$bookpermonth."'>
								<input type='hidden' name='addMatPlanning[$No][max_stock]' id='max_stock_$No' value='".$max_stock2."'>
	          </td>";
				echo "<td>".strtoupper($valx['nm_material'])."</td>";
	          echo "<td align='right'>".number_format($last_cost,2)." Kg</td>";
	        //   echo "<td align='right'>".number_format($qty_stock,2)." Kg</td>";
			//   echo "<td align='right'>".number_format($qty_booking,2)." Kg</td>";
	          echo "<td align='right'><span id='avl_$No'>".number_format($qty_stock - $qty_booking,2)."</span> Kg</td>";
	          echo "<td align='right'><input type='text' name='addMatPlanning[$No][use_stock]' data-no='$No' class='form-control input-sm text-right maskM use_stock' value='".number_format($last_cost,2)."'></td>";
				echo "<td align='right'><span  id='sisa_avllabel_$No'>".number_format($availabel_sisa,2)."</span>
					<input type='hidden' name='addMatPlanning[$No][sisa_avl]' id='sisa_avl_$No' data-no='$No' class='form-control input-sm text-right maskM' readonly tabindex='-1' value='".number_format($availabel_sisa,2)."'>
					</td>";
				
				echo "<td align='right'><input type='text' name='addMatPlanning[$No][reorder_point]' class='form-control input-sm text-right maskM' readonly tabindex='-1' value='".number_format($reorder,2)."'></td>";
	          echo "<td align='right'>".number_format($max_stock2,2)." Kg</td>";
			//   echo "<td align='center'>".number_format($moq)."</td>";
			  
				echo "<td align='right'><input type='text' name='addMatPlanning[$No][purchase]' class='form-control input-sm text-right maskM' value='".number_format($purchasex,2)."'></td>";
				// echo "<td align='left'><input type='text' name='addMatPlanning[$No][tanggal]' class='form-control input-sm tgl' readonly placeholder='Tgl DIbutuhkan'></td>";
          
		  echo "</tr>";
			}
			?>
			<tr>
				<td><b></b></td>
				<td><b>SUM TOTAL</b></td>
				<td align='right'><b><?= number_format($Total1, 3);?> Kg</b></td>
				<td colspan='9'><b></b></td>
			</tr>
			<?php
			}
			else{
				echo "<tr>";
					echo "<td colspan='3'>Data aktual belum di update, silahkan update data terlebih dahulu.</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	<?php
		if(!empty($data_result) AND empty($check_mat)){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'saveAddPlan')).' ';
		}
	?>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr>
				<td class='bg-blue' colspan='6'><b>ACCESSORIES </b></td>
			</tr>
		</thead>
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>No</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Est Material</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Unit</th>
				<!-- <th class="text-center" style='vertical-align:middle;' width='9%'>Free Stock</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Kebutuhan 1 Bulan</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Max Stock</th> -->
				<th class="text-center" style='vertical-align:middle;' width='9%'>Purchase</th>
				<!-- <th class="text-center" style='vertical-align:middle;' width='9%'>Tgl Dibutuhkan</th> -->
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($non_frp) OR !empty($pack_truck)){
				$Total1 = 0;
				$No=0;
				// $id_gudang_project = getGudangProject();
				// $GET_STOCK_PROJECT = get_warehouseStockProject($id_gudang_project);

				foreach($non_frp AS $val => $valx){
					$No++;
					
					$qty = $valx['qty'];
					$satuan = $valx['satuan'];
					if($valx['category'] == 'plate'){
						// $qty = $valx['berat'];
						$satuan = '1';
					}

					// $book_per_month = $this->db->select('SUM(kebutuhan_month) AS kebutuhan')->get_where('budget_rutin_detail', array('id_barang'=>$valx['code_group']))->result();
					// $b_permont = (!empty($book_per_month))?$book_per_month[0]->kebutuhan:0;
					// $max_stock = $b_permont * 1.5;
					// $stock = (!empty($GET_STOCK_PROJECT[$valx['code_group']]))?$GET_STOCK_PROJECT[$valx['code_group']]:0;
					
					$reorder 		= 0;
					$qty_booking 	= 0;
					$qty_order 		= 0;
					$availabel_sisa = ($valx['stock'] - $qty_booking) - $qty; 
					
					echo "<tr>";
						echo "<td align='center'>".$No."
								<input type='hidden' name='add_acc_planning[".$No."][no_ipp]' value='".str_replace('BQ-','',$valx['id_bq'])."'>
								<input type='hidden' name='add_acc_planning[".$No."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='add_acc_planning[".$No."][code_group]' value='".$valx['code_group']."'>
								<input type='hidden' name='add_acc_planning[".$No."][jumlah_mat]' value='".$qty."'>
								<input type='hidden' name='add_acc_planning[".$No."][qty_stock]' id='acc_stock_".$No."' value='".$valx['stock']."'>
								<input type='hidden' name='add_acc_planning[".$No."][satuan]' value='".$valx['satuan']."'>
								<input type='hidden' name='add_acc_planning[".$No."][qty_booking]' id='acc_book_".$No."' value='".$qty_booking."'>
								</td>";
						echo "<td>".get_name_acc($valx['id_material'])."</td>";
						echo "<td>".strtoupper(get_name('accessories','material','id',$valx['id_material']))."</td>";
						echo "<td align='right'>".number_format($qty,2)."</td>";
						echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
						// echo "<td align='right'>".number_format($stock,2)."</td>";
						// echo "<td align='right'>".number_format($b_permont,2)."</td>";
						// echo "<td align='right'>".number_format($max_stock,2)."</td>";
						echo "<td align='right'><input type='text' name='add_acc_planning[".$No."][purchase]' class='form-control input-sm text-right maskM' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						// echo "<td align='left'><input type='text' name='add_acc_planning[".$No."][tanggal]' class='form-control input-sm tgl' readonly placeholder='Tgl DIbutuhkan'></td>";
					echo "</tr>";
				}
			foreach($pack_truck AS $val => $valx){
					$No++;
					$qty = ($valx['category'] == 'packing')?1:$valx['qty'];
					
					$nama_acc = "";
					if($valx['category'] == 'packing'){
						$nama_acc = strtoupper($valx['category'].' '.$valx['sub_category'].' '.$valx['jenis_packing']);
					}
					if($valx['category'] == 'export'){
						$nama_acc = strtoupper('TRUCKING '.$valx['category'].' '.$valx['sub_category']);
					}
					if($valx['category'] == 'lokal'){
						$nama_acc = strtoupper('TRUCKING '.$valx['category'].' '.$valx['sub_category'].' ('.$valx['area'].' - '.$valx['tujuan'].') / '.get_name('truck','nama_truck','id',$valx['jenis_kendaraan']).'');
					}
					
					echo "<tr>";
						echo "<td align='center'>".$No."
								<input type='hidden' name='add_acc_planning[".$No."][no_ipp]' value='".str_replace('BQ-','',$valx['id_bq'])."'>
								<input type='hidden' name='add_acc_planning[".$No."][code_group]' value='non acc'>
								<input type='hidden' name='add_acc_planning[".$No."][id_material]' value='".$valx['id']."'>
								<input type='hidden' name='add_acc_planning[".$No."][jumlah_mat]' value='".$qty."'>
								<input type='hidden' name='add_acc_planning[".$No."][qty_stock]' value='0'>
								<input type='hidden' name='add_acc_planning[".$No."][satuan]'  value='".$valx['jenis_kendaraan']."'>
								<input type='hidden' name='add_acc_planning[".$No."][nm_material]'  value='".$nama_acc."'>
								<input type='hidden' name='add_acc_planning[".$No."][qty_booking]' value='0'>
								</td>";
						echo "<td>".$nama_acc."</td>";
						echo "<td></td>";
						echo "<td align='right'>".number_format($qty,2)."</td>";
						echo "<td align='center'>".strtoupper('-')."</td>";
						// echo "<td align='center'>-</td>";
						// echo "<td align='center'>-</td>";
						// echo "<td align='center'>-</td>";
						echo "<td align='right'><input type='text' name='add_acc_planning[".$No."][purchase]' class='form-control input-sm text-right maskM' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						// echo "<td align='left'><input type='text' name='add_acc_planning[".$No."][tanggal]' class='form-control input-sm tgl' readonly placeholder='Tgl DIbutuhkan'></td>";
					echo "</tr>";
				}
			}
			else{
				echo "<tr>";
					echo "<td colspan='9'>Data tidak ada</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <?php
		if((!empty($non_frp) OR !empty($pack_truck)) AND empty($check_acc)){
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','value'=>'Save','content'=>'Save','id'=>'saveAddPlan_acc')).' ';
		}
	?>
</div>
<style>
	.tgl{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
        swal.close();
		$('.maskM').maskMoney();
		$('.tgl').datepicker({
			dateFormat : 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
		});

        $(document).on('keypress keyup blur focus','.use_stock', function(){
            var nomor   = $(this).data('no');
            var stock   = getNum($('#stock_'+nomor).val());
            var book    = getNum($('#book_'+nomor).val());
            var use     = getNum($(this).val().split(",").join(""));
            var avl     = (stock - book) - use;
            // $('#avl_'+nomor).html(number_format(avl,2));
			$('#sisa_avl_'+nomor).val(number_format(avl,2));
			$('#sisa_avllabel_'+nomor).text(number_format(avl,2));
            // console.log(avl);
        });
		
		$(document).on('keypress keyup blur focus','.acc_use_stock', function(){
            var nomor   = $(this).data('no');
            var stock   = getNum($('#acc_stock_'+nomor).val());
            var book    = getNum($('#acc_book_'+nomor).val());
            var use     = getNum($(this).val().split(",").join(""));
            var avl     = (stock - book) - use;
            // $('#acc_avl_'+nomor).html(number_format(avl,2));
			$('#acc_sisa_avl_'+nomor).val(number_format(avl,2));
            // console.log(avl);
        });

    });

    function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

</script>
