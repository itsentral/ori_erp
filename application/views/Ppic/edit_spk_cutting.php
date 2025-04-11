<div class="box box-primary">
	<div class="box-body">
        <br>
        <table class="table table-hover table-condensed" width="100%">
			<tbody>
                <tr>
                    <td width='20%'><b>Product</b></td>
                    <td colspan='2'><input type="text" class='form-control input-sm' autocomplete='off' readonly value='<?=strtoupper($product);?>'></td>
                </tr>
                <tr>
                    <td><b>Qty SO</b></td>
                    <td colspan='2'><input type="text" class='form-control input-sm' autocomplete='off' readonly value='<?=strtoupper($qty_order);?>'></td>
                </tr>
                <tr>
                    <td><b>Qty SPK</b></td>
                    <td colspan='2'><input type="text" class='form-control input-sm' autocomplete='off' readonly value='1'></td>
                </tr>
                <tr>
                    <td><b>Cutting Plan (mm)</b></td>
                    <td colspan='2'><input type="text" class='form-control input-sm' autocomplete='off' readonly value='<?=strtoupper($cutting);?>'></td>
                 </tr>
                <tr>
                    <td><b>Panjang (mm)</b></td>
                    <td colspan='2'><input type="text" class='form-control input-sm' autocomplete='off' readonly value='<?=strtoupper($sum_split);?>'></td>
                 </tr>
                <tr>
                    <td><b>Mesin <span class='text-red'>*</span></b></td>
                    <td colspan='2'>
                        <select name="mesin" id="mesin" class='chosen-select'>
                            <option value="0">Pilih Mesin</option>
                            <?php
                            foreach ($mesin as $key => $value) {
                                $sel = ($result[0]->mesin == $value['id_mesin'])?'selected':'';
                               echo "<option value='".$value['id_mesin']."' ".$sel.">".$value['no_mesin']." - ".$value['nm_mesin']."</option>";
                            }
                            ?>
                        </select>
                        <input type="hidden" name='id' value='<?=$id;?>'>
                    </td>
                </tr>
                <tr>
                    <td><b>Target Selesai <span class='text-red'>*</span></b></td>
                    <td colspan='2'><input type="text" name='tanggal' id='tanggal' class='form-control input-sm' autocomplete='off' readonly value='<?=$result[0]->tanggal;?>'></td>
                </tr>
                <tr>
                    <td><b>#</b></td>
                    <td class='text-center'><b>Cycle time (Jam)</b></td>
                    <td class='text-center'><b>MP</b></td>
                </tr>
                <tr>
                    <td><b>Unit <span class='text-red'>*</span></b></td>
                    <td><input type="text" name='unit_ct' id='unit_ct' class='form-control input-sm autoNumeric text-center' autocomplete='off' value='<?=$result[0]->unit_ct;?>'></td>
                    <td><input type="text" name='unit_mp' id='unit_mp' class='form-control input-sm autoNumeric text-center' autocomplete='off' value='<?=$result[0]->unit_mp;?>'></td>
                </tr>
                <tr>
                    <td><b>Total Time <span class='text-red'>*</span></b></td>
                    <td><input type="text" name='tt_ct' id='tt_ct' class='form-control input-sm autoNumeric text-center' autocomplete='off' value='<?=$result[0]->tt_ct;?>'></td>
                    <td><input type="text" name='tt_mp' id='tt_mp' class='form-control input-sm autoNumeric text-center' autocomplete='off' value='<?=$result[0]->tt_mp;?>'></td>
                </tr>
                <tr>
                    <td colspan='3'><b>Tahapan Process <span class='text-red'>*</span></b></td>
                </tr>
                <tr>
                    <td colspan='3'>
                        <table class='table' width='100%'>
                            <?php
                            if(!empty($result[0]->tahapan)){
                                foreach (json_decode($result[0]->tahapan) as $value) {
                                    if(!empty($value)){
                                        echo "<tr>";
                                            echo "<td>";
                                            echo "<input type='text' class='form-control input-sm text-left' name='detail[]' placeholder='Tahapan' value='".$value."'></td>";
                                            echo "<td align='left'>";
                                            echo "<button type='button' class='btn btn-sm btn-success copy_eksclude' title='Add'><i class='fa fa-plus'></i></button>";
                                            echo "&nbsp;<button type='button' class='btn btn-sm btn-danger delete_test' style='margin-left:5px;' title='Delete'><i class='fa fa-trash'></i></button>";
                                            echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                            ?>
                            <tr>
                                <td>
                                    <input type='text' class='form-control input-sm text-left' name='detail[]' placeholder='Tahapan'>
                                </td>
                                <td width='25%' align='left'><button type='button' class='btn btn-sm btn-success copy_eksclude' title='Add'><i class='fa fa-plus'></i></button></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type='button' id='save_spk' class='btn btn-success' style='margin-top:5px; margin-right:5px; float:right;'>Save</button>	
	</div>
</div>
<style>
    #tanggal{
        cursor: pointer;
    }
</style>
<script>
    swal.close();
    $(document).ready(function(){
        $('.autoNumeric').autoNumeric();
        $('#tanggal').datepicker({
            dateFormat: 'dd-MM-yy',
            changeMonth:true,
            changeYear:true,
            minDate:0
        });
        $('.chosen-select').chosen({
            width : '100%'
        });
    });
</script>
