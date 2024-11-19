

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Nama Customer</b></label>
		<div class='col-sm-4'><?=$nm_customer;?></div>
	</div>
	<table class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='3%'>#</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Kode</th>
				<th class="text-center" style='vertical-align:middle;'>Accessories Name</th>
				<th class="text-center" style='vertical-align:middle;' width='9%'>Stock</th>
			</tr>
		</thead>
		<tbody>
			<?php
            $Total1 = 0;
            $No=0;
            foreach($result_aksesoris AS $val => $valx){
                $No++;
                echo "<tr>";
                    echo "<td align='center'>".$No."</td>";
                    echo "<td align='center'>".$valx['code_group']."</td>";
                    echo "<td>".get_name_acc($valx['id'])."</td>";
                    echo "<td align='right'>".number_format($valx['stock'],2)."</td>";
                echo "</tr>";
            }
			?>
		</tbody>
	</table>
</div>
<script>
	$(document).ready(function(){
        swal.close();
    });
</script>
