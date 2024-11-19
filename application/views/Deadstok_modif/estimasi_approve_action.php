
<form action="#" method="POST" id="form_proses_bro">   
	<div class="box box-primary">
		<div class="box-body">
            <div class='form-group row'>
                <div class="col-md-12">
                    <table width='80%'>
                        <tr>
                            <td width='20%'>Sales Order</td>
                            <td width='1%'>:</td>
                            <td><?=$HeaderDeadstok[0]['no_so'];?></td>
                        </tr>
                        <tr>
                            <td>IPP</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['no_ipp'];?></td>
                        </tr>
                        <tr>
                            <td>No SPK</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['no_spk'];?></td>
                        </tr>
                        <tr>
                            <td>Product Deadstok</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['product_name'];?></td>
                        </tr>
                        <tr>
                            <td>Spec Deadstok</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['product_spec'];?></td>
                        </tr>
                        <tr>
                            <td>Qty</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['qty'];?></td>
                        </tr>
                        <tr>
                            <td>Proses</td>
                            <td>:</td>
                            <td><?=$HeaderDeadstok[0]['proses'];?></td>
                        </tr>
                    </table>
                </div>
            </div>
			<!-- ====================================================================================================== -->
			<!-- ============================================LINER THICKNESS=========================================== -->
			<!-- ====================================================================================================== -->
			<?php if(!empty($detLiner) OR !empty($detLinerPlus) OR !empty($detLinerAdd)){?>
                <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
                    <head>
                        <tr class='bg-blue'>
                            <th class="text-left" colspan='3'>LINER THIKNESS / CB</th>
                        </tr>
                        <tr class='bg-blue'>
                            <th class="text-left" width='15%'>Type</th>
                            <th class="text-left">Material</th>
                            <th class="text-right" width='8%'>Berat</th>
                        </tr>
                    </head>
                    <tbody>
                        <?php
                            foreach($detLiner AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                            foreach($detLinerPlus AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                            foreach($detLinerAdd AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_category']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
			<?php } ?>
			
			<?php if(!empty($detStructure) OR !empty($detStructurePlus) OR !empty($detStructureAdd)){?>
                <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%"> 
                    <head>
                        <tr class='bg-blue'>
                            <th class="text-left" colspan='3'>STRUCTURE THIKNESS</th>
                        </tr>
                        <tr class='bg-blue'>
                            <th class="text-left" width='15%'>Type</th>
                            <th class="text-left">Material</th>
                            <th class="text-right" width='8%'>Berat</th>
                        </tr>
                    </head>
                    <tbody>
                    <?php
                        $no=0;
                        foreach($detStructure AS $val => $valx){
                            $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                            $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                            ?>
                            <tr>
                                <td><?= $DataCategory[0]['category'];?></td>
                                <td><?= $DataMaterial[0]['nm_material'];?></td>
                                <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                            </tr>
                            <?php
                        }
                        foreach($detStructurePlus AS $val => $valx){
                            $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                            $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                            ?>
                            <tr>
                                <td><?= $DataCategory[0]['category'];?></td>
                                <td><?= $DataMaterial[0]['nm_material'];?></td>
                                <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                            </tr>
                            <?php
                        }
                        foreach($detStructureAdd AS $val => $valx){
                            $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_category']))->result_array();
                            $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                            ?>
                            <tr>
                                <td><?= $DataCategory[0]['category'];?></td>
                                <td><?= $DataMaterial[0]['nm_material'];?></td>
                                <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                            </tr>
                            <?php
                            }
                        ?>
                    </tbody>
                </table>
			<?php } ?>
			
            <?php if(!empty($detEksternal) OR !empty($detEksternalPlus) OR !empty($detEksternalAdd)){?>
                <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
                    <head>
                        <tr class='bg-blue'>
                            <th class="text-left" colspan='3'>EXTERNAL THIKNESS</th>
                        </tr>
                        <tr class='bg-blue'>
                            <th class="text-left" width='15%'>Type</th>
                            <th class="text-left">Material</th>
                            <th class="text-right" width='8%'>Berat</th>
                        </tr>
                    </head>
                    <tbody>
                        <?php
                            $no=0;
                            foreach($detEksternal AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                            foreach($detEksternalPlus AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                        
                            foreach($detEksternalAdd AS $val => $valx){
                                $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_category']))->result_array();
                                $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                                ?>
                                <tr>
                                    <td><?= $DataCategory[0]['category'];?></td>
                                    <td><?= $DataMaterial[0]['nm_material'];?></td>
                                    <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                </table>
            <?php } ?>
            
            <?php if(!empty($detTopPlus) OR !empty($detTopAdd)){?>
                <table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
                    <head>
                        <tr class='bg-blue'>
                            <th class="text-left" colspan='3'>TOPCOAT</th>
                        </tr>
                        <tr class='bg-blue'>
                            <th class="text-left" width='15%'>Type</th>
                            <th class="text-left">Material</th>
                            <th class="text-right" width='8%'>Berat</th>
                        </tr>
                    </head>
                    <tbody>
                    <?php
                        foreach($detTopPlus AS $val => $valx){
                            $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_ori']))->result_array();
                            $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                            ?>
                            <tr>
                                <td><?= $DataCategory[0]['category'];?></td>
                                <td><?= $DataMaterial[0]['nm_material'];?></td>
                                <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                            </tr>
                            <?php
                        }
                        foreach($detTopAdd AS $val => $valx){
                            $DataCategory	= $this->db->get_where('raw_categories', array('id_category'=>$valx['id_category']))->result_array();
                            $DataMaterial	= $this->db->get_where('raw_materials', array('id_material'=>$valx['id_material']))->result_array();
                            ?>
                            <tr>
                                <td><?= $DataCategory[0]['category'];?></td>
                                <td><?= $DataMaterial[0]['nm_material'];?></td>
                                <td align='right'><?= number_format($valx['last_cost'],5);?></td>
                            </tr>
                            <?php
                        }
                    ?>
                    </tbody>
                </table>
            <?php } ?>

            <hr>
            <div class="form-group row">
                <div class='col-sm-4 '>
                <label class='label-control'>Approve Action</label>
                <select name='action' id='action' class='form-control input-md'>
                        <option value='Y'>APPROVE</option>
                        <option value='N'>REVISI</option>
                    </select>
                    <?php
                    echo form_input(array('type'=>'hidden','id'=>'kode','name'=>'kode'),$kode);
                    ?>
                </div>
                <div class='col-sm-8 '>
                    <div>
                        <label class='label-control'>Reject Reason</label>          
                        <?php
                            echo form_textarea(array('id'=>'reason','name'=>'reason','class'=>'form-control input-md', 'cols'=>'75','rows'=>'3','autocomplete'=>'off','placeholder'=>'Reject reason'));
                        ?>		
                    </div>
                </div>
            </div>
            <?php
                echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right; margin: 0px 0px 5px 0px;','value'=>'Process','content'=>'Process','id'=>'approve'));
            ?>
        </div>	
	</div>		
</form>
<script>	

	$(document).ready(function(){
        swal.close()
    });
</script>
