USE `soundhaven3`;

CREATE TABLE IF NOT EXISTS `tb_usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(64) NOT NULL UNIQUE,
  `nome` varchar(256) NOT NULL,
  `senha` varchar(255) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `tb_usuarios` (`usuario`, `nome`, `senha`)
VALUES ('admin', 'Administrador', '$2y$10$UwlTkpak7ko9z9F/Us5zm.GwGRZfx/jw3ETclYBkf99MGDGZAGo1q')
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`), `senha` = VALUES(`senha`);