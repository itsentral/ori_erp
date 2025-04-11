<form action="#" method="POST" id="form_update_this_ipp" enctype="multipart/form-data"> 
    <div class="box-body">
        <input type="hidden" name='id_bq' value='<?=$id_bq;?>'>
        <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead id='head_table'>
                <tr class='bg-blue'>
                    <th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
                    <th class="text-center" style='vertical-align:middle;'>Material Name</th>
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Est Material<br>KG</th>
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Price /kg<br>BEFORE</th> 
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Total Price<br>BEFORE</th> 
                    <th class="text-center" style='vertical-align:middle;' width='10%'>NEW PRICE</th> 
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Total Price<br>AFTER</th> 
                    <th class="text-center" style='vertical-align:middle;' width='15%'>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Total1 = 0;
                $Total2 = 0;
                $No=0;
                if(!empty($detail) OR !empty($detail2)){
                    if(!empty($detail)){
                        foreach($detail AS $val => $valx){
                            $No++;
                            $Total1 += $valx['last_cost_qty'];
                            $Total2 += $valx['last_cost_qty'] * $valx['cost_est'];

                            $bq_detail      = $this->db->select('id_detail')->get_where('bq_component_detail', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material'],'price_mat <= '=>0))->result_array();
                            $bq_detail_plus = $this->db->select('id_detail')->get_where('bq_component_detail_plus', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material'],'price_mat <= '=>0))->result_array();
                            $bq_detail_add  = $this->db->select('id_detail')->get_where('bq_component_detail_add', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material'],'price_mat <= '=>0))->result_array();
                            
                            $color = 'red';
                            $alert = "<br><span class='text-red'>Sebagian harga bernilai nol, please update again !!!</span>";
                            if(empty($bq_detail) AND empty($bq_detail_plus) AND empty($bq_detail_add)){
                                $color = 'aquamarine';
                                $alert = '';
                            }

                            echo "<tr>";
                                echo "<td align='center'>".$No."</td>";
                                echo "<td>".$valx['nm_material'].$alert."</td>";
                                echo "<td align='right'>".number_format($valx['last_cost_qty'],3)."</td>";
                                echo "<td align='right' style='background-color: ".$color."'>".number_format($valx['cost_est'],2)."</td>";
                                echo "<td align='right'>".number_format($valx['cost_est'] * $valx['last_cost_qty'],2)."</td>";
                                echo "<td align='right'>
                                    <input type='hidden' name='detail[".$No."][id_material]' class='form-control input-sm' value='".$valx['id_material']."'>
                                    <input type='hidden' name='detail[".$No."][nm_material]' class='form-control input-sm' value='".$valx['nm_material']."'>
                                    <input type='hidden' name='detail[".$No."][price_before]' class='form-control input-sm' value='".$valx['cost_est']."'>
                                    <input type='text' name='detail[".$No."][price_after]' class='form-control input-sm autoNumeric text-right change_price' placeholder='0'>
                                </td>";
                                echo "<td align='right'></td>";
                                echo "<td align='right'><input type='text' name='detail[".$No."][keterangan]' class='form-control input-sm'></td>";
                            echo "</tr>";
                        }
                    }
                    if(!empty($detail2)){
                        foreach($detail2 AS $val => $valx){
                            $No++;
                            $Total1 += $valx['qty'];
                            $Total2 += $valx['qty'] * $valx['unit_price'];
                            
                            $bq_detail_mat  = $this->db->select('id')->get_where('bq_acc_and_mat', array('id_bq'=>$id_bq,'id_material'=>$valx['id_material'],'unit_price <= '=>0))->result_array();
                            
                            $color = 'red';
                            $alert = "<br><span class='text-red'>Sebagian harga bernilai nol, please update again !!!</span>";
                            if(empty($bq_detail_mat)){
                                $color = 'aquamarine';
                                $alert = '';
                            }

                            echo "<tr>";
                                echo "<td align='center'>".$No."</td>";
                                echo "<td>".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."</td>";
                                echo "<td align='right'>".number_format($valx['qty'],3)."</td>";
                                echo "<td align='right' style='background-color: ".$color."'>".number_format($valx['unit_price'],2)."</td>";
                                echo "<td align='right'>".number_format($valx['unit_price'] * $valx['qty'],2)."</td>";
                                echo "<td align='right'>
                                    <input type='hidden' name='detail[".$No."][id_material]' class='form-control input-sm' value='".$valx['id_material']."'>
                                    <input type='hidden' name='detail[".$No."][nm_material]' class='form-control input-sm' value='".get_name('raw_materials','nm_material','id_material',$valx['id_material'])."'>
                                    <input type='hidden' name='detail[".$No."][price_before]' class='form-control input-sm' value='".$valx['unit_price']."'>
                                    <input type='text' name='detail[".$No."][price_after]' class='form-control input-sm autoNumeric text-right change_price' placeholder='0'>
                                </td>";
                                echo "<td align='right'></td>";
                                echo "<td align='right'><input type='text' name='detail[".$No."][keterangan]' class='form-control input-sm'></td>";
                            echo "</tr>";
                        }
                    }
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td align='right'><b><?= number_format($Total1, 3);?></b></td>
                    <td align='right'></td>
                    <td align='right'><b><?= number_format($Total2, 2);?></b></td>
                    <td align='right'></td>
                    <td align='right' class='sum_after' style='font-weight:bold;'></td>
                    <td align='right'></td>
                </tr>
                <?php 
                }
                else{
                    echo "<tr>";
                        echo "<td colspan='7'>Data tidak ada</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        
        <button type='button' class='btn btn-md btn-primary' style='float:right; margin-top: 10px;' id='update_this_ipp'>Update</button>
    </div>
</form>

<script>
	$(document).ready(function(){
		swal.close();

        $('.autoNumeric').autoNumeric();

        $(document).on('keyup','.change_price', function(){
            changePrice();
        });
	});
    //$(this).parent().parent().find("td:nth-child(1) input").val(HasilAkhir);
    let changePrice = () => {
        let after_price;
        let material_kg;
        let before_price;
        let price_perkalian;
        let total_price;
        let SUM = 0
        $('.change_price').each(function(){
            after_price     = getNum($(this).val().split(",").join(""));
            material_kg     = getNum($(this).parent().parent().find("td:nth-child(3)").html().split(",").join(""));
            before_price    = getNum($(this).parent().parent().find("td:nth-child(4)").html().split(",").join(""));

            price_perkalian = (after_price == 0) ? before_price : after_price;

            total_price     = material_kg * price_perkalian;
            SUM += total_price;
            $(this).parent().parent().find("td:nth-child(7)").html(number_format(total_price,2));
        });
        $('.sum_after').html(number_format(SUM,2));

    }

    function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

    function number_format (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }


</script>