<div id="albumModal" class="modal">
    <div class="modal-content modal-detalhes-loja">
        <span class="modal-close" onclick="closeModal()">&times;</span>
        
        <div class="modal-layout-grid">
            <div class="modal-capa-container">
                <img id="modalImg" src="" alt="Capa">
            </div>

            <div class="modal-info-container">
                <h2 id="modalTitle"></h2>
                
                <div class="info-data-grid">
                    <p><label>ARTISTA</label><span id="modalArtist"></span></p>
                    <p><label>GRAVADORA</label><span id="modalLabel"></span></p>
                    <p><label>LANÇAMENTO</label><span id="modalDate"></span></p>
                    <p><label>TIPO</label><span id="modalType"></span></p>
                    <p><label>PREÇO SUGERIDO</label><span id="modalPrice"></span></p>
                    <p><label>CADASTRADO POR</label><span id="modalUser"></span></p>
                </div>
                
                <div class="modal-actions">
                    <button type="button" id="btnOpenEdit" class="btn btn-edit" style="height: 35px;">
                        <i class="fa-solid fa-pen"></i> Editar
                    </button>
                    
                    <form method="POST" id="formDelete" onsubmit="return confirm('Deseja realmente descartar este álbum?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-delete"><i class="fa-solid fa-trash"></i> Descartar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
