<?php

$listCode	= "SELECT * FROM help_default_name ORDER BY nm_default";
$getDef		= $this->db->query($listCode)->result_array();

$getKomp		= $this->db->query("SELECT * FROM product_parent ORDER BY product_parent ASC")->result_array();
$getPN		= $this->db->query("SELECT * FROM list_help WHERE group_by = 'pressure' ORDER BY name ASC")->result_array();
$getLiner		= $this->db->query("SELECT * FROM list_help WHERE group_by = 'liner' ORDER BY name ASC")->result_array();
$getStep		= $this->db->query("SELECT * FROM cycletime_step")->result_array();


?>
<div class="box box-success">
	<div class="box-body" style="">
		<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
			<tbody>
				<tr style='background-color: #175477; color: white; font-size: 15px;'>
					<th class="text-center" colspan='3'>HEADER</th>
				</tr>
				<tr>
					<th class="text-left" width='34%'>Set</th>
					<th class="text-left" width='66%'>Value</th>
				</tr>
				<tr>
					<td class="text-left vMid">Machine <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'nm_mesin','name'=>'nm_mesin','class'=>'form-control input-sm inSpL','placeholder'=>'Machine Name','autocomplete'=>'off'));
						?>
					</td>
				</tr>
        <tr>
					<td class="text-left vMid">Capacity <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'capacity','name'=>'capacity','class'=>'form-control input-sm inSp','placeholder'=>'Capacity','autocomplete'=>'off'));
						?>
					</td>
				</tr>
        <tr>
					<td class="text-left vMid">Unit <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'unit','name'=>'unit','class'=>'form-control input-sm inSp','placeholder'=>'Unit(mm,cm,m,km)','autocomplete'=>'off'));
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Machine Price <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'machine_price','name'=>'machine_price','class'=>'form-control input-sm inSp numberOnly','placeholder'=>'Machine Price','autocomplete'=>'off'));
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Utilization Estimation(Year) <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'utilization_estimate','name'=>'utilization_estimate','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Utilization Estimation(Year)','autocomplete'=>'off'));
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Depresiation/Month <span class='text-red'>*</span></b></td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'depresiation_per_month','name'=>'depresiation_per_month','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Depresiation/Month','autocomplete'=>'off'));
						?>
					</td>
				</tr>
				<tr>
					<td class="text-left vMid">Machine Cost/Hour</td>
					<td class="text-left">
						<?php
							echo form_input(array('type'=>'text','id'=>'machine_cost_per_hour','name'=>'machine_cost_per_hour','class'=>'form-control input-sm inSpL numberOnly','placeholder'=>'Machine Cost/Hour','autocomplete'=>'off'));
						?>
					</td>
				</tr>


			</tbody>
		</table>
		<br>

		<?php
			echo form_button(array('type'=>'button','class'=>'btn btn-md btn-success','style'=>'min-width:100px; float:right;','value'=>'save','content'=>'Save','id'=>'addDefaultSave')).' ';
		?>
	</div>
</div>

<style>
	.inSp{
		text-align: center;
		display: inline-block;
		width: 100px;
	}
	.inSp2{
		text-align: center;
		display: inline-block;
		width: 80px;
	}
	.inSpL{
		text-align: left;
	}
	.vMid{
		vertical-align: middle !important;
	}

</style>

<script>
	$(".numberOnly").on("keypress keyup blur",function (event) {
		if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
			event.preventDefault();
		}
	});

	function getNum(val) {
	   if (isNaN(val) || val == '') {
		 return 0;
	   }
	   return parseFloat(val);
	}
	$(document).ready(function() {

    //ADD LIST BUTTON
    $('#addS').click(function(e){
      //console.log('a');
      var x = parseInt(document.getElementById("tbody-detail").rows.length)+1;
      var row = '<tr class="addjs">'+
            '<td style="background-color:#E0E0E0">'+
            '<a class="text-red hapus_item_js" href="javascript:void(0)" title="Hapus Item"><i class="fa fa-times"></i></a> || '+
            'Step '+x+
            '</td>'+
            '<td style="background-color:#E0E0E0;text-align:center">'+
            //'<input type="text" name="step[]"  class="form-control input-sm inSp2 " required="" placeholder="Input Step">'+
						'<select name="step[]" id="step[]" class="chosen_select form-control inline-block select_step">'+
							'<option value="0">Select Step</option>'+
						'<?php foreach($getStep AS $val => $valx){ ?>'+
								'<option value="<?=$valx["step_name"]?>"><?=strtoupper($valx["step_name"])?></option>'+
						'<?php } ?>'+
						'</select>'+
						'<button type="button" id="addStep" style="font-weight: bold; font-size: 12px; margin-top: 5px; color: #175477;">Add Standart Step</button>'+

            '</td>'+
        '</tr>'
        //console.log(document.getElementById("tbody").rows[0].column[0].text);
      $('#detail-step tbody').append(row);
      /*$.ajax({
        url: siteurl+"purchaseorder/purchaseorder_pusat/konfirmasi_save",
        dataType : "json",
        type: 'POST',
        data: formdata,
        success: function(result){
          //console.log(result['msg']);
          if(result.save=='1'){
            swal({
              title: "Sukses!",
              text: result['msg'],
              type: "success",
              timer: 1500,
              showConfirmButton: false
            });
            setTimeout(function(){
              window.location.href=siteurl+'purchaseorder/purchaseorder_pusat';
            },1600);
          } else {
            swal({
              title: "Gagal!",
              text: result['msg'],
              type: "error",
              timer: 1500,
              showConfirmButton: false
            });
          };
        },
        error: function (request, error) {
          console.log(arguments);
          alert(" Can't do because: " + error);
        }
      });*/
      //$('#tbody').find('tr')
    });

    //REMOVE LIST BUTTON
    $('#detail-step tbody').on( 'click', 'a.hapus_item_js', function () {
			console.log('a');
      $(this).parents('tr').remove();

    } );
  } );

</script>
