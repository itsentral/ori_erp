<?php
$this->load->view('include/side_menu');

//Liner
$ArrCostcenter = array();
$ArrCostcenter[0]	= 'Select An Costcenter';
foreach($costcenter AS $val => $valx){
	$ArrCostcenter[$valx['id']] = strtoupper(strtolower($valx['nm_costcenter']));
}

$id_costcenter 	= (!empty($get))?$get[0]->id_costcenter:'0';
?>
<form action="#" method="POST" id="form_ct" enctype="multipart/form-data" autocomplete='off'> 
<input type='hidden' name='uri' id='uri' value='<?=$uri;?>'>
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
            
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
        <div class="form-group row">
            <div class="col-md-2">
                <label for="customer">Costcenter <span class='text-red'>*</span></label>
            </div>
            <div class="col-md-4">
                <?php
                    echo form_dropdown('id_costcenter', $ArrCostcenter, $id_costcenter, array('id'=>'id_costcenter','class'=>'form-control input-md chosen-select'));
                ?>
            </div>
        </div>
        
        <table class='table table-striped table-bordered table-hover table-condensed' width='100%'>
            <thead>
                <tr class='bg-blue'>
                    <th class='text-center mid'>Product</th>
                    <th class='text-center mid' width='6%'>DN1</th>
                    <th class='text-center mid' width='6%'>DN2</th>
                    <th class='text-center mid' width='6%'>Sudut</th>
                    <th class='text-center mid' width='6%'>SR/LR</th>
					<th class='text-center mid' width='6%'>PN</th>
					<th class='text-center mid' width='6%'>Liner</th>
                    <th class='text-center mid' width='6%'>MP</th>
                    <th class='text-center mid' width='16%'>Mesin</th>
                    <th class='text-center mid' width='6%'>Time Process</th>
					<th class='text-center mid' width='6%'>Curing Time</th>
					<th class='text-center mid' width='6%'>Total Time</th>
					<th class='text-center mid' width='6%'>Man Hours</th>
				</tr>
            </thead>
            <tbody>
				<?php
				$id = 0;
				if(!empty($data)){
					foreach($data AS $val2 => $valx2){ $id++;
						$dn1 			= (!empty($valx2['dn1']))?number_format($valx2['dn1']):'';
						$dn2 			= (!empty($valx2['dn2']))?number_format($valx2['dn2']):'-';
						$sudut 			= (!empty($valx2['sudut']))?number_format($valx2['sudut']):'-';
						$srlr 			= ($valx2['srlr'] != '0')?$valx2['srlr']:'-';
						$man_power 		= (!empty($valx2['man_power']))?number_format($valx2['man_power']):'-';
						$time_process 	= (!empty($valx2['time_process']))?number_format($valx2['time_process'],2):'';
						$curing_time 	= (!empty($valx2['curing_time']))?number_format($valx2['curing_time'],2):'';
						$total_time 	= (!empty($valx2['total_time']))?number_format($valx2['total_time'],2):'';
						$man_hours 		= (!empty($valx2['man_hours']))?number_format($valx2['man_hours'],2):'';
						echo "<tr class='header_".$id."'>";
							echo "<td align='left'>".strtoupper($valx2['product'])."</td>";
							echo "<td align='right'>".$dn1."</td>";
							echo "<td align='right'>".$dn2."</td>";
							echo "<td align='right'>".$sudut."</td>";
							echo "<td align='center'>".$valx2['srlr']."</td>";
							echo "<td align='left'>PN ".$valx2['pn']."</td>";
							echo "<td align='center'>".$valx2['liner']."</td>";
							echo "<td align='center'>".$man_power."</td>";
							echo "<td align='left'>".strtoupper(get_name('machine','nm_mesin','no_mesin',$valx2['mesin']))."</td>";
							echo "<td align='right'>".$time_process."</td>";
							echo "<td align='right'>".$curing_time."</td>";
							echo "<td align='right'>".$total_time."</td>";
							echo "<td align='right'>".$man_hours."</td>";
						echo "</tr>";
					}
				}
				?>
            </tbody>
        </table>
        <div class='box-footer'>
        <?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-danger','style'=>'float:right; margin-left:5px;','id'=>'back','content'=>'Back'));
        ?>
        </div>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->

</form>
<?php $this->load->view('include/footer'); ?>
<style type="text/css">
	.chosen-container-active .chosen-single {
	     border: none;
	     box-shadow: none;
	}
	.chosen-container-single .chosen-single {
		height: 34px;
	    border: 1px solid #d2d6de;
	    border-radius: 0px;
	     background: none;
	    box-shadow: none;
	    color: #444;
	    line-height: 32px;
	}
	.chosen-container-single .chosen-single div{
		top: 5px;
	}
	.mid{
		vertical-align: middle !important;
	}
</style>
<script>
	$(document).on('click', '#back', function(e){
		window.location.href = base_url + active_controller;
	});
</script>
