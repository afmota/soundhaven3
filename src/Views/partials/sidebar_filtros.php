<aside class="sidebar-filters">
    <form method="GET" action="">
        <input type="hidden" name="url" value="loja">
        <h3><i class="fa-solid fa-sliders"></i> Filtros</h3>
        
        <div class="filter-group">
            <label>Título do Álbum</label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($filters['titulo'] ?? '') ?>" placeholder="Ex: Master of Puppets">
        </div>

        <div class="filter-group">
            <label>Artista</label>
            <select name="artista">
                <option value="">Todos os Artistas</option>
                <?php foreach ($artistas as $art): ?>
                    <option value="<?= htmlspecialchars($art) ?>" <?= ($filters['artista'] ?? '') === $art ? 'selected' : '' ?>>
                        <?= htmlspecialchars($art) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Gravadora</label>
            <select name="gravadora">
                <option value="">Todas as Gravadoras</option>
                <?php foreach ($gravadoras as $grav): ?>
                    <option value="<?= htmlspecialchars($grav) ?>" <?= ($filters['gravadora'] ?? '') === $grav ? 'selected' : '' ?>>
                        <?= htmlspecialchars($grav) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Tipo</label>
            <select name="tipo_album">
                <option value="">Todos os Tipos</option>
                <?php foreach ($tipos as $t): ?>
                    <option value="<?= htmlspecialchars($t) ?>" <?= ($filters['tipo_album'] ?? '') === $t ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Gênero</label>
            <select name="genero">
                <option value="">Todos os Gêneros</option>
                <?php foreach ($generos as $g): ?>
                    <option value="<?= htmlspecialchars($g) ?>" <?= ($filters['genero'] ?? '') === $g ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Ordenação</label>
            <select name="ordem">
                <option value="recentes" <?= ($filters['ordem'] ?? '') === 'recentes' ? 'selected' : '' ?>>Mais Recentes (Inclusão)</option>
                <option value="antigos" <?= ($filters['ordem'] ?? '') === 'antigos' ? 'selected' : '' ?>>Mais Antigos (Inclusão)</option>
                <option value="titulo_asc" <?= ($filters['ordem'] ?? '') === 'titulo_asc' ? 'selected' : '' ?>>Título (A-Z)</option>
                <option value="titulo_desc" <?= ($filters['ordem'] ?? '') === 'titulo_desc' ? 'selected' : '' ?>>Título (Z-A)</option>
                <option value="artista_asc" <?= ($filters['ordem'] ?? '') === 'artista_asc' ? 'selected' : '' ?>>Artista (A-Z)</option>
                <option value="artista_desc" <?= ($filters['ordem'] ?? '') === 'artista_desc' ? 'selected' : '' ?>>Artista (Z-A)</option>
                <option value="ano_desc" <?= ($filters['ordem'] ?? '') === 'ano_desc' ? 'selected' : '' ?>>Ano (Mais Novo)</option>
                <option value="ano_asc" <?= ($filters['ordem'] ?? '') === 'ano_asc' ? 'selected' : '' ?>>Ano (Mais Antigo)</option>
            </select>
        </div>

        <button type="submit" class="btn btn-search">
            <i class="fa-solid fa-magnifying-glass"></i> Filtrar
        </button>
        <a href="?url=loja" class="btn btn-clear">
            <i class="fa-solid fa-rotate-left"></i> Limpar Filtros
        </a>
    </form>
</aside>
