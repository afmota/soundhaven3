<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeEditModal()">&times;</span>
        <h2 id="editModalHeaderTitle" style="color:var(--accent-color); margin-top:0; margin-bottom: 20px;"></h2>
        
        <form method="POST" action="">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="album_id" id="editModalAlbumId">
            
            <div id="editModalBody">
                <div class="edit-modal-header-row">
                    <img id="editModalImg" class="edit-modal-capa" src="" alt="Capa Edição">
                    <div class="edit-field-group">
                        <label>URL DA CAPA</label>
                        <input type="text" name="url_capa" id="editModalCapaUrl">
                    </div>
                </div>

                <hr class="edit-modal-separator">

                <div class="edit-field-group">
                    <label>TÍTULO DO ÁLBUM</label>
                    <input type="text" name="titulo" id="editModalTitulo" required>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>ARTISTA</label>
                        <input type="text" name="artista" id="editModalArtista" list="listaArtistasEdicao" placeholder="Nome do artista..." required>
                        <datalist id="listaArtistasEdicao">
                            <?php if (!empty($artistas)): ?>
                                <?php foreach ($artistas as $art): ?>
                                    <option value="<?= htmlspecialchars($art) ?>"></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="edit-field-group">
                        <label>GRAVADORA</label>
                        <input type="text" name="gravadora" id="editModalGravadora" list="listaGravadorasEdicao" placeholder="Selecione ou digite uma nova...">
                        <datalist id="listaGravadorasEdicao">
                            <?php if (!empty($gravadoras)): ?>
                                <?php foreach ($gravadoras as $grav): ?>
                                    <option value="<?= htmlspecialchars($grav) ?>"></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>TIPO DE ÁLBUM</label>
                        <input type="text" name="tipo_album" id="editModalTipo" list="listaTiposEdicao" placeholder="Ex: CD, Vinil, Digital...">
                        <datalist id="listaTiposEdicao">
                            <?php if (!empty($tipos)): ?>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= htmlspecialchars($t) ?>"></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="edit-field-group">
                        <label>DATA DE LANÇAMENTO</label>
                        <input type="date" name="data_lancamento" id="editModalData">
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>PREÇO SUGERIDO (R$)</label>
                        <input type="number" step="0.01" name="preco_sugerido" id="editModalPreco" placeholder="0.00">
                    </div>
                    <div class="edit-field-group">
                        <label>Nº DE CATÁLOGO</label>
                        <input type="text" name="num_catalogo" id="editModalCatalogo" placeholder="Ex: 88875120972">
                    </div>
                </div>

                <div class="edit-modal-row">
                    <div class="edit-field-group">
                        <label>GÊNERO</label>
                        <input type="text" name="genero" id="editModalGenero" list="listaGenerosEdicao" placeholder="Ex: Rock, Metal...">
                        <datalist id="listaGenerosEdicao">
                            <?php if (!empty($generos)): ?>
                                <?php foreach ($generos as $g): ?>
                                    <option value="<?= htmlspecialchars($g) ?>"></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="edit-field-group">
                        <label>ESTILO</label>
                        <input type="text" name="estilo" id="editModalEstilo" placeholder="Ex: Heavy Metal, Thrash...">
                    </div>
                </div>
                
                <div class="modal-actions" style="margin-top:30px; display:flex; justify-content: flex-end; gap:10px;">
                    <button type="button" class="btn" style="background-color: var(--action-destructive);" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn" style="background-color: var(--action-positive);"><i class="fa-solid fa-save"></i> Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
