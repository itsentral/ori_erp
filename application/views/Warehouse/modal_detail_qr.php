
<div class="box-body">
	<table width="100%">
		<thead>
			<tr>
				<td class="text-left" style='vertical-align:middle;' width='15%'>No PO</td>
				<td class="text-left" style='vertical-align:middle;' width='2%'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_po;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No Transaksi</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$kode_trans;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>Tanggal Penerimaan</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$resv;?></td>
			</tr>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>No ROS</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$no_ros;?></td>
			</tr>
            <?php
			$LINK = "-";
			if(!empty($dokumen_file)){
				$LINK = "<a href='".base_url($dokumen_file)."' target='_blank'>Download</a> ";
			}
			?>
			<tr>
				<td class="text-left" style='vertical-align:middle;'>File Dokumen</td>
				<td class="text-left" style='vertical-align:middle;'>:</td>
				<td class="text-left" style='vertical-align:middle;'><?=$LINK;?></td>
			</tr>
		</thead>
	</table><br>
	<table id="my-grid" class="table table-striped table-bordered table-hover table-condensed" width="100%">
		<thead id='head_table'>
			<tr class='bg-blue'>
				<th class="text-center" style='vertical-align:middle;' width='5%'>#</th>
				<th class="text-center" style='vertical-align:middle;'>Name Barang</th>
				<th class="text-center" style='vertical-align:middle;' width='10%'>Qty Order</th>
                <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Diterima</th>
                <!-- <th class="text-center" style='vertical-align:middle;' width='10%'>Qty Kurang</th> -->
			</tr>
		</thead>
		<tbody>
			<?php
            $No=0;
			foreach($result AS $val => $valx){ $No++;
                $qty_oke 	= number_format($valx['check_qty_oke'],4);
                $qty_rusak 	= number_format($valx['check_qty_rusak'],4);
                $qty_kurang = number_format($valx['qty_order'] - $valx['check_qty_oke'],4);

                $listCheckMaterial = $this->db
                                            ->select('a.*, b.id_satuan, b.id_packing')
                                            ->join('raw_materials b','a.id_material=b.id_material')
                                            ->get_where('warehouse_adjustment_check a',array('a.id_detail'=>$valx['id']))
                                            ->result_array();
				
				echo "<tr>";
					echo "<td align='center'>".$No."</td>";
					echo "<td>".$valx['nm_material']."</td>";
					echo "<td align='center'>".number_format($valx['qty_order'],4)."</td>";
					echo "<td align='center' class='text-bold'>".$qty_oke."</td>";
					// echo "<td align='right'>".$qty_kurang."</td>";
				echo "</tr>";
                echo "<tr>";
					echo "<td></td>";
					echo "<td colspan='3'>";
                        echo "<table border='1' width='100%'>";
                            echo "<tr>";
                                echo "<th class='paddingCustom text-center' width='4%'>#</th>";
                                echo "<th class='paddingCustom text-center' width='10%'>Qty Incoming</th>";
                                echo "<th class='paddingCustom text-center' width='7%'>Unit</th>";
                                echo "<th class='paddingCustom text-center' width='7%'>Konversi</th>";
                                echo "<th class='paddingCustom text-center' width='10%'>Qty Packing</th>";
                                echo "<th class='paddingCustom text-center' width='7%'>Unit Pack</th>";
                                echo "<th class='paddingCustom text-center' width='10%'>Qty NG</th>";
                                echo "<th class='paddingCustom text-center' width='10%'>Expired</th>";
                                echo "<th class='paddingCustom text-center' width='10%'>Dokumen</th>";
                                echo "<th class='paddingCustom text-center'>LOT Description</th>";
                                echo "<th class='paddingCustom text-center' width='9%'>Check Date</th>";
                                echo "<th class='paddingCustom text-center' width='4%'>QR</th>";
                            echo "</tr>";
                            if(!empty($listCheckMaterial)){
                                foreach ($listCheckMaterial as $key => $value) { $key++;
                                    $expired_date = (!empty($value['expired_date']) AND $value['expired_date'] != '0000-00-00')?date('d-M-Y',strtotime($value['expired_date'])):'-';
                                    $qty_packing = 0;
                                    $konversi = get_name('raw_materials','nilai_konversi','id_material',$value['id_material']);
                                    if($value['qty_oke'] > 0 AND $konversi > 0){
                                        $qty_packing = $value['qty_oke']/$konversi;
                                    }
                                    echo "<tr>";
                                        echo "<td class='paddingCustom text-center'>".$key."</td>";
                                        echo "<td class='paddingCustom text-right'>".number_format($value['qty_oke'],4)."</td>";
                                        echo "<td class='paddingCustom text-center'>".strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$value['id_satuan']))."</td>";
                                        echo "<td class='paddingCustom text-center'>".number_format($konversi,2)."</td>";
                                        echo "<td class='paddingCustom text-right'>".number_format($qty_packing,4)."</td>";
                                        echo "<td class='paddingCustom text-center'>".strtolower(get_name('raw_pieces','kode_satuan','id_satuan',$value['id_packing']))."</td>";
                                        echo "<td class='paddingCustom text-right'>".number_format($value['qty_rusak'],4)."</td>";
                                        echo "<td class='paddingCustom text-center'>".$expired_date."</td>";
                                        $LINK = "";
                                        if(!empty($value['dokumen'])){
                                            $LINK = "<a href='".base_url($value['dokumen'])."' target='_blank'>Download</a> ";
                                        }
                                        echo "<td class='paddingCustom text-center'>".$LINK."</td>";
                                        echo "<td class='paddingCustom text-left'>".$value['keterangan']."</td>";
                                        echo "<td class='paddingCustom text-center'>".date('d-M-Y H:i',strtotime($value['update_date']))."</td>";
                                        echo "<td class='paddingCustom text-center'><input type='checkbox' class='check_box' name='qr[]' value='".$value['id']."' ></td>";
                                    echo "</tr>";
                                }
                            }
                            else{
                                echo "<tr>";
                                    echo "<td colspan='12'>Belum ada incoming</td>";
                                echo "</tr>";
                            }
                        echo "</table>";
                    echo "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
    <button type='button' class='btn btn-success' style='float:right; margin-top:10px;' id='download_qr'>Download QR</button>
</div>
<style>
    .paddingCustom{
        padding:2px;
    }
</style>
<script>
	swal.close();

    $(document).on('click', '#download_qr', function(e) {
        e.preventDefault();

        var countCheckedCheck = $('.check_box:checked').length;
        if (countCheckedCheck > 0) {
            var checkboxx = [];
            $('.check_box:checked').each(function() {
                checkboxx.push($(this).val());
            });

            // var formData = new FormData($('#form_download_qr')[0]);
            $.ajax({
                url: base_url + active_controller + '/save_download_qr',
                type: "POST",
                data: {
                    'checkboxx': checkboxx
                },
                cache: false,
                dataType: 'json',
                success: function(data) {
                    window.open(base_url + active_controller +'/download_incoming_checked_qr/' + data.id, '_blank');
                },
                error: function() {
                    swal({
                        title: "Error Message !",
                        text: 'An Error Occured During Process. Please try again..',
                        type: "warning",
                        timer: 7000
                    });
                    $('#checkMaterial').prop('disabled', false);
                }
            });
        }
        else{
            swal("Error !", "Please check at least on check box first !", "error");
        }
    });
</script>