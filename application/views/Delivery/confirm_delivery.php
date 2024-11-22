
<?php
$start_time				= (!empty($get_spk))?$get_spk[0]->start_time:'';
$finish_time			= (!empty($get_spk))?$get_spk[0]->finish_time:'';
$ekspedisi				= (!empty($get_spk))?$get_spk[0]->ekspedisi:'';
$upload_spk				= (!empty($get_spk))?$get_spk[0]->upload_spk:'';
$diterima_oleh			= (!empty($get_spk))?$get_spk[0]->diterima_oleh:'';
?>
<input type='hidden' name='kode_delivery' value='<?= $kode_delivery;?>'>
<div class="box box-primary">
	<div class="box-header">
	
	<div>
	<div class="box-body">
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Kode Delivery</b></label>
			<div class='col-sm-4'>             
				<?php
					echo form_input(array('id'=>'kode_deliveryx','name'=>'kode_deliveryx','class'=>'form-control input-md','readonly'=>'true'),$kode_delivery);											
				?>		
			</div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Tanggal Dikirim</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='start_time' name='start_time' class='form-control input-md datetimepicker' placeholder='Tanggal Dikirim' value='<?=$start_time;?>' readonly>
			</div>
			<label class='label-control col-sm-2'><b>Ekspedisi</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='ekspedisi' name='ekspedisi' class='form-control input-md ' placeholder='Ekspedisi' value='<?=$ekspedisi;?>'>
		    </div>
		</div>
		<div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Tanggal Diterima</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='finish_time' name='finish_time' class='form-control input-md datetimepicker' placeholder='Tanggal Diterima' value='<?=$finish_time;?>' readonly>		
			</div>
            <label class='label-control col-sm-2'><b>Diterima Oleh</b></label>
			<div class='col-sm-4'>             
				<input type='text' id='diterima_oleh' name='diterima_oleh' class='form-control input-md ' placeholder='Diterima Oleh' value='<?=$diterima_oleh;?>'>
		    </div>
		</div>
        <div class='form-group row'>		 	 
			<label class='label-control col-sm-2'><b>Upload Dokumen</b></label>
			<div class='col-sm-4 text-right'>             
				<input type='file' id='upload_spk' name='upload_spk' class='form-control input-md' placeholder='Upload SPK'>
				<?php if(!empty($upload_spk)){ ?>
				<a href='<?=base_url('assets/file/produksi/'.$upload_spk);?>' target='_blank' title='Download' data-role='qtip'>Download</a>
				<?php } ?>	
			</div>
		</div>
		<h4>LOOSE</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">IPP</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">No SPK</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result as $key => $value) { $key++;
                    if($value['sts'] == 'aksesoris'){
                        echo "<tr>";
                            echo "<td align='center'>".$key."</td>";
                            echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                            echo "<td align='left'>".strtoupper($value['no_drawing'])."</td>";
                            echo "<td align='left'></td>";
                            echo "<td align='center'>".number_format($value['berat'],2)."</td>";
                            echo "<td align='center'>".$value['no_spk']."</td>";
                        echo "</tr>";
                    }
                    else{
                        echo "<tr>";
                            echo "<td align='center'>".$key."</td>";
                            echo "<td align='center'>".str_replace('PRO-','',$value['id_produksi'])."</td>";
                            echo "<td align='left'>".strtoupper($value['product'])."</td>";
                            echo "<td align='left'>".spec_bq2($value['id_milik'])."</td>";
                            echo "<td align='center'>".number_format($value['qtyCount'])."</td>";
                            echo "<td align='center'>".$value['no_spk']."</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>

		<h4>SPOOL</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Kode</th>
                    <th class="text-center">Product</th>
                    <th class="text-center">Spec</th>
                    <th class="text-center">ID Product</th>
                    <th class="text-center">No SPK</th>
                    <th class="text-center">No Drawing</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result2 as $key => $value) { $key++;
                    $IMPLODE = explode('.', $value['product_code']);
                    $product_code = $IMPLODE[0].'.'.$value['product_ke'];

					$get_split_ipp = $this->db
													->select('a.*, b.product_code_cut AS type_product, b.id_product AS product_tanki')
													->join('production_detail b','a.id_pro=b.id','left')
													->get_where('delivery_product_detail a',
														array(
															'a.kode_delivery' => $value['kode_delivery'], 
															'a.kode_spool' => $value['kode_spool'], 
															'a.spool_induk' => $value['spool_induk']
															)
														)->result_array();
					$ArrNo_Spool = [];
					$ArrNo_IPP = [];
					$ArrNo_SPK = [];
					$ArrNo_ID = [];
					$ArrNo_Drawing = [];
					foreach ($get_split_ipp as $key2 => $value2) { $key2++;
						$key2++;
                        $LENGTH = '';
                        if ($value2['product'] == 'pipe') {
                            $no_spk_list = $this->db->select('length')->get_where('so_detail_header', array('id' => $value2['id_milik']))->result();
                            $LENGTH = ($value2['sts'] == 'cut') ? number_format($value2['length']) : number_format($no_spk_list[0]->length);
                        }

                        $nm_product = ($value2['type_product'] == 'tanki')?$value2['product_tanki']:$value2['product'];
                        $spec = ($value2['type_product'] == 'tanki')?$tanki_model->get_spec($value2['id_milik']):spec_bq2($value2['id_milik']);

                        $ArrNo_IPP[] = $key2 . '. ' . strtoupper($nm_product . ' ' . $LENGTH);

                        if($value2['sts'] == 'loose_dead'){
                            $ArrNo_Spool[] = $key2 . '. ' . strtoupper($value2['kode_spk']);
                        }
                        else{
                            $ArrNo_Spool[] = $key2 . '. ' . strtoupper($spec);
                        }

                        $CUTTING_KE = (!empty($value2['cutting_ke'])) ? '.' . $value2['cutting_ke'] : '';

                        $IMPLODE = explode('.', $value2['product_code']);
                        $ArrNo_SPK[] = $key2 . '. ' . $value2['no_spk'];
                        $ArrNo_ID[] = $key2 . '. ' . $IMPLODE[0] . '.' . $value2['product_ke'] . $CUTTING_KE;

                        $ArrNo_Drawing[] = $key2.'. '.$value2['no_drawing'];
					}
					// print_r($ArrGroup); exit;
					$explode_spo = implode('<br>',$ArrNo_Spool);
					$explode_ipp = implode('<br>',$ArrNo_IPP);
					$explode_spk = implode('<br>',$ArrNo_SPK);
					$explode_id = implode('<br>',$ArrNo_ID);
					$explode_nd = implode('<br>',$ArrNo_Drawing);
					
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='left'>".$value['spool_induk']."-".$value['kode_spool']."</td>";
                        echo "<td align='left'>".$explode_ipp."</td>";
                        echo "<td align='left'>".$explode_spo."</td>";
                        echo "<td align='left'>".$explode_id."</td>";
                        echo "<td align='left'>".$explode_spk."</td>";
                        echo "<td align='left'>".$explode_nd."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

		<h4>MATERIAL</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Nama Material</th>
                    <th class="text-center">Berat</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result3 as $key => $value) { $key++;
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='left'>".strtoupper($GET_MATERIAL[$value['product']]['nm_material'])."</td>";
                        echo "<td align='right'>".number_format($value['berat'],2)."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <h4>FIELD JOINT</h4>
        <table class="table table-sm table-bordered table-striped" id="my-grid" width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class="text-center">#</th>
                    <th class="text-center">Nama Material</th>
                    <th class="text-center">Qty (kit)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result4 as $key => $value) { $key++;
                    echo "<tr>";
                        echo "<td align='center'>".$key."</td>";
                        echo "<td align='left'> FIELD JOINT".spec_bq2($value['id_milik'])."</td>";
                        echo "<td align='center'>".number_format($value['berat'])."</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
	<div>
    <?php if(empty($get_spk[0]->confirm_date)){?>
	<div class='box-footer'>
		<button type='button' id='saveConfirm' class='btn btn-md btn-success' style='float:right; margin-left:10px;'><b>Confirm</b></button>
	</div>
    <?php } ?>
</div>
<script>
	$(document).ready(function(){
		swal.close();
		$('.chosen_select').chosen({
			width : '100%'
		});
		$('.autoNumeric').autoNumeric();
		$('.datetimepicker').datepicker();
	});
</script>