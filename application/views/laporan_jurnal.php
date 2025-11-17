<!DOCTYPE html>
<html>
<head>
    <title>Laporan Jurnal</title>
    <style>
        table { border-collapse: collapse; width: 100%; } 
        td, th { border: 1px solid #000; padding: 6px; }
    </style>
</head>
<body>

<h2>Laporan Jurnal (Revenue vs COGS)</h2>

<form method="get">
    Dari: <input type="date" name="dari" required>
    Sampai: <input type="date" name="sampai" required>
    <button type="submit">Filter</button>
</form>

<br>
<a href="?download=excel&dari=<?= $_GET['dari'] ?? '' ?>&sampai=<?= $_GET['sampai'] ?? '' ?>">
    <button type="button">Download Excel</button>
</a>


<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Nomor Jurnal</th>
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
            <td><?= $r->no_invoice; ?></td>
            <td><?= $r->no_so; ?></td>
            <td><?= $r->customer; ?></td>
            <td><?= number_format($r->revenue, 2); ?></td>
            <td><?= number_format($r->cogs, 2); ?></td>
            <td><?= number_format($r->persentase, 2); ?>%</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
