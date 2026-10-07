<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundHaven - Loja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/loja.css">
    <link rel="icon" type="image/x-icon" href="assets/images/SoundHaven.ico">
    <style>
        .pagination {
            margin: 15px 0;
        }
        .pagination:first-of-type {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .pagination:last-of-type {
            margin-top: 30px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="page-wrapper">
        <div class="spacer-left">
            <button type="button" class="btn btn-add" onclick="window.location.href='index.php?url=novo_album_loja'">
                <i class="fas fa-compact-disc" style="font-size: 1.2em; margin-left: -5px; vertical-align: top;"></i>
                ADICIONAR ÁLBUM
            </button>
            <button type="button" class="btn btn-import" onclick="document.getElementById('importModal').style.display='block'">
                <i class="fas fa-file-csv"></i> IMPORTAR ÁLBUNS
            </button>
        </div>

        <main class="content">
            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div style="background: rgba(16, 185, 129, 0.2); border: 1px solid #10b981; color: #86efac; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <!-- Navegador de páginas ACIMA (antes dos cards) -->
            <?php include __DIR__ . '/../partials/paginacao.php'; ?>

            <div class="store-grid">
                <?php if (empty($albuns)): ?>
                    <p style="grid-column: span 5; text-align: center; color: var(--text-secondary); padding: 50px;">
                        Nenhum álbum encontrado para os filtros aplicados.
                    </p>
                <?php else: ?>
                    <?php foreach ($albuns as $album): ?>
                        <article class="album-card album-card-modern" style="cursor:pointer; position: relative;" data-album='<?= htmlspecialchars(json_encode($album), ENT_QUOTES, 'UTF-8') ?>'>
                            <img src="<?= htmlspecialchars($album['capa_url'] ?: 'assets/images/placeholder.jpg') ?>" alt="Capa" onerror="this.src='assets/images/placeholder.jpg'">
                            <div class="album-info">
                                <span class="album-title"><?= htmlspecialchars($album['titulo']) ?></span>
                                <span class="artist-name"><?= htmlspecialchars($album['artista_nome'] ?? $album['artista']) ?></span>
                                <span class="release-year">
                                    <?= (!empty($album['data_lancamento']) && $album['data_lancamento'] !== '0000-00-00') ? date('Y', strtotime($album['data_lancamento'])) : 'N/D' ?>
                                </span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Navegador de páginas ABAIXO (depois dos cards) -->
            <?php include __DIR__ . '/../partials/paginacao.php'; ?>
        </main>
        
        <?php include __DIR__ . '/../partials/sidebar_filtros.php'; ?>
    </div>

    <?php include __DIR__ . '/../partials/modal_detalhes.php'; ?>
    <?php include __DIR__ . '/../partials/modal_edicao.php'; ?>
    <?php include __DIR__ . '/../partials/modal_importacao.php'; ?>

    <script src="assets/js/loja.js"></script>
</body>
</html>
