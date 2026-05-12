<!-- <style>
    #submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease;
    }

    #submenu.show {
        max-height: 500px;
    }
</style>

<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item mb-2">
        <button
            id="btnSurat"
            class="btn nav-link text-white rounded-pill px-4 w-100 text-start d-flex justify-content-between align-items-center">

            Surat
            <i class="bi bi-chevron-down"></i>
        </button>
        <div id="submenu">
            <ul class="list-unstyled ps-4 pt-2">
                <li class="mb-2">
                    <a href="pengajuan_surat.php"
                        class="text-white text-decoration-none">
                        Pengajuan Surat
                    </a>
                </li>

                <li>
                    <a href="daftar_pengajuan.php"
                        class="text-white text-decoration-none">
                        Daftar Pengajuan Surat
                    </a>
                </li>
            </ul>
        </div>
    </li>
</ul>

<script>
    const btn = document.getElementById("btnSurat");
    const submenu = document.getElementById("submenu");

    btn.addEventListener("click", function() {
        submenu.classList.toggle("show");
    });
</script> -->