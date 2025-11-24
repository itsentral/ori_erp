<?php
$this->load->view('include/side_menu');
?>

<div class="box box-primary">
	<div class="box-header">
		<h3 class="box-title"><?php echo $title;?></h3> 
		<div class="box-tool pull-right">
		
		</div><br><br>
                <form method="get">
                Dari: <input type="date" name="dari" required>
                Sampai: <input type="date" name="sampai" required> 
                <button type="submit">Filter</button>
            </form>

	</div>


<br>
<a href="?download=excel&dari=<?= isset($_GET['dari']) ? $_GET['dari'] : '' ?>
&sampai=<?= isset($_GET['sampai']) ? $_GET['sampai'] : '' ?>">
    <button type="button">Download Excel</button>
</a>


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

