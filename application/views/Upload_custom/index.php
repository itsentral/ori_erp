<?php
$this->load->view('include/side_menu'); 
?>    
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3><br> 
		<div class="box-tool pull-right">
            <?php
            if($akses_menu['create']=='1'){
            ?>
                <a href="<?php echo site_url('upload_custom/upload') ?>" class="btn btn-sm btn-success" style='float:right;' id='btn-add'>
                    <i class="fa fa-plus"></i> &nbsp;&nbsp;Upload
                </a>
            <?php
            }
            ?>
			<br><br><label>Search : </label>
			<select id='series' name='series' class='form-control input-sm' style='max-width:120px;'>
				<option value='0'>All Series</option>
				<?php
					foreach($listseries AS $val => $valx){
						echo "<option value='".$valx['kode_group']."'>".strtoupper($valx['kode_group'])."</option>";
					}
				?>
			</select>
			<select id='diameter' name='diameter' class='form-control input-sm' style='max-width:120px;'>
				<option value='0'>All Diamater 1</option>
				<?php
					foreach($diameter AS $val => $valx){
						echo "<option value='".$valx['value_d']."'> DN ".strtoupper($valx['value_d'])."</option>";
					}
				?>
			</select>
			<select id='diameter2' name='diameter2' class='form-control input-sm' style='max-width:120px;'>
				<option value='0'>All Diamater 2</option>
				<?php
					foreach($diameter2 AS $val => $valx){
						echo "<option value='".$valx['value_d2']."'> DN ".strtoupper($valx['value_d2'])."</option>";
					}
				?>
			</select>
			<?php
			if(empty($this->uri->segment(3))){
			?>
			<select id='komponen' name='komponen' class='form-control input-sm' style='max-width:220px;'>
				<option value='0'>All Component</option>
				<?php
					foreach($listkomponen AS $val => $valx){
						echo "<option value='".$valx['product_parent']."'>".strtoupper($valx['product_parent'])."</option>"; 
					}
				?>
			</select> 
			<?php
			}
			?>
			<select id='cust' name='cust' class='form-control input-sm' style='max-width:400px;'>
				<option value='0'>All Customer</option>
				<?php
					foreach($cust AS $val => $valx){
						echo "<option value='".$valx['id_customer']."'>".strtoupper($valx['nm_customer'])."</option>";
					}
				?>
			</select>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table id="example1" width='100%' class="table table-bordered table-striped">
			<thead>
				<tr class='bg-blue'>
					<th class="text-center" width='3%'>#</th>
					<th class="text-center" width='22%'>Product ID</th>
					<th class="text-center">Customer</th>
					<th class="text-center">Product</th>
					<th class="text-center  no-sort" width='10%'>Spesifikasi</th>
					<th class="text-center" width='5%'>Stifness</th>
					<th class="text-center" width='6%'>Est</th>
					<th class="text-center  no-sort" width='3%'>Rev</th>
					<th class="text-center" width='6%'>Update</th>
					<th class="text-center" width='6%'>Weight</th>
                    <th class="text-center  no-sort" width='9%'>Status</th>
					<th class="text-center  no-sort" width='13%'>Option</th> 
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView">
		<div class="modal-dialog"  style='width:95%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title"></h4>
					</div>
					<div class="modal-body" id="view">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->

<?php $this->load->view('include/footer'); ?>
<style>
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
</style>
<script>
	$(document).ready(function(){
		
		$(document).ready(function(){
			var series = $('#series').val();
			var komponen = $('#komponen').val();
			var cust = $('#cust').val();
			var diameter = $('#diameter').val();
			var diameter2 = $('#diameter2').val();
			DataTables(series, komponen, cust, diameter, diameter2); 
		});
		
		$(document).on('change','#series, #komponen, #cust, #diameter, #diameter2', function(e){
			e.preventDefault();
			var series = $('#series').val();
			var komponen = $('#komponen').val();
			var cust = $('#cust').val();
			var diameter = $('#diameter').val();
			var diameter2 = $('#diameter2').val();
			DataTables(series, komponen, cust, diameter, diameter2); 
		});
		
		$('#btn-add').click(function(){
			loading_spinner();
		});
		
		
		$('#printSPK').click(function(e){
			e.preventDefault();
			var id_product	= $(this).data('id_product');
			
			var Links		= base_url +'index.php/'+ active_controller+'/printSPK/'+id_product;
			window.open(Links,'_blank');
		});
		
		$(document).on('click', '.MatDetail', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/component/modalDetail/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});

		$(document).on('click', '.mat_weight', function(e){
			e.preventDefault();
			loading_spinner();
			$("#head_title").html("<b>DETAIL ESTIMATION ["+$(this).data('id_product')+"]</b>");
			$("#view").load(base_url +'index.php/component/modalWeight/'+$(this).data('id_product'));
			$("#ModalView").modal();
		});
		
		$(document).on('click', '#del_type', function(){
			var bF	= $(this).data('idcategory');
			// alert(bF);
			// return false;
			swal({
			  title: "Apakah anda yakin ?",
			  text: "Data akan terhapus secara Permanen !!!",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			   confirmButtonText: "Ya, Lanjutkan !",
			  cancelButtonText: "Tidak, Batalkan !",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					$.ajax({
						url			: base_url+'index.php/'+active_controller+'/hapus/'+bF,
						type		: "POST",
						data		: "id="+bF,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								window.location.href = base_url + active_controller;
							}
							else if(data.status == 0){
								swal({
								  title	: "Save Failed!",
								  text	: data.pesan,
								  type	: "warning",
								  timer	: 7000,
								  showCancelButton	: false,
								  showConfirmButton	: false,
								  allowOutsideClick	: false
								});
							}
						},
						error: function() {
							swal({
							  title				: "Error Message !",
							  text				: 'An Error Occured During Process. Please try again..',						
							  type				: "warning",								  
							  timer				: 7000,
							  showCancelButton	: false,
							  showConfirmButton	: false,
							  allowOutsideClick	: false
							});
						}
					});
				} else {
				swal("Dibatalkan", "Data dapat di hapus kembali ...", "error");
				return false;
				}
			});
		});
	});
	
	function DataTables(series = null, komponen = null, cust = null, diameter = null, diameter2 = null){
		// alert(series);
		// alert(komponen);
		var dataTable = $('#example1').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"bAutoWidth": true,
			"destroy": true,
			"responsive": true,
			"aaSorting": [[ 1, "asc" ]],
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			}],
			"sPaginationType": "simple_numbers",
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
			"ajax":{
				url : base_url + active_controller+'/getDataJSON2',
				type: "post",
				data: function(d){
					d.series 	= series,
					d.komponen 	= komponen,
					d.cust 	= cust,
					d.diameter = diameter,
					d.diameter2 = diameter2
				},
				cache: false,
				error: function(){
					$(".my-grid-error").html("");
					$("#my-grid").append('<tbody class="my-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
					$("#my-grid_processing").css("display","none");
				}
			}
		});
	}
	
</script>
