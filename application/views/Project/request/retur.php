

<div class="box-body">
    <input type="hidden" name='id_customer' value='<?=$id_customer;?>'>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Nama Customer</b></label>
		<div class='col-sm-4'><?=$nm_customer;?></div>
	</div>
    <div class='form-group row'>
		<label class='label-control col-sm-2'><b>No Surat Jalan</b></label>
		<div class='col-sm-4'><input type="text" name='no_surat_jalan' id='no_surat_jalan' class='form-control'></div>
	</div>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Kode</th>
				<th class="text-center" style='vertical-align:middle;'>Accessories Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Stock</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Proses Retur</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Max Retur</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Retur</th>
				<th class="text-center" style='vertical-align:middle;' width='18%'>Note</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total1 = 0;
            $No=0;
            foreach($result_aksesoris AS $val => $valx){
                $No++;
                $nm_material = get_name_acc($valx['id']);
                echo "<tr>";
                    echo "<td align='center'>".$No."
                            <input type='hidden' name='add[".$No."][code_group]' value='".$valx['code_group']."'>
                            <input type='hidden' name='add[".$No."][id_stock]' value='".$valx['id_stock']."'>
                            <input type='hidden' name='add[".$No."][nm_material]' value='".$nm_material."'>
                            </td>";
                    echo "<td align='center'>".$valx['code_group']."</td>";
                    echo "<td>".$nm_material."</td>";
                    echo "<td align='right'>".number_format($valx['stock'],2)."</td>";
                    echo "<td align='right'>".number_format($valx['retur'],2)."</td>";
                    echo "<td align='right' id='maxStock".$No."'>".number_format($valx['stock']-$valx['retur'],2)."</td>";
                    echo "<td align='right'><input type='text' name='add[".$No."][request]' data-no='".$No."' class='form-control input-sm text-center autoNumeric2 requestQtyRetur'></td>";
                    echo "<td align='right'><input type='text' name='add[".$No."][ket]' data-no='".$No."' class='form-control input-sm'></td>";
                echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<div class="box-footer">
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Retur','id'=>'btnRetur'));
	?>
</div>
<script>
	$(document).ready(function(){
        swal.close();

        $(document).on('keyup','.requestQtyRetur', function(){
            var nomor   = $(this).data('no');
            var stock   = getNum($('#maxStock'+nomor).text().split(",").join(""));
            var request = getNum($(this).val().split(",").join(""));

            if(request > stock){
                $(this).val(stock)
            }
        });
    });
</script>
