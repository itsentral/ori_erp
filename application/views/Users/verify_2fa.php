# view verify_2fa

<h2>Verifikasi OTP</h2>
<?php if ($this->session->flashdata('error')): ?>
    <p style="color:red"><?= $this->session->flashdata('error') ?></p>
<?php endif; ?>
<form method="post" action="<?= site_url('login/check_otp') ?>">
    <label>Masukkan OTP dari Google Authenticator:</label><br>
    <input type="text" name="otp" required>
    <br><button type="submit">Verifikasi</button>
</form>