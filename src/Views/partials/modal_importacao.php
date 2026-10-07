<div id="importModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <span class="modal-close" onclick="document.getElementById('importModal').style.display='none'">&times;</span>
        <h2 style="color: var(--accent-color);">Importar Lote (CSV)</h2>
        <form action="?url=loja" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="import_csv">
            
            <div class="edit-field-group" style="margin-top: 20px;">
                <label>SELECIONE O ARQUIVO CSV</label>
                <input type="file" name="csv_file" accept=".csv" required>
            </div>
            
            <p style="font-size: 0.8em; color: var(--text-secondary); margin-top: 10px; line-height: 1.4;">
                Formato esperado (cabeçalho ou dados separados por vírgula ou ponto-e-vírgula):<br>
                <code>titulo, artista, url_capa, num_catalogo, tipo_album, gravadora, genero, estilo, data_lancamento, preco_sugerido</code>
            </p>

            <button type="submit" class="btn" style="background-color: var(--action-positive); width: 100%; margin-top: 20px;">
                <i class="fas fa-upload"></i> PROCESSAR IMPORTAÇÃO
            </button>
        </form>
    </div>
</div>
