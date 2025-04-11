
<div class="box box-primary">
<!-- /.box-header -->
    <div class="box-body"><br>
        <div class="form-group row">
            <div class="col-md-2">
                <label>Product Name</label>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control input-sm" id="id_product"  name="id_product" readonly="readonly" value="<?= strtoupper($data[0]->nm_product); ?>">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-2">
                <label>Pressure | Liner</label>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control input-sm" id="pressure"  name="pressure" readonly="readonly" value="PN <?= strtoupper($data[0]->pressure); ?>">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control input-sm" id="liner"  name="liner" readonly="readonly" value="<?= strtoupper($data[0]->liner); ?> mm">
            </div>
        </div>
        <br>
        <div class="form-group row">
            <div class="col-md-12">
                <table border='0' width='100%' class="table table-striped table-bordered table-hover table-condensed">
                    <thead id='head_table'>
                        <tr class='bg-blue'>
                            <th class='text-center' style='width: 4%;'>#</th>
                            <th class='text-center' style='width: 35%;'>Cost Center</th>
                            <th class='text-center'>Machine</th>
                            <th class='text-center'>Mould/Tools</th>
                            <th class='text-center'>Information</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($detail AS $val2 => $val2x){ $val2++;
                            echo "<tr>";
                                echo "<td align='center'>".$val2."</td>";
                                echo "<td align='left'><b>".strtoupper(get_name('costcenter', 'nm_costcenter', 'id_costcenter', $val2x['costcenter']))."</b></td>";
                                echo "<td align='left'><b>".strtoupper(get_name('machine', 'nm_mesin', 'id_mesin', $val2x['machine']))."</b></td>";
                                echo "<td align='left'><b>NONE MOULD/TOOLS</b></td>";
                                echo "<td align='left'></td>";
                            echo "</tr>";
                            $q_dheader_test = $this->db->query("SELECT * FROM cycletime_detail_detail WHERE id_costcenter='".$val2x['id_costcenter']."'")->result_array();
                            foreach($q_dheader_test AS $val2D => $val2Dx){ $val2D++;
                                $nomor = ($val2D==1)?$val2D:'';
                                echo "<tr>";
                                    echo "<td align='center'></td>";
                                    echo "<td align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".strtoupper(get_name('process', 'nm_process', 'code_process', $val2Dx['id_process']))."</td>";
                                    echo "<td align='left'>Time : ".$val2Dx['cycletime']." minutes</td>";
                                    echo "<td align='left'>Qty MP : ".$val2Dx['qty_mp']."</td>";
                                    echo "<td align='left'>".ucfirst(strtolower($val2Dx['note']))."</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
swal.close();
</script>