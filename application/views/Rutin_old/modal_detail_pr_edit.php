<div class="box box-primary">
    <input type="hidden" name='pengajuangroup' value='<?=$pengajuangroup;?>'>
    <div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-left">Nama Barang</th>
					<th class="text-left" width='15%'>Category</th>
					<th class="text-center" width='8%'>Qty</th>
					<th class="text-center" width='5%'>Unit</th>
					<th class="text-center" width='8%'>Dibutuhkan</th>
					<th class="text-center" width='10%'>Spec PR</th>
					<th class="text-center" width='10%'>Info PR</th>
					<th class="text-center" width='10%'>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($result AS $val => $valx){ $no++;
					$SPEC 		= (!empty($GET_COMSUMABLE[$valx['id_material']]['spec']))?' - '.$GET_COMSUMABLE[$valx['id_material']]['spec']:'';
					$CATEGORY 	= get_name('con_nonmat_category_awal', 'category', 'id', $valx['category_awal']);
					$SATUAN 	= get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['satuan']);
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material'].$SPEC."</td>";
						echo "<td align='left'>".$CATEGORY."</td>";
                        if($valx['sts_app'] == 'N'){
						    echo "<td align='center'>";
                                echo "<input type='text' name='update_data[".$valx['id']."][qty]' class='form-control input-sm numberOnly2 text-center' value='".$valx['purchase']."'>";
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
					echo "</tr>";
				}
				?>
				<tr id='add_<?=$no;?>'>
					<td align='center'></td>
					<td align='left'><button type='button' data-category='<?=$valx['category_awal'];?>' class='btn btn-sm btn-success addPart' title='Add Item'><i class='fa fa-plus'></i>&nbsp;&nbsp;Add Item</button></td>
					<td align='center' colspan='7'></td>
				</tr>
			</tbody>
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
</script>