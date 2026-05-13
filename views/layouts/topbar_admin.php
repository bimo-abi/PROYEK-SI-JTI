<div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
    <div class="topbar-left" style="display: flex; align-items: center; gap: 15px;">
        <i class="fas fa-bars" style="cursor: pointer; color: #666; font-size: 1.2rem;"></i>
        <h4 style="margin: 0; color: #333; font-weight: 500;">
            <?= isset($page_title) ? $page_title : 'Panel Administrasi' ?>
        </h4>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 20px;">
        <div class="user-info" style="display: flex; align-items: center; gap: 10px;">
             <span style="font-size: 0.9rem; color: #333; font-weight: 500;"><?= htmlspecialchars($_SESSION['nama']) ?></span>
             <img src="<?= $foto_sidebar ?? '../../assets/img/avatar.png' ?>?t=<?= time() ?>"
                  alt="Profile"
                  style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #00a2ed;">
        </div>
    </div>
</div>