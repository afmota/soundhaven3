document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalDetalhesArtista');
    const closeBtn = document.getElementById('closeModalArtista');
    
    // Elementos da Edição
    const modalEdicao = document.getElementById('modalEdicaoArtista');
    const btnEditar = document.getElementById('btnEditarArtista');
    const closeBtnEdicao = document.getElementById('closeModalEdicaoArtista');
    const btnCancelarEdicao = document.getElementById('btnCancelarEdicaoArtista');
    
    // Elementos de Preview da Imagem no Modal de Edição
    const inputUrl = document.getElementById('editArtistaImgUrl');
    const imgPreview = document.getElementById('editArtistaImgPreview');
    
    let artistaAtual = null;

    // 1. Lógica dos Cards (Abrir Detalhes)
    document.querySelectorAll('.js-open-artista-modal').forEach(card => {
        card.addEventListener('click', function() {
            try {
                artistaAtual = JSON.parse(this.dataset.artista);
                
                const txtNome = document.getElementById('detalheNomeArtista');
                const imgFoto = document.getElementById('detalheArtistaFoto');
                const bandeira = document.getElementById('detalheBandeira');
                const nomePais = document.getElementById('nomePaisTexto');
                const txtInicio = document.getElementById('detalheDataInicio');
                const txtGenero = document.getElementById('detalheGeneroPrincipal');
                const txtBio = document.getElementById('detalheBiografia');
                const btnVerAlbuns = document.getElementById('btnVerAlbunsArtista');

                if (txtNome) txtNome.textContent = artistaAtual.nome;
                if (imgFoto) imgFoto.src = artistaAtual.imagem_url || 'assets/images/placeholder_artist.jpg';
                if (nomePais) nomePais.textContent = artistaAtual.pais_nome || 'Origem não informada';

                if (bandeira) {
                    bandeira.className = 'fi'; 
                    if (artistaAtual.codigo_iso) {
                        const codigo = artistaAtual.codigo_iso.toLowerCase();
                        if (['gb-eng', 'gb-sct', 'gb-wls'].includes(codigo)) bandeira.classList.add('fis');
                        bandeira.classList.add('fi-' + codigo);
                    }
                }

                if (txtInicio) {
                    txtInicio.textContent = artistaAtual.ano_formacao || 'N/D';
                }
                if (txtGenero) txtGenero.textContent = artistaAtual.genero_nome || 'N/D';
                if (txtBio) txtBio.textContent = artistaAtual.biografia || 'Nenhuma biografia disponível.';
                
                if (btnVerAlbuns) {
                    btnVerAlbuns.onclick = () => window.location.href = `?url=colecao&artista_id=${artistaAtual.artista_id}`;
                }

                if (modal) modal.style.display = 'block';

                const containerSite = document.getElementById('containerSiteOficial');
                const txtSiteLink = document.getElementById('detalheSiteLink');
                const btnIrParaSite = document.getElementById('btnIrParaSite');

                // Verifica se a propriedade existe no objeto
                if (artistaAtual.site_oficial && artistaAtual.site_oficial.trim() !== "") {
                    containerSite.style.display = 'block'; // Mostra o container
                    txtSiteLink.textContent = artistaAtual.site_oficial;
                    
                    // Garante o protocolo para o link
                    const url = artistaAtual.site_oficial.startsWith('http') ? 
                                artistaAtual.site_oficial : 'https://' + artistaAtual.site_oficial;
                    btnIrParaSite.href = url;
                } else {
                    containerSite.style.display = 'none'; // Esconde se estiver vazio
                }

            } catch (e) {
                console.error("Erro ao abrir modal:", e);
            }
        });
    });

    // 2. Lógica de Edição (Abrir Modal de Edição e popular campos)
    if (btnEditar) {
        btnEditar.addEventListener('click', function() {
            if (!artistaAtual) return;

            // Define o título dinâmico
            document.getElementById('editArtistaModalTitle').textContent = 'Editar ' + artistaAtual.nome;

            // Preenche os campos
            document.getElementById('editArtistaId').value = artistaAtual.artista_id;
            document.getElementById('editArtistaNome').value = artistaAtual.nome;
            document.getElementById('editArtistaImgUrl').value = artistaAtual.imagem_url || '';
            document.getElementById('editArtistaPais').value = artistaAtual.pais_origem || '';
            document.getElementById('editArtistaGenero').value = artistaAtual.genero_principal || '';
            document.getElementById('editArtistaAnoFormacao').value = artistaAtual.ano_formacao || '';
            document.getElementById('editArtistaAnoEncerramento').value = artistaAtual.ano_encerramento || '';
            document.getElementById('editArtistaBio').value = artistaAtual.biografia || '';
            document.getElementById('editArtistaSite').value = artistaAtual.site_oficial || '';

            // Atualiza o preview da imagem
            if (imgPreview) {
                imgPreview.src = artistaAtual.imagem_url || 'assets/images/placeholder_artist.jpg';
            }

            if (modalEdicao) modalEdicao.style.display = 'block';
        });
    }

    // 3. Live Update: Preview da imagem ao colar URL
    if (inputUrl && imgPreview) {
        inputUrl.addEventListener('input', function() {
            const novaUrl = this.value.trim();
            imgPreview.src = novaUrl || 'assets/images/placeholder_artist.jpg';
        });
    }

    // 4. Fechamentos
    if (closeBtn) closeBtn.onclick = () => modal.style.display = 'none';
    if (closeBtnEdicao) closeBtnEdicao.onclick = () => modalEdicao.style.display = 'none';
    if (btnCancelarEdicao) btnCancelarEdicao.onclick = () => modalEdicao.style.display = 'none';
    
    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = 'none';
        if (event.target == modalEdicao) modalEdicao.style.display = 'none';
    };
});