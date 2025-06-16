<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses" enctype="multipart/form-data" autocomplete="off">
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<a href="<?php echo site_url($this->uri->segment(1).'/'.$this->uri->segment(4)) ?>" class="btn btn-md btn-danger">Back</a>
		</div>
	</div>
    <?php
        echo form_input(array('type'=>'hidden','id'=>'no_ipp','name'=>'no_ipp','class'=>'form-control input-md','readonly'=>'readonly'),str_replace('BQ-','',$id_bq));
    ?>
    <div class="box-body">
        <div class='form-group row'>
            <div class='col-sm-12'>
                <table style='width:100%'>
                    <tr>
                        <th width='15%'>IPP Number</th>
                        <td width='1%'>:</td>
                        <td><?=$no_ipp;?></td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td>:</td>
                        <td><?=$nm_customer;?></td>
                    </tr>
                    <tr>
                        <th>Project Name</th>
                        <td>:</td>
                        <td><?=$nm_project;?></td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- <select id="cb_info" class="cb_bu_info" style='width:100%'></select> -->
        <div class="tab-content table-responsive">
        <table class="table table-striped table-bordered table-hover table-condensed" width="100%">
            <thead id='head_table'>
                <tr class='bg-blue'>
                    <th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
                    <th class="text-center" style='vertical-align:middle;' width='50%'>Material Name</th>
                    <!-- <th class="text-center" style='vertical-align:middle;'>Material Name</th> -->
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Estimasi</th>
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Tot Request</th>
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Max Request</th>
                    <th class="text-center" style='vertical-align:middle;' width='5%'>Unit</th>
                    <th class="text-center" style='vertical-align:middle;' width='10%'>Request</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $Total1 = 0;
                $No=0;

                foreach($result_aksesoris AS $val => $valx){
                    $No++;
                    
                    $qty    = $valx['qty'];
                    $satuan = $valx['satuan'];
                    if($valx['category'] == 'plate'){
                        $qty    = $valx['berat'];
                        $satuan = '1';
                    }

                    $qty_req = $valx['qty_req'];
                    $id_material = $valx['id_material'];
                    if($tandaTanki == 'IPPT'){
                        $id_material = (!empty($valx['id_material']))?$valx['id_material']:$valx['id_material_tanki'];
                    }
                    echo "<tr>";
                        echo "<td align='center'>".$No."
                                <input type='hidden' name='add[".$No."][id]' value='".$valx['id']."'>
                                <input type='hidden' name='add[".$No."][id_material]' value='".$valx['id_material']."'>
                                </td>";
                        // echo "<td>".get_name_acc($valx['id_material'])."</td>";
                        echo "<td>";
                        // echo "<select name='add[".$No."][id_material2]' class='form-control chosen-select'>";
                        //     foreach ($list_aksesoris as $key => $value) {
                        //         $selected = ($valx['id_material'] == $value['id'])?'selected':'';
                        //         echo "<option value='".$value['id']."' ".$selected.">".$value['nama'].", ".$value['spsifikasi'].", ".$value['material']."</option>";
                        //     }
                        // echo "</select>";
                        // if($tandaTanki != 'IPPT'){
                            $ArrSelect = [];
                            if(!empty($id_material)){
                                $ArrSelect[$id_material] = strtoupper(get_name_accessories($id_material));
                            }
                            echo form_dropdown("add[".$No."][id_material2]",$ArrSelect, $id_material, array('class'=>'cb_bu_info'));
                        // }
                        // else{
                        //     echo  $ArrSelect[$valx['id_material']];
                        // }
                        echo "</td>";
                        // echo "<td>".strtoupper(get_name('accessories','material','id',$valx['id_material']))."</td>";
                        echo "<td align='right' class='text-primary text-bold'>".number_format($qty,2)."</td>";
                        echo "<td align='right' class='text-success text-bold'>".number_format($qty_req,2)."</td>";
                        echo "<td align='right' class='text-danger text-bold' id='maxRequest".$No."'>".number_format($qty-$qty_req,2)."</td>";
                        echo "<td align='left'>".ucwords(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan)))."</td>";
                        echo "<td align='right'><input type='text' name='add[".$No."][request]' data-no='".$No."' class='form-control input-sm text-center autoNumeric2 requestQty'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </div>
    <div class="box-footer">
        <?php
            echo form_button(array('type'=>'button','class'=>'btn btn-md btn-primary','content'=>'Request','id'=>'btnRequest'));
        ?>
    </div>
</div>
</form>
<?php $this->load->view('include/footer'); ?>
<style>
	.tgl{
		cursor:pointer;
	}
</style>
<script>
	$(document).ready(function(){
        swal.close();
        $('select').removeClass('chosen-select');
		$('.autoNumeric2').autoNumeric();
		// $('.chosen-select').chosen({width:'100%'});

        $(document).on('keyup','.requestQty', function(){
            var nomor   = $(this).data('no');
            var max     = getNum($('#maxRequest'+nomor).text().split(",").join(""));
            var request = getNum($(this).val().split(",").join(""));

            if(request > max){
                $(this).val(max)
            }
        });

        // var url = "https://jsonplaceholder.typicode.com/posts";
		var url = base_url+'api/getDataAccessories'

        $(".cb_bu_info").select2({
			minimumInputLength: 3,
            theme: "classic",
            width:'100%',
			tags: true,
			ajax: {
				url: url,
				dataType: 'json',
				type: "GET",
				quietMillis: 50,
				data: function (term) {
				    return {
				        term: term
				    };
				},
				processResults: function (data) {
					console.log(data)
					return {
						results: $.map(data, function (item) {
							return {
								text: item.title,
								// slug: item.slug,
								id: item.id
							}
						})
					};
				}
			}
		});

       

    });

    $(document).on('click', '#btnRequest', function(e){
		e.preventDefault();
        let pageFirst = '<?=$this->uri->segment(4);?>'
		swal({
			title: "Are you sure?",
			text: "You will not be able to process again this data!",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn-danger",
			confirmButtonText: "Yes, Process it!",
			cancelButtonText: "No, cancel process!",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				var formData  	= new FormData($('#form_proses')[0]);
				$.ajax({
					url			: base_url + active_controller+'/add',
					type		: "POST",
					data		: formData,
					cache		: false,
					dataType	: 'json',
					processData	: false,
					contentType	: false,
					success		: function(data){
						if(data.status == 1){
							swal({
									title	: "Save Success!",
									text	: data.pesan,
									type	: "success",
									timer	: 7000
								});
							window.location.href = base_url + active_controller+'/'+pageFirst;
						}
						else if(data.status == 0){
							swal({
								title	: "Save Failed!",
								text	: data.pesan,
								type	: "warning",
								timer	: 7000
							});
						}
					},
					error: function() {
						swal({
							title   : "Error Message !",
							text    : 'An Error Occured During Process. Please try again..',
							type    : "warning",
							timer   : 7000
						});
					}
				});
			} else {
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});

    function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

</script>
