

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Nama Customer</b></label>
		<div class='col-sm-4'>
			<input type="hidden" name='id_bq' value='<?=$id_bq;?>'>
			<input type="hidden" name='id_customer' value='<?=$id_customer;?>'>
			<?php
				echo form_input(array('id'=>'nm_customer','name'=>'nm_customer','class'=>'form-control input-md','readonly'=>'readonly'),$nm_customer);
			?>
		</div>
	</div>
	<!-- <div class='form-group row'>
		<label class='label-control col-sm-2'><b>Project</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_textarea(array('id'=>'project','name'=>'project','class'=>'form-control input-md','rows'=>'2','cols'=>'75','readonly'=>'readonly'),strtoupper(get_name('production','project','no_ipp',str_replace('BQ-','',$id_bq))));
			?>
		</div>
	</div> -->
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Code</th>
				<th class="text-center" style='vertical-align:middle;'>Accessories Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Tot Request</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Max Request</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Request</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Keterangan</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total1 = 0;
            $No=0;
            foreach($result_aksesoris AS $val => $valx){
                $No++;
                
                $qty        = $valx['jumlah_mat'];
                $satuan     = $valx['satuan'];
                $qty_req    = $valx['request'];
                
                echo "<tr>";
                    echo "<td align='center'>".$No."
                            <input type='hidden' name='add[".$No."][no_ipp]' value='".$valx['no_ipp']."'>
                            <input type='hidden' name='add[".$No."][id]' value='".$valx['id']."'>
                            </td>";
                    echo "<td align='center'>".$valx['code_group']."</td>";
                    echo "<td>".get_name_by_code_group($valx['code_group'])."</td>";
                    echo "<td align='right'>".number_format($qty,2)."</td>";
                    echo "<td align='right'>".number_format($qty_req,2)."</td>";
                    echo "<td align='right' id='maxRequest".$No."'>".number_format($qty-$qty_req,2)."</td>";
                    echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
                    echo "<td align='right'><input type='text' name='add[".$No."][request]' data-no='".$No."' class='form-control input-sm text-center autoNumeric2 requestQtyPusat'></td>";
					echo "<td>".$valx['ket_request']."</td>";
				echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<div class="box-footer">
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Request','id'=>'btnRequest'));
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
		$('.autoNumeric2').autoNumeric();

        $(document).on('keyup','.requestQtyPusat', function(){
            var nomor   = $(this).data('no');
            var max     = getNum($('#maxRequest'+nomor).text().split(",").join(""));
            var request = getNum($(this).val().split(",").join(""));

            // if(request > max){
            //     $(this).val(max)
            // }
        });



    });

    function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

</script>
