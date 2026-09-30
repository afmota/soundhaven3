/**
 * SoundHaven - Script Global de Interatividade
 */
document.addEventListener('DOMContentLoaded', () => {
    // Referências dos Modais
    const modal = document.getElementById('albumModal');
    const editModal = document.getElementById('editModal');
    const createModal = document.getElementById('createModal'); // Novo modal

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
            currentAlbumData = JSON.parse(card.getAttribute('data-album'));
            openModal(currentAlbumData);
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
    const avatarTrigger = document.getElementById('avatarTrigger');
    const dropdown = document.getElementById('myDropdown');

    if (avatarTrigger) {
        avatarTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // --- 4. FECHAR AO CLICAR FORA ---
    window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
        if (e.target === editModal) closeEditModal();
        if (e.target === createModal) closeCreateModal(); // Fecha inclusão

        if (dropdown && !dropdown.contains(e.target) && !avatarTrigger.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
});

/**
 * FUNÇÕES DE CONTROLE DOS MODAIS
 */

// MODAL DE DETALHES
function openModal(album) {
    // 1. Preenchimento visual (o que já funcionava e não mexe no fluxo)
    document.getElementById('modalTitle').innerText = album.titulo;
    document.getElementById('modalArtist').innerText = album.artista_nome;
    document.getElementById('modalLabel').innerText = album.gravadora_nome || 'N/D';
    document.getElementById('modalDate').innerText = formatDate(album.data_lancamento);
    document.getElementById('modalImg').src = album.capa_url || 'assets/images/placeholder.jpg';
    document.getElementById('modalType').innerText = album.tipo_desc || 'N/D';

    // 2. Prepara o ID de descarte (que você disse que funciona)
    const deleteIdField = document.getElementById('deleteId');
    if (deleteIdField) deleteIdField.value = album.album_id;

    // 3. A CHINELADA: Redirecionamento forçado e isolado
    const btn = document.getElementById('btnAdquirirDireto');
    if (btn) {
        // Removemos qualquer comportamento antigo
        btn.onclick = null;

        // Definimos o novo comportamento NA HORA
        btn.onclick = function (e) {
            // Impede que o clique "vaze" para o resto da página (mata a propagação)
            e.preventDefault();
            e.stopPropagation();

            // Vai direto para o alvo
            window.location.href = `index.php?url=adquirir_album&id=${album.album_id}`;
        };
    }

    // Exibe o modal
    document.getElementById('albumModal').style.display = "block";
}

function closeModal() {
    document.getElementById('albumModal').style.display = "none";
}

// MODAL DE EDIÇÃO
function openEditModal(album) {
    const setVal = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.value = (value !== null && value !== undefined) ? String(value) : "";
    };

    const headerTitle = document.getElementById('editModalHeaderTitle');
    if (headerTitle) headerTitle.innerText = `Editar ${album.titulo}`;

    const imgPreview = document.getElementById('editModalImg');
    if (imgPreview) imgPreview.src = album.capa_url || 'assets/images/placeholder.jpg';

    setVal('editModalAlbumId', album.album_id);
    setVal('editModalCapaUrl', album.capa_url || '');
    setVal('editModalTitulo', album.titulo);
    setVal('editModalArtista', album.artista_id);
    //setVal('editModalGravadora', album.gravadora_id);
    setVal('editModalGravadora', album.gravadora_nome || '');
    setVal('editModalTipo', album.tipo_id);
    setVal('editModalData', album.data_lancamento || '');

    document.getElementById('editModal').style.display = "block";
}

function closeEditModal() {
    document.getElementById('editModal').style.display = "none";
}

// MODAL DE INCLUSÃO (NOVO)
function openCreateModal() {
    document.getElementById('createModal').style.display = "block";
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');
    modal.style.display = "none";
    // Limpa o formulário para a próxima vez
    const form = modal.querySelector('form');
    if (form) form.reset();
    // Reseta o preview da imagem para o placeholder
    const preview = document.getElementById('createModalImg');
    if (preview) preview.src = 'assets/images/placeholder.jpg';
}

// FORMATADOR DE DATA
function formatDate(dateStr) {
    if (!dateStr || dateStr === 'N/D') return 'N/D';
    const parts = dateStr.split('-');
    return parts.length !== 3 ? dateStr : `${parts[2]}/${parts[1]}/${parts[0]}`;
}