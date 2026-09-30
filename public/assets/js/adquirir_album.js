// assets/js/adquirir_album.js

// Variável global para controlar o índice das faixas
let faixaIndex = 0;

function popularTagsImportadas(data) {
    const mapa = {
        generos: ['generos', 'genres', 'genre'],
        estilos: ['estilos', 'styles', 'style'],
        produtores: ['produtores', 'producers', 'producer']
    };

    Object.entries(mapa).forEach(([chave, aliases]) => {
        const container = document.getElementById(chave === 'generos' ? 'containerGeneros' : chave === 'estilos' ? 'containerEstilos' : 'containerProdutores');
        if (!container) return;

        const valores = aliases
            .map((alias) => data?.[alias])
            .find((valor) => Array.isArray(valor) ? valor.length > 0 : Boolean(valor));

        const lista = Array.isArray(valores)
            ? valores
            : (typeof valores === 'string' && valores.trim() !== '' ? [valores] : []);

        if (lista.length === 0) return;

        container.innerHTML = '';

        lista.forEach((valor) => {
            const nome = String(valor || '').trim();
            if (!nome) return;

            const span = document.createElement('span');
            span.className = 'tag-item';
            span.innerHTML = `${nome}<input type="hidden" name="${chave}[]" value="${nome}"><i class="fas fa-times remove-tag"></i>`;
            container.appendChild(span);
        });
    });
}

function aplicarErroCampo(campo, mostrar) {
    if (!campo) return;
    campo.classList.toggle('field-error', mostrar);
    campo.setAttribute('aria-invalid', mostrar ? 'true' : 'false');
}

function sincronizarArtistaPrincipal() {
    const campoHidden = document.getElementById('edicaoArtista');
    if (!campoHidden) return;

    const tags = document.querySelectorAll('#containerArtistas input[name="artistas[]"]');
    if (!tags.length) {
        campoHidden.value = '';
        return;
    }

    const primeiroNome = tags[0].value.trim();
    const datalist = document.getElementById('listaSugestoesArtistas');

    if (!datalist) {
        campoHidden.value = '';
        return;
    }

    const opcao = Array.from(datalist.options).find((opt) =>
        (opt.value || '').trim().toLowerCase() === primeiroNome.toLowerCase()
    );

    campoHidden.value = opcao ? opcao.getAttribute('data-id') || '' : '';
}

function validarFormularioInclusao(event) {
    const form = document.getElementById('formAdicionarColecao');
    if (!form) return true;

    const camposObrigatorios = [
        document.getElementById('edicaoTitulo')
    ];

    const temArtista = document.querySelectorAll('#containerArtistas input[name="artistas[]"]').length > 0;

    let primeiroInvalido = null;

    camposObrigatorios.forEach((campo) => {
        const valor = campo ? String(campo.value || '').trim() : '';
        const invalido = !valor;
        aplicarErroCampo(campo, invalido);

        if (invalido && !primeiroInvalido) {
            primeiroInvalido = campo;
        }
    });

    if (!temArtista) {
        const containerArtistas = document.getElementById('containerArtistas');
        if (containerArtistas) {
            containerArtistas.classList.add('field-error');
            setTimeout(() => containerArtistas.classList.remove('field-error'), 1800);
        }
        event.preventDefault();
        const resumo = document.querySelector('[data-validation-summary]');
        if (resumo) resumo.scrollIntoView({ behavior: 'smooth', block: 'start' });
        return false;
    }

    if (primeiroInvalido) {
        event.preventDefault();
        primeiroInvalido.focus();
        const resumo = document.querySelector('[data-validation-summary]');
        if (resumo) {
            resumo.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        return false;
    }

    sincronizarArtistaPrincipal();
    return true;
}

document.addEventListener("DOMContentLoaded", async () => {
    // --- 1. ATIVAÇÃO DOS COMPORTAMENTOS COMUNS ---
    if (typeof inicializarComportamentosFormulario === 'function') {
        inicializarComportamentosFormulario();
    }

    const formAdicionarColecao = document.getElementById('formAdicionarColecao');
    if (formAdicionarColecao) {
        formAdicionarColecao.addEventListener('submit', validarFormularioInclusao);
    }

    ['edicaoTitulo', 'edicaoFormato'].forEach((id) => {
        const campo = document.getElementById(id);
        if (!campo) return;

        campo.addEventListener('input', () => aplicarErroCampo(campo, false));
        campo.addEventListener('change', () => aplicarErroCampo(campo, false));
    });

    const containerArtistas = document.getElementById('containerArtistas');
    if (containerArtistas) {
        const observer = new MutationObserver(() => sincronizarArtistaPrincipal());
        observer.observe(containerArtistas, { childList: true, subtree: true });
    }

    // === PREVIEW DA CAPA ===
    const inputCapa = document.getElementById('edicaoCapaUrl');
    const imgPreview = document.getElementById('edicaoImg');

    if (inputCapa && imgPreview) {
        inputCapa.addEventListener('input', () => {
            const url = inputCapa.value.trim();
            imgPreview.src = url ? url : 'assets/images/placeholder.jpg';
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const albumId = urlParams.get('id');
    const btnImport = document.getElementById('btn-import-tracks');
    const corpoTabela = document.getElementById('corpoListaFaixas');

    // --- 2. CARREGAMENTO INICIAL DE DETALHES ---
    if (albumId) {
        try {
            const response = await fetch(`index.php?url=obter_detalhes_album&id=${albumId}`);
            const album = await response.json();

            if (!album.error) {
                // Título e Capa
                document.getElementById('edicaoTitulo').value = album.titulo || '';
                document.getElementById('edicaoCapaUrl').value = album.capa_url || '';
                document.getElementById('edicaoImg').src = album.capa_url || 'assets/images/placeholder.jpg';

                // Artista (Select)
                const campoArtista = document.getElementById('edicaoArtista');
                if (campoArtista) campoArtista.value = album.artista_id || '';

                // Gravadora (Input + Datalist)
                const campoGravadora = document.getElementById('edicaoGravadoraNome');
                const campoGravadoraId = document.getElementById('edicaoGravadoraId');
                if (campoGravadora) campoGravadora.value = album.gravadora_nome || '';
                if (campoGravadoraId) campoGravadoraId.value = album.gravadora_id || '';

                // Outros campos
                if (document.getElementById('edicaoLancamento')) document.getElementById('edicaoLancamento').value = album.data_lancamento || '';
                if (document.getElementById('edicaoTipo')) document.getElementById('edicaoTipo').value = album.tipo_id || '';
                if (document.getElementById('edicaoPreco')) document.getElementById('edicaoPreco').value = album.preco_custo || '';
                if (document.getElementById('edicaoCodBarras')) document.getElementById('edicaoCodBarras').value = album.codigo_barras || '';

                // Carrega as faixas se existirem
                if (album.faixas && album.faixas.length > 0) {
                    corpoTabela.innerHTML = '';
                    album.faixas.forEach(f => {
                        inserirLinhaNaTabela(f.posicao, f.titulo, f.duracao);
                    });
                }
            }
        } catch (error) {
            console.error("Erro ao buscar dados do álbum:", error);
        }
    }

    // --- 3. IMPORTAÇÃO VIA DISCOGS ---
    if (btnImport) {
        btnImport.addEventListener('click', async () => {
            const inputCatalogo = document.getElementById('inputCatalogo');
            const inputTitulo = document.getElementById('edicaoTitulo');
            const catalogo = inputCatalogo ? inputCatalogo.value.trim() : '';
            const titulo = inputTitulo ? inputTitulo.value.trim() : '';

            if (!catalogo) {
                alert("Opa! Preciso do Número de Catálogo.");
                if (inputCatalogo) inputCatalogo.focus();
                return;
            }

            const originalHTML = btnImport.innerHTML;
            btnImport.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';
            btnImport.disabled = true;

            try {
                const response = await fetch(`index.php?url=api_importar_discogs`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ catalogo, titulo })
                });

                const data = await response.json();

                if (data.success && data.tracklist) {
                    corpoTabela.innerHTML = '';
                    faixaIndex = 0; // Reseta para a nova lista importada

                    const inputDiscogsId = document.getElementById('inputDiscogsId');
                    if (inputDiscogsId) inputDiscogsId.value = data.discogs_id;

                    popularTagsImportadas(data);

                    data.tracklist.forEach(track => {
                        inserirLinhaNaTabela(track.numero, track.titulo, track.duracao);
                    });

                    alert(`Sucesso! Importamos ${data.tracklist.length} faixas.`);
                } else {
                    alert("Discogs diz: " + (data.message || "Não encontrado."));
                }
            } catch (error) {
                console.error('Erro na importação:', error);
                alert('Erro de comunicação com o servidor.');
            } finally {
                btnImport.disabled = false;
                btnImport.innerHTML = originalHTML;
            }
        });
    }

    // --- 4. FUNÇÃO PARA INSERIR LINHA DE FAIXA ---
    function inserirLinhaNaTabela(posicao, titulo, duracao) {
        const tr = document.createElement('div');
        tr.className = 'faixa-item';
        tr.style.display = 'flex';
        tr.style.gap = '10px';
        tr.style.marginBottom = '8px';
        tr.style.alignItems = 'center';
        tr.style.transition = 'all 0.2s ease';

        tr.innerHTML = `
            <input type="hidden" name="faixas[${faixaIndex}][id]" value="new">
            <input type="text" name="faixas[${faixaIndex}][posicao]" class="input-posicao" style="width: 50px; text-align: center;" value="${posicao || ''}" placeholder="01">
            <input type="text" name="faixas[${faixaIndex}][titulo]" class="input-titulo" style="flex: 1;" value="${titulo || ''}" placeholder="Nome da música">
            <input type="text" name="faixas[${faixaIndex}][duracao]" class="input-duracao" style="width: 80px; text-align: center;" value="${duracao || ''}" placeholder="00:00" maxlength="8">
            <button type="button" class="btn-remove-faixa" style="background: none; border: none; color: var(--action-destructive); cursor: pointer;"><i class="fa-solid fa-trash"></i></button>
        `;
        corpoTabela.appendChild(tr);
        faixaIndex++;
    }

    // Botão Adicionar Faixa Manual
    const btnAddManual = document.getElementById('btnAdicionarFaixa');
    if (btnAddManual) {
        btnAddManual.addEventListener('click', () => {
            const proximaPos = corpoTabela.querySelectorAll('.faixa-item').length + 1;
            inserirLinhaNaTabela(proximaPos, '', '');

            const ultimaFaixa = corpoTabela.lastElementChild;
            if (ultimaFaixa) {
                const inputTitulo = ultimaFaixa.querySelector('.input-titulo');
                if (inputTitulo) inputTitulo.focus();
            }
        });
    }

    // --- 5. DELEGAÇÃO DE EVENTOS (REMOÇÃO E MÁSCARA) ---
    corpoTabela.addEventListener('click', (e) => {
        if (e.target.closest('.btn-remove-faixa')) {
            const linha = e.target.closest('.faixa-item');
            if (confirm('Deseja remover esta faixa da lista?')) {
                linha.style.opacity = '0';
                setTimeout(() => linha.remove(), 200);
            }
        }
    });

    corpoTabela.addEventListener('input', (e) => {
        if (e.target.classList.contains('input-duracao')) {
            let v = e.target.value.replace(/\D/g, '');
            if (v.length >= 3 && v.length <= 4) {
                v = v.substring(0, v.length - 2) + ':' + v.substring(v.length - 2);
            } else if (v.length > 4) {
                v = v.substring(0, v.length - 4) + ':' + v.substring(v.length - 4, v.length - 2) + ':' + v.substring(v.length - 2);
            }
            e.target.value = v.substring(0, 8);
        }
    });
});