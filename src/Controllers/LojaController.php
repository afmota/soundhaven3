<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;

class LojaController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function novoAlbum(): void
    {
        $artistas = $this->getDistinctValues('artista');
        $gravadoras = $this->getDistinctValues('gravadora');
        $generos = $this->getDistinctValues('genero');
        $album = [];
        $erroValidacao = [];

        require_once __DIR__ . '/../Views/loja/novo_album.php';
    }

    public function index(): void
    {
        // 1. Processamento de Ações (POST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create') {
                $this->handleCreate();
                return;
            }

            if ($action === 'update') {
                $this->handleUpdate();
                return;
            }

            if ($action === 'delete') {
                $this->handleDelete();
                return;
            }

            if ($action === 'import_csv') {
                $this->handleImportCsv();
                return;
            }
        }

        // 2. Preparação de Dados para a View (GET)
        $filters = [
            'titulo'     => trim((string)($_GET['titulo'] ?? '')),
            'artista'    => trim((string)($_GET['artista'] ?? '')),
            'gravadora'  => trim((string)($_GET['gravadora'] ?? '')),
            'tipo_album' => trim((string)($_GET['tipo_album'] ?? '')),
            'genero'     => trim((string)($_GET['genero'] ?? '')),
            'ordem'      => trim((string)($_GET['ordem'] ?? 'recentes')),
        ];

        // Construir cláusula WHERE
        $where = [];
        $params = [];

        if ($filters['titulo'] !== '') {
            $where[] = 'titulo LIKE :titulo';
            $params[':titulo'] = '%' . $filters['titulo'] . '%';
        }

        if ($filters['artista'] !== '') {
            $where[] = 'artista = :artista';
            $params[':artista'] = $filters['artista'];
        }

        if ($filters['gravadora'] !== '') {
            $where[] = 'gravadora = :gravadora';
            $params[':gravadora'] = $filters['gravadora'];
        }

        if ($filters['tipo_album'] !== '') {
            $where[] = 'tipo_album = :tipo_album';
            $params[':tipo_album'] = $filters['tipo_album'];
        }

        if ($filters['genero'] !== '') {
            $where[] = 'genero LIKE :genero';
            $params[':genero'] = '%' . $filters['genero'] . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Contagem total para paginação
        $countSql = "SELECT COUNT(*) FROM tb_albuns {$whereClause}";
        $countStmt = $this->db->prepare($countSql);
        foreach ($params as $k => $v) {
            $countStmt->bindValue($k, $v);
        }
        $countStmt->execute();
        $totalItens = (int)$countStmt->fetchColumn();

        // 25 itens por página para formar o Grid 5x5
        $itensPorPagina = 25;
        $totalPaginas = max(1, (int)ceil($totalItens / $itensPorPagina));

        $paginaAtual = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        if ($paginaAtual < 1) $paginaAtual = 1;
        if ($paginaAtual > $totalPaginas) $paginaAtual = $totalPaginas;

        $offset = ($paginaAtual - 1) * $itensPorPagina;

        // Janela de paginação
        $inicioPagina = max(1, $paginaAtual - 2);
        $fimPagina = min($totalPaginas, $paginaAtual + 2);

        // Ordenação (padrão: mais recentes primeiro - ordem de inclusão DESC)
        switch ($filters['ordem']) {
            case 'antigos':
                $orderBy = 'id_album ASC';
                break;
            case 'titulo_asc':
                $orderBy = 'titulo ASC';
                break;
            case 'titulo_desc':
                $orderBy = 'titulo DESC';
                break;
            case 'artista_asc':
                $orderBy = 'artista ASC, titulo ASC';
                break;
            case 'artista_desc':
                $orderBy = 'artista DESC, titulo ASC';
                break;
            case 'ano_desc':
                $orderBy = 'data_lancamento DESC, id_album DESC';
                break;
            case 'ano_asc':
                $orderBy = 'data_lancamento ASC, id_album ASC';
                break;
            case 'recentes':
            default:
                $orderBy = 'id_album DESC';
                break;
        }

        // Consulta dos itens paginados
        $selectSql = "SELECT * FROM tb_albuns {$whereClause} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($selectSql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit', $itensPorPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rawAlbuns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mapear compatibilidade com atributos do Soundhaven 2
        $albuns = [];
        foreach ($rawAlbuns as $row) {
            $albuns[] = array_merge($row, [
                'album_id'        => $row['id_album'],
                'capa_url'        => $row['url_capa'],
                'artista_nome'    => $row['artista'],
                'gravadora_nome'  => $row['gravadora'],
                'tipo_desc'       => $row['tipo_album'],
                'numero_catalogo' => $row['num_catalogo'],
                'preco'           => $row['preco_sugerido'],
            ]);
        }

        // Dados para os selects dos filtros e modais
        $artistas   = $this->getDistinctValues('artista');
        $gravadoras = $this->getDistinctValues('gravadora');
        $tipos      = $this->getDistinctValues('tipo_album');
        $generos    = $this->getDistinctValues('genero');

        require_once __DIR__ . '/../Views/loja/grid.php';
    }

    private function handleCreate(): void
    {
        $titulo = trim((string)($_POST['titulo'] ?? ''));
        $artista = trim((string)($_POST['artista'] ?? ''));
        $dataLancamento = trim((string)($_POST['data_lancamento'] ?? ''));

        if ($titulo === '' || $artista === '' || $dataLancamento === '') {
            $_SESSION['flash_error'] = 'Preencha os campos obrigatórios (Título, Artista e Data de Lançamento).';
            header('Location: index.php?url=novo_album_loja');
            exit;
        }

        $usuario = $_SESSION['usuario_login'] ?? $_SESSION['usuario_nome'] ?? 'admin';
        $dataAquisicao = !empty($_POST['data_aquisicao']) ? $_POST['data_aquisicao'] : date('Y-m-d');
        $precoSugerido = !empty($_POST['preco_sugerido']) ? str_replace(',', '.', $_POST['preco_sugerido']) : null;

        $sql = "INSERT INTO tb_albuns (
                    titulo, artista, url_capa, num_catalogo, tipo_album,
                    gravadora, genero, estilo, data_lancamento, data_aquisicao,
                    preco_sugerido, origem, faixas, usuario
                ) VALUES (
                    :titulo, :artista, :url_capa, :num_catalogo, :tipo_album,
                    :gravadora, :genero, :estilo, :data_lancamento, :data_aquisicao,
                    :preco_sugerido, :origem, :faixas, :usuario
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo'          => $titulo,
            ':artista'         => $artista,
            ':url_capa'        => trim((string)($_POST['url_capa'] ?? '')) ?: null,
            ':num_catalogo'    => trim((string)($_POST['num_catalogo'] ?? '')) ?: null,
            ':tipo_album'      => trim((string)($_POST['tipo_album'] ?? 'CD')) ?: 'CD',
            ':gravadora'       => trim((string)($_POST['gravadora'] ?? '')) ?: null,
            ':genero'          => trim((string)($_POST['genero'] ?? '')) ?: null,
            ':estilo'          => trim((string)($_POST['estilo'] ?? '')) ?: null,
            ':data_lancamento' => $dataLancamento,
            ':data_aquisicao'  => $dataAquisicao,
            ':preco_sugerido'  => $precoSugerido,
            ':origem'          => trim((string)($_POST['origem'] ?? '')) ?: null,
            ':faixas'          => trim((string)($_POST['faixas'] ?? '')) ?: null,
            ':usuario'         => $usuario,
        ]);

        $_SESSION['flash_success'] = "Álbum '{$titulo}' cadastrado com sucesso na Loja!";
        header('Location: index.php?url=loja');
        exit;
    }

    private function handleUpdate(): void
    {
        $id = filter_input(INPUT_POST, 'album_id', FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['flash_error'] = 'ID do álbum inválido para atualização.';
            header('Location: index.php?url=loja');
            exit;
        }

        $titulo = trim((string)($_POST['titulo'] ?? ''));
        $artista = trim((string)($_POST['artista'] ?? ''));
        $dataLancamento = trim((string)($_POST['data_lancamento'] ?? ''));

        if ($titulo === '' || $artista === '') {
            $_SESSION['flash_error'] = 'Título e Artista são obrigatórios na edição.';
            header('Location: index.php?url=loja');
            exit;
        }

        $precoSugerido = !empty($_POST['preco_sugerido']) ? str_replace(',', '.', $_POST['preco_sugerido']) : null;

        $sql = "UPDATE tb_albuns SET
                    titulo = :titulo,
                    artista = :artista,
                    url_capa = :url_capa,
                    num_catalogo = :num_catalogo,
                    tipo_album = :tipo_album,
                    gravadora = :gravadora,
                    genero = :genero,
                    estilo = :estilo,
                    data_lancamento = :data_lancamento,
                    preco_sugerido = :preco_sugerido
                WHERE id_album = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id'              => $id,
            ':titulo'          => $titulo,
            ':artista'         => $artista,
            ':url_capa'        => trim((string)($_POST['url_capa'] ?? '')) ?: null,
            ':num_catalogo'    => trim((string)($_POST['num_catalogo'] ?? '')) ?: null,
            ':tipo_album'      => trim((string)($_POST['tipo_album'] ?? '')) ?: null,
            ':gravadora'       => trim((string)($_POST['gravadora'] ?? '')) ?: null,
            ':genero'          => trim((string)($_POST['genero'] ?? '')) ?: null,
            ':estilo'          => trim((string)($_POST['estilo'] ?? '')) ?: null,
            ':data_lancamento' => $dataLancamento ?: date('Y-m-d'),
            ':preco_sugerido'  => $precoSugerido,
        ]);

        $_SESSION['flash_success'] = "Álbum '{$titulo}' atualizado com sucesso!";
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?url=loja'));
        exit;
    }

    private function handleDelete(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $this->db->prepare("DELETE FROM tb_albuns WHERE id_album = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Álbum descartado com sucesso!';
        }
        header('Location: index.php?url=loja');
        exit;
    }

    private function handleImportCsv(): void
    {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash_error'] = 'Erro ao enviar o arquivo CSV.';
            header('Location: index.php?url=loja');
            exit;
        }

        $filepath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            $_SESSION['flash_error'] = 'Não foi possível ler o arquivo CSV.';
            header('Location: index.php?url=loja');
            exit;
        }

        $usuario = $_SESSION['usuario_login'] ?? $_SESSION['usuario_nome'] ?? 'admin';
        $hoje = date('Y-m-d');
        $importedCount = 0;

        // Detectar delimitador (vírgula ou ponto-e-vírgula)
        $primeiraLinha = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count($primeiraLinha, ';') > substr_count($primeiraLinha, ',')) ? ';' : ',';

        $sql = "INSERT INTO tb_albuns (
                    titulo, artista, url_capa, num_catalogo, tipo_album,
                    gravadora, genero, estilo, data_lancamento, data_aquisicao,
                    preco_sugerido, origem, faixas, usuario
                ) VALUES (
                    :titulo, :artista, :url_capa, :num_catalogo, :tipo_album,
                    :gravadora, :genero, :estilo, :data_lancamento, :data_aquisicao,
                    :preco_sugerido, :origem, :faixas, :usuario
                )";
        $stmt = $this->db->prepare($sql);

        $lineIndex = 0;
        while (($row = fgetcsv($handle, 4096, $delimiter)) !== false) {
            $lineIndex++;
            if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                continue;
            }

            // Ignorar cabeçalho caso a primeira coluna seja 'titulo' ou 'title'
            if ($lineIndex === 1 && in_array(strtolower(trim($row[0])), ['titulo', 'title', 'título'])) {
                continue;
            }

            // Colunas esperadas:
            // 0: titulo, 1: artista, 2: url_capa, 3: num_catalogo, 4: tipo_album,
            // 5: gravadora, 6: genero, 7: estilo, 8: data_lancamento, 9: preco_sugerido
            $titulo = trim($row[0] ?? '');
            $artista = trim($row[1] ?? '');
            if ($titulo === '' || $artista === '') {
                continue;
            }

            $urlCapa = trim($row[2] ?? '') ?: null;
            $numCatalogo = trim($row[3] ?? '') ?: null;
            $tipoAlbum = trim($row[4] ?? 'CD') ?: 'CD';
            $gravadora = trim($row[5] ?? '') ?: null;
            $genero = trim($row[6] ?? '') ?: null;
            $estilo = trim($row[7] ?? '') ?: null;
            $dataLanc = trim($row[8] ?? '') ?: $hoje;
            $preco = !empty($row[9]) ? (float)str_replace(',', '.', trim($row[9])) : null;

            $stmt->execute([
                ':titulo'          => $titulo,
                ':artista'         => $artista,
                ':url_capa'        => $urlCapa,
                ':num_catalogo'    => $numCatalogo,
                ':tipo_album'      => $tipoAlbum,
                ':gravadora'       => $gravadora,
                ':genero'          => $genero,
                ':estilo'          => $estilo,
                ':data_lancamento' => $dataLanc,
                ':data_aquisicao'  => $hoje,
                ':preco_sugerido'  => $preco,
                ':origem'          => 'Importação CSV',
                ':faixas'          => null,
                ':usuario'         => $usuario,
            ]);
            $importedCount++;
        }

        fclose($handle);

        $_SESSION['flash_success'] = "Importação em lote concluída com sucesso! {$importedCount} álbuns adicionados.";
        header('Location: index.php?url=loja');
        exit;
    }

    private function getDistinctValues(string $column): array
    {
        $allowedColumns = ['artista', 'gravadora', 'tipo_album', 'genero'];
        if (!in_array($column, $allowedColumns, true)) {
            return [];
        }

        $stmt = $this->db->query("SELECT DISTINCT {$column} FROM tb_albuns WHERE {$column} IS NOT NULL AND {$column} != '' ORDER BY {$column} ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
