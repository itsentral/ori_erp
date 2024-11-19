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
        <!-- <a href="<?php echo site_url('delivery') ?>" class="btn btn-sm btn-default" style='float:right; margin-bottom:10px; margin-left:5px;'>Back</a> -->
        
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">ID Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">No Drawing</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result as $key => $value) { $key++;
					$CUTTING_KE = (!empty($value['cutting_ke']))?'.'.$value['cutting_ke']:'';
				
                    $IMPLODE = explode('.', $value['product_code']);
                    $product_code = $IMPLODE[0].'.'.$value['product_ke'].$CUTTING_KE;
                    $loose_spool = (!empty($value['spool_induk']))?$value['spool_induk'].'-'.$value['kode_spool']:'LOOSE';
                    $no_drawing = $value['no_drawing'];

                    if($value['sts_product'] == 'so material'){
                        $loose_spool = 'SO MATERIAL';
                        $product_code = '';
                    }
                    if($value['sts_product'] == 'field joint'){
                        $loose_spool = 'FIELD JOINT';
                        $product_code = '';
                    }
                    $LENGTH = '';
                    if($value['product'] == 'pipe'){
                        $no_spk_list = $this->db->select('length')->get_where('so_detail_header',array('id'=>$value['id_milik']))->result();
                        $LENGTH = ($value['sts'] == 'cut')?number_format($value['length']):number_format($no_spk_list[0]->length);
                    }

                    $PRODUCT = strtoupper($value['product'].' '.$LENGTH);
                    if($value['sts_product'] == 'so material'){
                        $PRODUCT = strtoupper(get_name('raw_materials','nm_material','id_material',$value['product']));
                    }
                    

                    $SPEC = spec_bq3($value['id_milik']);
                    if($value['sts_product'] == 'so material'){
                        $SPEC = number_format($value['berat'],2).' kg';
                    }
                    if($value['sts'] == 'loose_dead'){
                        $SPEC = $value['kode_spk'].' x '.$value['length'];
                    }

                    if($value['type_product'] == 'tanki'){
                        $PRODUCT = strtoupper($value['product_tanki']);
                        $SPEC = $tanki_model->get_spec($value['id_milik']);
                    }

                    if($value['sts_product'] == 'aksesoris'){
                        $PRODUCT = strtoupper($value['no_drawing']);
                        $SPEC = '';
                        $product_code = $value['product_code'];
                        $loose_spool = 'AKSESORIS';
                        $no_drawing = '';
                    }
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                        echo "<td align='left'>".$PRODUCT."</td>";
                        echo "<td align='left'>".$SPEC."</td>";
                        echo "<td align='left'>".$product_code."</td>";
                        echo "<td align='center'>".$value['no_spk']."</td>";
                        echo "<td align='left'>".$loose_spool."</td>";
                        echo "<td align='left'>".$no_drawing."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
</form>	
<?php $this->load->view('include/footer'); ?>

