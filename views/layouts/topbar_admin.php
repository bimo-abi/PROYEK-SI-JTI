<div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="topbar-left" style="display: flex; align-items: center; gap: 15px;">
        <i class="fas fa-bars" style="cursor: pointer; color: #666; font-size: 1.2rem;"></i>
        <h4 style="margin: 0; color: #333; font-weight: 500;">
            <?= isset($page_title) ? $page_title : 'Panel Administrasi' ?>
        </h4>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 20px;">
        <div class="admin-badge" style="display: flex; align-items: center; gap: 10px; background: #f8f9fa; padding: 5px 15px; border-radius: 50px; border: 1px solid #eee;">
            <i class="fas fa-user-shield" style="color: #00a2ed;"></i>
            <span style="font-size: 0.85rem; font-weight: 600; color: #555;">ADMIN</span>
        </div>
        <div class="user-info">
             <span style="font-size: 0.9rem; color: #333;"><?= htmlspecialchars($_SESSION['nama']) ?></span>
        </div>
    </div>
</div>