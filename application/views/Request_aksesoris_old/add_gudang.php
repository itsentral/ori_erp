

<div class="box-body">
	<table width='100%'>
        <tr>
            <td width='15%'>Kode Transaksi</td>
            <td width='3%'>:</td>
            <td><?=$kode;?></td>
        </tr>
        <tr>
            <td>No IPP</td>
            <td>:</td>
            <td><?=$result_aksesoris[0]['no_ipp'];?></td>
        </tr>
    </table>
    <input type="hidden" name='kode' value='<?=$kode;?>'>
    <br>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Qty Req</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Sudah Out</th>
				<th class="text-center" style='vertical-align:middle;' width='9%' hidden>Max Out</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Stok</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Qty Out</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total1 = 0;
            $No=0;
            foreach($result_aksesoris AS $val => $valx){
                $No++;
                
                $qty    = $valx['qty'];
                $satuan = $valx['satuan'];
                if($valx['category'] == 'plate'){
                    $qty    = $valx['berat'];
                    $satuan = '1';
                }

                $qty_req = $valx['qty_request'];
                $qty_out = $valx['qty_out'];

                $code_group = (!empty($GET_ACCESSORIES[$valx['id_material']]['code_group']))?$GET_ACCESSORIES[$valx['id_material']]['code_group']:0;
                $STOK = (!empty($GET_STOK[$code_group]))?$GET_STOK[$code_group]:0;
                
                echo "<tr>";
                    echo "<td align='center'>".$No."
                            <input type='hidden' name='add[".$No."][id]' value='".$valx['id']."'>
                            <input type='hidden' name='add[".$No."][id_material]' value='".$valx['id_material']."'>
                            <input type='hidden' name='add[".$No."][code_group]' value='".$code_group."'>
                            </td>";
                    echo "<td title='".$code_group."'>".get_name_acc($valx['id_material'])."</td>";
                    echo "<td>".strtoupper(get_name('accessories','material','id',$valx['id_material']))."</td>";
                    echo "<td align='right'>".number_format($qty,2)."</td>";
                    echo "<td align='right'>".number_format($qty_req,2)."</td>";
                    echo "<td align='right'>".number_format($qty_out,2)."</td>";
                    echo "<td align='right' id='maxRequest".$No."' hidden>".number_format($qty_req-$qty_out,2)."</td>";
                    echo "<td align='right' id='stockQty".$No."'>".number_format($STOK,2)."</td>";
                    echo "<td align='center'>".strtoupper(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan))."</td>";
                    echo "<td align='right'><input type='text' name='add[".$No."][request]' data-no='".$No."' class='form-control input-sm text-center autoNumeric2 requestQty'></td>";
                echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<div class="box-footer">
    <?php
		echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Release To Finish Good','id'=>'btnRequest'));
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

        $(document).on('keyup','.requestQty', function(){
            var nomor   = $(this).data('no');
            var max     = getNum($('#maxRequest'+nomor).text().split(",").join(""));
            var stok    = getNum($('#stockQty'+nomor).text().split(",").join(""));
            var request = getNum($(this).val().split(",").join(""));

            if(request > max){
                if(request > stok){
                    $(this).val(stok)
                }
                else{
                    $(this).val(max)
                }
            }
            else{
                if(request > stok){
                    $(this).val(stok)
                }
                else{
                    $(this).val(request)
                }
            }
        });



    });

    function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

</script>
