<?php
$total_debet  = $total_debet  ?? 0;
$total_kredit = $total_kredit ?? 0;
$is_balance   = $is_balance   ?? false;
?>

<!-- Header info -->
<div class="row" style="margin-bottom:10px;">
    <div class="col-sm-6">
        <table class="table table-condensed table-bordered">
            <tr><th width="35%">Nomor JV</th><td><b><?= $header->nomor ?></b></td></tr>
            <tr><th>Tanggal</th><td><?= date('d-M-Y', strtotime($header->tgl)) ?></td></tr>
            <tr><th>Jenis Transaksi</th><td><span class="label label-info"><?= strtoupper($header->jenis_transaksi) ?></span></td></tr>
            <tr><th>Keterangan</th><td><?= $header->keterangan ?></td></tr>
            <tr><th>Memo</th><td><?= $header->memo ?></td></tr>
        </table>
    </div>
    <div class="col-sm-6">
        <table class="table table-condensed table-bordered">
            <tr>
                <th width="35%">Status</th>
                <td>
                    <?php if($header->status == 'posted'): ?>
                        <span class="label label-success">POSTED</span>
                    <?php elseif($header->status == 'error'): ?>
                        <span class="label label-danger">ERROR</span>
                    <?php else: ?>
                        <span class="label label-warning">PENDING</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr><th>Posted At</th><td><?= !empty($header->posted_at) ? date('d-M-Y H:i:s', strtotime($header->posted_at)) : '-' ?></td></tr>
            <tr>
                <th>Balance</th>
                <td>
                    <?php if($is_balance): ?>
                        <span class="label label-success"><i class="fa fa-check"></i> BALANCE</span>
                    <?php else: ?>
                        <span class="label label-danger"><i class="fa fa-times"></i> TIDAK BALANCE</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if(!empty($header->error_msg)): ?>
            <tr><th>Error</th><td class="text-danger"><?= htmlspecialchars($header->error_msg) ?></td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Line items -->
<table class="table table-striped table-bordered table-condensed" width="100%">
    <thead>
        <tr class="bg-blue">
            <th class="text-center" width="3%">#</th>
            <th class="text-center" width="12%">No Perkiraan</th>
            <th class="text-center">Keterangan</th>
            <th class="text-center" width="10%">No Reff</th>
            <th class="text-center" width="10%">Jenis Jurnal</th>
            <th class="text-center" width="12%">Debet</th>
            <th class="text-center" width="12%">Kredit</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach($details as $d): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><b><?= $d['no_perkiraan'] ?></b></td>
            <td><?= $d['keterangan'] ?></td>
            <td class="text-center"><?= $d['no_reff'] ?></td>
            <td class="text-center"><small><?= $d['jenis_jurnal'] ?></small></td>
            <td class="text-right"><?= $d['debet'] > 0 ? number_format($d['debet'],2) : '-' ?></td>
            <td class="text-right"><?= $d['kredit'] > 0 ? number_format($d['kredit'],2) : '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr class="<?= $is_balance ? 'bg-green' : 'bg-red' ?>">
            <th colspan="5" class="text-right text-white">TOTAL</th>
            <th class="text-right text-white"><?= number_format($total_debet,2) ?></th>
            <th class="text-right text-white"><?= number_format($total_kredit,2) ?></th>
        </tr>
        <?php if(!$is_balance): ?>
        <tr>
            <td colspan="7" class="text-center text-danger">
                <b><i class="fa fa-exclamation-triangle"></i> 
                Selisih: <?= number_format(abs($total_debet - $total_kredit),2) ?></b>
            </td>
        </tr>
        <?php endif; ?>
    </tfoot>
</table>
