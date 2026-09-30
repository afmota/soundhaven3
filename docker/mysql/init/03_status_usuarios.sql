USE `soundhaven3`;

ALTER TABLE `tb_usuarios`
  ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pendente' AFTER `senha`,
  ADD COLUMN `data_cadastro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`;

UPDATE `tb_usuarios` SET `status` = 'ativo' WHERE `usuario` = 'admin';