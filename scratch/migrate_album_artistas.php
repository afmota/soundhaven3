<?php

require_once __DIR__ . '/../autoload.php';

use App\Config\Database;

$db = Database::getConnection();

echo "1. Criando tabela tb_album_artistas...\n";
$db->exec("
CREATE TABLE IF NOT EXISTS `tb_album_artistas` (
  `id_album` int NOT NULL,
  `artista_id` int NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_album`, `artista_id`),
  KEY `fk_album_artistas_artista` (`artista_id`),
  CONSTRAINT `fk_album_artistas_album` FOREIGN KEY (`id_album`) REFERENCES `tb_albuns` (`id_album`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_album_artistas_artista` FOREIGN KEY (`artista_id`) REFERENCES `tb_artistas` (`artista_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
");

echo "2. Flexibilizando tb_albuns.artista...\n";
$db->exec("ALTER TABLE `tb_albuns` MODIFY `artista` varchar(256) DEFAULT NULL;");

// Corrigir títulos/artistas com caracteres corrompidos no seed
$db->exec("UPDATE `tb_albuns` SET `titulo` = 'Construção' WHERE `id_album` = 16;");
$db->exec("UPDATE `tb_albuns` SET `artista` = 'Milton Nascimento & Lô Borges' WHERE `id_album` = 8;");

echo "3. Migrando artistas existentes dos álbuns para tb_album_artistas...\n";
$albuns = $db->query("SELECT id_album, titulo, artista FROM tb_albuns")->fetchAll(PDO::FETCH_ASSOC);

$stmtFindArtist = $db->prepare("SELECT artista_id, nome FROM tb_artistas WHERE LOWER(TRIM(nome)) = LOWER(TRIM(:nome)) LIMIT 1");
$stmtInsertArtist = $db->prepare("INSERT INTO tb_artistas (nome, ativo, criado_em) VALUES (:nome, 1, NOW())");
$stmtLink = $db->prepare("INSERT IGNORE INTO tb_album_artistas (id_album, artista_id) VALUES (:id_album, :artista_id)");

foreach ($albuns as $album) {
    $rawArt = trim($album['artista'] ?? '');
    if ($rawArt === '') continue;

    // Separar colaborações com &, e, com, feat., ft. se existirem
    $partes = [];
    if (strpos($rawArt, '&') !== false) {
        $partes = array_map('trim', explode('&', $rawArt));
    } else {
        $partes = [$rawArt];
    }

    foreach ($partes as $nomeArtista) {
        $nomeArtista = trim($nomeArtista);
        if ($nomeArtista === '') continue;

        // Normalização especial de nomes conhecidos
        if (strcasecmp($nomeArtista, 'Guns N Roses') === 0 || strcasecmp($nomeArtista, 'Guns N\' Roses') === 0) {
            $nomeArtista = "Guns n' Roses";
        }

        $stmtFindArtist->execute([':nome' => $nomeArtista]);
        $art = $stmtFindArtist->fetch(PDO::FETCH_ASSOC);

        if ($art) {
            $artistaId = (int)$art['artista_id'];
            echo "  [Encontrado] Álbum #{$album['id_album']} '{$album['titulo']}' -> Artista #{$artistaId} '{$art['nome']}'\n";
        } else {
            $stmtInsertArtist->execute([':nome' => $nomeArtista]);
            $artistaId = (int)$db->lastInsertId();
            echo "  [Criado Novo] Artista #{$artistaId} '{$nomeArtista}' para Álbum #{$album['id_album']}\n";
        }

        $stmtLink->execute([
            ':id_album' => $album['id_album'],
            ':artista_id' => $artistaId
        ]);
    }
}

echo "Migração concluída com sucesso!\n";
