/**
 * 1. SINCRONIZAÇÃO DE GRAVADORA (ID VS NOME)
 * Esta lógica roda em todas as páginas via delegação, mas só age se encontrar os campos.
 */
document.addEventListener('input', function (e) {
    if (e.target && (e.target.id === 'edicaoGravadora' || e.target.id === 'edicaoGravadoraNome')) {

        const inputNome = e.target;
        const nomeDigitado = inputNome.value;
        const inputHidden = document.getElementById('idGravadoraHidden') || document.getElementById('edicaoGravadoraId');
        const datalist = document.getElementById('listaSugestoesGravadoras');

        if (inputHidden && datalist) {
            const opcao = Array.from(datalist.options).find(opt => opt.value === nomeDigitado);

            if (opcao) {
                inputHidden.value = opcao.getAttribute('data-id');
            } else {
                inputHidden.value = '';
            }
            console.log("Sync Gravadora - ID:", inputHidden.value, "| Nome:", nomeDigitado);
        }
    }
});

/**
 * 2. UTILITÁRIOS DE TEMPO E RENDERIZAÇÃO
 */
const formatarTempo = segundos => {
    if (!segundos) return '0:00';
    const min = Math.floor(segundos / 60);
    const seg = segundos % 60;
    return `${min}:${seg.toString().padStart(2, '0')}`;
};

// Cache global para evitar requisições repetidas ao trocar de álbuns no dashboard/coleção
const cacheFaixasGeral = {};

/**
 * Busca as faixas via AJAX e popula a tabela do modal
 * Atualizado para receber e repassar o nome do artista
 */
async function carregarFaixas(midiaId, artistaNome = 'N/D') {
    const ID_CONTAINER = 'corpoTabelaFaixas';
    const corpoTabela = document.getElementById(ID_CONTAINER);

    if (!corpoTabela) return;

    // Se já buscamos esse álbum antes, usa o cache (passando midiaId agora)
    if (cacheFaixasGeral[midiaId]) {
        renderizarFaixas(cacheFaixasGeral[midiaId], ID_CONTAINER, artistaNome, midiaId);
        return;
    }

    corpoTabela.innerHTML = '<tr><td colspan="3" class="text-center">Carregando faixas...</td></tr>';

    try {
        const res = await fetch(`index.php?url=buscar_faixas&midia_id=${midiaId}`);
        const faixas = await res.json();

        cacheFaixasGeral[midiaId] = faixas;
        // Passando midiaId aqui também para a renderização ter o dado
        renderizarFaixas(faixas, ID_CONTAINER, artistaNome, midiaId);
    } catch (e) {
        corpoTabela.innerHTML = '<tr><td colspan="3" class="text-center">Erro ao carregar faixas</td></tr>';
        console.error("Erro no fetch das faixas:", e);
    }
}

/**
 * Converte duração do formato hh:mm:ss ou mm:ss para mm:ss
 * Se começar com 00:, remove o prefixo
 */
function formatarDuracaoExibicao(duracao) {
    if (!duracao) return '--:--';

    const partes = duracao.split(':');
    if (partes.length === 3) {
        // Formato hh:mm:ss
        const horas = parseInt(partes[0], 10);
        const minutos = parseInt(partes[1], 10);
        const segundos = partes[2];

        // Se tem horas, mantém o formato completo
        if (horas > 0) {
            return duracao;
        }
        // Se não tem horas, retorna mm:ss
        return `${minutos}:${segundos}`;
    } else if (partes.length === 2) {
        // Já está em mm:ss
        return duracao;
    }

    return duracao;
}

function renderizarFaixas(faixas, containerId = 'corpoTabelaFaixas', artistaNome = 'N/D', midiaId = 0) {
    const corpoTabela = document.getElementById(containerId);
    if (!corpoTabela) return;

    corpoTabela.innerHTML = '';

    if (!faixas || faixas.length === 0) {
        corpoTabela.innerHTML = '<tr><td colspan="4" class="text-center">Nenhuma faixa cadastrada.</td></tr>';
        return;
    }

    faixas.forEach(f => {
        const tr = document.createElement('tr');
        const pos = f.numero_faixa || f.position || '-';
        const titulo = f.titulo || f.title || 'Sem título';
        const durationRaw = f.duracao || f.duration || '--:--';
        const duracao = formatarDuracaoExibicao(durationRaw);
        const videoUrl = f.video_url || f.video_ulr || '';
        const temVideo = Boolean(videoUrl && videoUrl.trim());

        tr.innerHTML = `
            <td class="col-pos text-center">${pos}</td>
            <td class="col-titulo">
                <span class="link-letra" style="cursor: pointer; color: #3b82f6; transition: color 0.2s;" 
                      data-artista="${encodeURIComponent(artistaNome)}" 
                      data-musica="${encodeURIComponent(titulo)}"
                      data-midia="${midiaId}"
                      data-faixa="${pos}"
                      onmouseover="this.style.color='#60a5fa'" 
                      onmouseout="this.style.color='#3b82f6'">
                    ${titulo}
                </span>
            </td>
            <td class="col-duracao text-right">${duracao}</td>
            <td class="col-video text-center">
                <button type="button"
                        class="btn-video-faixa ${temVideo ? 'has-video' : ''}"
                        data-midia="${midiaId}"
                        data-faixa="${pos}"
                        data-video="${encodeURIComponent(videoUrl)}"
                        title="${temVideo ? 'Abrir vídeo' : 'Adicionar vídeo'}"
                        aria-label="${temVideo ? 'Abrir vídeo' : 'Adicionar vídeo'}">
                    <i class="${temVideo ? 'fas fa-play-circle' : 'far fa-play-circle'}"></i>
                </button>
            </td>
        `;
        corpoTabela.appendChild(tr);
    });
}

/**
 * 3. FORMULÁRIOS: FAIXAS E TAGS
 */
function inserirLinhaNaTabela(numero, titulo, duracao, containerId = 'corpoListaFaixas') {
    const corpo = document.getElementById(containerId);
    if (!corpo) return;

    const novaLinha = document.createElement('div');
    novaLinha.className = 'faixa-item';

    novaLinha.innerHTML = `
        <input type="hidden" name="faixas[${faixaIndex}][id]" value="new">
        <input type="number" name="faixas[${faixaIndex}][posicao]" value="${numero}" class="input-pos">
        <input type="text" name="faixas[${faixaIndex}][titulo]" value="${titulo}" class="input-titulo">
        <input type="text" name="faixas[${faixaIndex}][duracao]" value="${duracao}" class="input-duracao">
        <button type="button" class="btn-remove-faixa"><i class="fas fa-trash"></i></button>
    `;

    corpo.appendChild(novaLinha);
    faixaIndex++;
}

function inicializarComportamentosFormulario() {
    const containersTags = ['containerArtistas', 'containerGeneros', 'containerEstilos', 'containerProdutores'];
    containersTags.forEach(id => {
        const container = document.getElementById(id);
        if (container) {
            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-tag')) {
                    const tag = e.target.closest('.tag-item');
                    tag.style.opacity = '0';
                    setTimeout(() => tag.remove(), 200);
                }
            });
        }
    });

    document.querySelectorAll('.btn-add-tag').forEach(btn => {
        btn.onclick = function () {
            const target = this.getAttribute('data-target');
            const searchBox = document.getElementById('searchContainer' + target);
            if (searchBox) {
                const isHidden = searchBox.style.display === 'none' || searchBox.style.display === '';
                searchBox.style.display = isHidden ? 'flex' : 'none';
                if (isHidden) {
                    const input = searchBox.querySelector('input');
                    if (input) input.focus();
                }
            }
        };
    });

    document.querySelectorAll('.input-search-tag').forEach(input => {
        input.onkeypress = function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                processarAdicaoTag(this);
            }
        };
        input.onchange = function () {
            setTimeout(() => { if (this.value.trim() !== '') processarAdicaoTag(this); }, 100);
        };
    });
}

function processarAdicaoTag(input) {
    const valor = input.value.trim();
    if (valor === '') return;

    const tipo = input.getAttribute('data-tipo');
    const container = document.getElementById('container' + (tipo.charAt(0).toUpperCase() + tipo.slice(1)));

    if (!container) return;

    const jaExiste = Array.from(container.querySelectorAll('input[type="hidden"][name="' + tipo + '[]"]')).some(item => item.value.trim().toLowerCase() === valor.toLowerCase());
    if (jaExiste) {
        input.value = '';
        const parent = input.closest('.search-tag-container');
        if (parent) parent.style.display = 'none';
        return;
    }

    const span = document.createElement('span');
    span.className = 'tag-item';
    span.innerHTML = `${valor} <input type="hidden" name="${tipo}[]" value="${valor}"> <i class="fas fa-times remove-tag"></i>`;

    container.appendChild(span);
    if (tipo === 'artistas') {
        sincronizarArtistaPrincipal();
    }
    input.value = '';
    const parent = input.closest('.search-tag-container');
    if (parent) parent.style.display = 'none';
}

/**
 * 4. COMPONENTES GLOBAIS (AVATAR, DROPDOWNS, MODAIS)
 */
function inicializarComponentesGlobais() {
    const avatarTrigger = document.getElementById('avatarTrigger');
    const dropdown = document.getElementById('myDropdown');

    if (avatarTrigger && dropdown) {
        avatarTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
    }

    // Gerenciamento Universal de Cliques (Dropdown e Fechamento de Modais)
    window.addEventListener('click', (e) => {
        // 1. Fecha dropdown do avatar
        if (dropdown && dropdown.classList.contains('show')) {
            if (!dropdown.contains(e.target) && !avatarTrigger.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        }

        // 2. Fecha Modal de Detalhes (Universal)
        const modal = document.getElementById('modalDetalhesColecao');
        if (modal && modal.style.display === 'block') {
            const closeBtn = modal.querySelector('.modal-close');
            if (e.target === modal || e.target === closeBtn) {
                modal.style.display = 'none';
            }
        }

        // 3. NOVO: Fecha o Modal de Letras ao clicar fora ou no 'X'
        const modalLetra = document.getElementById('modalLetraMusica');
        if (modalLetra && modalLetra.style.display === 'block') {
            const closeBtnLetra = document.getElementById('fecharModalLetra');
            if (e.target === modalLetra || e.target === closeBtnLetra) {
                modalLetra.style.display = 'none';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', inicializarComponentesGlobais);

function atualizarBotaoVideoFaixa(midiaId, numeroFaixa, videoUrl) {
    const botao = document.querySelector(`.btn-video-faixa[data-midia="${midiaId}"][data-faixa="${numeroFaixa}"]`);
    if (!botao) return;

    const temVideo = Boolean(videoUrl && videoUrl.trim());
    botao.classList.toggle('has-video', temVideo);
    botao.setAttribute('data-video', encodeURIComponent(videoUrl || ''));
    botao.setAttribute('title', temVideo ? 'Abrir vídeo' : 'Adicionar vídeo');
    botao.setAttribute('aria-label', temVideo ? 'Abrir vídeo' : 'Adicionar vídeo');

    const icone = botao.querySelector('i');
    if (icone) {
        icone.className = temVideo ? 'fas fa-play-circle' : 'far fa-play-circle';
    }
}

function abrirModalVideoFaixa(midiaId, numeroFaixa, videoUrl = '') {
    const modal = document.getElementById('modalVideoFaixa');
    if (!modal) return;

    const input = document.getElementById('inputVideoUrlFaixa');
    const iframe = document.getElementById('iframeVideoFaixa');
    const conteudo = document.getElementById('conteudoVideoFaixa');
    const btnSalvar = document.getElementById('btnSalvarVideoFaixa');
    const status = document.getElementById('statusVideoFaixa');
    const areaInput = document.getElementById('areaInputVideoFaixa');
    const texto = document.getElementById('textoVideoFaixa');
    const temVideo = Boolean(videoUrl && videoUrl.trim());

    if (input) input.value = videoUrl || '';
    if (iframe) iframe.src = '';
    if (conteudo) conteudo.style.display = 'none';
    if (status) status.textContent = temVideo ? 'Vídeo associado. Você pode alterar a URL abaixo.' : '';
    if (areaInput) areaInput.style.display = 'block';
    if (btnSalvar) btnSalvar.textContent = temVideo ? 'Atualizar vídeo' : 'Salvar vídeo';
    if (texto) {
        texto.textContent = temVideo
            ? 'Este vídeo já está associado. Você pode alterar a URL abaixo se quiser trocar o link.'
            : 'Insira a URL do YouTube ou Vimeo para associar ao vídeo da música.';
    }

    modal.dataset.midiaId = midiaId;
    modal.dataset.numeroFaixa = numeroFaixa;

    if (temVideo) {
        const embedUrl = converterUrlVideo(videoUrl);
        if (iframe) {
            iframe.src = embedUrl;
            if (conteudo) conteudo.style.display = 'block';
        }
    }

    modal.style.display = 'block';

    if (btnSalvar) {
        btnSalvar.onclick = async () => {
            const url = input ? input.value.trim() : '';
            if (!url) {
                if (status) status.textContent = 'Informe uma URL válida.';
                return;
            }

            const formData = new FormData();
            formData.append('midia_id', midiaId);
            formData.append('numero_faixa', numeroFaixa);
            formData.append('video_url', url);

            try {
                const response = await fetch('index.php?url=salvar_video_faixa', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data && data.success) {
                    if (status) status.textContent = 'Vídeo atualizado com sucesso.';
                    atualizarBotaoVideoFaixa(midiaId, numeroFaixa, url);
                    if (input) input.value = url;
                    if (btnSalvar) btnSalvar.textContent = 'Atualizar vídeo';
                    if (texto) {
                        texto.textContent = 'Este vídeo já está associado. Você pode alterar a URL abaixo se quiser trocar o link.';
                    }
                    const iframeUrl = converterUrlVideo(url);
                    if (iframe) {
                        iframe.src = iframeUrl;
                        if (conteudo) conteudo.style.display = 'block';
                    }
                } else {
                    if (status) status.textContent = 'Falha ao salvar o vídeo.';
                }
            } catch (error) {
                console.error('Erro ao salvar vídeo:', error);
                if (status) status.textContent = 'Erro ao salvar o vídeo.';
            }
        };
    }
}

function converterUrlVideo(url) {
    if (!url) return '';
    const valor = url.trim();

    if (valor.includes('youtube.com/watch?v=')) {
        return valor.replace('watch?v=', 'embed/');
    }

    if (valor.includes('youtu.be/')) {
        return valor.replace('https://youtu.be/', 'https://www.youtube.com/embed/');
    }

    if (valor.includes('vimeo.com/')) {
        const id = valor.split('vimeo.com/')[1].split(/[?&#]/)[0];
        return `https://player.vimeo.com/video/${id}`;
    }

    return valor;
}

function inicializarVideoFaixa() {
    document.addEventListener('click', function (event) {
        const botao = event.target.closest('.btn-video-faixa');
        if (!botao) return;

        event.stopPropagation();
        const midiaId = botao.getAttribute('data-midia');
        const numeroFaixa = botao.getAttribute('data-faixa');
        const videoUrl = decodeURIComponent(botao.getAttribute('data-video') || '');
        abrirModalVideoFaixa(midiaId, numeroFaixa, videoUrl);
    });

    document.addEventListener('click', function (event) {
        const modal = document.getElementById('modalVideoFaixa');
        if (!modal || modal.style.display !== 'block') return;

        if (event.target === modal || event.target.closest('[data-close-video-modal]')) {
            modal.style.display = 'none';
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarVideoFaixa);
} else {
    inicializarVideoFaixa();
}
