<?php
$ArrSelect['Y']	= 'Active';
$ArrSelect['N']	= 'Not Active';

$id             = (!empty($data[0]->id))?$data[0]->id:'';
$nm_dept        = (!empty($data[0]->project))?$data[0]->project:'';
$nilai        = (!empty($data[0]->nilai))?$data[0]->nilai:'';
$tanggal        = (!empty($data[0]->tanggal))?$data[0]->tanggal:'';
$status         = (!empty($data[0]->status))?$data[0]->status:'Y';
?>
<div class="box box-primary"><br>
    <div class="box-body">
	<div class="form-group row">
            <div class="col-md-3">
                <label>Tanggal</label>
            </div>
            <div class="col-md-9">
                <input type="date" class="form-control" id="tanggal" name="tanggal" placeholder="tanggal" value='<?=$tanggal;?>'>
                
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <label>Project Name</label> 
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control" id="project" name="project" placeholder="Project Name" value='<?=$nm_dept;?>'>
                <input type="hidden" class="form-control" id="id" name="id" value='<?=$id;?>'>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-3">
                <label>Nilai</label>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control" id="nilai" name="nilai" placeholder="nilai" value='<?=$nilai;?>'>
                
            </div>
        </div>
		<div class="form-group row">
			<div class="col-md-3">
                <label>Customer Name</label>
            </div>
           	<div class='col-sm-9'>
				<select name='id_customer' id='id_customer' class='form-control input-sm chosen-select' style='min-width:150px; float:left; margin-bottom: 5px;'>
					<option value='0'>Select An Customer</option>
				 <?php
					foreach($CustList AS $val => $valx){
						echo "<option value='".$valx['id_customer']."'>".$valx['nm_customer']."</option>";
					}
				 ?>
				 </select>
			</div>
        </div>
        <div class="form-group row">
            <div class="col-md-3"></div>
            <div class="col-md-9">
                <button type="button" class="btn btn-primary" name="save" id="save"><i class="fa fa-save"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<script>
    swal.close();
</script>