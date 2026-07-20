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
            margin: 30px 40px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }
        .header-table .logo {
            width: 80px;
            border: 1px solid #333;
            text-align: center;
            padding: 10px;
        }
        .header-table .title-cell {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #333;
        }
        .header-table .doc-info {
            border: 1px solid #333;
            font-size: 11px;
            padding: 5px 10px;
        }
        .header-table .doc-info table td {
            padding: 2px 5px;
            border: none;
        }
        .info-section {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-section td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-label {
            width: 100px;
        }
        .checkbox-section {
            margin-bottom: 15px;
        }
        .checkbox-section span {
            margin-right: 80px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th, .detail-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            vertical-align: top;
        }
        .detail-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
        }
        .detail-table td.center {
            text-align: center;
        }
        .note-section {
            margin-top: 10px;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-table td {
            text-align: center;
            padding: 10px 5px;
            vertical-align: top;
            width: 25%;
        }
        .signature-space {
            height: 60px;
        }
        .note-box {
            border: 1px solid #333;
            padding: 10px;
            margin-top: 15px;
            font-size: 11px;
        }
        .note-box ul {
            margin: 5px 0;
            padding-left: 20px;
        }
        .note-box li {
            margin-bottom: 3px;
        }
        @media print {
            body { margin: 15px 20px; }
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

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="logo" width="80">
                <img src="<?= base_url('assets/images/ori_logo.jpg'); ?>" alt="Logo" style="max-width:60px; max-height:50px;">
            </td>
            <td class="title-cell">FORM SERAH TERIMA ASSET & TOOLS</td>
            <td class="doc-info" width="180">
                <table>
                    <tr><td>Document No.</td><td>: FM-S2.2-12</td></tr>
                    <tr><td>Rev. No.</td><td>: 1</td></tr>
                    <tr><td>Issue Date</td><td>: 11 Sept 2024</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- INFO SECTION -->
    <table class="info-section">
        <tr>
            <td class="info-label">Nama</td>
            <td width="5">:</td>
            <td><?= $data['receiver']; ?></td>
            <td width="100" style="text-align:right;">Tanggal</td>
            <td width="5" style="text-align:center;">:</td>
            <td width="150"><?= date('d F Y', strtotime($data['created_date'])); ?></td>
        </tr>
        <tr>
            <td class="info-label">Lokasi</td>
            <td>:</td>
            <td><?= $data['lokasi']; ?></td>
            <td></td><td></td><td></td>
        </tr>
        <tr>
            <td class="info-label">Department</td>
            <td>:</td>
            <td><?= $data['nm_dept']; ?></td>
            <td></td><td></td><td></td>
        </tr>
    </table>

    <!-- CHECKBOX SECTION -->
    <div class="checkbox-section">
        <p>Telah dilakukan,</p>
        <span>&#9744; Penerimaan Asset / Tools*</span>
        <span>&#9744; Pemasangan Sparepart / Komponen</span>
        <p>Dengan jumlah dan spesifikasi sebagai berikut :</p>
    </div>

    <!-- DETAIL TABLE -->
    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Deskripsi Asset / Tools</th>
                <th width="12%">TTB</th>
                <th width="8%">QTY</th>
                <th width="15%">KODE ASSET</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($detail)): ?>
                <?php foreach($detail as $idx => $det): ?>
                <tr>
                    <td class="center" rowspan="4"><?= $idx + 1; ?></td>
                    <td><b>Nama Asset / Tools :</b> <?= $det['nm_asset']; ?></td>
                    <td></td>
                    <td></td>
                    <td><?= $det['new_assets_code']; ?></td>
                </tr>
                <tr>
                    <td><b>Model</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= $det['model']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b>Merk</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= $det['merk']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><b>Kode Asset Utama / Kode Asset lama* :</b> <?= $det['assets_code']; ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr>
                <td colspan="2" class="center"><b>Total</b></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- DISCLAIMER -->
    <div class="note-section">
        <p>*Asset/Tools/Sparepart/komponen tersebut telah kami terima/pasangkan dalam kondisi baik dan dapat digunakan untuk kegiatan operasional perusahaan.<br>
        <i>*coret yang tidak perlu</i></p>
    </div>

    <!-- SIGNATURE -->
    <table class="signature-table">
        <tr>
            <td>Diserahkan Oleh</td>
            <td>Diterima Oleh</td>
            <td>Disetujui Oleh</td>
            <td>Diketahui Oleh</td>
        </tr>
        <tr>
            <td class="signature-space"></td>
            <td class="signature-space"></td>
            <td class="signature-space"></td>
            <td class="signature-space"></td>
        </tr>
        <tr>
            <td>( Bag Gudang )</td>
            <td>( User )</td>
            <td>( User Dept Head )</td>
            <td>( Fixed Asset )</td>
        </tr>
    </table>
    <p style="font-size:10px;"><i>Wajib dilengkapi dengan nama jelas</i></p>

    <!-- NOTE BOX -->
    <div class="note-box">
        <b>Note :</b>
        <ul>
            <li>Pihak Penerima Asset Harus bertanggung Jawab sepenuhnya untuk menjaga Asset & Tools ini sebagaimana mestinya dan sesuai dengan masa manfaat dari Asset tsb.</li>
            <li>Form ini diprintout Rangkap 3 (1. Asli bag. Fixed Asset ; 2. User Asset ; 3. Pihak Gudang )</li>
            <li>Dilampirkan juga copy surat jalan lalu dikirim ke Fixed Asset.</li>
        </ul>
    </div>
</body>
</html>
