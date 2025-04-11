<?php
$no_ipp = $this->uri->segment(3);
$old_customer = get_name('production','id_customer','no_ipp',$no_ipp);
$old_customer_nm = get_name('production','nm_customer','no_ipp',$no_ipp);
$ListSeries		= $this->db->order_by('nm_customer','asc')->get_where('customer',array('deleted'=>'N'))->result_array();

?>
<input type="hidden" name='old_customer' id='old_customer' value='<?=$old_customer;?>'>
<div class="box-body">
    <div class='form-group row'>
        <label class='label-control col-sm-4'>Customer Lama</label>
        <div class='col-sm-8'><?=strtoupper($old_customer_nm);?></div>
    </div>
    <div class='form-group row'>
        <label class='label-control col-sm-4'>Customer Baru</label>
        <div class='col-sm-8'>
            <select name='new_customer' id='new_customer' class='form-control input-md chosen_select'>
                <?php
                foreach($ListSeries AS $valX => $valxX){
                    $selx	= ($old_customer == $valxX['id_customer'])?'selected':''; 
                    echo "<option value='".$valxX['id_customer']."' ".$selx.">".strtoupper($valxX['nm_customer'])."</option>";
                }
                ?> 
            </select>
        </div>
    </div>
    <div class='form-group row'>
        <label class='label-control col-sm-4'></label>
        <div class='col-sm-8'><button type='button' id='change_customer' data-no_ipp='<?=$no_ipp;?>' class='btn btn-sm btn-success'>Update</button></div>
    </div>
</div>

<script>
	swal.close();
	$(document).ready(function(){
		$('.chosen_select').chosen({width: '100%'});
	});
</script>