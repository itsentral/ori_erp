<?php
$idt = group_company();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Form Serah Terima Asset - <?= $data['form_no']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-title h2 {
            margin: 0;
            padding: 5px 0;
        }
        .header-title h3 {
            margin: 0;
            padding: 5px 0;
            font-weight: normal;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
        }
        .detail-table th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }
        .detail-table td.center {
            text-align: center;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table td {
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            width: 150px;
            margin: 60px auto 5px auto;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:20px;">
        <button onclick="window.print();" style="padding:8px 16px; cursor:pointer;">
            <b>Print</b>
        </button>
        <button onclick="window.close();" style="padding:8px 16px; cursor:pointer;">
            <b>Close</b>
        </button>
    </div>

    <div class="header-title">
        <h2><?= $idt->nm_perusahaan; ?></h2>
        <h3>FORM SERAH TERIMA ASSET</h3>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Form No.</td>
            <td>: <?= $data['form_no']; ?></td>
            <td class="info-label">Lokasi</td>
            <td>: <?= $data['lokasi']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Tanggal</td>
            <td>: <?= date('d F Y', strtotime($data['created_date'])); ?></td>
            <td class="info-label">Departemen</td>
            <td>: <?= $data['nm_dept']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Sender</td>
            <td>: <?= $data['sender']; ?></td>
            <td class="info-label">Receiver</td>
            <td>: <?= $data['receiver']; ?></td>
        </tr>
    </table>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Asset Name</th>
                <th>Assets Code</th>
                <th>New Assets Code</th>
                <th>Model</th>
                <th>Merk</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($detail)): ?>
                <?php foreach($detail as $idx => $det): ?>
                <tr>
                    <td class="center"><?= $idx + 1; ?></td>
                    <td><?= $det['nm_asset']; ?></td>
                    <td><?= $det['assets_code']; ?></td>
                    <td><?= $det['new_assets_code']; ?></td>
                    <td><?= $det['model']; ?></td>
                    <td><?= $det['merk']; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <b>Yang Menyerahkan</b><br>(Sender)
                <div class="signature-line"></div>
                <b><?= $data['sender']; ?></b>
            </td>
            <td>
                <b>Yang Menerima</b><br>(Receiver)
                <div class="signature-line"></div>
                <b><?= $data['receiver']; ?></b>
            </td>
            <td>
                <b>Mengetahui</b><br>(Atasan)
                <div class="signature-line"></div>
                ___________________
            </td>
        </tr>
    </table>
</body>
</html>
