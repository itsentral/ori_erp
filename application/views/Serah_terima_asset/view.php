<?php
$this->load->view('include/side_menu');
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $title;?></h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><b>Form No.</b></td>
                        <td width="5%">:</td>
                        <td><?= $data['form_no']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Sender</b></td>
                        <td>:</td>
                        <td><?= $data['sender']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Receiver</b></td>
                        <td>:</td>
                        <td><?= $data['receiver']; ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><b>Lokasi</b></td>
                        <td width="5%">:</td>
                        <td><?= $data['lokasi']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Departemen</b></td>
                        <td>:</td>
                        <td><?= $data['nm_dept']; ?></td>
                    </tr>
                    <tr>
                        <td><b>Created Date</b></td>
                        <td>:</td>
                        <td><?= date('d-M-Y', strtotime($data['created_date'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <hr>
        <h4><b>Detail Assets</b></h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="bg-blue">
                        <th class="text-center" width="5%">No</th>
                        <th class="text-center">Asset Name</th>
                        <th class="text-center">Assets Code</th>
                        <th class="text-center">New Assets Code</th>
                        <th class="text-center">Model</th>
                        <th class="text-center">Merk</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($detail)): ?>
                        <?php foreach($detail as $idx => $det): ?>
                        <tr>
                            <td class="text-center"><?= $idx + 1; ?></td>
                            <td><?= $det['nm_asset']; ?></td>
                            <td><?= $det['assets_code']; ?></td>
                            <td><?= $det['new_assets_code']; ?></td>
                            <td><?= $det['model']; ?></td>
                            <td><?= $det['merk']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="box-footer">
            <a href="<?= site_url('serah_terima_asset/print_pdf/'.$data['id']); ?>" target="_blank" class="btn btn-md btn-default" style="float:right; margin-left:5px;">
                <i class="fa fa-print"></i> Print PDF
            </a>
            <?php echo form_button(array('type'=>'button','style'=>'float:right; width:100px;','class'=>'btn btn-md btn-danger','value'=>'back','content'=>'Back','onClick'=>'javascript:back()')); ?>
        </div>
    </div>
</div>
<?php $this->load->view('include/footer'); ?>
