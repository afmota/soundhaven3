<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoundHaven - Novo Álbum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/adquirir_album.css">
    <link rel="icon" type="image/x-icon" href="assets/images/SoundHaven.ico">
    <style>
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            border: none;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.2s ease;
        }
        .btn-cancel {
            background-color: #ef4444;
        }
        .btn-cancel:hover {
            background-color: #dc2626;
        }
        .btn-save {
            background-color: var(--action-positive);
        }
        .btn-save:hover {
            background-color: var(--action-positive-hover);
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="content">
        <h2 id="edicaoHeaderTitle">Adicionar Novo Álbum na Loja</h2>

        <?php if (!empty($erroValidacao)): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;" role="alert">
                <strong>Corrija os campos obrigatórios antes de continuar:</strong>
                <ul style="margin-left: 20px; margin-top: 5px;">
                    <?php foreach ($erroValidacao as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?url=loja" id="formAdicionarAlbum">
            <input type="hidden" name="action" value="create">

            <div id="edicaoPaginaBody">
                <div class="edit-modal-header-row">
                    <img id="edicaoImg" class="edit-modal-capa" src="assets/images/placeholder.jpg" alt="Capa do álbum" onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="edit-field-group">
                        <label>URL DA CAPA</label>
                        <input type="text" name="url_capa" id="edicaoCapaUrl" value="<?= htmlspecialchars($album['url_capa'] ?? '') ?>" placeholder="https://..." oninput="document.getElementById('edicaoImg').src = this.value || 'assets/images/placeholder.jpg'">
                    </div>
                </div>

                <hr class="edit-modal-separator">

                <div class="edit-field-group">
                    <label>TÍTULO DO ÁLBUM *</label>
                    <input type="text" name="titulo" id="edicaoTitulo" value="<?= htmlspecialchars($album['titulo'] ?? '') ?>" required placeholder="Ex: Master of Puppets">
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>ARTISTA *</label>
                        <input type="text" name="artista" id="edicaoArtista" value="<?= htmlspecialchars($album['artista'] ?? '') ?>" list="listaSugestoesArtistas" required placeholder="Ex: Metallica">
                        <datalist id="listaSugestoesArtistas">
                            <?php if (!empty($artistas)): ?>
                                <?php foreach ($artistas as $art): ?>
                                    <option value="<?= htmlspecialchars($art) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="edit-field-group">
                        <label>GRAVADORA</label>
                        <input type="text" name="gravadora" id="edicaoGravadora" value="<?= htmlspecialchars($album['gravadora'] ?? '') ?>" list="listaSugestoesGravadoras" placeholder="Ex: Elektra">
                        <datalist id="listaSugestoesGravadoras">
                            <?php if (!empty($gravadoras)): ?>
                                <?php foreach ($gravadoras as $grav): ?>
                                    <option value="<?= htmlspecialchars($grav) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>DATA DE LANÇAMENTO *</label>
                        <input type="date" name="data_lancamento" value="<?= htmlspecialchars($album['data_lancamento'] ?? '') ?>" required>
                    </div>
                    <div class="edit-field-group">
                        <label>DATA DE AQUISIÇÃO</label>
                        <input type="date" name="data_aquisicao" value="<?= htmlspecialchars($album['data_aquisicao'] ?? date('Y-m-d')) ?>">
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>TIPO DE ÁLBUM</label>
                        <select name="tipo_album" id="edicaoTipo">
                            <option value="CD" <?= ($album['tipo_album'] ?? 'CD') === 'CD' ? 'selected' : '' ?>>CD</option>
                            <option value="Vinil" <?= ($album['tipo_album'] ?? '') === 'Vinil' ? 'selected' : '' ?>>Vinil</option>
                            <option value="Cassete" <?= ($album['tipo_album'] ?? '') === 'Cassete' ? 'selected' : '' ?>>Cassete</option>
                            <option value="Digital" <?= ($album['tipo_album'] ?? '') === 'Digital' ? 'selected' : '' ?>>Digital</option>
                            <option value="DVD" <?= ($album['tipo_album'] ?? '') === 'DVD' ? 'selected' : '' ?>>DVD</option>
                            <option value="Outro" <?= ($album['tipo_album'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                    <div class="edit-field-group">
                        <label>PREÇO SUGERIDO (R$)</label>
                        <input type="number" step="0.01" name="preco_sugerido" value="<?= htmlspecialchars($album['preco_sugerido'] ?? '') ?>" placeholder="0.00">
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>Nº DE CATÁLOGO</label>
                        <input type="text" name="num_catalogo" value="<?= htmlspecialchars($album['num_catalogo'] ?? '') ?>" placeholder="Ex: 88875120972">
                    </div>
                    <div class="edit-field-group">
                        <label>ORIGEM</label>
                        <input type="text" name="origem" value="<?= htmlspecialchars($album['origem'] ?? '') ?>" placeholder="Ex: Nacional, Importado...">
                    </div>
                </div>

                <hr class="edit-modal-separator">

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>GÊNERO</label>
                        <input type="text" name="genero" value="<?= htmlspecialchars($album['genero'] ?? '') ?>" list="listaSugestoesGeneros" placeholder="Ex: Rock, Metal...">
                        <datalist id="listaSugestoesGeneros">
                            <?php if (!empty($generos)): ?>
                                <?php foreach ($generos as $g): ?>
                                    <option value="<?= htmlspecialchars($g) ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="edit-field-group">
                        <label>ESTILO</label>
                        <input type="text" name="estilo" value="<?= htmlspecialchars($album['estilo'] ?? '') ?>" placeholder="Ex: Thrash Metal, Hard Rock...">
                    </div>
                </div>

                <div class="edit-field-group">
                    <label>FAIXAS (uma por linha ou separadas por vírgula)</label>
                    <textarea name="faixas" rows="5" style="width: 100%; background: #121212; border: 1px solid var(--border-color); color: var(--text-primary); border-radius: 4px; padding: 10px; font-family: inherit; font-size: 0.9em; box-sizing: border-box;" placeholder="1. Battery&#10;2. Master of Puppets&#10;3. The Thing That Should Not Be..."><?= htmlspecialchars($album['faixas'] ?? '') ?></textarea>
                </div>

                <div class="edicao-actions" style="margin-top:30px; display:flex; justify-content: flex-end; gap:10px;">
                    <a href="index.php?url=loja" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-save"><i class="fa-solid fa-check"></i> CADASTRAR ÁLBUM</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
