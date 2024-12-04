<?php
$this->load->view('include/side_menu');	
?>

<link rel="stylesheet" href="<?= base_url('assets/plugins/datatables/dataTables.bootstrap.css')?>">
 <form id="form-header-mutasi" method="post">
<div class="nav-tabs-salesorder">
     <div class="tab-content">
        <div class="tab-pane active" id="salesorder">
            <div class="box box-primary">
               	<?php //print_r($kode_customer)?>
                <div class="box-body">
                    <div class="col-sm-6 form-horizontal">
						<div class="row">
                          <div class="form-group ">
                          <label for="tgl_bayar" class="col-sm-4 control-label">No Transaksi :</label>
                            <div class="col-sm-6">
                                <input type="text" name="no_request" id="no_request"  placeholder="Automatic" class="form-control input-sm" readonly>
                            </div>
                          </div>
                        </div>
						 <div class="row">
                          <div class="form-group">
                              <label for="dari" class="col-sm-4 control-label">Jenis Transaksi :</font></label>
                              <div class="col-sm-6">
								<select class="form-control input-sm" name="jenis_transaksi" id="jenis_transaksi">
								<option value ="terima" >Penerimaan</option>
								<option value ="keluar">Pengeluaran</option>
								</select>		
                                
                              </div>
                          </div>
                        </div>
					    <div class="row">
                          <div class="form-group ">
                            <?php
                            $tglinv=date('Y-m-d');
                            ?>
                            <label for="tgl_bayar" class="col-sm-4 control-label">Tgl :</label>
                            <div class="col-sm-6">
                                <input type="date" name="tgl_request" id="tgl_request" class="form-control input-sm" value="<?php echo date('Y-m-d') ?>">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group">
                              <label for="dari" class="col-sm-4 control-label">COA Tujuan :</font></label>
                              <div class="col-sm-6">
								<?php
								echo form_dropdown('dari',$allcoa, '',array('id'=>'dari','required'=>'required','class'=>'form-control'));
								?>								 
                                
                              </div>
                          </div>
                        </div>
						<div class="row">
							<div class="form-group">
								  <label for="ke" class="col-sm-4 control-label">COA Bank :</font></label>
								  <div class="col-sm-6">
									<?php
									echo form_dropdown('ke',$datbank, '',array('id'=>'ke','required'=>'required','class'=>'form-control'));
									?>								 
								  </div>
							  </div>
						</div>	
                        <div class="row">
                          <div class="form-group ">
                          <label for="tgl_bayar" class="col-sm-4 control-label">Transaksi :</label>
                            <div class="col-sm-6">
                                 <input type="text" name="transaksi" class="form-control input-sm divide " id="transaksi" value="0" readonly>
							 </div>
                          </div>
                        </div>						
						
				   </div>
                   <div class="col-sm-6 form-horizontal">
                         <div class="row">
						  <div class="form-group">
                            <label for="ket_bayar" class="col-sm-4 control-label">Keterangan Transaksi </font></label>
                            <div class="col-sm-6">
                              <textarea name="keterangan" class="form-control input-sm" id="keterangan"></textarea>
                            </div>
                          </div>
						</div>
						
						<div class="row">
                          <div class="form-group">
                              <label for="matauang" class="col-sm-4 control-label">Mata Uang</font></label>
                              <div class="col-sm-6">
                              <?php
								echo form_dropdown('matauang',$matauang, '',array('id'=>'matauang','required'=>'required','class'=>'form-control'));
								?>	
                              </div>
                          </div>
                        </div>
						<div class="row">
                          <div class="form-group ">
                          <label for="tgl_bayar" class="col-sm-4 control-label">Kurs :</label>
                            <div class="col-sm-6">
                                 <input type="text" name="kurs" class="form-control input-sm divide " id="kurs" value="1" onblur="total()">
							 </div>
                          </div>
                        </div> 
						<div class="row">
                          <div class="form-group ">
                          <label for="tgl_bayar" class="col-sm-4 control-label">Nilai Transaksi :</label>
                            <div class="col-sm-6">
                                 <input type="text" name="nilai" class="form-control input-sm divide " id="nilai" value="0" onblur="total()">
							 </div>
                          </div>
                        </div>
						
				</div>
				<div class="row">
				  <div class="form-group ">
				  <label for="tgl_bayar" class="col-sm-4 control-label"></label>
					<div class="col-sm-8">
						<input type="text" name="terbilang" id="terbilang"  placeholder="TERBILANG" class="form-control input-sm" readonly>
					</div>
				  </div>
			   </div>
				</div>
				
			</div>
		</div>
   </div>
</div>
<hr>
		<div class="row">
			<div class="col-lg-12">
			
				<button class="btn btn-danger" > 
				<a href="<?= base_url() ?>request_mutasi">
				   <i class="fa fa-refresh"></i><b> Kembali</b>
			    </a> 
				</button>
			
				<button id="simpanpenerimaan" class="btn btn-primary" type="button" onclick="savemutasi()">
					<i class="fa fa-save"></i><b> Simpan Transaksi</b>
				</button>
				
				
			</div>
		</div>
    </div>
  </div>
</div>


<?php $this->load->view('include/footer'); ?>

<script>
	$(document).ready(function(){
		//$('.select2').select2();
		swal.close();
		$('#incomplete').hide();
		$('#pakailebihbayar').hide();
		$("#list_item_unlocated").DataTable({lengthMenu:[10,15,25,30]}).draw();
		$(".divide").divide();
	});
	 function savemutasi(){
		
	    if ($('#tgl_request').val() == "") {
          swal({
            title	: "Tanggal Tidak Boleh Kosong!",
            text	: "Isi Tanggal Terlebih Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else if ($('#matauang').val() == "0") {
          swal({
            title	: "Mata uang tidak boleh kosong!",
            text	: "Silahkan Pilih Mata Uang Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
         }		
		 else if ($('#dari').val() == "0") {
          swal({
            title	: "Bank Asal tidak boleh kosong!",
            text	: "Silahkan Pilih Bank Asal Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
         }		
		 else if ($('#ke').val() == "0") {
          swal({
            title	: "Bank Tujuan tidak boleh kosong!",
            text	: "Silahkan Pilih Bank Tujuan Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
         }	

        else if ($('#nilai').val() == "0") {
          swal({
            title	: "Nilai tidak boleh kosong!",
            text	: "Silahkan Isi Nilai Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
         }	
		 
        else {
        swal({
          title: "Peringatan !",
          text: "Pastikan data sudah lengkap dan benar",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Ya, simpan!",
          cancelButtonText: "Batal!",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
			if(isConfirm) {
				//$('#simpanpenerimaan').hide();
				var formdata = $("#form-header-mutasi").serialize();
				$.ajax({
					url: base_url+"request_mutasi/save_transaksi",
					dataType : "json",
					type: 'POST',
					data: formdata,
					success: function(data){
						if(data.status == 1){
						swal({
						  title	: "Save Success!",
						  text	: data.pesan,
						  type	: "success",
						  timer	: 15000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
						window.location.href = base_url + active_controller+'/admin';
					  }else{

						if(data.status == 2){
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}else{
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}

					  }
					},
					error: function(){
						swal({
							title: "Gagal!",
							text: "Batal Proses, Data bisa diproses nanti",
							type: "error",
							timer: 1500,
							showConfirmButton: false
						});
					}
				});
			}
        });
		}
    }
	
	function savedraf(){
		
	    if ($('#tgl_bayar').val() == "") {
          swal({
            title	: "Tanggal Tidak Boleh Kosong!",
            text	: "Isi Tanggal Terlebih Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		  if ($('#matauang').val() == "") {
          swal({
            title	: "Mata uang tidak boleh kosong!",
            text	: "Silahkan Pilih Mata Uang Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		
		else if ($('#matauang').val() == "usd" && $('#kurs').val() == "0" ) {
          swal({
            title	: "Perhatian",
            text	: "Kurs Harus di isi terlebih dahulu !",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else if ($('#control').val() != "0") {
          swal({
            title	: "Perhatian",
            text	: "Kontrol harus 0!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else if ($('#bank').val() == "") {
          swal({
            title	: "BANK TIDAK BOLEH KOSONG!",
            text	: "ISI TANGGAL INVOICE!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else if ($('#total_bank').val() != $('#total_terima').val()) {
          swal({
            title	: "JUMLAH BAYAR DAN PENERIMAAN BANK TIDAK SAMA!",
            text	: "SILAHKAN PERBAIKI DATA ANDA!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        } else {
        swal({
          title: "Peringatan !",
          text: "Pastikan data sudah lengkap dan benar untuk di preview",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Ya!",
          cancelButtonText: "Batal!",
          closeOnConfirm: false,
          closeOnCancel: true
        },
        function(isConfirm){
			if(isConfirm) {
				//$('#simpanpenerimaan').hide();
				var formdata = $("#form-header-mutasi").serialize();
				$.ajax({
					url: base_url+"penerimaan/save_penerimaan_proforma",
					dataType : "json",
					type: 'POST',
					data: formdata,
					success: function(data){
						if(data.status == 1){
						swal({
						  title	: "Save Success!",
						  text	: data.pesan,
						  type	: "success",
						  timer	: 15000,
						  showCancelButton	: false,
						  showConfirmButton	: false,
						  allowOutsideClick	: false
						});
						window.location.href = base_url + active_controller+'/printout_draft/'+data.nomor;
					  }else{

						if(data.status == 2){
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}else{
						  swal({
							title	: "Save Failed!",
							text	: data.pesan,
							type	: "warning",
							timer	: 10000,
							showCancelButton	: false,
							showConfirmButton	: false,
							allowOutsideClick	: false
						  });
						}

					  }
					},
					error: function(){
						swal({
							title: "Gagal!",
							text: "Batal Proses, Data bisa diproses nanti",
							type: "error",
							timer: 1500,
							showConfirmButton: false
						});
					}
				});
			}
        });
		}
    }

	function kembali_inv(){
		
		   window.location.href = base_url + active_controller;
    }
	function cekall(){
		var total_bank=$("#total_bank").val();
		var total_invoice=$("#total_invoice").val();
		var selisih=(parseFloat(total_bank)-parseFloat(total_invoice));
		$("#selisih").val(number_format(selisih,2));
		var biaya_adm=$("#biaya_adm").val();
		var biaya_pph=$("#biaya_pph").val();
		var tambah_lebih_bayar=$("#tambah_lebih_bayar").val();
		var control1=(parseFloat(selisih)+parseFloat(biaya_adm)+parseFloat(biaya_pph)-parseFloat(tambah_lebih_bayar));
		var control = number_format(control1,0);
		console.log(control);
		$("#control").val(control);
		var total_terima=(parseFloat(total_invoice)-parseFloat(biaya_adm)-parseFloat(biaya_pph)+parseFloat(tambah_lebih_bayar));
		$("#total_terima").val(total_terima);
	}
	
	
	

	$("#tambah").click(function(){
		$('#dialog-data-stok').modal('show');

	});

	function startmutasi(id,surat,ipp,nm,avl,real,ret,kurs){
		var avl2 =numx(avl);
		var real2= numx(real);
		var ret2= numx(ret);
		var tot= parseFloat(ret)+parseFloat(real);
		
		
		//console.log(id);
		

		
       //  Cek Ada Data Gagal
	   var Cek_OK		= 1;
	   var Urut			= 1;
	   var total_row	= $('#list_item_mutasi').find('tr').length;
	   if(total_row > 0){
		  var kode_tr_akhir= $('#list_item_mutasi tr:last').attr('id');
		  var row_akhir		= kode_tr_akhir.split('_');
		  var Urut			= parseInt(row_akhir[1]) + 1;
		  $('#list_item_mutasi').find('tr').each(function(){
			  var kode_row	= $(this).attr('id');
			  var id_row	= kode_row.split('_');
			  var kode_produknya	= $('#kode_produk_'+id_row[1]).val();
			  if(id==kode_produknya){
				  Cek_OK	= 0;
			  }
		  });
	   }
	   if(Cek_OK==1){
			var idnya = "'"+id+"'";
			html='<tr id="tr_'+Urut+'">'
				+ '<td style="padding:3px;">'
				+ '<input type="text" class="form-control input-sm kode-produk" name="kode_produk[]" id="kode_produk_'+Urut+'" readonly value="'+surat+'">'
				+ '</td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="no_surat[]" id="no_surat'+Urut+'" readonly value="'+ipp+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="nm_customer2[]" id="nm_customer2'+Urut+'" readonly value="'+nm+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="kurs_jual[]" id="kurs_jual'+Urut+'" style="text-align:center;" readonly value="'+kurs+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="jml_invoice[]" id="jml_invoice'+Urut+'" style="text-align:center;" readonly value="'+avl2+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="sisa_invoice[]" id="sisa_invoice'+Urut+'" style="text-align:center;" readonly value="'+real2+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm" name="sisa_retensi[]" id="sisa_retensi'+Urut+'" style="text-align:center;" readonly value="'+ret2+'"></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm sum_change_bayar divide" name="jml_bayar[]" id="jml_bayar'+Urut+'" style="text-align:right;" value="'+number_format(tot)+'" onchange="cekall()" ></td>'
				+ '<td style="padding:3px;"><select class="form-control input-sm tipe_bayar" name="tipe_bayar[]" id="tipe_bayar'+Urut+'"><option value="">Pilih Tipe</option><option value="PROGRESS">PROGRESS</option><option value="RETENSI">RETENSI</option></select></td>'
				+ '<td style="padding:3px;"><select class="form-control input-sm" name="jenis_pph2[]" id="jenis_pph2'+Urut+'"><option value="">Pilih PPH</option><option value="1106-01-04">PREPAID TAX - PPH 22</option><option value="1106-01-03">PREPAID TAX - PPH 23</option><option value="1106-01-06">PREPAID TAX - PPH PSL 4 (2)</option></select></td>'
				+ '<td style="padding:3px;"><input type="text" class="form-control input-sm sum_change_pph divide" name="pph[]" id="pph'+Urut+'" style="text-align:right;" onchange="cekall()"></td>'
				+ '<td style="padding:3px;"><center><div class="btn-group" style="margin:0px;">'
				+ '<button type="button" onclick="deleterow('+Urut+','+idnya+')" id="delete-row" class="btn btn-sm btn-danger delete_bayar"><i class="fa fa-trash"></i> Hapus</button>'
				+ '</div></center></td>'
				+ '</tr>';
			$("#tabel-detail-mutasi").append(html);
			$("#btn-"+id).removeClass('btn-warning');
			$("#btn-"+id).addClass('btn-danger');
			$("#btn-"+id).attr('disabled',true);
			$("#btn-"+id).text('Sudah');
			sumchangebayar();
	   }
    }

	function deleterow(tr,id){
        $('#tr_'+tr).remove();
        $("#btn-"+id).removeClass('btn-danger');
        $("#btn-"+id).addClass('btn-warning');
        $("#btn-"+id).attr('disabled',false);
        $("#btn-"+id).text('Pilih');
		sumchangebayar();
    }

	//ARWANT
	$(document).on('keyup','.sum_change_bayar', function(){
		var jumlah_bayar = 0;
		$(".sum_change_bayar" ).each(function() {
			jumlah_bayar += getNum($(this).val().split(",").join(""));
		});
		$('#total_invoice').val(number_format(jumlah_bayar,2));
	});

	//SYAM
	$(document).on('keyup','.sum_change_pph', function(){
		var jumlah_bayar = 0;
		$(".sum_change_pph" ).each(function() {
			jumlah_bayar += getNum($(this).val().split(",").join(""));
		});
		$('#biaya_pph').val(number_format(jumlah_bayar,2));
		//totalterima();
	});

	function sumchangebayar(){
		var jumlah_bayar = 0;
		$(".sum_change_bayar" ).each(function() {
			jumlah_bayar += getNum($(this).val().split(",").join(""));
		});
		$('#total_invoice').val(number_format(jumlah_bayar,2));
	}

	function getNum(val) {
        if (isNaN(val) || val == '') {
            return 0;
        }
        return parseFloat(val);
    }

	function num(n) {
      return (n).toFixed(0).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

	function num2(n) {
      return (n).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

	function num3(n) {
      return (n).toFixed(0);
    }
	function numx(n) {
      return (n).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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

	$(document).on('change', '#bank', function(){
		var dataCoa	  = $(this).val();
		if(dataCoa=='2101-07-01'){
			$('#incomplete').show();
		} else{
			$('#incomplete').hide();
		}
	});

	$("#incomplete").click(function(){
		$('#dialog-data-incomplete').modal('show');
//        $("#list_item_stok").DataTable({lengthMenu:[10,15,25,30]}).draw();
	});

	$("#lebihbayar-1").click(function(){
		$('#dialog-data-lebihbayar').modal('show');
		$('#pakailebihbayar').show();
//        $("#list_item_stok").DataTable({lengthMenu:[10,15,25,30]}).draw();
	});

	function startunlocated(id,value){

		$("#total_bank").val(value);
		$("#id_unlocated").val(id);
		$("#btn-"+id).removeClass('btn-warning');
		$("#btn-"+id).addClass('btn-danger');
		$("#btn-"+id).attr('disabled',true);
		$("#btn-"+id).text('Sudah');
		var totalBank     = parseFloat(value).toFixed(0);
		$('#total_bank').val(number_format(totalBank));
//		totalterima();
		cekall();
	   }

	function startlebihbayar(id,value){

		$("#pakai_lebih_bayar").val(value);
		$("#id_lebihbayar").val(id);
		$("#btn-"+id).removeClass('btn-warning');
		$("#btn-"+id).addClass('btn-danger');
		$("#btn-"+id).attr('disabled',true);
		$("#btn-"+id).text('Sudah');
		var totalBank     = parseFloat(value).toFixed(0);
		$('#pakai_lebih_bayar').val(number_format(totalBank));
//		totalterima();
		cekall();
	}

    $(document).on('click', '.add', function(){
		var id_customer=$("#customer").val();

		if (id_customer == "") {
          swal({
            title	: "Customer Tidak Boleh Kosong!",
            text	: "Pilih Customer Terlebih Dahulu!",
            type	: "warning",
            timer	: 3000,
            showCancelButton	: false,
            showConfirmButton	: false,
            allowOutsideClick	: false
          });
        }
		else {

		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Tambah Invoice</b>");
		$.ajax({
			type:'POST',
			url:base_url + active_controller +'/TambahInvoice/'+id_customer,
			data:{'id_customer':id_customer},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
		}
	});

	$(document).on('click', '#lebihbayar', function(){
		// $('#dialog-data-lebihbayar').modal('show');
		$('#pakailebihbayar').show();
		var id_customer=$("#customer").val();
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Tambah Lebih Bayar</b>");
		$.ajax({
			type:'POST',
			 url:siteurl+'penerimaan/TambahLebihBayar/'+id_customer,
			data:{'id_customer':id_customer},
			success:function(data){
				$("#dialog-data-lebihbayar").modal();
				$("#MyModalBodyLebihbayar").html(data);
			}
		})
	});

	$(document).on('click', '.lebih', function(){
		var id_customer=$("#customer").val();
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Tambah Lebih Bayar</b>");
		$.ajax({
			type:'POST',
			 url:siteurl+'penerimaan/lebihbayar',
			data:{'id_customer':id_customer},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});

	 $(document).on('change', '#customer', function(){
		var id_customer=$("#customer").val();
		$("#id_customer").val(id_customer);
	});
	
	// $(document).on('change', '#matauang', function(){
		// var mataUang	  = $(this).val();
		// if(mataUang=='usd'){
			// $('#kurs').show();
		// } else{
			// $('#kurs').hide();
		// }
	// });

	function totalterima(){
		cekall();
		/*
		var pakai_lebih_bayar   = parseFloat($('#pakai_lebih_bayar').val().split(",").join(""))
		var tambah_lebih_bayar   = parseFloat($('#tambah_lebih_bayar').val().split(",").join(""))
	    var biaya_adm   = parseFloat($('#biaya_adm').val().split(",").join(""))
		var total_bank	= parseFloat($('#total_bank').val().split(",").join(""));
        var biaya_pph	= parseFloat($('#biaya_pph').val().split(",").join(""));
		var Total       = parseInt(biaya_adm)+parseInt(total_bank)+parseInt(biaya_pph)+parseInt(pakai_lebih_bayar)-parseInt(tambah_lebih_bayar);
		$('#total_terima').val(number_format(Total));
		*/
	}

	$(document).on('click', '.createunlocated', function(){
		var id_customer=$("#customer").val();
		$("#head_title").html("<i class='fa fa-list-alt'></i><b>Tambah Unlocated</b>");
		$.ajax({
			type:'POST',
			 url:siteurl+'penerimaan/createunlocated',
			data:{'id_customer':id_customer},
			success:function(data){
				$("#dialog-popup").modal();
				$("#ModalView").html(data);
			}
		})
	});

   function total(){
		var kurs = $('#kurs').val();
		var nilai = $('#nilai').val();		
		var kurs = getNum($('#kurs').val().split(",").join(""));
		var nilai = getNum($('#nilai').val().split(",").join(""));
		
	    var jumlah = parseFloat(nilai)*parseFloat(kurs);			
		$('#transaksi').val(number_format(jumlah,2));
		
		fn_terbilang();
		
	}
	
   function fn_terbilang(){
        var nilai=$("#transaksi").val();
		var matauang=$("#matauang").val();	
			
		$.ajax({
            type:"GET",
            url:base_url+"request_mutasi/terbilang",
            data:"nilai="+nilai+"&matauang="+matauang,
            success:function(html){
               $("#terbilang").val(html);
            }
        });
		
		
		 
    }
	
// $('#tgl_bayar').datepicker({
	// format: 'yyyy-mm-dd',
	// todayHighlight: true
// });
</script>
