<!-- form_otp.php -->
<form method="post" action="<?= base_url('otp/send_otp') ?>">
    <input type="text" name="phone" placeholder="Nomor WhatsApp (628xxxx)">
    <button type="submit">Kirim OTP</button>
</form>

<form method="post" action="<?= base_url('otp/verify_otp') ?>">
    <input type="text" name="otp" placeholder="Masukkan OTP">
    <button type="submit">Verifikasi</button>
</form>
