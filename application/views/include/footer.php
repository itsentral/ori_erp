</div>
					</div>
					<div class="row">

						<div class="pull-right" style="margin-right:10px;">

						</div>
					</div>
				</div>
			</section>
		</div>
		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
	</aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>

	<div id="spinner" class="spinner" style="display:none;">
		<img src="<?php echo base_url('assets/img/loading.gif') ?>" id="img-spinner" alt="Loading...">
	</div>
	<div class="modal fade" id="Mymodal" >
		<div class="modal-dialog" style="width:80%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">List Master COA</h4>
				</div>
				<div class="modal-body" id="listCoa">

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<style>
		.note {
			background-color: #e7f3fe;
			border-left: 6px solid #2196F3;
			margin-bottom: 15px;
    		padding: 11px 11px 11px 11px;
		}

		.ui-datepicker select.ui-datepicker-month, .ui-datepicker select.ui-datepicker-year{
			color:#666;
		}
		.spinner{
			position	: fixed;
			top			: 0;
			right		: 0;
			bottom		: 0;
			left		: 0;
			background-color : rgba(255,255,255,0.7);

		}
		#img-spinner {
			left: 50%;
			margin-left: -4em;
			font-size: 16px;
			border: .8em solid rgba(218, 219, 223, 1);
			border-left: .8em solid rgba(58, 166, 165, 1);
			animation: spin 1.1s infinite linear;

		}
		#img-spinner, #img-spinner:after {
			border-radius: 50%;
			width: 8em;
			height: 8em;
			display: block;
			position: absolute;
			top: 50%;
			margin-top: -4.05em;
		}

		@keyframes spin {
		  0% {
			transform: rotate(360deg);
		  }
		  100% {
			transform: rotate(0deg);
		  }
		}

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
		textarea {
			resize: none;
		}
	</style>
	<!--<script src="<?php echo base_url('assets/angular/angular.min.js'); ?>"></script>-->
	<script src="<?php echo base_url('adminlte/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script>
	<!-- Bootstrap 3.3.6 -->
	<script src="<?php echo base_url('adminlte/bootstrap/js/bootstrap.min.js'); ?>"></script>
	<!-- DataTables -->
	<script src="<?php echo base_url('adminlte/plugins/datatables/jquery.dataTables.min.js'); ?>"></script>
	<script src="<?php echo base_url('adminlte/plugins/datatables/dataTables.bootstrap.min.js'); ?>"></script>
	<script src="<?php echo base_url('adminlte/plugins/datatables/extensions/FixedColumns/js/dataTables.fixedColumns.min.js'); ?>"></script>
	<!-- <script src="<?php echo base_url('adminlte/plugins/datatables/extensions/FixedHeader/js/dataTables.fixedHeader.min.js'); ?>"></script> -->
	<script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>
	<!-- FastClick -->
	<script src="<?php echo base_url('adminlte/plugins/fastclick/fastclick.js'); ?>"></script>
	<!-- date-range-picker -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
	<script src="<?php echo base_url('adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
	<!-- bootstrap datepicker -->
	<!-- <script src="<?php echo base_url('adminlte/plugins/datepicker/bootstrap-datepicker.js') ?>"></script> -->
	<script src="<?php echo base_url('assets/jquery-ui-1.12.1/jquery-ui.js'); ?>"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url('adminlte/dist/js/app.min.js'); ?>"></script>
	<!-- Sparkline -->
	<script src="<?php echo base_url('adminlte/plugins/sparkline/jquery.sparkline.min.js'); ?>"></script>
	<!-- jvectormap -->
	<script src="<?php echo base_url('adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'); ?>"></script>
	<script src="<?php echo base_url('adminlte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'); ?>"></script>
	<!-- SlimScroll 1.3.0 -->
	<script src="<?php echo base_url('adminlte/plugins/slimScroll/jquery.slimscroll.min.js'); ?>"></script>
	<!-- ChartJS 1.0.1 -->
	<script src="<?php echo base_url('adminlte/plugins/chartjs/Chart.min.js'); ?>"></script>
	<script src="<?php echo base_url('jquery-ui/jquery-ui.min.js') ?>"></script>
	<!-- iCheck 1.0.1 -->
	<script src="<?php echo base_url('adminlte/plugins/iCheck/icheck.min.js') ?>"></script>
	<script src="<?php echo base_url('chosen/chosen.jquery.min.js') ?>"></script>
	<script src="<?php echo base_url('assets/select2/select2.min.js') ?>"></script>
	<script src="<?php echo base_url('assets/dist/event_keypress.js'); ?>"></script>
	<script src="<?php echo base_url('assets/dist/jquery.maskMoney.js'); ?>"></script>
	<script src="<?php echo base_url('assets/dist/jquery.maskedinput.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/autoNumeric/autoNumeric.js'); ?>"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="<?php echo base_url('adminlte/dist/js/demo.js'); ?>"></script>
	<script src="<?php echo base_url('sweetalert/dist/sweetalert.min.js'); ?>"></script>
	<!--<script src="<?php echo base_url('assets/dist/bootstrap-datepicker.min.js');?>"></script>!-->
	<script src="<?php echo base_url('assets/datetimepicker/jquery.datetimepicker.js'); ?>"></script>

	<script src="<?php echo base_url('assets/wysihtml5/bootstrap3-wysihtml5.all.min.js'); ?>"></script>

	<script src="<?php echo base_url('assets/js/dataTables.buttons.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/buttons.html5.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/buttons.print.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/jszip.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/pdfmake.min.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/vfs_fonts.js'); ?>"></script>
	<script src="<?php echo base_url('assets/js/bs-custom-file-input.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/number-divider.min.js')?>"></script>
	<?php
	$openmenus='';
	$breadcrumbmenus='';
	$menus=str_replace(site_url(),"",current_url());
	$arraymenusid=array(); $arraymenustitle=array();
	if($menus!=''){
		$menudata = $this->db->query("SELECT id,name,parent_id FROM menus WHERE path='".$menus."' LIMIT 1")->row();
		if(empty($menudata)) {
			$menudata = $this->db->query("SELECT id,name,parent_id FROM menus WHERE path='".$this->uri->segment(1)."' LIMIT 1")->row();
		}
		if($menudata) {
			$parent_id = $menudata->parent_id;
			if($parent_id>0) {
				$arraymenusid[]=$menudata->id;$arraymenustitle[]=$menudata->name;
				for($lopmenu=1;$lopmenu=10;$lopmenu++){
					$menudata = $this->db->query("SELECT id,name,parent_id FROM menus WHERE id='".$parent_id."' LIMIT 1")->row();
					if($menudata->parent_id>0){
						$parent_id = $menudata->parent_id;
						$arraymenusid[]=$menudata->id;$arraymenustitle[]=$menudata->name;
					}else{
						$arraymenusid[]=$menudata->id;$arraymenustitle[]=$menudata->name;
						break;
					}
				}
			}
			$arraymenustitle=array_reverse($arraymenustitle);
			foreach($arraymenusid as $key=>$value){
				$openmenus.='$("#menu_'.$value.'").addClass("active");';
				$breadcrumbmenus.='<li>'.$arraymenustitle[$key].'</li>';
			}
		}
	}
	?>
	<!-- page script -->
    <script type="text/javascript">
		var base_url			= '<?php echo base_url(); ?>';
		var active_controller	= '<?php echo($this->uri->segment(1)); ?>';
		var active_action		= '<?php echo($this->uri->segment(2)); ?>';

//		var ses_level3			= '<?php echo (!empty($_SESSION["ses_level3"]))?$_SESSION["ses_level3"]:0; ?>';
//		var ses_level2			= '<?php echo (!empty($_SESSION["ses_level2"]))?$_SESSION["ses_level2"]:0; ?>';
//		var ses_level1			= '<?php echo (!empty($_SESSION["ses_level1"]))?$_SESSION["ses_level1"]:0; ?>';
		<?php
		if($breadcrumbmenus!='') echo '$(".breadcrumb").html("'.$breadcrumbmenus.'");' ;
		echo $openmenus;
		?>

		$(function(){
			// console.log(ses_level3)
			// console.log(ses_level2)
			// console.log(ses_level1)

//			$( ".menu"+ses_level3 ).addClass( "active" );
//			$( ".menu"+ses_level2 ).addClass( "active" );
//			$( ".menu"+ses_level1 ).addClass( "active" );

//			$( ".ulmenu"+ses_level2 ).addClass( "menu-open" );
//			$( ".ulmenu"+ses_level1 ).addClass( "menu-open" );

			$('#spinner').bind("ajaxSend",function(){
				$(this).show();
			}).bind("ajaxStop",function(){
				$(this).hide();
			}).bind("ajaxError",function(){
				$(this).hide();
			});

			$('.autoNumeric').autoNumeric();
			$('.maskMoney').maskMoney();


			$("#example1").DataTable({
				"stateSave" : true,
				"bAutoWidth": true,
				"destroy": true,
				"processing": true,
				"responsive": true,
				"fixedHeader": {
					"header": true,
					"footer": true
				}
			});
			$('select').addClass('chosen-select');
			$('input[type="text"][data-role="datepicker"]').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth:true,
				changeYear:true,
			    maxDate:'+0d'
			});
			$('input[type="text"][data-role="datepicker_lost"]').datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth:true,
				changeYear:true
			});
			$('[data-role="qtip"]').tooltip();

			if($('#flash-message')){ window.setTimeout(function(){$('#flash-message').fadeOut();}, 3000); }


			//	B:CHOSEN SETUP =================================================================================================================================

			//	general setup
			$('.chosen-select').chosen({
				allow_single_deselect	: true,
				search_contains			: true,
				no_results_text			: 'No result found for : ',
				placeholder_text_single	: 'Select an option'
			});

			//	disable chosen for multiple select, and data grid's select
			//select[multiple="multiple"],
			$('#data-grid select , #listDetailShift select').removeAttr('style', '').removeClass('chzn-done').data('chosen', null).next().remove();

			//	E:CHOSEN SETUP =================================================================================================================================
			$(".numberOnly").on("keypress keyup blur",function (event) {
				if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
					event.preventDefault();
				}
			});

			//AJAX MENU
			$(document).on('click', '.klikmenu', function(){
				let id_klikmenu = $(this).data('idmenu')
				let hrefget = $(this).find('a').attr("href");
				console.log(hrefget)
				$.ajax({
					type:'POST',
					url: base_url +'dashboard/menu_session/'+id_klikmenu,
					cache: false,
					success:function(data){
						console.log('Success!!!')
						$(window).load(hrefget);
					},
					error: function() {
						console.log('Error!!!')
					}
				});
			});

		});

		function back(){
			loading_spinner();
			window.location.href = base_url + active_controller;
		}
		function loading_spinner(){
			//$('#spinner').show();

			swal({
			  title: "Loading !!!",
			  text: "Please Wait ...",
			  imageUrl: base_url+'assets/img/loading.gif',
			  showConfirmButton: false,
			  showCancelButton: false
			});

		}

		function close_loading_spinner(){
			$('#spinner').hide();

		}

		function getNum(val) {
		   if (isNaN(val) || val == '') {
		     return 0;
		   }
		   return parseFloat(val);
		}

		function number_format (number, decimals, dec_point, thousands_sep) {
			// Strip all characters but numerical ones.
			number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
			var n = !isFinite(+number) ? 0 : +number,
				prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
				sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
				dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
				s = '',
				toFixedFix = function (n, prec) {
					var k = Math.pow(10, prec);
					return '' + Math.round(n * k) / k;
				};
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || '').length < prec) {
				s[1] = s[1] || '';
				s[1] += new Array(prec - s[1].length + 1).join('0');
			}
			return s.join(dec);
		}
	</script>
</body>
</html>
