<?php 
$token = $_GET['token'] ?? ''; 
?>
<form action="../../process/auth_process.php?action=simpan_password_baru" method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?php
            if ($_GET['error'] == 'password') echo "Konfirmasi password tidak sesuai.";
            else if ($_GET['error'] == 'password_length') echo "Kata sandi harus 8-12 karakter.";
            ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label>Kata Sandi Baru (8-12 Karakter)</label>
        <div class="input-wrapper">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" minlength="8" maxlength="12" required>
        </div>
    </div>

    <div class="form-group">
        <label>Konfirmasi Kata Sandi Baru</label>
        <div class="input-wrapper">
            <i class="fas fa-shield-halved"></i>
            <input type="password" name="confirm_password" minlength="8" maxlength="12" required>
        </div>
    </div>

    <button type="submit" class="btn-primary">Simpan Password Baru</button>
</form>