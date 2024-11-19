<div class="box-body">
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" width='20%'>#</th>
				<th class="text-center">No PR</th>
			</tr>
		</thead>
		<tbody>
			<?php
            if($detail){
				foreach ($detail as $key => $value) { $key++;
                    echo "<tr>";
                        echo "<td class='text-center'>".$key."</td>";
                        echo "<td class='text-center'>".$value['no_pr']."</td>";
                    echo "</tr>";
                }
            }
            else{
                echo "<tr>";
                    echo "<td class='text-left' colspan='2'>Tidak ada data yang ditampilkan</td>";
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