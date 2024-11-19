<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <a href="<?php echo site_url('ppic/spool') ?>" class="btn btn-sm btn-default" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a>
        <br>
        <?php
        foreach ($result as $key2 => $value2) { $key2++;
		    $result2 = $this->db->get_where('spool_group_all', array('spool_induk'=>$spool_induk,'kode_spool'=>$value2['kode_spool']))->result_array();
		    
            ?>  
                <h4><?=$key2?>. Kode Spool : <?=$value2['kode_spool'];?></h4>
                <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
                    <thead>
                        <tr class='bg-blue'>
                            <th class="text-center">#</th>
                            <th class="text-center">IPP</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Spec</th>
                            <th class="text-center">Length</th>
                            <th class="text-center">Code</th>
                            <th class="text-center">No SPK</th>
                            <th class="text-center">No Drawing</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result2 as $key => $value) { $key++;
                            
                            $SPEC = $value['kode_spk'];
                            $product_code = $value['product_code'];

                            $CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
                            $LENGTH = (!empty($value['length']))?number_format($value['length']):'';

                            if($value['sts'] == 'loose' OR $value['sts'] == 'cut'){
                                $SPEC = spec_bq2($value['id_milik']);
                                $IMPLODE = explode('.', $value['product_code']);
                                $product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE;
                            }

                            echo "<tr>";
                                echo "<td align='center'>".$key."</td>";
                                echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                                echo "<td align='left'>".strtoupper($value['id_category'])."</td>";
                                echo "<td align='left'>".$SPEC."</td>";
                                echo "<td align='right'>".$LENGTH."</td>";
                                echo "<td align='center'>".$product_code."</td>";
                                echo "<td align='center'>".$value['no_spk']."</td>";
                                echo "<td align='left'>".$value['no_drawing']."</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table><br>
            <?php
        }
        ?>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>

