
<div class="nav-tabs-salesorder">
    <div class="tab-content">
        <div class="tab-pane active" id="salesorder">
            <div class="box box-primary">
                <div class="box-body">                    
                    <table class="table table-bordered table-striped" id="my-grid" width='100%'>
                        <thead>
                            <tr class='bg-blue'>
                                <th class="text-center" width='4%'>#</th>
                                <th class="text-center" width='8%'>No SPK</th>
                                <th class="text-center" width='8%'>Id Trans</th>
                                <th class="text-center" width='8%'>Product</th>
                                <th class="text-center" width='8%'>Kode Trans</th>
                                <th class="text-center" width='8%'>No Delivery</th>
                                <th class="text-center" width='12%'>Cogs</th>
                                <th class="text-center" width='12%'>Material</th>
                                <th class="text-center" width='12%'>Direct</th>
                                <th class="text-center" width='12%'>Indirect</th>
                                <th class="text-center" width='12%'>Consumable</th>
                                <th class="text-center" width='12%'>FOH</th>
                            </tr>
                        </thead>
				       	<tbody>
                            <?php
                            $numb=0;
                            
                            $no = 0;
                            foreach($getDetail AS $val => $valx){                               
                                $no++;
                                $numb++;                                                         
                                ?>
                                <tr id='tr_<?= $numb;?>' >
                                <td align='center'><?=$no;?></td>
                                <td ><?= strtoupper($valx['no_spk']);?></td>
                                <td ><?= strtoupper($valx['id_trans']);?></td>
                                <td ><?= strtoupper($valx['product']);?></td>
                                <td ><?= strtoupper($valx['kode_trans']);?></td>
                                <td ><?= strtoupper($valx['kode_delivery']);?></td>
                                 <td align='right'><?= number_format($valx['nilai_unit']);?></td>
                                <td align='right'><?= number_format($valx['material']);?></td>
                                <td align='right'><?= number_format($valx['wip_direct']);?></td>
                                <td align='right'><?= number_format($valx['wip_indirect']);?></td>
                                <td align='right'><?= number_format($valx['wip_consumable']);?></td>
                                <td align='right'><?= number_format($valx['wip_foh']);?></td>
                                <?php
                               
                            }
                            ?>
                        </tbody>
			    </table>
<style>
.HeaderHr{
	background-color: #ce4c00;
    color: white;
}

.bg-bluexyz{
	background-color: #05b3a3 !important;
	color : white;
}
</style>
