<?php
$this->load->view('include/side_menu');
?>
<form action="#" method="POST" id="form_proses_bro" enctype="multipart/form-data"> 
<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3>
		<div class="box-tool pull-right">
		<?php
			// if($akses_menu['create']=='1'){
		?>
		<!--
		  <a href="<?php echo site_url('Company_plants/add_company_plant') ?>" class="btn btn-sm btn-success" id='btn-add'>
			<i class="fa fa-plus"></i> Add Production
		  </a>
		  -->
		  <?php
			// }
		  ?>
		</div>
	</div>
	<!-- /.box-header -->
	<div class="box-body">
		<table class="table table-bordered table-striped" id="my-grid" width='100%'>
			<thead>
				<tr class='bg-blue'>
					<th class="text-center">#</th>
					<th class="text-center">IPP</th>
					<th class="text-center no-sort">Customer</th>
					<th class="text-center no-sort">Project</th>
					<th class="text-center no-sort">Type</th>
					<th class="text-center no-sort">Series</th>
					<th class="text-center no-sort">Rev</th>
					<th class="text-center no-sort">Reason</th>
					<th class="text-center no-sort">Status</th>
					<th class="text-center no-sort">Option</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
	<!-- /.box-body -->
 </div>
  <!-- /.box -->
  
  <!-- modal -->
	<div class="modal fade" id="ModalView"  style='overflow-y: auto;'>
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
	<!-- modal -->
	<div class="modal fade" id="ModalView2" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title2"></h4>
					</div>
					<div class="modal-body" id="view2">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
	<!-- modal -->
	<div class="modal fade" id="ModalView3" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:80%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title3"></h4>
					</div>
					<div class="modal-body" id="view3">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
	<!-- modal -->
	<div class="modal fade" id="ModalView4" style='overflow-y: auto;'>
		<div class="modal-dialog"  style='width:40%; '>
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="head_title4"></h4>
					</div>
					<div class="modal-body" id="view4">
					</div>
					<div class="modal-footer">
					<!--<button type="button" class="btn btn-primary">Save</button>-->
					<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<!-- modal -->
</form>
<?php $this->load->view('include/footer'); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/datepicker/css/datepicker.css');?>">
<script src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js'); ?>"></script>
<script src="<?php echo base_url('application/views/Component/general.js'); ?>"></script>
<script>
	$(document).ready(function(){
		DataTables();
	});
	
	$(document).on('click', '#detailBQ', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalDetailBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '.detail_est', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>DETAIL DATA BQ ["+$(this).data('id_bq')+"]</b>");
		$.ajax({
			type:'POST',
			url: base_url + active_controller+'/modalviewDT/'+$(this).data('id_bq'),
			success:function(data){
				$("#ModalView").modal();
				$("#view").html(data);

			},
			error: function() {
				swal({
				  title				: "Error Message !",
				  text				: 'Connection Timed Out ...',
				  type				: "warning",
				  timer				: 5000,
				  showCancelButton	: false,
				  showConfirmButton	: false,
				  allowOutsideClick	: false
				});
			}
		});
	});
	
	$(document).on('click', '#editBQ', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+$(this).data('id_bq')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+$(this).data('id_bq'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#ActEst', function(e){ 
		e.preventDefault();
		$("#head_title2").html("<b>ESTIMATION PROJECT ["+$(this).data('id_bq')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalEst/'+$(this).data('id_delivery')+'/'+$(this).data('sub_delivery')+'/'+$(this).data('id_bq')+'/'+$(this).data('sts_delivery'));
		$("#ModalView2").modal();
	}); 
	
	
	$(document).on('click', '#editX', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'));
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#detailX', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>DETAIL ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalDetailX/'+$(this).data('id_bq')+'/'+$(this).data('id_milik'));
		$("#ModalView3").modal();
	});
	
	
	
	
	
	
	$(document).on('click', '#edit_end_cap', function(e){
		// console.log("Lala");
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_end_cap/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_blindflange', function(e){
		// console.log("Lala");
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_blindflange/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_pipeslongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_pipeslongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_elbowmould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_elbowmould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_elbowmitter', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_elbowmitter/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_eccentric_reducer', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_eccentric_reducer/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_concentric_reducer', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_concentric_reducer/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_equal_tee_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_equal_tee_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_reducer_tee_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_reducer_tee_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_equal_tee_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_equal_tee_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_reducer_tee_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_reducer_tee_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_flange_mould', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_flange_mould/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_flange_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_flange_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_colar', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_colar/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_colar_slongsong', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_colar_slongsong/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	$(document).on('click', '#edit_field_joint', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_field_joint/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});

	$(document).on('click', '#edit_shop_joint', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_shop_joint/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});

	$(document).on('click', '#edit_branch_joint', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/'+ active_controller+'/modalEditX_branch_joint/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	}); 

	$(document).on('click', '#edit_custom', function(e){
		e.preventDefault();
		loading_spinner();
		$("#head_title3").html("<b>EDIT ESTIMATION</b>");
		$("#view3").load(base_url +'index.php/cust_component/modal_custom/'+$(this).data('id_bq')+'/'+$(this).data('id_milik')+'/'+$('#pembeda').val());
		$("#ModalView3").modal();
	});
	
	
	
	
	$(document).on('click', '#printSPK', function(e){
		e.preventDefault();
		$("#head_title").html("<b>PRINT SPK ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalPrint/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	$(document).on('click', '#batalProduksi', function(e){
		e.preventDefault();
		$("#head_title").html("<b>CANCEL PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalBatal/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#updateProduksi', function(e){
		e.preventDefault();
		$("#head_title").html("<b>UPDATE/FINISH PRODUCTION ["+$(this).data('id_produksi')+"]</b>");
		$("#view").load(base_url +'index.php/'+ active_controller+'/modalEdit/'+$(this).data('id_produksi'));
		$("#ModalView").modal();
	});
	
	$(document).on('click', '#Perbandingan', function(e){
		e.preventDefault();
		$("#head_title2").html("<b>COMPARISON PRODUCTION ["+$(this).data('id_product')+"]</b>");
		$("#view2").load(base_url +'index.php/'+ active_controller+'/modalPerbandingan/'+$(this).data('id_product')+'/'+$(this).data('id_pro_detail')+'/'+$(this).data('id_produksi'));
		$("#ModalView2").modal();
	});
	
	$(document).on('click', '.ajuAppBQ', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Mengajukan Estimasi BQ untuk di approve",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/ajukanAppBQEst/'+bq,
					type		: "POST",
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
							window.location.href = base_url + active_controller + '/index_estimasi';
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
	
	//=========================================================================================================================
	//====================================================SAVE EDIT PROJECT====================================================
	//=========================================================================================================================
	
	//REDUCER TEE SLONGSONG
	$(document).on('click', '#simpan-bro-reducerteeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_reducerteeslongsong')[0]);
					var baseurl		= base_url +'/edit_bq/reducer_tee_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
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
							$('#simpan-bro-reducerteeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-reducerteeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//REDUCER TEE MOULD
	$(document).on('click', '#simpan-bro-reducerteemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-reducerteemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-reducerteemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_reducerteemould')[0]);
					var baseurl		= base_url +'/edit_bq/reducer_tee_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-reducerteemould').prop('disabled',false);
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
							$('#simpan-bro-reducerteemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-reducerteemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//FLANGE SLONGSONG
	$(document).on('click', '#simpan-bro-flangeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-flangeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_flangeslongsong')[0]);
					var baseurl		= base_url +'/edit_bq/flange_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-flangeslongsong').prop('disabled',false);
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
							$('#simpan-bro-flangeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-flangeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//FLANGE MOULD
	$(document).on('click', '#simpan-bro-flangemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-flangemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-flangemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_flangemould')[0]);
					var baseurl		= base_url +'/edit_bq/flange_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-flangemould').prop('disabled',false);
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
							$('#simpan-bro-flangemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-flangemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//EQUAL TEE SLONGSONG
	$(document).on('click', '#simpan-bro-equalteeslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteeslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-equalteeslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_equalteeslongsong')[0]);
					var baseurl		= base_url +'/edit_bq/equal_tee_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-equalteeslongsong').prop('disabled',false);
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
							$('#simpan-bro-equalteeslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-equalteeslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//EQUAL TEE MOULD
	$(document).on('click', '#simpan-bro-equalteemould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-equalteemould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-equalteemould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_equalteemould')[0]);
					var baseurl		= base_url +'/edit_bq/equal_tee_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-equalteemould').prop('disabled',false);
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
							$('#simpan-bro-equalteemould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-equalteemould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//END CAP
	$(document).on('click', '#simpan-bro-endcap', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-endcap').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-endcap').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_endcap')[0]);
					var baseurl		= base_url +'/edit_bq/end_cap_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-endcap').prop('disabled',false);
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
							$('#simpan-bro-endcap').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-endcap').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ECCENTRIC
	$(document).on('click', '#simpan-bro-eccentric', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-eccentric').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-eccentric').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_eccentric')[0]);
					var baseurl		= base_url +'edit_bq/eccentric_reducer_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-eccentric').prop('disabled',false);
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
							$('#simpan-bro-eccentric').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-eccentric').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//CONCENTRIC
	$(document).on('click', '#simpan-bro-concentric', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-concentric').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-concentric').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_concentric')[0]);
					var baseurl		= base_url +'edit_bq/concentric_reducer_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-concentric').prop('disabled',false);
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
							$('#simpan-bro-concentric').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-concentric').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//COLAR SLONGSONG
	$(document).on('click', '#simpan-bro-colarslongsong', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-colarslongsong').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-colarslongsong').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_colarslongsong')[0]);
					var baseurl		= base_url +'/edit_bq/colar_slongsong_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-colarslongsong').prop('disabled',false);
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
							$('#simpan-bro-colarslongsong').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-colarslongsong').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//COLAR
	
	
	$(document).on('click', '#simpan-bro-colar', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-colar').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-colar').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_colar')[0]);
					var baseurl		= base_url +'/edit_bq/colar_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-colar').prop('disabled',false);
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
							$('#simpan-bro-colar').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-colar').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//BLIND FLANGE
	$(document).on('click', '#simpan-bro-blindflange', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-blindflange').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-blindflange').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_blindflange')[0]);
					var baseurl		= base_url +'/edit_bq/blind_flange_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-blindflange').prop('disabled',false);
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
							$('#simpan-bro-blindflange').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-blindflange').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ELBOW MITTER
	$(document).on('click', '#simpan-bro-elbowmitter', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmitter').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-elbowmitter').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_elbowmitter')[0]);
					var baseurl		= base_url +'/edit_bq/elbow_mitter_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-elbowmitter').prop('disabled',false);
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
							$('#simpan-bro-elbowmitter').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-elbowmitter').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//ELBOW MOULD
	$(document).on('click', '#simpan-bro-elbowmould', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-elbowmould').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-elbowmould').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_elbowmould')[0]);
					var baseurl		= base_url +'/edit_bq/elbow_mould_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-elbowmould').prop('disabled',false);
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
							$('#simpan-bro-elbowmould').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-elbowmould').prop('disabled',false); 
				return false;
			  }
		});
	});
	
	//PIPE
	$(document).on('click', '#simpan-bro-pipe', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		var waste				= $('#waste').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Pipe Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		if(waste == '' || waste == null || waste == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'New Standart Default is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		var hasil_linier_thickness 	= $('#hasilLin').val();
		var hasil_linier_thickness2 = $('#hasilStr').val();
		var hasil_linier_thickness3 = $('#hasilEks').val();
		
		if(hasil_linier_thickness != 'OK' || hasil_linier_thickness2 != 'OK' || hasil_linier_thickness3 != 'OK' ){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness is To High or To Low, please check back ...',
			  type	: "warning"
			});
			$('#simpan-bro-pipe').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-pipe').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_pipe')[0]);
					var baseurl		= base_url +'/edit_bq/pipe_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-pipe').prop('disabled',false);
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
							$('#simpan-bro-pipe').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-pipe').prop('disabled',false); 
				return false;
			  }
		});
	});

	//CUSTOM
	$(document).on('click', '#simpan-bro-custom', function(e){
		e.preventDefault(); 
		
		var top_tebal_design	= $('#design').val();
		
		$(this).prop('disabled',true);
		
		if(top_tebal_design == '' || top_tebal_design == null || top_tebal_design == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Thickness Design is Empty, please input first ...',
			  type	: "warning"
			});
			$('#simpan-bro-custom').prop('disabled',false);
			return false;	
		} 
		
		$('#simpan-bro-custom').prop('disabled',false);
		
		swal({
			  title	: "Peringatan !!!",
			  text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
			  type	: "warning",
			  showCancelButton: true,
			  confirmButtonClass: "btn-danger",
			  confirmButtonText: "Ya, Proses",
			  cancelButtonText: "Tidak, Batalkan",
			  closeOnConfirm: false,
			  closeOnCancel: false
			},
			function(isConfirm) {
			  if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro_custom')[0]);
					var baseurl		= base_url +'cust_component/custom_bq'; 
					$.ajax({
						url			: baseurl,
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
									  timer	: 7000,
									  showCancelButton	: false,
									  showConfirmButton	: false,
									  allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-custom').prop('disabled',false);
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
							$('#simpan-bro-custom').prop('disabled',false);
						}
					});
			  } else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-custom').prop('disabled',false); 
				return false;
			  }
		});
	});

	//JOINT
	$(document).on('click', '#simpan-bro-joint', function(e){
		e.preventDefault(); 
		var id_product = $('#id_product').val();
		$('#simpan-bro-joint').prop('disabled',false);
		
		swal({
				title	: "Peringatan !!!",
				text	: "Hanya merubah estimasi yang di BQ, tidak merubah di master product !!!",
				type	: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn-danger",
				confirmButtonText: "Ya, Proses",
				cancelButtonText: "Tidak, Batalkan",
				closeOnConfirm: false,
				closeOnCancel: false
			},
			function(isConfirm) {
				if (isConfirm) {
					loading_spinner();
					var formData  	= new FormData($('#form_proses_bro')[0]);
					var baseurl		= base_url +'/edit_bq/joint_bq'; 
					$.ajax({
						url			: baseurl,
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
										timer	: 7000,
										showCancelButton	: false,
										showConfirmButton	: false,
										allowOutsideClick	: false
									});
								// window.location.href = base_url + active_controller +'/revisi_est';
								$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bq+"]</b>");
								$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bq+'/'+data.pembeda);
								$("#ModalView").modal();
								$("#ModalView3").modal('hide');
							}
							else if(data.status == 2){
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
							else{
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
							$('#simpan-bro-joint').prop('disabled',false);
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
							$('#simpan-bro-joint').prop('disabled',false);
						}
					});
				} else {
				swal("Dibatalkan...", "Data dapat diproses kembali...", "error");
				$('#simpan-bro-joint').prop('disabled',false); 
				return false;
				}
		});
	});
	
	//=========================================================================================================================
	//====================================================END EDIT PROJECT=====================================================
	//=========================================================================================================================
	
	$(document).on('click', '.updateComp', function(){
		var id_bq = $(this).data('id_bq');
		var id_milik = $(this).data('id_milik');
		var panjang = $(this).data('panjang');
		var nomor = $(this).data('nomor');
		var product = $("#id_product_"+nomor).val();
		var pembeda = $(this).data('pembeda');
		
		if(product == '' || product == null || product == 0){
			swal({
			  title	: "Error Message!",
			  text	: 'Product is Empty, please input first ...',
			  type	: "warning"
			});
			$('.updateComp').prop('disabled',false);
			return false;	
		} 
		
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
				// var formData  	= new FormData($('#form_proses_bro')[0]);
				$.ajax({
					url			: base_url+'index.php/'+active_controller+'/updateEstSatuan/'+id_bq+'/'+id_milik+'/'+panjang+'/'+product+'/'+pembeda,
					type		: "POST",
					// data		: formData,
					// data:{
							// 'id_bq' : id_bq
						// },
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
							
							$("#head_title").html("<b>ESTIMATION STRUCTURE BQ ["+data.id_bqx+"]</b>");
							$("#view").load(base_url +'index.php/'+ active_controller+'/modalEstBQ/'+data.id_bqx+'/'+data.pembeda);
							$("#ModalView").modal();
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
			swal("Cancelled", "Data can be process again :)", "error");
			return false;
			}
		});
	});
	
	$(document).on('click', '.back_to_bq', function(){
		var bq		= $(this).data('id_bq');
		// alert(bq);
		// return false;
		swal({
		  title: "Apakah anda yakin ???",
		  text: "Kembali ke Structure BQ untuk revisi",
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-danger",
		  confirmButtonText: "Ya, Proses !",
		  cancelButtonText: "Tidak, Batalkan !",
		  closeOnConfirm: true,
		  closeOnCancel: false
		},
		function(isConfirm) {
			if (isConfirm) {
				loading_spinner();
				$.ajax({
					url			: base_url + active_controller+'/back_to_bq/'+bq,
					type		: "POST",
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
							window.location.href = base_url + active_controller + '/index_estimasi';
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
			swal("Dibatalkan", "Data dapat di proses kembali ...", "error");
			return false;
			}
		});
	});
		
	function DataTables(){
		var dataTable = $('#my-grid').DataTable({
			"processing": true,
			"serverSide": true,
			"stateSave" : true,
			"autoWidth": true,
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
				url : base_url + active_controller+'/getDataJSONEst',
				type: "post",
				data: function(d){
					// d.kode_partner = $('#kode_partner').val()
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

	function getNum(val) {
		if (isNaN(val) || val == '') {
			return 0;
		}
		return parseFloat(val);
	}
	function getF4(val) {
		return val.toFixed(4);
	}
	
</script>
