<div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    
    <div class="topbar-left">
        <span style="font-weight: 600; color: #333; font-size: 1.1rem;">
            <?= isset($page_title) ? htmlspecialchars($page_title) : 'Panel Administrasi' ?>
        </span>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 12px;">
        <div style="text-align: right;">
            <span style="display: block; font-weight: 600; color: #444; font-size: 0.9rem;">
                <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>
            </span>
            <span style="display: block; font-size: 0.75rem; color: #00a2ed; margin-top: -2px;">Online</span>
        </div>

        <img src="<?= $foto_sidebar ?>?t=<?= time() ?>"
             alt="Admin Avatar"
             style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid #00a2ed;">
    </div>
</div>