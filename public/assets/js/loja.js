/**
 * SoundHaven - Script Global de Interatividade da Loja
 */
document.addEventListener('DOMContentLoaded', () => {
    // Referências dos Modais
    const modal = document.getElementById('albumModal');
    const editModal = document.getElementById('editModal');
    const createModal = document.getElementById('createModal');

    let currentAlbumData = null;

    // --- 1. PREVIEWS DE CAPA (LIVE UPDATE) ---

    // Preview na Edição
    const inputCapaEdit = document.getElementById('editModalCapaUrl');
    const imgPreviewEdit = document.getElementById('editModalImg');
    if (inputCapaEdit && imgPreviewEdit) {
        inputCapaEdit.addEventListener('input', (e) => {
            imgPreviewEdit.src = e.target.value || 'assets/images/placeholder.jpg';
        });
    }

    // Preview na Inclusão
    const inputCapaCreate = document.getElementById('createModalCapaUrl');
    const imgPreviewCreate = document.getElementById('createModalImg');
    if (inputCapaCreate && imgPreviewCreate) {
        inputCapaCreate.addEventListener('input', (e) => {
            imgPreviewCreate.src = e.target.value || 'assets/images/placeholder.jpg';
        });
    }

    // --- 2. EVENTOS DE CLIQUE ---
    document.addEventListener('click', (e) => {
        const card = e.target.closest('.album-card');
        if (card) {
            try {
                currentAlbumData = JSON.parse(card.getAttribute('data-album'));
                openModal(currentAlbumData);
            } catch (err) {
                console.error("Erro ao ler dados do álbum:", err);
            }
        }
    });

    // Abrir Edição (botão dentro do modal de detalhes)
    const btnOpenEdit = document.getElementById('btnOpenEdit');
    if (btnOpenEdit) {
        btnOpenEdit.addEventListener('click', () => {
            if (currentAlbumData) {
                closeModal();
                openEditModal(currentAlbumData);
            }
        });
    }

    // --- 3. MENU DE PERFIL ---
    if (!window.__headerDropdownInitialized) {
        const avatarTrigger = document.getElementById('avatarTrigger');
        const dropdown = document.getElementById('myDropdown');

        if (avatarTrigger && dropdown) {
            avatarTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
        }
    }

    // --- 4. FECHAR AO CLICAR FORA ---
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
        if (e.target === editModal) closeEditModal();
        if (e.target === createModal) closeCreateModal();

        const dropdown = document.getElementById('myDropdown');
        const avatarTrigger = document.getElementById('avatarTrigger');
        if (dropdown && !dropdown.contains(e.target) && (!avatarTrigger || !avatarTrigger.contains(e.target))) {
            dropdown.classList.remove('show');
        }
    });
});

/**
 * FUNÇÕES DE CONTROLE DOS MODAIS
 */

// MODAL DE DETALHES
function openModal(album) {
    if (!album) return;

    const setTxt = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.innerText = val || 'N/D';
    };

    setTxt('modalTitle', album.titulo);
    setTxt('modalArtist', album.artista_nome || album.artista);
    setTxt('modalLabel', album.gravadora_nome || album.gravadora);
    setTxt('modalDate', formatDate(album.data_lancamento));
    setTxt('modalType', album.tipo_desc || album.tipo_album);

    const price = album.preco_sugerido || album.preco;
    setTxt('modalPrice', price ? 'R$ ' + parseFloat(price).toFixed(2).replace('.', ',') : 'N/D');

    setTxt('modalUser', album.usuario || 'N/D');

    const modalImg = document.getElementById('modalImg');
    if (modalImg) {
        modalImg.src = album.capa_url || album.url_capa || 'assets/images/placeholder.jpg';
        modalImg.onerror = function() { this.src = 'assets/images/placeholder.jpg'; };
    }

    const deleteIdField = document.getElementById('deleteId');
    if (deleteIdField) {
        deleteIdField.value = album.album_id || album.id_album || '';
    }

    const modal = document.getElementById('albumModal');
    if (modal) modal.style.display = "block";
}

function closeModal() {
    const modal = document.getElementById('albumModal');
    if (modal) modal.style.display = "none";
}

// MODAL DE EDIÇÃO
function openEditModal(album) {
    if (!album) return;

    const setVal = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = (value !== null && value !== undefined) ? String(value) : "";
    };

    const headerTitle = document.getElementById('editModalHeaderTitle');
    if (headerTitle) headerTitle.innerText = `Editar ${album.titulo || ''}`;

    const imgPreview = document.getElementById('editModalImg');
    if (imgPreview) {
        imgPreview.src = album.capa_url || album.url_capa || 'assets/images/placeholder.jpg';
        imgPreview.onerror = function() { this.src = 'assets/images/placeholder.jpg'; };
    }

    setVal('editModalAlbumId', album.album_id || album.id_album);
    setVal('editModalCapaUrl', album.capa_url || album.url_capa || '');
    setVal('editModalTitulo', album.titulo || '');
    setVal('editModalArtista', album.artista || album.artista_nome || '');
    setVal('editModalGravadora', album.gravadora || album.gravadora_nome || '');
    setVal('editModalTipo', album.tipo_album || album.tipo_desc || '');
    setVal('editModalData', album.data_lancamento || '');
    setVal('editModalPreco', album.preco_sugerido || album.preco || '');
    setVal('editModalCatalogo', album.num_catalogo || album.numero_catalogo || '');
    setVal('editModalGenero', album.genero || '');
    setVal('editModalEstilo', album.estilo || '');

    const editModal = document.getElementById('editModal');
    if (editModal) editModal.style.display = "block";
}

function closeEditModal() {
    const editModal = document.getElementById('editModal');
    if (editModal) editModal.style.display = "none";
}

// MODAL DE INCLUSÃO
function openCreateModal() {
    const createModal = document.getElementById('createModal');
    if (createModal) createModal.style.display = "block";
}

function closeCreateModal() {
    const createModal = document.getElementById('createModal');
    if (createModal) {
        createModal.style.display = "none";
        const form = createModal.querySelector('form');
        if (form) form.reset();
        const preview = document.getElementById('createModalImg');
        if (preview) preview.src = 'assets/images/placeholder.jpg';
    }
}

// FORMATADOR DE DATA
function formatDate(dateStr) {
    if (!dateStr || dateStr === 'N/D' || dateStr === '0000-00-00') return 'N/D';
    const parts = dateStr.split('-');
    return parts.length !== 3 ? dateStr : `${parts[2]}/${parts[1]}/${parts[0]}`;
}