

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Nomor IPP</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','readonly'=>'readonly'),str_replace('BQ-','',$id_bq));
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
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;'>Material Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Estimasi</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Tot Request</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Max Request</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Unit</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Request</th>
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

                $qty_req = $valx['qty_req'];
                
                echo "<tr>";
                    echo "<td align='center'>".$No."
                            <input type='hidden' name='add[".$No."][id]' value='".$valx['id']."'>
                            </td>";
                    echo "<td>".get_name_acc($valx['id_material'])."</td>";
                    echo "<td>".strtoupper(get_name('accessories','material','id',$valx['id_material']))."</td>";
                    echo "<td align='right'>".number_format($qty,2)."</td>";
                    echo "<td align='right'>".number_format($qty_req,2)."</td>";
                    echo "<td align='right' id='maxRequest".$No."'>".number_format($qty-$qty_req,2)."</td>";
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

        $(document).on('keyup','.requestQty', function(){
            var nomor   = $(this).data('no');
            var max     = getNum($('#maxRequest'+nomor).text().split(",").join(""));
            var request = getNum($(this).val().split(",").join(""));

            if(request > max){
                $(this).val(max)
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
