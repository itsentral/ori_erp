<table class="table table-bordered table-striped table-sm">
    <thead>
        <tr class='bg-blue'>
            <th class="text-center" rowspan='2'>#</th>
            <th class="text-center" rowspan='2'>NO SO</th>
            <th class="text-center" rowspan='2'>CUSTOMER</th>
            <th class="text-center" rowspan='2'>NO SPK</th>
            <th class="text-center" rowspan='2'>PRODUCT</th>
            <th class="text-center" rowspan='2'>SPEC</th>
            <th class="text-center" rowspan='2'>QTY_SO</th>
            <th class="text-center" colspan='2'>SPK</th>
            <th class="text-center" colspan='3'>PRODUKSI</th>
            <th class="text-center" colspan='2'>FG</th>
            <th class="text-center" colspan='2'>IN TRANSIT</th>
            <th class="text-center" colspan='2'>CUSTOMER</th>
        </tr>
        <tr class='bg-blue'>
            <th class="text-center">R</th>
            <th class="text-center">O</th>
            <th class="text-center">R</th>
            <th class="text-center">O</th>
            <th class="text-center">D</th>
            <th class="text-center">R</th>
            <th class="text-center">O</th>
            <th class="text-center">R</th>
            <th class="text-center">O</th>
            <th class="text-center">R</th>
            <th class="text-center">O</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(!empty($result)){
            foreach($result as $key => $value){ 
                $key++;

                $getSPK         = $this->db->get_where('production_detail',array('kode_spk !='=>'deadstok','id_milik' => $value['id'],'kode_spk !=' => NULL))->result_array();
                $getLap         = $this->db->get_where('production_detail',array('kode_spk !='=>'deadstok','id_milik' => $value['id'],'closing_produksi_date !=' => NULL))->result_array();
                $getFg          = $this->db->get_where('production_detail',array('kode_spk !='=>'deadstok','id_milik' => $value['id'],'fg_date !=' => NULL))->result_array();
                $getFgDead      = $this->db->get_where('production_detail',array('kode_spk'=>'deadstok','id_milik' => $value['id']))->result_array();
                $getInTrans     = $this->db->get_where('production_detail',array('kode_spk !='=>'deadstok','id_milik' => $value['id'],'lock_delivery_date !=' => NULL))->result_array();
                $getGdCust      = $this->db->get_where('production_detail',array('kode_spk !='=>'deadstok','id_milik' => $value['id'],'release_delivery_date !=' => NULL))->result_array();

                $QTY_SO             = $value['qty'];

                $QTY_SPK            = COUNT($getSPK);
                $QTY_SPK_OUT        = ($QTY_SO - $QTY_SPK > 0)?$QTY_SO - $QTY_SPK:'-';

                $DEADSTOK           = COUNT($getFgDead);
                $QTY_LAP_PRO        = COUNT($getLap);
                $QTY_LAP_PRO_OUT    = ($QTY_SPK - $QTY_LAP_PRO > 0)?$QTY_SPK - $QTY_LAP_PRO - $DEADSTOK:'-';
                
                $QTY_FG             = COUNT($getFg);
                $QTY_FG_OUT         = ($QTY_LAP_PRO - $QTY_FG > 0)?$QTY_LAP_PRO - $QTY_FG:'-';

                $QTY_INTRAN         = COUNT($getInTrans);
                $QTY_INTRAN_OUT     = ($QTY_FG - $QTY_INTRAN > 0)?$QTY_FG - $QTY_INTRAN:'-';

                $QTY_CUST           = COUNT($getGdCust);
                $QTY_CUST_OUT       = ($QTY_INTRAN - $QTY_CUST > 0)?$QTY_INTRAN - $QTY_CUST:'-';



                $Label_QTY_SPK            = ($QTY_SPK > 0)?$QTY_SPK:'-';
                $Label_QTY_LAP_PRO        = ($QTY_LAP_PRO > 0)?$QTY_LAP_PRO:'-';
                $Label_QTY_FG             = ($QTY_FG > 0)?$QTY_FG:'-';
                $Label_QTY_INTRAN         = ($QTY_INTRAN > 0)?$QTY_INTRAN:'-';
                $Label_QTY_CUST           = ($QTY_CUST > 0)?$QTY_CUST:'-';


                echo "<tr>";
                    echo "<td align='center'>".$key."</td>";
                    echo "<td align='center'>".$value['so_number']."</td>";
                    echo "<td align='left'>".strtoupper($value['nm_customer'])."</td>";
                    echo "<td align='center'>".$value['no_spk']."</td>";
                    echo "<td align='left'>".strtoupper($value['id_category'])."</td>";
                    echo "<td align='left'>".spec_bq2($value['id'])."</td>";
                    echo "<td align='center'>".$QTY_SO."</td>";
                    echo "<td class='text-center text-bold text-primary'>".$Label_QTY_SPK."</td>";
                    echo "<td class='text-center text-bold text-primary'>".$QTY_SPK_OUT."</td>";
                    echo "<td class='text-center text-bold text-success'>".$Label_QTY_LAP_PRO."</td>";
                    echo "<td class='text-center text-bold text-success'>".$QTY_LAP_PRO_OUT."</td>";
                    echo "<td class='text-center text-bold text-danger'>".$DEADSTOK."</td>";
                    echo "<td class='text-center text-bold text-info'>".$Label_QTY_FG."</td>";
                    echo "<td class='text-center text-bold text-info'>".$QTY_FG_OUT."</td>";
                    echo "<td class='text-center text-bold text-warning'>".$Label_QTY_INTRAN."</td>";
                    echo "<td class='text-center text-bold text-warning'>".$QTY_INTRAN_OUT."</td>";
                    echo "<td class='text-center text-bold text-purple'>".$Label_QTY_CUST."</td>";
                    echo "<td class='text-center text-bold text-purple'>".$QTY_CUST_OUT."</td>";
                echo "</tr>";
            }
        }
        else{
            echo "<tr>";
                echo "<td colspan='9'>Tidak ada data yang ditampilkan.</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>