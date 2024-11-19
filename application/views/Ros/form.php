<?php
$this->load->view('include/side_menu');
$curency=array('USD'=>'USD','IDR'=>'IDR');
$no_po=(isset($data->no_po)?$data->no_po:'');
$tipetrans=(isset($data->type_material)?$data->type_material:$tipetrans);
$id_ros=(isset($data->id)?$data->id:'');
$freight_curs=(isset($data->freight_curs)?$data->freight_curs:'1');
$mata_uang=(isset($data->mata_uang)?$data->mata_uang:'IDR');
$tax=(isset($data->tax)?$data->tax:'0');
$sel_local="selected";
$sel_import="";
if(isset($data->lokasi)){
	$sel_local 	= ($data->lokasi == 'local')?'selected':'';
	$sel_import = ($data->lokasi == 'import')?'selected':'';
}
$status_rg_check='';$statusmaterial='';
if(isset($data->status_rg_check)){
	if($data->status_rg_check=='DONE') {
		$status_rg_check='checked';
		$statusmaterial='readonly';
	}
}
?>
<form action="#" method="POST" id="form_proses_bro">
<input type="hidden" name="id_ros" value="<?=(isset($data->id)?$data->id:'')?>">
<input type="hidden" id="type_material" name="type_material" value="<?=$tipetrans?>">
<input type="hidden" id="id_supplier" name="id_supplier" value="<?=$data->id_supplier?>">
<input type="hidden" id="tax" name="tax" value="<?=$tax?>">
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title"><?php echo $title;?></h3>
		</div>
		<!-- /.box-header -->
		<div class="box-body">
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nomor PO </b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'no_po','name'=>'no_po','class'=>'form-control input-md','value'=>$data->no_po,'readonly'=>'readonly'));
				?>
				</div>
				<label class='label-control col-sm-2'><b>Supplier Name</b></label>
				<div class='col-sm-4'>
				<?php
					echo form_input(array('id'=>'nm_supplier','name'=>'nm_supplier','class'=>'form-control input-md','value'=>$data->nm_supplier,'readonly'=>'readonly'));
				?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Nomor ROS <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'no_ros','name'=>'no_ros','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Auto','value'=>(isset($data->no_ros)?$data->no_ros:''),'readonly'=>'readonly'));
						?>
				</div>
				<label class='label-control col-sm-2'><b>AWB / BL Date</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'awb_date','name'=>'awb_date','class'=>'form-control input-md tanggal','autocomplete'=>'off','placeholder'=>'AWB Date','value'=>(isset($data->awb_date)?$data->awb_date:'')));
						?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Date ROS <span class='text-red'>*</span></b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'date','name'=>'date','class'=>'form-control input-md tanggal','autocomplete'=>'off','placeholder'=>'Date ROS','value'=>(isset($data->date)?$data->date:'')));
						?>
				</div>
				<label class='label-control col-sm-2'><b>AWB / BL Number</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'awb_number','name'=>'awb_number','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'AWB Number','value'=>(isset($data->awb_number)?$data->awb_number:'')));
						?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Mode Shipment</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'shipment','name'=>'shipment','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Mode Shipment','value'=>(isset($data->shipment)?$data->shipment:'')));
						?>
				</div>
				<label class='label-control col-sm-2'><b>ETA Warehouse</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'eta_date','name'=>'eta_date','class'=>'form-control input-md tanggal','autocomplete'=>'off','placeholder'=>'ETA Warehouse','value'=>(isset($data->eta_date)?$data->eta_date:'')));
						?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Delivery Date</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'deliv_date','name'=>'deliv_date','class'=>'form-control input-md tanggal','value'=>(isset($data->deliv_date)?$data->deliv_date:''),'placeholder'=>'Delivery Date'));
						?>
				</div>
				<label class='label-control col-sm-2'><b>Est. Day(s)</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'est_day','name'=>'est_day','class'=>'form-control input-md','value'=>(isset($data->est_day)?$data->est_day:''),'placeholder'=>'Est. Day(s)'));
						?>
				</div>
			</div>
			<div class='form-group row'>
				<label class='label-control col-sm-2'><b>Kurs Terima Barang</b></label>
				<div class='col-sm-4'>
						<?php
							echo form_input(array('id'=>'freight_curs','name'=>'freight_curs','class'=>'form-control input-md freight_curs divide','value'=>(isset($data->freight_curs)?$data->freight_curs:''),'placeholder'=>'','required'=>'required'));
						?>
				</div>
				<label class='label-control col-sm-2'> </label>
				<div class='col-sm-4'>

					<select id='lokasi' name='lokasi' class='form-control input-md chosen-select'>
						<option value='local' <?=$sel_local;?>>LOCAL</option>
						<option value='import' <?=$sel_import;?>>IMPORT</option>
					</select>
				</div>
			</div>
		</div>



        <!-- List product -->
      <div class="box box-danger">
        <div class="box-header">
          <h4 class="box-title"><label for=""><i class="fa fa-list"></i> List Details</label></h4>
        </div>

        <div class="box-body">
          <div class="table-responsive">
            <table width="200%" class="table-bordered table-striped table-condensed table-responsive">
              <thead>
                <tr class="bg-primary">
                  <th class="text-nowrap" width="1%">No.</th>
                  <th class="text-nowrap" width="">Nama Barang</th>
                  <th class="text-center text-nowrap" width="3%">Currency</th>
                  <th class="text-center text-nowrap" width="4%">Price/Unit</th>
                  <th class="text-center text-nowrap" width="5%">Price/Unit (Rp)</th>
                  <th class="text-center text-nowrap" width="5%">F&C Cost/Unit (Rp)</th>
                  <th class="text-center text-nowrap" width="5%">Price F&C <br>Cost/Unit (Rp)</th>
                  <th class="text-center text-nowrap" width="5%">Q T Y P O</th>
                  <th class="text-center text-nowrap" width="5%">Q T Y S h i p</th>
                  <th class="text-center text-nowrap" width="5%">Total Price (Rp)</th> 
				  <th class="text-center text-nowrap" width="10%">BM (Rp)</th>
                  <th class="text-center text-nowrap" width="5%">Total Price F&C Cost (Rp)</th>
                </tr>
              </thead>
 		<tbody>
			<?php
            $No=0;
			$gtotal_price=0;
			$gtotal_price_us=0;
			$total_ppn=0;
			$gTotal_inc_ppn=0;
			$total_price_aft_fc=0;
			foreach($datadetail AS $val => $valx){
                $No++;
				if($mata_uang=='') $mata_uang=$valx->curency;
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td><input type='hidden' name='nm_material[]' id='nm_material".$No."' value='".$valx->nm_material."'>
					<input type='hidden' name='id_material[]' id='id_material".$No."' value='".$valx->id_material."'>
					".$valx->nm_material."</td>";
					echo "<td><input type='hidden' name='curency[]' id='curency".$No."' value='".$mata_uang."'>
					".$mata_uang."</td>";
					if(isset($valx->idpo)) {
						$idpo=$valx->idpo;
					}else{
						$idpo=$valx->id;
					}
					if(isset($valx->id_ros)) {
						$id_ros=$valx->id_ros;
					}else{
						$id_ros="";
					}
					$price_ref_sup=0;
					if(isset($valx->price_ref_sup)) $price_ref_sup=$valx->price_ref_sup;

					$price=$price_ref_sup*$freight_curs;
					if(isset($valx->price)) $price=$valx->price;

					$fc_cost_unit=0;
					if(isset($valx->fc_cost_unit)) $fc_cost_unit=$valx->fc_cost_unit;

					$price_aft_fc=$fc_cost_unit+$price;
					if(isset($valx->price_aft_fc)) $price_aft_fc=$valx->price_aft_fc;

					$qty_po=0;
					if(isset($valx->qty_po)) {
						$qty_po=$valx->qty_po;
					}else{
						$qty_po=$valx->qty_purchase;
					}

					$qty_ship=0;
					if(isset($valx->qty_ship)) $qty_ship=$valx->qty_ship;
					$total_price=$price*$qty_ship;
					$total_fc_costprd=$fc_cost_unit*$qty_ship;
					$total_price_fc_cost=$total_price+$total_fc_costprd;
					if(isset($valx->bm)) $bm=$valx->bm;

					echo "<td align=right><input type='hidden' name='price_ref_sup[]' id='price_ref_sup".$No."' value='".$price_ref_sup."' class='price_ref_sup'>
					<input type='hidden' name='nomor[]' id='nomor".$No."' value='".$No."' class='nomor'>
					<input type='hidden' name='idpo[]' id='idpo".$No."' value='".$idpo."'>
					".number_format($price_ref_sup,2)."</td>";
					echo "<td><input type='text' name='price[]' id='price".$No."' readonly class='form-control divide price' value='".($price)."'></td>";
					echo "<td><input type='text' name='fc_cost_unit[]' id='fc_cost_unit".$No."' readonly class='form-control divide fc_cost_unit' value='".($fc_cost_unit)."'></td>";
					echo "<td><input type='text' name='price_aft_fc[]' id='price_aft_fc".$No."' readonly class='form-control divide price_aft_fc' value='".($price_aft_fc)."'></td>";
					echo "<td><input type='text' name='qty_po[]' id='qty_po".$No."' readonly class='form-control divide qty_po' value='".($qty_po)."'></td>";
					echo "<td><input type='text' name='qty_ship[]' id='qty_ship".$No."' class='form-control divide qty_ship' value='".($qty_ship)."' onblur='material_fc_cost()' ".$statusmaterial."></td>";
					echo "<td><input type='text' name='total_price[]' id='total_price".$No."' readonly class='form-control divide total_price' value='".($total_price)."'></td>";
					echo "<td><input type='text' name='bm[]' id='bm".$No."' class='form-control divide bm' value='".($bm)."' onblur='material_fc_cost()'></td>";
					echo "<td>
					<input type='hidden' name='total_fc_costprd[]' id='total_fc_costprd".$No."' readonly class='form-control divide total_fc_costprd' value='".($total_fc_costprd)."'>
					<input type='text' name='total_price_fc_cost[]' id='total_price_fc_cost".$No."' readonly class='form-control divide total_price_fc_cost' value='".($total_price_fc_cost+$bm)."'></td>";
				echo "</tr>";
				$gtotal_price=($gtotal_price+$total_price);
				$gtotal_price_us=($gtotal_price_us+($price_ref_sup*$qty_ship));
			}
			$total_ppn=($tax*$gtotal_price/100);
			$gTotal_inc_ppn=($gtotal_price+$total_ppn+$bm);
			$grand_total_fc_cost=(isset($data->grand_total_fc_cost) ? ($data->grand_total_fc_cost) :0);
			$total_price_aft_fc=($gTotal_inc_ppn+$grand_total_fc_cost);
			?>
		</tbody>
           </table>
          </div>
        </div>
      </div>

      <div class="box box-warning">
        <div class="box-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-horizontal">
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">Total Price Material</label>
                  <div class="col-md-5">
                    <input type="text" name="gtotal_price" id="gtotal_price" readonly class="form-control text-right divide" value="<?= $gtotal_price; ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">Total (US)</label>
                  <div class="col-md-5">
                    <input type="text" name="gtotal_price_us" id="gtotal_price_us" readonly class="form-control text-right divide" value="<?= $gtotal_price_us; ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">Update Material</label>
                  <div class="col-md-5">
                    <input type="checkbox" name="status_rg_check" value="DONE" <?=$status_rg_check;?> <?=(($statusmaterial!='')?'style="pointer-events: none;"':'')?> />
                  </div>
                </div>
              </div>
            </div>
            <div class=" col-md-6">
              <div class="form-horizontal">
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">PPN Material</label>
                  <div class="col-md-5">
                    <input type="text" name="total_ppn" id="total_ppn" readonly class="form-control text-right divide" value="<?= $total_ppn; ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">Grand Total + PPN + BM</label>
                  <div class="col-md-5">
                    <input type="text" name="gTotal_inc_ppn" id="gTotal_inc_ppn" readonly class="form-control text-right divide" value="<?= $gTotal_inc_ppn; ?>">
                  </div>
                </div>
                <div class="form-group">
                  <label for="" class="col-md-5 control-label">Total Price After F&C </label>
                  <div class="col-md-5">
                    <input type="text" name="total_price_aft_fc" id="total_price_aft_fc" readonly class="form-control text-right divide" value="<?= $total_price_aft_fc; ?>">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


      <!-- F&C Cost Estimation -->
      <div class="box box-warning" id="dtlFccost" >
        <div class="box-header" hidden>
          <!--<h4 class="box-title"><label for=""><i class="fa fa-truck"></i> F&C Cost Estimation</label></h4>-->
        </div>
        <div class="box-body">
          <div class="table-responsive"hidden >
            <table id="tbl_dtlFccost" class="table table-bordered table-condensed table-striped">
              <thead class="bg-info">
                <tr>
                  <th width="40px">#</th>
                  <th>Forwarder Name</th>
                  <th colspan="4">Remark</th>
                  <th width="80px">Weight</th>
                  <th width="80px">Volume(M<sup>3</sup>)</th>
                  <th width="50px">#</th>
                </tr>
              </thead>
              <tbody class="data_fc" hidden>
                <?php
				$fw_cost=0;$fw_ppn=0;$fw_grand_total=0;
				$dt_supplier[]="Select Forwarder";
				foreach ($data_supplier as $keys) {
					$dt_supplier[$keys['id_supplier']] = $keys['nm_supplier'];
				}
                if (isset($forward)) :
                  $n = 0;
                  foreach ($forward as $fw) : $n++;
				  $fw_cost=$fw->cost;if($fw_cost=='') $fw_cost=0;
				  $fw_ppn=$fw->ppn;if($fw_ppn=='') $fw_ppn=0;
				  $fw_grand_total=$fw->grand_total;
				  if($fw_grand_total=='') $fw_grand_total=0;
				  ?>
                    <tr class="data_fc row_<?= $n; ?>">
                      <td rowspan="2"><?= $n; ?>
                        <input type="hidden" class="form-control" name="dtlFccost[<?= $n; ?>][id]" value="<?= $fw->id; ?>">
                      </td>
                      <td>
					  <?php
					  echo form_dropdown('dtlFccost['.$n.'][fwd_name]',$dt_supplier, $fw->farward_name, array('id'=>'dtlFccost['.$n.'][fwd_name]'.$n,'class'=>'form-control input-sm chosen-select','required'=>'required'));
					  ?></td>
                      <td colspan="4"><input type="text" class="form-control" name="dtlFccost[<?= $n; ?>][remark]" placeholder="Note" value="<?= $fw->remark; ?>"></td>
                      <td><input type="text" class="form-control text-right divide" name="dtlFccost[<?= $n; ?>][weight]" placeholder="0" value="<?= $fw->weight; ?>"></td>
                      <td><input type="text" class="form-control text-right divide" name="dtlFccost[<?= $n; ?>][volume]" placeholder="0" value="<?= $fw->volume; ?>"></td>
                      <td rowspan="2"><button type="button" data-id="<?= $fw->id; ?>" data-row="<?= $n; ?>" class="del_fwdCost"><i class="text-red fa fa-close btn-xs"></i></button></td>
                    </tr>
                    <tr class="row_<?= $n; ?>">
                      <td colspan="7" style="padding: 10px;">
                        <div class="contdainer">
                          <table class="table table-bordered table-condensed table-striped detail_item_fc" id="detail_fc_<?= $n; ?>" data-id="<?= $n; ?>">
                            <thead class="bg-gray">
                              <tr>
                                <th width="20px">No</th>
                                <th>Item</th>
                                <th width="100px">Curency</th>
                                <th width="100px">Cost</th>
                                <th width="100px">Kurs</th>
                                <th width="100px">Cost(Rp)</th>
                                <th width="100px">PPN</th>
                                <th width="120px">Total</th>
                                <th width="">#</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php if (isset($itemFccost)) : $no = 0;
							  if(!empty($itemFccost)){
                                foreach ($itemFccost[$fw->id] as $item) : $no++; ?>
                                  <tr class="list-detail">
                                    <td><?= $no; ?>
                                      <input type="hidden" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][id]" value="<?= $item->id; ?>">
                                    </td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][item]" class="form-control" value="<?= $item->item; ?>"></td>
                                    <td>
                                      <select name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][curency]" class="form-control">
                                        <option value=""></option>
                                        <?php foreach ($curency as $cur => $val) : ?>
                                          <option value="<?= $cur; ?>" <?= ($item->curency == $cur) ? 'selected' : ''; ?>><?= $val; ?></option>
                                        <?php endforeach ?>
                                      </select>
                                    </td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][cost_curency]" id="item_fc_cost_curency_<?= $n . $no; ?>" class="form-control cost_curency divide text-right" data-id="<?= $no; ?>" data-row="<?= $n; ?>" value="<?= ($item->cost_curency); ?>"></td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][kurs]" id="item_fc_kurs_<?= $n . $no; ?>" class="form-control kurs divide text-right" data-id="<?= $no; ?>" data-row="<?= $n; ?>" value="<?= ($item->kurs); ?>"></td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][cost]" id="item_fc_cost_<?= $n . $no; ?>" class="form-control item_fc_cost_<?= $n; ?> divide text-right" data-id="<?= $no; ?>" data-row="<?= $n; ?>" value="<?= ($item->cost); ?>" readonly></td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][ppn]" id="item_fc_ppn_fc_<?= $n . $no; ?>" class="form-control nominal divide item_ppn_fc item_fc_ppn_fc_<?= $n; ?> text-right" data-id="<?= $no; ?>" data-row="<?= $n; ?>" value="<?= ($item->ppn); ?>"></td>
                                    <td><input type="text" name="dtlFccost[<?= $n; ?>][itemFc][<?= $no; ?>][total]" id="item_total_fc_<?= $n . $no; ?>" class="form-control divide item_total_fc_<?= $n; ?> text-right" data-id="<?= $no; ?>" data-row="<?= $n; ?>" value="<?= ($item->total); ?>" readonly></td>
                                    <td class="text-center"><button type="button" data-id="<?= $item->id; ?>" data-row="<?= $n; ?>" class="btn btn-xs text-danger del_item_fc text-center"><i class="fa fa-times-circle"></i></button></td>
                                  </tr>
                              <?php endforeach;
							  }
                              endif; ?>
                            </tbody>
                            <tfoot>
                              <tr>
                                <th><button type="button" class="btn btn-sm btn-warning add_item_fc" data-id="<?= $n; ?>">Add Item</button></th>
                                <th colspan="4" class="text-right">Total</th>
                                <th colspan="" class="text-right">
                                  <input type="text" name="dtlFccost[<?= $n; ?>][cost]" data-id="<?= $n; ?>" id="fc_cost__<?= $n; ?>" class="form-control text-right fc_cost divide" readonly value="<?= ($fw_cost); ?>" title="">
                                </th>
                                <th colspan="" class="text-right">
                                  <input type="text" name="dtlFccost[<?= $n; ?>][ppn]" data-id="<?= $n; ?>" id="ppn__<?= $n; ?>" class="form-control text-right ppn_fc divide" readonly value="<?= ($fw_ppn); ?>">
                                </th>
                                <th colspan="" class="text-right">
                                  <input type="text" name="dtlFccost[<?= $n; ?>][grand_total]" data-id="<?= $n; ?>" id="grand_total__<?= $n; ?>" class="form-control text-right grand_total_fc divide" readonly value="<?= ($fw_grand_total); ?>" title="">
                                </th>
                                <th></th>
                              </tr>
                            </tfoot>
                          </table>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!--<button type="button" id="addFccost" class="btn btn-primary"><i class="fa fa-plus"></i> Add F&C </button>-->
          <div class="hidden" id="delete_fw"></div>

  <div class="box box-success" style="margin-top: 2em;" hidden>
	<div class="box-body">
	  <div class="row">
		<div class="form-horizontal">
		  <div class="col-md-4">
			<div class="form-group">
			  <label for="" class="col-md-5 control-label">Total Freight</label>
			  <div class="col-md-7">
				<input type="hidden" name="fc_cost" id="total_fc" value="<?= (isset($data->fc_cost)) ? ($data->fc_cost) : '0'; ?>" placeholder="0">
				<input type="text" name="total_fc_cost" id="total_fc_cost" readonly class="form-control text-right divide" value="<?= (isset($data->total_fc_cost)?($data->total_fc_cost):0); ?>">
			  </div>
			</div>
		  </div>
		  <div class="col-md-3">
			<div class="form-group">
			  <label for="" class="col-md-3 control-label">PPN</label>
			  <div class="col-md-8">
				<input type="text" readonly class="form-control text-right ppn_fc_cost divide" name="ppn_fc_cost" id="ppn_fc_cost" value="<?= (isset($data->ppn_fc_cost)) ? ($data->ppn_fc_cost) : '0'; ?>">
			  </div>
			</div>
		  </div>
		  <div class="col-md-5">
			<div class="form-group">
			  <label for="" class="col-md-5 control-label">Total Freight + PPN</label>
			  <div class="col-md-7">
				<input type="text" class="form-control text-right divide" readonly name="grand_total_fc_cost" id="grand_total" placeholder="0" value="<?= isset($data->grand_total_fc_cost) ? ($data->grand_total_fc_cost) : '0'; ?>">
			  </div>
			</div>
		  </div>
		</div>
	  </div>
  </div>

		
		<!-- /.box-body -->

	 </div>

  <!-- /.box -->
  
   <!-- F&C Cost Estimation -->
      <div class="box box-warning" id="dtlFccost">
        <div class="box-header" >
         <div class='box-footer'>
			<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','value'=>'save','content'=>'Save','id'=>'simpan-bro')).' ';
			//echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()'));
			?>
			<a href="javascript:back()" class="btn btn-danger btn-md">Back</a>
		</div>
        </div>
	 </div>

</form>
<style type="text/css">
	#unit_chosen{
		width: 100% !important;
	}
</style>
<?php $this->load->view('include/footer'); ?>
<script src="<?=base_url()?>assets/js/number-divider.min.js"></script>
<script>
$('.divide').divide();
$(document).on('click', '.del_fwdCost', function() {
    let id = $(this).data('id');
    let row = $(this).data('row');
    total_fc_cost();
    $('#dtlFccost tbody').find('tr.row_' + row).remove();
    if (id) {
        $('#delete_fw').append('<input type="text" name="delete_fw[][id]" value="' + id + '">');
    }
})

$(function() {
    $(".tanggal").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
    });
});
$(document).ready(function() {
	$('.chosen-select').chosen();
    $('#simpan-bro').click(function(e) {
		errros='';
		if($("#freight_curs").val()=="") errros="Kurs Terima Barang tidak boleh kosong";
		if($("#freight_curs").val()=="0") errros="Kurs Terima Barang tidak boleh kosong";
		if($("#date").val()=="") errros="Tanggal ROS tidak boleh kosong";
		if(errros!="") {
			alert(errros);
			return;
		}
        e.preventDefault();
        $(this).prop('disabled', true);
        swal({
                title: "Are you sure?",
                text: "You will not be able to process again this data!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, Process it!",
                cancelButtonText: "No, cancel process!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    loading_spinner();
                    var formData = new FormData($('#form_proses_bro')[0]);
                    var baseurl = base_url + active_controller + '/save_ros';
                    $.ajax({
                        url: baseurl,
                        type: "POST",
                        data: formData,
                        cache: false,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            //console.log(data);
                            if (data.status == 1) {
                                swal({
                                    title: "Save Success!",
                                    text: data.pesan,
                                    type: "success",
                                    timer: 7000
                                });
                                window.location.href = base_url + active_controller;
                            } else {
                                if (data.status == 2) {
                                    swal({
                                        title: "Save Failed!",
                                        text: data.pesan,
                                        type: "warning",
                                        timer: 7000
                                    });
                                } else {
                                    swal({
                                        title: "Save Failed!",
                                        text: data.pesan,
                                        type: "warning",
                                        timer: 7000,
                                        showCancelButton: false,
                                        showConfirmButton: false,
                                        allowOutsideClick: false
                                    });
                                }
                                $('#simpan-bro').prop('disabled', false);
                            }
                        },
                        error: function() {
                            swal({
                                title: "Error Message !",
                                text: 'An Error Occured During Process. Please try again..',
                                type: "warning",
                                timer: 7000,
                                showCancelButton: false,
                                showConfirmButton: false,
                                allowOutsideClick: false
                            });
                            $('#simpan-bro').prop('disabled', false);
                        }
                    });
                } else {
                    swal("Cancelled", "Data can be process again :)", "error");
                    $('#simpan-bro').prop('disabled', false);
                    return false;
                }
            });
    });
});

$(document).on('change', '.cost_curency,.kurs,.item_ppn_fc,.freight_curs', function() {
    let n = $(this).data('id');
    let row = $(this).data('row');
    let kurs = $('#item_fc_kurs_' + row + n).val() || 0;
    let ppn = $('#item_fc_ppn_fc_' + row + n).val() || 0;
    let cost_curency = $('#item_fc_cost_curency_' + row + n).val() || 0;
    fc_cost = parseFloat(cost_curency) * parseFloat(kurs);
    $('#item_fc_cost_' + row + n).val((fc_cost));
    grand_total = parseFloat(fc_cost) + parseFloat(ppn);
    $('#item_total_fc_' + row + n).val((grand_total));
    total_item_fc_cost(row);

})
function material_fc_cost() {
    let total_qty_ship = 0;
    let fc_cost_per_unit = 0;
    let grand_total_fc = 0;
	let bm = 0;
	freight_curs=$("#freight_curs").val();
    $('.price_ref_sup').each(function() {
        price_ref_sup= Number($(this).val() || 0)
    })
    $('.qty_ship').each(function() {
        total_qty_ship += Number($(this).val() || 0)
    })
	ttl_fc_cost=$("#total_fc_cost").val();
	if(total_qty_ship>0){
		fc_cost_per_unit=(parseFloat(ttl_fc_cost)/parseFloat(total_qty_ship));
	}
    $('.fc_cost_unit').each(function() {
        $(this).val(fc_cost_per_unit);
    })
	gtotal_price=0;
	gtotal_price_us=0;
	total_ppn=0;
	gTotal_inc_ppn=0;
	total_price_aft_fc=0;
    $('.nomor').each(function() {
        ids=$(this).val();
		price_ref_sup=$("#price_ref_sup"+ids).val();
		bm=$("#bm"+ids).val();
		price=parseFloat(freight_curs)*parseFloat(price_ref_sup);
		$("#price"+ids).val(price);
		fc_cost_unit=$("#fc_cost_unit"+ids).val();
		price_aft_fc=parseFloat(price)+parseFloat(fc_cost_unit);
		$("#price_aft_fc"+ids).val(price_aft_fc);
		qty_ship=$("#qty_ship"+ids).val();
		gtotal_price_us=parseFloat(gtotal_price_us)+(parseFloat(qty_ship)*parseFloat(price_ref_sup));
		total_price=parseFloat(qty_ship)*parseFloat(price);
		gtotal_price=parseFloat(gtotal_price)+parseFloat(total_price);
		total_fc_costprd=parseFloat(qty_ship)*parseFloat(fc_cost_unit);
		$("#total_price"+ids).val(total_price);		
		$("#total_fc_costprd"+ids).val(total_fc_costprd);
		total_price_fc_cost=parseFloat(total_price)+parseFloat(total_fc_costprd)+parseFloat(bm);
		$("#total_price_fc_cost"+ids).val(total_price_fc_cost);
    })
	$("#gtotal_price").val(gtotal_price);	
	$("#gtotal_price_us").val(gtotal_price_us);
	total_ppn=(parseFloat(gtotal_price)*parseFloat(<?=$tax?>)/100);
	$("#total_ppn").val(total_ppn);
	gTotal_inc_ppn=parseFloat(gtotal_price)+parseFloat(total_ppn)+parseFloat(bm);
	$("#gTotal_inc_ppn").val(gTotal_inc_ppn);
	grand_total_fc_cost=$("#grand_total").val();
	total_price_aft_fc=parseFloat(gTotal_inc_ppn)+parseFloat(grand_total_fc_cost);
	$("#total_price_aft_fc").val(total_price_aft_fc);
}

function total_fc_cost() {
    let total_fc = 0;
    let ppn_fc = 0;
    let grand_total_fc = 0;

    $('.fc_cost').each(function() {
        total_fc += Number($(this).val() || 0)
    })
    $('.ppn_fc').each(function() {
        ppn_fc += Number($(this).val() || 0)
    })

    $('.grand_total_fc').each(function() {
        grand_total_fc += Number($(this).val() || 0)
    })

    $('#total_fc').val((total_fc.toFixed()));
    $('#ppn_fc_cost,.ppn_fc_cost').val((ppn_fc.toFixed()));
    $('#grand_total').val((grand_total_fc.toFixed()));
    $('#total_fc_cost').val((total_fc.toFixed()));
	material_fc_cost();

}

function total_item_fc_cost(row) {
    let cost = 0;
    let ppn = 0;
    let total = 0;

    $('.item_fc_cost_' + row).each(function() {
        cost += Number($(this).val() || 0)
    })
    $('.item_fc_ppn_fc_' + row).each(function() {
        ppn += Number($(this).val() || 0)
    })

    $('.item_total_fc_' + row).each(function() {
        total += Number($(this).val() || 0)
    })

    console.log(row + "," + cost + "," + ppn + "," + total);
    $('#fc_cost__' + row).val((cost.toFixed()));
    $('#ppn__' + row).val((ppn.toFixed()));
    $('#grand_total__' + row).val((total.toFixed()));
    total_fc_cost();
}
$(document).on('click', '.del_item_fc', function() {
    let btn = $(this);
    let row = $(this).data('row');
    $(btn).parents('tr.list-detail').remove();
    total_item_fc_cost(row);
})
    // Detail FC Cost
$(document).on('click', '.add_item_fc', function() {
    let row = $(this).data('id');
    let n = $('#detail_fc_' + row + ' tbody tr.list-detail').length + 1;
    let html = '';
    html += `
      <tr class="list-detail">
        <td>` + n + `
          <input type="hidden" name="dtlFccost[` + row + `][itemFc][` + n + `][id]" value="">
        </td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][item]" class="form-control" placeholder="Item Name" value=""></td>
        <td>
          <select name="dtlFccost[` + row + `][itemFc][` + n + `][curency]" class="form-control">
            <option value=""></option>
			<option value='USD'>USD</option>
			<option value='IDR'>IDR</option>
          </select>
        </td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][cost_curency]" id="item_fc_cost_curency_` + row + n + `" class="form-control cost_curency divide text-right" placeholder="0" value="0" data-id="` + n + `" data-row="` + row + `"></td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][kurs]" id="item_fc_kurs_` + row + n + `" class="form-control nominal divide kurs text-right" placeholder="1" value="1" data-id="` + n + `" data-row="` + row + `"></td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][cost]" id="item_fc_cost_` + row + n + `" class="form-control item_fc_cost_` + row + ` text-right divide" readonly placeholder="0" value="0" data-id="` + n + `" data-row="` + row + `"></td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][ppn]" id="item_fc_ppn_fc_` + row + n + `" class="form-control nominal divide item_fc_ppn_fc_` + row + ` item_ppn_fc text-right" placeholder="0" value="0" data-id="` + n + `" data-row="` + row + `"></td>
        <td><input type="text" name="dtlFccost[` + row + `][itemFc][` + n + `][total]" id="item_total_fc_` + row + n + `" class="form-control item_total_fc_` + row + ` total_item_fc text-right divide" placeholder="0" value="0" readonly data-id="` + n + `" data-row="` + row + `"></td>
        <td class="text-center"><button type="button" class="btn btn-xs text-danger del_item_fc"><i class="fa fa-times-circle"></i></button></td>
      </tr>`;

    $('#detail_fc_' + row + ' tbody').append(html);
    $('.divide').divide();
})
    // for F&C Cost ================================
$(document).on('click', '#addFccost', function() {
    let n = $('table#tbl_dtlFccost tbody.data_fc tr.data_fc').length + 1;
    let html = '';
    html += `
			<tr class="data_fc row_` + n + `">
              <td rowspan="2">` + n + `</td>
              <td>
			  <?php
			  echo form_dropdown("dtlFccost[` + n + `][fwd_name]", $dt_supplier, '0', array('id' => "dtlFccost_` + n + `_fwd_name", 'required' => 'required', 'class' => 'form-control input-sm chosen-select'));
			  ?></td>
              <td colspan="4"><input type="text" class="form-control" name="dtlFccost[` + n + `][remark]" placeholder="Note" value=""></td>
              <td><input type="text" class="form-control text-right divide" name="dtlFccost[` + n + `][weight]" placeholder="0" value="0"></td>
              <td><input type="text" class="form-control text-right divide" name="dtlFccost[` + n + `][volume]" placeholder="0" value="0"></td>
              <td rowspan="2"><a href="javascript:void(0)" data-id="" data-row="` + n + `" class="del_fwdCost"><i class="text-red fa fa-close"></i></a></td>
            </tr>
            <tr class="row_` + n + `">
              <td colspan="7" style="padding: 10px;">
                <div class="contdainer">
                  <table class="table table-bordered table-condensed table-striped detail_item_fc" id="detail_fc_` + n + `" data-id="` + n + `">
                    <thead class="bg-gray">
                      <tr>
                        <th width="20px">No</th>
                        <th>Item</th>
                        <th width="100px">Curency</th>
                        <th width="100px">Cost</th>
                        <th width="100px">Kurs</th>
                        <th width="100px">Cost(Rp)</th>
                        <th width="100px">PPN</th>
                        <th width="120px">Total</th>
                        <th width="">#</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                      <tr>
                        <th><button type="button" class="btn btn-sm btn-warning add_item_fc" data-id="` + n + `">Add Item</button></th>
                        <th colspan="4" class="text-right">Total</th>
                        <th colspan="" class="text-right">
                          <input type="text" name="dtlFccost[` + n + `][cost]" data-id="` + n + `" id="fc_cost__` + n + `" class="form-control text-right fc_cost divide" readonly value="0">
                        </th>
                        <th colspan="" class="text-right">
                          <input type="text" name="dtlFccost[` + n + `][ppn]" data-id="` + n + `" id="ppn__` + n + `" class="form-control text-right ppn_fc divide" readonly value="0">
                        </th>
                        <th colspan="" class="text-right">
                          <input type="text" name="dtlFccost[` + n + `][grand_total]" data-id="` + n + `" id="grand_total__` + n + `" class="form-control text-right grand_total_fc divide" readonly value="0">
                        </th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </td>
            </tr>
			`;
    $('table#tbl_dtlFccost tbody.data_fc').append(html);
    $('.divide').divide();
	$('.chosen-select').chosen();
})
<?php if($tipe=='view') echo '$("#form_proses_bro :input").prop("disabled", true);';?>
</script>
