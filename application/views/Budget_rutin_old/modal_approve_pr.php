

<div class="box box-primary">
    <div class="box-body">
        <br>
		<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Asal Permintaan</b></label>
            <div class='col-sm-4'>              
                <?=$no_ipp;?>
            </div>
        </div>
		<div class='form-group row'>		 	 
            <label class='label-control col-sm-2'><b>Kebutuhan</b></label>
            <div class='col-sm-4'>              
                <?=$kebutuhan;?>
            </div>
        </div>
		<br>
		<input type='hidden' name='no_ipp' value='<?=$no_ipp2;?>'>
		<input type='hidden' name='tanda' value='<?=$tanda;?>'>
		<table class="table table-bordered table-striped" id="my-grid3" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center no-sort" width='5%'>#</th>
					<th class="text-center">Material Name</th>
					<th class="text-center" width='15%'>Category</th>
					<th class="text-center" width='10%'>MOQ</th>
					<th class="text-center" width='10%'>Qty (Kg)</th>
					<th class="text-center" width='10%'>Tanggal Dibutuhkan</th>
					<th class="text-center" width='10%'>Rev Qty (Kg)</th>
					<th class="text-center" width='10%'>Keterangan</th>
					<th class="text-center" width='5%'>Reject</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$no  = 0;
				foreach($result AS $val => $valx){ $no++;
					echo "<tr>";
						echo "<td align='center'>".$no."</td>";
						echo "<td align='left'>".$valx['nm_material']."</td>";
						echo "<td align='left'>".get_name('raw_materials', 'nm_category', 'id_material', $valx['id_material'])."</td>";
						echo "<td align='right'>".number_format($valx['moq_m'])."</td>";
						echo "<td align='right'>".number_format($valx['qty_request'])."</td>";
						echo "<td align='center'>".date('d-m-Y', strtotime($valx['tanggal']))."</td>";
						echo "<td align='center'><input type='text' name='detail[".$no."][qty_revisi]' id='tot_rev_".$no."' class='form-control input-sm text-right maskM' style='width:100%;' data-decimal='.' data-thousand='' data-precision='0' data-allow-zero=''></td>";
						echo "<td align='center'>
								<input type='text' name='detail[".$no."][keterangan]' id='keterangan_".$no."' class='form-control input-sm text-left' style='width:100%;'>
								<input type='hidden' name='detail[".$no."][id_material]' value='".$valx['id_material']."'>
								<input type='hidden' name='detail[".$no."][idmaterial]' value='".$valx['idmaterial']."'>
								<input type='hidden' name='detail[".$no."][nm_material]' value='".$valx['nm_material']."'>
								<input type='hidden' name='detail[".$no."][moq]' value='".$valx['moq_m']."'>
								<input type='hidden' name='detail[".$no."][qty_request]' value='".$valx['qty_request']."'>
								<input type='hidden' name='detail[".$no."][reorder_point]' value='".$valx['reorder_point']."'>
								<input type='hidden' name='detail[".$no."][book_per_month]' value='".$valx['book_per_month']."'>
								<input type='hidden' name='detail[".$no."][sisa_avl]' value='".$valx['sisa_avl']."'>
								<input type='hidden' name='detail[".$no."][tanggal]' value='".$valx['tanggal']."'>
								</td>";
						echo "<td align='center'><button type='button'class='btn btn-sm btn-danger rejectPR' title='Reject PR' data-id_material='".$valx['id_material']."' data-id='".$valx['id']."''><i class='fa fa-close'></i></button></td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-sm btn-success','style'=>'min-width:100px; float:right; margin: 5px 0px 5px 0px;','content'=>'Approve','id'=>'app_pr')).' ';
		?>
	</div>
</div>

<script>
    swal.close();
	$(document).ready(function(){
		$('.maskM').maskMoney();
	});
</script>