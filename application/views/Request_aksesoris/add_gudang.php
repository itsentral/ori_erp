

<div class="box-body">
	<table width='100%'>
        <tr>
            <td width='15%'>Kode Transaksi</td>
            <td width='3%'>:</td>
            <td><?=$kode;?></td>
        </tr>
       
        <?php if($tanda == 'P'){ ?>
        <tr>
            <td>No IPP</td>
            <td>:</td>
            <td><?=$result_aksesoris[0]['no_ipp'];?></td>
        </tr>
        <?php } ?>
        <tr>
            <td>No Surat Jalan</td>
            <td>:</td>
            <td><input type="text" id='no_surat_jalan_req' name='no_surat_jalan' class='form-control input-sm' style='width:200px;'></td>
        </tr>
    </table>
    <input type="hidden" name='kode' value='<?=$kode;?>'>
    <input type="hidden" name='no_ipp' value='<?=$result_aksesoris[0]['no_ipp'];?>'>
    <br>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Kode</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Qty Req</th>
				<th class="text-center" style='vertical-align:middle;' width='7%'>Sudah Out</th>
				<th class="text-center" style='vertical-align:middle;' width='7%' hidden>Max Out</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Stok</th>
				<th class="text-center" style='vertical-align:middle;' width='5%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Qty Out</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total1 = 0;
            $No=0;

            $TOTAL_REQ = 0;
            $TOTAL_OUT = 0;
            foreach($result_aksesoris AS $val => $valx){
                $No++;
                
                $qty    = $valx['qty'];
                $satuan = $valx['satuan'];

                $qty_req = $valx['qty_request'];
                $qty_out = $valx['qty_out'];

                $TOTAL_REQ += $qty_req;
                $TOTAL_OUT += $qty_out;

                $code_group = (!empty($GET_ACCESSORIES[$valx['id_material']]['code_group']))?$GET_ACCESSORIES[$valx['id_material']]['code_group']:0;
                $nm_material = get_name_acc($valx['id_material']);
                $material = get_name('accessories','material','id',$valx['id_material']);
                if($tanda == 'X'){
                    $code_group = $valx['code_group'];
                    $nm_material = get_name_by_code_group($valx['code_group']);
                    $material = get_name('con_nonmat_new','material_name','code_group',$code_group);
                }
                $STOK = (!empty($GET_STOK[$code_group]))?$GET_STOK[$code_group]:0;
                
                echo "<tr>";
                    echo "<td align='center'>".$No."
                            <input type='hidden' name='add[".$No."][id]' value='".$valx['id']."'>
                            <input type='hidden' name='add[".$No."][id_material]' value='".$valx['id_material']."'>
                            <input type='hidden' name='add[".$No."][no_ipp]' value='".$valx['no_ipp']."'>
                            <input type='hidden' name='add[".$No."][id_customer]' value='".$valx['id_customer']."'>
                            <input type='hidden' name='add[".$No."][code_group]' value='".$code_group."'>
                            </td>";
                    echo "<td align='center'>".$code_group."</td>";
                    echo "<td title='".$code_group."'>".$nm_material."</td>";
                    echo "<td>".$material."</td>";
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
        if($TOTAL_REQ != $TOTAL_OUT){
            if($tanda == 'X'){
                echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Outgoing To Subgudang','id'=>'btnRequestSub'));
            }
            else{
                echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Release To Finish Good','id'=>'btnRequest'));
            }
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
