<?php
$this->load->view('include/side_menu');
?>

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3> 
		<div class="box-tool pull-right">
		
		</div><br><br>
                <form method="get">
                Dari: <input type="date" name="dari" id="dari" required>
                Sampai: <input type="date" name="sampai" id="sampai" required> 
                <button type="submit">Filter</button>
            </form>

	</div>


<br>
            <div class='col-sm-8'>
                <button type='button' class='btn btn-md btn-primary' id='download_excel_header2'  title='Excel'>Download</i></button>
            </div>


<div class="box-body">
        <table id="example1" class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nomor Jurnal</th>
            <th>Keterangan</th>
            <th>Nomor Invoice</th>
            <th>Nomor SO</th>
            <th>Customer</th>
            <th>Revenue</th>
            <th>COGS</th>
            <th>Persentase (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        foreach($results as $r): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r->tanggal; ?></td>
            <td><?= $r->nomor_jurnal; ?></td>
            <td><?= $r->keterangan; ?></td>
            <td><?= $r->no_invoice; ?></td>
            <td><?= $r->so_number; ?></td>
            <td><?= $r->customer; ?></td>
            <td><?= number_format($r->revenue, 2); ?></td>
            <td><?= number_format($r->cogs, 2); ?></td>
            <td><?= number_format($r->persentase, 2); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
<div id="form-data"></div>
<?php $this->load->view('include/footer'); ?>
<script>
$(document).on('click', '#download_excel_header2', function(){
		let range = $('#dari').val();
		var tgl_awal 	= $('#dari').val();
		var tgl_akhir 	= $('#sampai').val();
		if(range == ''){
			alert('Range date wajib diisi !!!')
			return false
		}
		var Links		= 'laporan_jurnal/excel_report_subgudang3/'+tgl_awal+'/'+tgl_akhir;
		window.open(Links,'_blank');
	});

    </script>

