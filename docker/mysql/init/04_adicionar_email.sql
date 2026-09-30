USE `soundhaven3`;

ALTER TABLE `tb_usuarios`
  ADD COLUMN `email` VARCHAR(256) NOT NULL AFTER `nome`;

CREATE UNIQUE INDEX idx_usuarios_email ON `tb_usuarios` (`email`);

UPDATE `tb_usuarios`
  SET `email` = 'soundhaven.midiacollection@gmail.com'
  WHERE `usuario` = 'admin';