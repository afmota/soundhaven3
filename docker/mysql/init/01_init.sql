CREATE DATABASE IF NOT EXISTS `soundhaven3` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `soundhaven3`;

CREATE TABLE IF NOT EXISTS `tb_albuns` (
  `id_album` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(256) NOT NULL,
  `artista` varchar(256) NOT NULL,
  `url_capa` text,
  `num_catalogo` varchar(32) DEFAULT NULL,
  `tipo_album` varchar(12) DEFAULT NULL,
  `gravadora` varchar(32) DEFAULT NULL,
  `genero` varchar(256) DEFAULT NULL,
  `estilo` varchar(256) DEFAULT NULL,
  `data_lancamento` date NOT NULL,
  `data_aquisicao` date NOT NULL,
  `preco_sugerido` decimal(10,2) DEFAULT NULL,
  `origem` varchar(20) DEFAULT NULL,
  `faixas` text,
  `usuario` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_album`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;