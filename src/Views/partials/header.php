<header class="main-header">
    <div class="nav-content">
        <a href="index.php?url=dashboard" class="header-logo-container">
            <img src="assets/images/SoundHaven.png" alt="Logo SoundHaven" class="header-logo-img">
            <div class="header-logo-text">
                <span class="logo-main-title">SoundHaven</span>
                <span class="logo-subtitle">Acervo Digital</span>
            </div>
        </a>
        
        <div class="header-right-menu">
            <div class="profile-dropdown-container" id="profileDropdown">
                <div class="profile-avatar-trigger" id="avatarTrigger" role="button" tabindex="0" aria-haspopup="true" aria-expanded="false"> 
                    <img src="assets/images/default-avatar.png" alt="Perfil" class="profile-avatar">
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>

                <nav class="dropdown-menu" id="myDropdown" aria-label="Menu do Usuário">
                    <div class="dropdown-user-info" style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: left; background-color: rgba(0, 0, 0, 0.2);">
                        <span style="display: block; font-weight: 600; color: #fff; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?>
                        </span>
                        <span style="display: block; font-size: 0.75rem; color: var(--text-secondary); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?>
                        </span>
                    </div>
                    <ul>
                        <li><a href="index.php?url=dashboard"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li><a href="index.php?url=colecao"><i class="fas fa-list-alt"></i> Minha Coleção</a></li>
                        <li><a href="index.php?url=artistas"><i class="fas fa-microphone-lines"></i> Artistas</a></li>
                        <li><a href="index.php?url=loja"><i class="fas fa-store"></i> Loja</a></li>
                        <?php if (isset($_SESSION['usuario_login']) && $_SESSION['usuario_login'] === 'admin'): ?>
                            <li><a href="index.php?url=dashboard#solicitacoes"><i class="fas fa-users-cog"></i> Gerenciar Usuários</a></li>
                        <?php endif; ?>
                        <li class="separator"></li>
                        <li><a href="index.php?url=perfil"><i class="fas fa-user-circle"></i> Meu Perfil</a></li>
                        <li><a href="index.php?url=relatorios"><i class="fas fa-file-pdf"></i> Relatórios</a></li>
                        <li><a href="index.php?url=logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>
<script>
(function() {
    if (window.__headerDropdownInitialized) return;
    window.__headerDropdownInitialized = true;

    const trigger = document.getElementById('avatarTrigger');
    const dropdown = document.getElementById('myDropdown');
    if (trigger && dropdown) {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
            const expanded = dropdown.classList.contains('show');
            trigger.setAttribute('aria-expanded', expanded);
        });
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !trigger.contains(e.target)) {
                dropdown.classList.remove('show');
                trigger.setAttribute('aria-expanded', 'false');
            }
        });
    }
})();
</script>