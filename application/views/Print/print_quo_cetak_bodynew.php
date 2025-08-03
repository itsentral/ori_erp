
<table class='gridtable3' width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tbody>
		<tr>
			<td colspan='5'>Jakarta, <?=date('d F Y');?></td>
			<td rowspan='6' style='vertical-align:top; text-align:right;'><img src='<?=$sroot;?>/assets/images/alamatori.png' style='float:right; padding-top:-42px;' alt="" height='160' width='90'></td>
		</tr>
		<tr>
			<td colspan='2'>Ref. No</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$quo_number;?></td>
		</tr>
		<tr>
			<td colspan='5' height='40px;' style='vertical-align:bottom;'><b><?=$customer;?></b></td>
		</tr>
		<tr>
			<td colspan='4' height='40px;' style='vertical-align:top; font-size:10px;'><?= $alamat_cust;?></td>
			<td></td>
		</tr>
		<tr>
			<td colspan='2'>Attn</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$attn;?></td>
		</tr>
		<!-- <tr>
			<td colspan='2'>Telp.</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$telephone;?></td>
		</tr> -->
		<tr>
			<td colspan='2'>Perihal</td>
			<td colspan='3'>:&nbsp;&nbsp;&nbsp;<?=$subject;?></td>
		</tr>
		<tr>
			<td colspan='5' height='40px;'></td>
		</tr>
		<tr>
			<td colspan='6' class='justify'>
				Dengan hormat,<br>
				Sehubungan dengan pembicaraan beberapa waktu lalu melalui email antara <b><?=$customer;?></b> dengan PT ORI POLYTEC COMPOSITES, sebuah perseroan terbatas yang didirikan berdasarkan ketentuan hukum negara Republik Indonesia, berdomisili dan beralamat di Jl. Akasia II Blok  A9/3, Delta Silicon Industrial Park Kawasan Industri Lippo Cikarang, Bekasi 17340, Indonesia (“ORI POLYTEC COMPOSITES”), dengan ini ingin mengajukan proposal penawaran Produk sebagai berikut :
			</td>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td><b>A.</b></td>
			<td colspan='5'><b>Jenis dan Ketentuan Produk</b></td>
		</tr>
		<tr>
			<td width='5%'></td>
			<td width='7%'>1. </td>
			<td width='27%'>Produk</td>
			<td width='3%'>:</td>
			<td colspan='2'><?=$product;?></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td>Material </td>
			<td>:</td>
			<td colspan='2'><?=$resin;?></td>
		</tr>
		<tr>
			<td></td>
			<td>3.</td>
			<td>Jenis Pengiriman</td>
			<td>:</td>
			<td colspan='2'><?=$pengiriman;?></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td class='valign'>Jangka Waktu Penawaran</td>
			<td class='valign'>:</td>
			<td colspan='2'><?=$jangka_waktu_penawaran;?></td>
		</tr>
		<tr>
			<td></td>
			<td>5.</td>
			<td>Waktu Pengiriman</td>
			<td>:</td>
			<td colspan='2'><?=$waktu_pengiriman;?></td>
		</tr>
		<tr>
			<td></td>
			<th>6.</th>
			<th>Garansi Produk</th>
			<th>:</th>
			<th colspan='2'><?=$garansi_porduct;?></th>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td colspan='6' height='10px;'></td>
		</tr>
		<tr>
			<td><b>B.</b></td>
			<td colspan='5'><b>Kondisi dan Tahap Pembayaran Harga</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='4' class='justify'>
				Sehubungan dengan Produk yang disebutkan di atas, dengan ini kami mengajukan harga produk yaitu <b>terlampir</b>. Dimana harga ini hanya berlaku 1 Bulan dan akan berubah sesuai dengan perubahan harga material. Harga tersebut tidak meliputi hal sebagai berikut :
				<br>
				<ul>
					<li>PPN</li>
					<li>PPH. (khusus instalasi).</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='4' class='justify'>
				Tahap Pembayaran: <?=$tahap_pembayaran;?>
				<br>
				<ul>
					<li>SKBDN harus diterima oleh PT ORI POLYTEC COMPOSITES H+7 hari kerja setelah PO atau Pembayaran DP diterima, dengan menerima nomor swift SKBDN</li>
					<li>SKBDN setelah di terbitkan tidak bisa di batalkan tanpa persetujuan kedua belah pihak</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='4' class='justify'>Seluruh proses pembayaran akan dilakukan melalui transfer ke rekening kami di OBC NISP. - Cabang Mangga Dua Le Grandeur, atas nama PT. ORI POLYTEC COMPOSITES dengan Nomor Rekening: 0278.0001.6993 (USD) atau 0278.0001.6993 (IDR) dan terhitung 30 hari setelah PT. ORI POLYTEC COMPOSITES/ <?=$customer;?> menerima fotokopi/salinan dokumen terkait seperti (Surat Jalan / DO, BA / DCN / MDR, Faktur, PPN.PPH).</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td colspan='4' class='justify'>Produk akan diproduksi setelah Uang Muka Penjualan diterima PT Ori Polytec Composites.</td>
		</tr>
	</tbody>
</table>
<?php
echo "<pagebreak />";
?>
<table class='gridtable3' width='100%' border='0' cellpadding='0' cellspacing='0'>
	<tbody>
		<tr>
			<td width='5%'></td>
			<td width='4%'></td>
			<td width='27%'></td>
			<td width='3%'></td>
			<td width='61%' style='vertical-align:top; text-align:right;'><img src='<?=$sroot;?>/assets/images/alamatori.png' style='float:right; padding-top:-42px;' alt="" height='160' width='90'></td>
		</tr>
		<tr>
			<td><b>C.</b></td>
			<td colspan='5'><b>Warranty</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>Garansi tersebut tidak mencakup kerusakan-kerusakan yang disebabkan; kecelakaan, penyalahgunaan, kesalahan penggunaan, bencana alam, perbaikan yang dilakukan oleh orang-orang yang tidak diberikan hak yang semestinya tanpa sepengetahuan atau persetujuan dari Brother dan perwakilannya.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>Periode Garansi diberikan 12 Bulan, terhitung sejak tanggal pengiriman yang tercantum dalam surat Jalan PT ORI.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='3' class='justify'>
				Proses Klaim Garansi dilakukan dengan ketentuan sebagai berikut:<br>
				<ul>
					<li>(a) <?=$customer;?> akan mengajukan pemberitahuan dan deskripsi masalah atas barang atau material kepada Perusahaan; dan</li>
					<li>(b)	Tim dari Perusahaan bersama <?=$customer;?> akan melakukan pemeriksaan terhadap kerusakan, cacat barang, atau kegagalan material untuk menentukan klaim garansi, yang selanjutnya akan dituangkan dalam berita acara dan ditandatangani oleh Perusahaan serta <?=$customer;?></li>
				</ul>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td colspan='3' class='justify'>Segala dokumen, surat, dan korespondensi yang berkaitan dengan ruang lingkup Perjanjian tunduk terhadap ketentuan garansi dalam Perjanjian.</td>
		</tr>
		<tr>
			<td colspan='5' height='5px;'></td>
		</tr>
		<tr>
			<td><b>D.</b></td>
			<td colspan='5'><b>Penalty</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>Setiap terjadi keterlambatan pembayaran akan dikenakan denda 0.1% per hari, maksimum sebesar 10% dari total harga;</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>
				Setiap terjadi keterlambatan pengiriman dan/atau penghentian produksi baik di workshop maupun di proyek atas permintaan dari pihak Customer, maka akan dikenakan tambahan biaya  sebagai berikut:<br>
				<ul>
					<li>Biaya penyimpanan sementara akan ditagihkan jika produk berada di Pabrik kami dan/atau terjadi penghentian pengiriman sementara dengan jangka waktu 2 minggu  atau lebih ,  dengan harga Rp 15.000 / m2 / hari. (jika export USD 1 / m2 / hari)</li>
					<li>PT Ori Polytec Composites berhak untuk melakukan penagihan sesuai dengan tanggal pengiriman yang telah disepakati sejak awal pemesanan.</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='3' class='justify'>Biaya Penalty tidak akan dikenakan dalam bentuk apapun apabila dalam keadaan kahar atau Force Majeure.</td>
		</tr>
		<tr>
			<td colspan='5' height='5px;'></td>
		</tr>
		<tr>
			<td><b>E.</b></td>
			<td colspan='5'><b>Perselisihan</b></td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>1.</td>
			<td colspan='3' class='justify'>Setiap perselisihan yang timbul akibat pelaksanaan kegiatan sebagaimana dimaksud dalam Proposal Penawaran ini, seluruh pihak yang terkait akan menyelesaikannya secara musyawarah untuk mencapai mufakat.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>2.</td>
			<td colspan='3' class='justify'>Apabila proses musyawarah untuk mufakat tidak terjadi, maka seluruh pihak yang terkait setuju dan sepakat untuk menyelesaikannya dan memilih domisili hukum keperdataaan di Pengadilan Negeri Jakarta Pusat.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>3.</td>
			<td colspan='3' class='justify'>Hukum yang mengatur perjanjian ini adalah Undang-undang Republik Indonesia.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>4.</td>
			<td colspan='3' class='justify'>Order tidak bisa di cancel atau di batalkan dalam kondisi dan situasi apapun.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>5.</td>
			<td colspan='3' class='justify'>Penurunan MTO maksimal <b>10% (sepuluh persen)</b> dari total jumlah yang tercantum dalam Purchase Order (PO) tanpa dikenakan biaya tambahan.</td>
		</tr>
		<tr>
			<td></td>
			<td class='valign'>6.</td>
			<td colspan='3' class='justify'>Apabila penurunan melebihi batas 10% (sepuluh persen), maka <b>seluruh selisih kuantitas yang melebihi batas tersebut akan dikenakan biaya material</b> sesuai harga satuan dalam PO atau berdasarkan perhitungan yang disepakati bersama secara tertulis oleh Para Pihak.</td>
		</tr>
		<tr>
			<td colspan='5' height='5px;'></td>
		</tr>
		<tr>
			<td><b>F.</b></td>
			<td colspan='4'><b>Kerahasiaan</b></td>
		</tr>
		<tr>
            <td></td>
			<td colspan='4' class='justify'>
				Seluruh informasi, data, spesifikasi teknis, harga, gambar, dan dokumen lain yang disampaikan dalam Penawaran Harga ini bersifat <b>rahasia</b> dan hanya ditujukan untuk keperluan evaluasi dan pertimbangan oleh Pihak Penerima Penawaran.
			</td>
		</tr>
	</table>
	<?php
	echo "<pagebreak />";
	?>
	<table class='gridtable3' width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td><b>G.</b></td>
			<td colspan='4'><b>Penutup</b></td>
		</tr>
		<tr>
			<td colspan='5' class='justify'>
				Demikian beberapa hal pokok yang dapat kami sampaikan, apabila masih terdapat hal-hal yang kurang jelas, mohon Bapak/Ibu berkenan untuk dapat menghubungi kantor perwakilan ataupun agen kami. Terima kasih atas perhatian dan kepercayaannya.
			</td>
		</tr>
		<tr>
			<td colspan='5' height='30px;'></td>
		</tr>
		<tr>
			<td colspan='5'>
				Hormat kami,
			</td>
		</tr>
		<tr>
			<td colspan='5' height='50px;'></td>
		</tr>
		<tr>
			<td colspan='5'>
				<?=$sales;?>
			</td>
		</tr>
	</tbody>
</table>

<?php
echo "<pagebreak />";
?>
<table class="gridtable" width='100%' border='0' cellpadding='2'>
	<tbody>
		<tr>
			<td colspan='11' style='background-color: white; padding-left:0px; font-size: 14px; height:30px; vertical-align:top; text-align: left;'><b>LAMPIRAN</b></td>
		</tr>
	</tbody>
	<?php
	$SUM = 0;
	if(!empty($detail_product)){
	?>
		<tbody>
			<tr>
				<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>PRODUCT</b></th>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='3' width='22%'>Item Product</th>
				<th class="text-center" width='7%'>Dim 1</th>
				<th class="text-center" width='7%'>Dim 2</th>
				<th class="text-center" width='10%'>Series</th>
				<th class="text-center" width='17%'>Specification</th>
				<th class="text-center" width='9%'>Qty</th>
				<th class="text-center" width='6%'>Unit</th>
				<th class="text-center" width='11%'>Unit Price</th>
				<th class="text-center" width='11%'>Total Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			
			$no = 0;
			foreach($detail_product AS $val => $valx){
				$no++;
				$dataSum = 0;
				if($valx['qty'] <> 0){
					$dataSum	= $valx['cost'] * $kurs;
				}
				$SUM += $dataSum;
				
				if($valx['id_category'] == 'pipe' OR $valx['id_category'] == 'pipe slongsong'){
					$unitT = "Btg";
				}
				else{
					$unitT = "Pcs";
				}
				echo "<tr>";
					echo "<td colspan='3'>".strtoupper($valx['id_category'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_1'])."</td>";
					echo "<td align='right'>".number_format($valx['diameter_2'])."</td>";
					echo "<td align='center'>".$valx['series']."</td>";
					echo "<td align='left'>".spec_bq($valx['id_milik'])."</td>";
					echo "<td align='center'>".$valx['qty']."</td>";
					echo "<td align='center'>".$unitT."</td>";
					echo "<td align='right'>".number_format($dataSum / $valx['qty'])."</td>";
					echo "<td align='right'>".number_format($dataSum)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL COST  OF PRODUCT</b></td>
				<td align='right'><b><?= number_format($SUM);?></b></td>
			</tr>
		</tbody>
		<?php
	}
	$SUM_NONFRP = 0;
	if(!empty($non_frp)){
		echo "<tbody>";
			echo "<tr>";
				echo "<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>BQ NON FRP</b></th>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Qty</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center'>Unit Price</th>";
				echo "<th class='text-center'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		
		foreach($non_frp AS $val => $valx){
			$SUM_NONFRP += $valx['price_total'] * $kurs;
			
			$harga_kurs = $valx['price_total'] * $kurs;
			
			$get_detail = $this->db->get_where('accessories', array('id'=>$valx['caregory_sub']))->result();
			$radx = (!empty($get_detail[0]->radius) AND $get_detail[0]->radius > 0)?'x '.floatval($get_detail[0]->radius).' R':'';
			$nama_acc = "";
			if($valx['category'] == 'baut'){
				$nama_acc = strtoupper($get_detail[0]->nama).' M '.floatval($get_detail[0]->diameter).' x '.floatval($get_detail[0]->panjang).' L '.$radx;
			}
			if($valx['category'] == 'plate'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'gasket'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material).' x '.floatval($get_detail[0]->thickness)." T";
			}
			if($valx['category'] == 'lainnya'){
				$nama_acc = strtoupper($get_detail[0]->nama.', '.$get_detail[0]->material.' - '.$get_detail[0]->dimensi.' - '.$get_detail[0]->spesifikasi);
			}
				
			$qty = $valx['qty'];
			$satuan = $valx['option_type'];
			if($valx['category'] == 'plate'){
				$qty = $valx['weight'];
				$satuan = '1';
			}
			echo "<tr>";
				echo "<td colspan='7'>".$nama_acc."</td>";
				echo "<td align='right'>".number_format($qty,2)."</td>";
				echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $satuan)))."</td>";
				$harga_tot = number_format($harga_kurs,2);
				$harga_sat = number_format($harga_kurs/$qty,2);
				if($harga_kurs <= 0){
					$harga_tot = 'No Quote';
					$harga_sat = 'No Quote';
				}
				echo "<td align='right'>".$harga_sat."</td>";
				echo "<td align='right'>".$harga_tot."</td>";
			echo "</tr>";
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL COST OF BQ NON FRP</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_NONFRP)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	$SUM_MAT = 0;
	if(!empty($material)){
		echo "<tbody>";
			echo "<tr>";
				echo "<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>MATERIAL</b></th>";
			echo "</tr>";
			echo "<tr class='bg-bluexyz'>";
				echo "<th class='text-center' colspan='7'>Material Name</th>";
				echo "<th class='text-center'>Qty</th>";
				echo "<th class='text-center'>Unit</th>";
				echo "<th class='text-center' colspan='2'>Total Price</th>";
			echo "</tr>";
		echo "</tbody>";
		echo "<tbody class='body_x'>";
		
		foreach($material AS $val => $valx){
			if($valx['price_total'] > 0){
				$SUM_MAT += $valx['price_total'] * $kurs;
				echo "<tr>";
					echo "<td colspan='7'>".strtoupper(get_name('raw_materials', 'nm_material', 'id_material', $valx['caregory_sub']))."</td>";
					echo "<td align='right'>".number_format($valx['qty_berat'],2)."</td>";
					echo "<td align='center'>".ucfirst(strtolower(get_name('raw_pieces', 'kode_satuan', 'id_satuan', $valx['option_type'])))."</td>";
					echo "<td align='right' colspan='2'>".number_format($valx['price_total'] * $kurs)."</td>";
				echo "</tr>";
			}
		}
		echo "<tr class='FootColor'>";
			echo "<td colspan='10'><b>TOTAL COST OF MATERIAL</b></td> ";
			echo "<td align='right'><b>".number_format($SUM_MAT)."</b></td>";
		echo "</tr>";
		echo "</tbody>";
	}
	?>
	<?php
	if(!empty($enggenering)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>ENGINEERING</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='7'>Test Name</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Unit</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no1=0;
		$SUM1=0;
		foreach($enggenering AS $val => $valx){
			$Qty1 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$Price1 	= (!empty($valx['price']))?number_format($valx['price'] * $kurs):'-';
			$TotalP1 	= (!empty($valx['price_total']))?number_format($valx['price_total'] * $kurs):'-';
			$SUM1 += $valx['price_total'] * $kurs;
			$no1++;
			echo "<tr>";
				echo "<td colspan='7'>".strtoupper($valx['name'])."</td>";
				echo "<td align='center'>".$Qty1."</td>";
				echo "<td align='center'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$valx['unit']."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$Price1."</div>";
				echo "</td>";
				echo "<td align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".$TotalP1."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF ENGINEERING</b></td>
			<td align='right'><b><?= number_format($SUM1);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($packing)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>PACKING COST</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='9'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no2=0;
		$SUM2=0;
		foreach($packing AS $val => $valx){
			$no2++;
			$SUM2 += $valx['price_total'] * $kurs;
			echo "<tr>";
				echo "<td colspan='9'>".strtoupper($valx['name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['option_type']);
				echo "</td>";
				echo "<td align='right'>".number_format($valx['price_total'] * $kurs);
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF PACKING</b></td>
			<td align='right'><b><?= number_format($SUM2);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($export)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>TRUCKING EXPORT</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center" colspan='6'>Category</th>
			<th class="text-center">Type</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Fumigation</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no3=0;
		$SUM3=0;
		foreach($export AS $val => $valx){
			$Qty3 	= (!empty($valx['qty']))?$valx['qty']:'-';
			$SUM3 += $valx['price_total'] * $kurs;
			$no3++;
			echo "<tr>";
				echo "<td colspan='6'>".strtoupper($valx['shipping_name']);
				echo "</td>";
				echo "<td align='center'>".strtoupper($valx['type'])."</td>";
				echo "<td align='center'>".$Qty3."</td>";
				echo "<td align='right'>".number_format($valx['fumigasi'] * $kurs)."</td>";
				echo "<td align='right'>".number_format($valx['price'] * $kurs)."</td>";
				echo "<td align='right'>".number_format($valx['price_total'] * $kurs)."</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF TRUCKING EXPORT</b></td>
			<td align='right'><b><?= number_format($SUM3);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	if(!empty($local)){
	?>
	<tbody>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='11'><b>TRUCKING LOKAL</b></th>
		</tr>
		<tr class='bg-bluexyz'>
			<th class="text-center">Via</th>
			<th class="text-center" colspan='3'>Area</th>
			<th class="text-center" colspan='2'>Destination</th>
			<th class="text-center" colspan='2'>Vehicle</th>
			<th class="text-center">Qty</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total Price</th>
		</tr>
	</tbody>
	<tbody class='body_x'>
		<?php
		$no4=0;
		$SUM4=0;
		foreach($local AS $val => $valx){
			$SUM4 += $valx['price_total'] * $kurs;
			$Areax = ($valx['area'] == '0')?'-':strtoupper($valx['area']);
			$Tujuanx = ($valx['tujuan'] == '0')?'-':strtoupper($valx['tujuan']);
			if(strtolower($valx['caregory_sub']) == 'via laut' || strtolower($valx['caregory_sub']) == 'via darat'){
				$Kendaraanx = ($valx['nama_truck'] == '')?'-':strtoupper($valx['nama_truck']);
			}
			else{
				$Kendaraanx = strtoupper($valx['kendaraan']);
			}
			$Qty4 	= (!empty($valx['qty']))?$valx['qty']:'-';
			
			$no4++;
			echo "<tr>";
				echo "<td style='vertical-align:top' align='left'>".strtoupper($valx['caregory_sub'])."</td>";
				echo "<td style='vertical-align:top' align='left' colspan='3'>".$Areax."</td>";
				echo "<td style='vertical-align:top' align='left' colspan='2'>".$Tujuanx."</td>";
				echo "<td style='vertical-align:top' align='left' colspan='2'>".$Kendaraanx."</td>";
				echo "<td style='vertical-align:top' align='center'>".$Qty4."</td>";
				echo "<td style='vertical-align:top' align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price'] * $kurs)."</div>";
				echo "</td>";
				echo "<td style='vertical-align:top' align='right'>";
					echo "<div id='unit_".$no1."' class='unitEngCost'>".number_format($valx['price_total'] * $kurs)."</div>";
				echo "</td>";
			echo "</tr>";
		}
		?>
		<tr class='FootColor'>
			<td colspan='10'><b>TOTAL COST OF TRUCKING LOKAL</b></td>
			<td align='right'><b><?= number_format($SUM4);?></b></td>
		</tr>
	</tbody>
	<?php
	}
	?>
	<?php
		$SUM_OTHER = 0;
		if(!empty($otherArray)){
		?>
		<tbody>
			<tr>
				<td class='bg-bluexyz' style='text-align:left;' colspan='11'><b>OTHER</b></td>
			</tr>
			<tr class='bg-bluexyz'>
				<th class="text-center" colspan='8'>Description</th>
				<th class="text-center">Qty</th>
				<th class="text-center">Unit Price</th>
				<th class="text-center">Price</th>
			</tr>
		</tbody>
		<tbody class='body_x'>
			<?php
			
			foreach($otherArray AS $val => $value){
				$SUM_OTHER += $value['price_total'] * $kurs;
				
				echo "<tr>";
					echo "<td style='vertical-align:top' align='left'  colspan='8'>".strtoupper($value['caregory_sub'])."</td>";
					echo "<td style='vertical-align:top' align='center'>".number_format($value['qty'],2)."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price'] * $kurs,2)."</td>";
					echo "<td style='vertical-align:top' align='right'>".number_format($value['price_total'] * $kurs,2)."</td>";
				echo "</tr>";
			}
			?>
			<tr class='FootColor'>
				<td colspan='10'><b>TOTAL OTHER</b></td>
				<td align='right'><b><?= number_format($SUM_OTHER,2);?></b></td>
			</tr>
		</tbody>
		<?php
		}
		?>
	<tfoot>
		<tr>
			<th class='bg-bluexyz' style='text-align:left;' colspan='10'>TOTAL QUOTATION</th>
			<th class='bg-bluexyz' style='text-align:right;'><?=  number_format($SUM + $SUM2 + $SUM3 + $SUM4 + $SUM1 + $SUM_MAT + $SUM_NONFRP + $SUM_OTHER);?></th>
		</tr>
		<?php
		// if($resultNONFRP_Num > 0){
			// echo "<tr>";
				// echo "<th align='left' style='background-color: #0e5ca9; color:white; font-size:10px' colspan='9'></th>";
				// echo "<th align='center' style='background-color: #0e5ca9; color:white; font-size:10px'>IDR</th>";
				// echo "<th align='right' style='background-color: #0e5ca9; color:white; font-size:10px'>".number_format($SUM_NONFRP, 2)."</th>";
			// echo "</tr>";
		// }
		?>
	</tfoot>
	
	
</table>
	
	
<style>
	
	.justify{
		text-align: justify;
	}
	
	.valign{
		vertical-align: top;
	}
	
	table.gridtable3 {
		font-family: "Garamond", serif;
		font-size:12px;
		color:#333333;
		margin-left: 60px;
		margin-right: 60px;
	}
	table.gridtable3 td {
		padding: 3px;
	}
	table.gridtable3 td.cols {
		padding: 3px;
	}
	
	<!-- BAGIAN LAMPIRAN -->
	.header_style{
		border-style: solid;
		border-bottom-width: 5px;
		border-bottom-color: #0e5ca9;
		background-color: #ea572b;
		padding: 15px;
		color: white;
	}
	
	.header_style2{
		font-family: verdana,arial,sans-serif;
		font-size:12px;
		color:#333333;
		border-width: 1px;
		border-style: solid;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: black;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	table.gridtable th {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #cccccc;
	}
	table.gridtable th.head {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #7f7f7f;
		color: #ffffff;
	}
	table.gridtable td {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
		
	}
	table.gridtable td.cols {
		border-width: 1px;
		padding: 4px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #f2f2f2;
	}
	
	
	table.gridtable2 {
		font-family: verdana,arial,sans-serif;
		font-size:10px;
		color:#333333;
		border-width: 1px;
		border-color: #ffffff;
		border-collapse: collapse;
		margin-top: 0cm;
		margin-left: 0.5cm;
		margin-right: 0.5cm;
		margin-bottom: 0cm;
	}
	
	table.gridtable2 td {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
		
	}
	table.gridtable2 td.cols {
		border-width: 1px;
		padding: 5px;
		border-style: solid;
		border-color: #ffffff;
		background-color: #ffffff;
	}
	
	#space{
		padding: 3px;
		width: 180px;
		height: 1px;
	}
	p {
		margin: 0 0 0 0;
	}
</style>