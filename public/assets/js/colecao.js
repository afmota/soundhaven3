// assets/js/colecao.js

/**
 * Funções Globais para os Modais de Gráficos
 * Definidas fora do DOMContentLoaded para que o 'onclick' do HTML as encontre.
 */
window.abrirModalDecadas = function() {
    const modal = document.getElementById('modalDecadas');
    if (!modal) return;
    
    modal.style.display = 'block';
    
    const canvas = document.getElementById('chartDecadas');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Se os dados não existirem (erro no PHP), evita quebrar o JS
    if (typeof dadosDecadas === 'undefined') {
        console.error("Variável 'dadosDecadas' não encontrada.");
        return;
    }

    // Destruir gráfico anterior para evitar sobreposição ao reabrir
    if (window.meuGraficoDecadas instanceof Chart) {
        window.meuGraficoDecadas.destroy();
    }

    window.meuGraficoDecadas = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dadosDecadas.labels,
            datasets: [{
                label: 'Total de Álbuns',
                data: dadosDecadas.datasets,
                backgroundColor: '#00eeff',
                borderColor: '#3b82f6',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            // --- ATIVAR CURSOR DE CLIQUE AO PASSAR NAS BARRAS ---
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },
            // --- A MACUMBA DO CLIQUE AQUI ---
            onClick: (evt, elementos) => {
                if (elementos.length > 0) {
                    const index = elementos[0].index;
                    const label = dadosDecadas.labels[index]; // Pega ex: "1980s"
                    const decada = parseInt(label, 10);      // Transforma em 1980
                    
                    // Redireciona limpando outros filtros e focando na década
                    window.location.href = `index.php?url=colecao&decada=${decada}`;
                }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
};

window.abrirModalAnos = function() {
    console.log("Tentando abrir modal de Anos..."); // Debug básico
    const modal = document.getElementById('modalAnos');
    
    if (!modal) {
        console.error("Erro: O elemento 'modalAnos' não existe na página.");
        return;
    }

    modal.style.display = 'block';

    const canvas = document.getElementById('chartAnos');
    if (!canvas) {
        console.error("Erro: O canvas 'chartAnos' não existe dentro do modal.");
        return;
    }

    const ctx = canvas.getContext('2d');

    if (window.meuGraficoAnos instanceof Chart) {
        window.meuGraficoAnos.destroy();
    }

    window.meuGraficoAnos = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dadosAquisicoes.labels,
            datasets: [{
                label: 'Álbuns Adquiridos',
                data: dadosAquisicoes.datasets,
                borderColor: '#2ec22e',
                backgroundColor: '#14f714',
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointBackgroundColor: '#338d33',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onHover: (event, chartElement) => {
                event.native.target.style.cursor = chartElement[0] ? 'pointer' : 'default';
            },
            onClick: (evt, elementos) => {
                if (elementos.length > 0) {
                    const index = elementos[0].index;
                    const ano = dadosAquisicoes.labels[index]; // Pega o ano direto (ex: 2024)
                    
                    // Redireciona aplicando o filtro de ano de aquisição
                    window.location.href = `index.php?url=colecao&ano_aquisicao=${ano}`;
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0, color: '#aaa' } },
                x: { ticks: { color: '#aaa' } }
            },
            plugins: {
                legend: { labels: { color: '#fff' } }
            }
        }
    });
};

window.fecharModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.style.display = 'none';
};

// --- Início do carregamento do DOM ---
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalDetalhesColecao');
    const cards = document.querySelectorAll('.album-card');
    const btnEditar = document.getElementById('btnEditarColecao');
    const btnDescartar = document.getElementById('btnDescartarColecao');

    // Funções utilitárias locais
    const formatarMoeda = valor =>
        new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor || 0);

    const setTxt = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text || 'N/D';
    };

    const renderizarTags = (containerId, dadosString, extraClass = '') => {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        if (!dadosString) {
            container.innerHTML = '<span class="no-data">Nenhum registro encontrado</span>';
            return;
        }
        dadosString.split('|').forEach(item => {
            const span = document.createElement('span');
            span.className = `tag-item ${extraClass}`;
            span.textContent = item.trim();
            container.appendChild(span);
        });
    };

    // Evento de clique nos cards da Coleção (Detalhes)
    cards.forEach(card => {
        card.addEventListener('click', () => {
            const album = JSON.parse(card.dataset.album);
            
            modal.setAttribute('data-current-midia-id', album.midia_id);
            
            document.getElementById('detalheCapa').src = album.capa_url || 'assets/images/placeholder.jpg';
            setTxt('detalheTitulo', album.titulo);
            setTxt('detalheArtista', album.artista_nome);
            setTxt('detalheGravadora', album.gravadora_nome);
            setTxt('detalheCatalogo', album.numero_catalogo);
            setTxt('detalhePreco', formatarMoeda(album.preco));
            setTxt('detalheObservacoes', album.observacoes);
            
            const lanc = album.data_lancamento ? new Date(album.data_lancamento + 'T12:00:00').toLocaleDateString('pt-BR') : 'N/D';
            setTxt('detalheLancamento', lanc);
            
            const aquis = album.data_aquisicao ? new Date(album.data_aquisicao + 'T12:00:00').toLocaleDateString('pt-BR') : 'N/D';
            setTxt('detalheAquisicao', aquis);
            
            const tag = document.getElementById('detalheFormatoTag');
            if (tag) {
                tag.textContent = album.formato_nome;
                tag.style.backgroundColor = album.formato_cor;
            }

            renderizarTags('containerTagsGeneros', album.generos);
            renderizarTags('containerTagsEstilos', album.estilos);
            renderizarTags('containerTagsProdutores', album.produtores);
            
            if (typeof carregarFaixas === 'function') {
                carregarFaixas(album.midia_id, album.artista_nome);
            }
            
            modal.style.display = 'block';
        });
    });

    // Ação do botão Editar
    if (btnEditar) {
        btnEditar.onclick = () => {
            const midiaId = modal.getAttribute('data-current-midia-id');
            if (midiaId) window.location.href = `index.php?url=editar_album&midia_id=${midiaId}`;
        };
    }

    // Ação do botão Descartar
    if (btnDescartar) {
        btnDescartar.onclick = async () => {
            const midiaId = modal.getAttribute('data-current-midia-id');
            if (!midiaId || !confirm('Remover este álbum da coleção?')) return;
            
            const formData = new URLSearchParams();
            formData.append('midia_id', midiaId);
            
            try {
                const res = await fetch('index.php?url=descartar_album', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) location.reload();
                else alert(data.error || 'Erro ao descartar');
            } catch (e) { 
                console.error("Erro ao descartar álbum:", e); 
            }
        };
    }

    // Lógica de "Marcar como Ouvido"
    const botoesOuvir = document.querySelectorAll('.btn-ouvir-tag');
    botoesOuvir.forEach(botao => {
        botao.addEventListener('click', function(e) {
            e.stopPropagation(); 
            e.preventDefault(); 
            const midiaId = this.getAttribute('data-midia-id');
            const btn = this;
            btn.classList.add('checked');

            fetch(`index.php?url=registrar_audicao&id=${midiaId}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Erro ao registrar audição.');
                        btn.classList.remove('checked');
                    }
                })
                .catch(err => {
                    console.error('Erro:', err);
                    btn.classList.remove('checked');
                });
        });
    });

    // Lógica de Filtros por Pills
    const pills = document.querySelectorAll('.pill');
    pills.forEach(pill => {
        try {
            pill.addEventListener('click', function() {
                document.querySelectorAll('.pill').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                
                const filtroSelecionado = this.getAttribute('data-filter');
                const todosOsCards = document.querySelectorAll('.album-card');
                
                todosOsCards.forEach(card => {
                    const album = JSON.parse(card.dataset.album);
                    if (filtroSelecionado === 'all' || album.tipo_id == filtroSelecionado) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        } catch (e) {
            // Previne quebras caso o JSON do dataset falte em algum card
        }
    });

    // --- Lógica de Auto-Ocultar Filtros ---
    const btnToggle = document.getElementById('btnToggleFiltros');
    const painelFiltros = document.getElementById('barraFiltrosAvancados');
    const txtToggle = document.getElementById('txtToggleFiltros');

    if (btnToggle && painelFiltros) {
        // Pega os parâmetros da URL atual
        const urlParams = new URLSearchParams(window.location.search);
        const temFiltroAtivo = urlParams.has('busca') || urlParams.has('produtor');

        // Se já tiver algo filtrado, começa aberto
        if (temFiltroAtivo) {
            painelFiltros.style.display = 'block';
            txtToggle.textContent = 'Ocultar Filtros';
        }

        btnToggle.addEventListener('click', () => {
            if (painelFiltros.style.display === 'none') {
                painelFiltros.style.display = 'block';
                txtToggle.textContent = 'Ocultar Filtros';
            } else {
                painelFiltros.style.display = 'none';
                txtToggle.textContent = 'Mostrar Filtros';
            }
        });
    }

    // --- Lógica de Auto-Ocultar Ordenação ---
    const btnToggleOrdem = document.getElementById('btnToggleOrdenacao');
    const painelOrdem = document.getElementById('barraOrdenacao');
    const txtToggleOrdem = document.getElementById('txtToggleOrdenacao');

    if (btnToggleOrdem && painelOrdem) {
        const urlParams = new URLSearchParams(window.location.search);
        const temOrdemAtiva = urlParams.has('ordem') && urlParams.get('ordem') !== 'padrao';

        // Se já tiver alguma ordenação ativada, começa aberto
        if (temOrdemAtiva) {
            painelOrdem.style.display = 'block';
            txtToggleOrdem.textContent = 'Ocultar Ordenação';
        }

        btnToggleOrdem.addEventListener('click', () => {
            if (painelOrdem.style.display === 'none') {
                painelOrdem.style.display = 'block';
                txtToggleOrdem.textContent = 'Ocultar Ordenação';
            } else {
                painelOrdem.style.display = 'none';
                txtToggleOrdem.textContent = 'Mostrar Ordenação';
            }
        });
    }


    // --- BUSCA E CADASTRO DE LETRAS (SOUNDHAVEN) ---
    document.getElementById('corpoTabelaFaixas').addEventListener('click', function(e) {
        const alvo = e.target.closest('.link-letra');
        if (!alvo) return;

        const artista = decodeURIComponent(alvo.getAttribute('data-artista'));
        const musica = decodeURIComponent(alvo.getAttribute('data-musica'));
        const midiaId = alvo.getAttribute('data-midia'); // Garanta que esses atributos existem no HTML da tabela
        const numFaixa = alvo.getAttribute('data-faixa');

        const modalLetra = document.getElementById('modalLetraMusica');
        const tituloModal = document.getElementById('tituloLetraModal');
        const corpoModal = document.getElementById('corpoLetraModal');

        if (!modalLetra || !tituloModal || !corpoModal) return;

        tituloModal.textContent = `${musica} - ${artista}`;
        corpoModal.innerHTML = '<div style="text-align:center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Procurando letra...</div>';
        modalLetra.style.display = 'block';

        // Enviamos os parâmetros completos para o PHP purista
        const urlLocal = `index.php?url=buscar_letra&artista=${encodeURIComponent(artista)}&mus=${encodeURIComponent(musica)}&midia_id=${midiaId}&numero_faixa=${numFaixa}`;

        fetch(urlLocal)
            .then(response => response.json())
            .then(data => {
                if (data && data.status === 'success') {
                    corpoModal.style.whiteSpace = 'pre-line';
                    // Se veio do banco local, adicionamos um marcador sutil, se não, exibe o texto puro
                    corpoModal.innerHTML = data.origem === 'local' 
                        ? `<small style="color: #888; display:block; margin-bottom:10px;"><i class="fas fa-database"></i> Letra do acervo local</small>${data.lyrics}`
                        : data.lyrics;
                } else if (data && data.status === 'not_found') {
                    // Monta o formulário de inserção usando as suas variáveis de CSS para os botões
                    corpoModal.innerHTML = `
                        <p style="text-align:center; margin-bottom:15px; color: #888;">
                            <i class="fas fa-music"></i> Letra não encontrada automaticamente. Deseja cadastrá-la?
                        </p>
                        <textarea id="txtNovaLetra" style="width:100%; height:250px; padding:10px; border-radius:4px; border:1px solid #ccc; font-family:inherit; resize:vertical;" placeholder="Cole ou digite a letra da música aqui..."></textarea>
                        <div style="text-align:right; margin-top:15px; display:flex; justify-content:flex-end; gap:10px;">
                            <button id="btnCancelarLetra" style="background: var(--action-destructive); color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;">Cancelar</button>
                            <button id="btnSalvarLetra" style="background: var(--action-positive); color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;"><i class="fas fa-save"></i> Salvar no Banco</button>
                        </div>
                    `;

                    // Evento do botão Cancelar (limpa e fecha o modal)
                    document.getElementById('btnCancelarLetra').addEventListener('click', () => {
                        modalLetra.style.display = 'none';
                    });

                    // Evento do botão Salvar Letra
                    document.getElementById('btnSalvarLetra').addEventListener('click', function() {
                        const textoLetra = document.getElementById('txtNovaLetra').value.trim();
                        if (!textoLetra) {
                            alert('Digite a letra antes de salvar!');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('midia_id', midiaId);
                        formData.append('numero_faixa', numFaixa);
                        formData.append('texto_letra', textoLetra);

                        fetch('index.php?url=salvar_letra', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.json())
                        .then(resData => {
                            if (resData && resData.status === 'success') {
                                alert('Letra salva com sucesso no acervo!');
                                // Recarrega o modal com a letra nova já impressa
                                corpoModal.style.whiteSpace = 'pre-line';
                                corpoModal.textContent = `<small style="color: #888; display:block; margin-bottom:10px;"><i class="fas fa-database"></i> Letra do acervo local</small>${textoLetra}`;
                            } else {
                                alert('Erro ao salvar: ' + resData.message);
                            }
                        })
                        .catch(err => console.error('Erro ao salvar letra:', err));
                    });
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                corpoModal.innerHTML = '<p style="color: var(--action-destructive); text-align:center;"><i class="fas fa-exclamation-circle"></i> Erro interno no servidor.</p>';
            });
    });
});