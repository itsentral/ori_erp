
<div class="box box-primary">
	<div class="box-header">

	</div>
	<div class="box-body">
    <div class='form-group row'>
      <label class='label-control col-sm-2'><b>Inventory Type</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'add_category','name'=>'add_category','class'=>'form-control input-md numAlfa','disabled'=>'disabled'),strtoupper($header[0]->cate_awal));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>Status </b></label>
					<?php
					$color		= ($header[0]->status =='1')?'green':'red';
					$label		= ($header[0]->status =='1')?'ACTIVE':'NOT ACTIVE';
				?>
				<div class='col-sm-4'>
					<span class='badge bg-<?=$color;?>'><?=$label?></span>
				</div>
    </div>
    <div class='form-group row'>
      <label class='label-control col-sm-2'><b>Excel Code / ID Accurate</b></label>
      <div class='col-sm-2'>
        <?php
         echo form_input(array('id'=>'kode_excel','name'=>'kode_excel','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Excel Code'),strtoupper($header[0]->kode_excel));
        ?>
      </div>
      <div class='col-sm-2'>
        <?php
         echo form_input(array('id'=>'id_accurate','name'=>'id_accurate','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'ID Accurate'),strtoupper($header[0]->id_accurate));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>Item Code</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'kode_item','name'=>'kode_item','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Item Code'),strtoupper($header[0]->kode_item));
        ?>
      </div>
    </div>
    <div class='form-group row'>
      <label class='label-control col-sm-2'><b>Material Name</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'material_name','name'=>'material_name','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Material Name'),strtoupper($header[0]->material_name));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>Trade Name</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'trade_name','name'=>'trade_name','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Trade Name'),strtoupper($header[0]->trade_name));
        ?>
      </div>
    </div>
    <div class='form-group row'>
      <label class='label-control col-sm-2'><b>Spesification</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'spec','name'=>'spec','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Spesification'),strtoupper($header[0]->spec));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>Brand</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'brand','name'=>'brand','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Brand'),strtoupper($header[0]->brand));
        ?>
      </div>
    </div>
    <div class='form-group row'>
      <label class='label-control col-sm-2'><b>Minimal Order Stock</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'min_order','name'=>'min_order','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Minimal Order Stock'),number_format($header[0]->min_order,2));
        ?>
      </div>
	  <label class='label-control col-sm-2'><b>Lead Time (Day)</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'lead_time','name'=>'lead_time','class'=>'form-control input-md numberFull','disabled'=>'disabled','placeholder'=>'Lead Time'),number_format($header[0]->lead_time,2));
        ?>
      </div>
    </div>
    <div class='form-group row'>
	    <label class='label-control col-sm-2'><b>Satuan</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'satuan','name'=>'satuan','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Note'),get_name('raw_pieces','kode_satuan','id_satuan',$header[0]->satuan));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>No Rak</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'no_rak','name'=>'no_rak','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'No Rak'),strtoupper($header[0]->no_rak));
        ?>
      </div>
    </div>
    <div class='form-group row'>
	    <label class='label-control col-sm-2'><b>Konversi</b></label>
      <div class='col-sm-2'>
        <?php
         echo form_input(array('id'=>'konversi','name'=>'konversi','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Konversi'),number_format($header[0]->konversi,2));
        ?>
      </div>
      <div class='col-sm-2'>
        <?php
         echo form_input(array('id'=>'satuan_konversi','name'=>'satuan_konversi','class'=>'form-control input-md','disabled'=>'disabled'),get_name('raw_pieces','kode_satuan','id_satuan',$header[0]->satuan_konversi));
        ?>
      </div>
      <label class='label-control col-sm-2'><b>Note</b></label>
      <div class='col-sm-4'>
        <?php
         echo form_input(array('id'=>'note','name'=>'note','class'=>'form-control input-md','disabled'=>'disabled','placeholder'=>'Note'),strtoupper($header[0]->note));
        ?>
      </div>
    </div>
	</div>
 </div>

<script>
swal.close();
</script>
