<?php
$id_produksi = $this->uri->segment(3);

$qSupplier 	= "	SELECT * FROM production_header WHERE id_produksi = '".$id_produksi."' ";
$row	= $this->db->query($qSupplier)->result_array();

$qDetail	= "	SELECT 
					a.*, 
					b.nm_product,
					COUNT(a.id_product) AS qtyx,
					c.delivery_name
				FROM 
					production_detail a 
					LEFT JOIN product_header b ON a.id_product=b.id_product
					LEFT JOIN delivery c ON a.id_delivery=c.id_delivery 
				WHERE 
					a.id_produksi = '".$id_produksi."' 
				GROUP BY 
					a.id_delivery, 
					a.id_product 
				ORDER BY
					a.id_delivery ASC";
$rowD	= $this->db->query($qDetail)->result_array();
// echo $qDetail;
// echo $qDetail;
// print_r($rowD);
// echo "</pre>";

?>

<div class="box-body">
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>IPP Number</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'nm_customer','name'=>'nm_customer','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['no_ipp']);
				echo form_input(array('type'=>'hidden','id'=>'id_produksi','name'=>'id_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Plane Name','style'=>'text-transform:uppercase', 'readonly'=>'readonly'), $row[0]['id_produksi']);
			?>				
		</div>
		<label class='label-control col-sm-2'><b>Machine</b></label>
		<div class='col-sm-4'>
			<?php
				echo form_input(array('id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Machine Name', 'readonly'=>'readonly'), $row[0]['nm_mesin']);
			?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Plant Start Production</b></label>
		<div class='col-sm-4'>
		<?php
			echo form_input(array('id'=>'plan_start_produksi','name'=>'plan_start_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_start_produksi'])));
		?>
		</div>
		<label class='label-control col-sm-2'><b>Plant End Production</b></label>
		<div class='col-sm-4'>
		<?php
			echo form_input(array('id'=>'plan_end_produksi','name'=>'plan_end_produksi','class'=>'form-control input-md','autocomplete'=>'off','placeholder'=>'Initial Name','readonly'=>'readonly'), date('d F Y', strtotime($row[0]['plan_end_produksi'])));
		?>
		</div>
	</div>
	<div class='form-group row'>
		<label class='label-control col-sm-2'><b>Information</b></label>
		<div class='col-sm-4'>
			 <?php
				// echo form_hidden('id',$row[0]->kode_divisi);
				echo form_textarea(array('id'=>'ket','name'=>'ket','class'=>'form-control input-md','rows'=>'3','cols'=>'75','placeholder'=>'Address company plants', 'readonly'=>'readonly'), ucfirst(strtolower($row[0]['ket'])));
			?>
		</div>
	</div>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" class="no-sort" width="75px">No</th>
				<th class="text-center" style='width: 250px;'>Product Delivery</th>
				<th class="text-center" style='width: 250px;'>Product Type</th>
				<th class="text-center">Product Name</th>
				<th class="text-center" style='width: 150px;'>Qty</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$a=0;
				foreach($rowD AS $val => $valx){
					$a++;
					echo "<tr>";
						echo "<td align='center'>".$a."</td>";
						echo "<td>".strtoupper($valx['delivery_name'])."</td>";
						echo "<td>".strtoupper($valx['id_category'])."</td>";
						echo "<td>".$valx['nm_product']."</td>";
						echo "<td align='center'><span class='badge bg-orange'>".$valx['qty']."</span></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>
</div>