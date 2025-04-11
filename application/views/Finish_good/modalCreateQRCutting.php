<div class="box box-info">
    <div class="box-body bg-info">
        <div class="row">
            <div class="col-md-3">
                <div class="form-horizontal">
                    <label class="">Logo :</label>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="logo" id="logo_ORI" value="ORI" checked="checked">
                                    ORI
                                </label>
                            </div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="logo" id="logo_NOV" value="NOV">
                                    NOV
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-horizontal">
                    <label class="">Size :</label>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="size" id="size_lg" value="lg" checked="checked">
                                    Large
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="size" id="size_md" value="md">
                                    Medium
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="size" id="size_sm" value="sm">
                                    Small
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box-footer bg-info">
        <input type="hidden" id="idCutting" value="<?= $idCutting; ?>">
        <button type="button" style="margin-bottom:10px" id="print_qrcode_cutting" class="btn btn-success"><i class="fa fa-print"></i> Print QR Code</button>
    </div>
</div>