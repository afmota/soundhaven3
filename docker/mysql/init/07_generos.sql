USE `soundhaven3`;

CREATE TABLE IF NOT EXISTS `tb_generos` (
  `genero_id` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(50) NOT NULL,
  PRIMARY KEY (`genero_id`),
  UNIQUE KEY `descricao` (`descricao`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `tb_generos` (`genero_id`, `descricao`) VALUES 
(1,'Eletrônica'),
(2,'Rock'),
(3,'Funk / Soul'),
(4,'Pop'),
(5,'Stage & Screen'),
(6,'Reggae'),
(7,'Hip Hop'),
(8,'Blues'),
(9,'Clássica'),
(10,'Non-Music'),
(11,'Jazz')
ON DUPLICATE KEY UPDATE `descricao` = VALUES(`descricao`);
