<div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="topbar-title" style="font-weight: 700; color: #1e293b; font-size: 1.1rem; display: flex; align-items: center; gap: 10px;">
        <i class="fas fa-chalkboard-teacher" style="color: #4f46e5;"></i>
        <?= isset($current_page) ? ucwords(str_replace('_', ' ', $current_page)) : 'Panel Dosen' ?>
    </div>

    <div class="topbar-user" style="display: flex; align-items: center; gap: 12px;">
        <div class="user-info" style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 5px 15px; border-radius: 50px; border: 1px solid #f1f5f9;">
            <div style="text-align: right; line-height: 1.2;">
                <span style="display: block; font-weight: 600; color: #334155; font-size: 0.85rem;">
                    <?= htmlspecialchars($_SESSION['nama'] ?? 'User') ?>
                </span>
                <small style="color: #22c55e; font-weight: 600; font-size: 0.7rem;">Online</small>
            </div>

            <img src="<?= $foto_sidebar ?>?t=<?= time() ?>"
                alt="Profile"
                style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #4f46e5;">
        </div>
    </div>
</div>