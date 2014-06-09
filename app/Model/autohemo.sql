SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `locais`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `locais` ;

CREATE  TABLE IF NOT EXISTS `locais` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(60) NOT NULL ,
  `criado` DATETIME NOT NULL ,
  `modificado` DATETIME NOT NULL ,
  `retirada` INT NOT NULL DEFAULT 0 COMMENT 'local de retirada ?' ,
  `aplicacao` INT NOT NULL DEFAULT 0 COMMENT 'local de aplicação ?' ,
  PRIMARY KEY (`id`) ,
  INDEX `i_nome` (`nome` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `retiradas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `retiradas` ;

CREATE  TABLE IF NOT EXISTS `retiradas` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `data` DATETIME NOT NULL ,
  `reti_qtd` DECIMAL(12,2) NOT NULL DEFAULT 0.00 ,
  `usuario_id` INT(11) NOT NULL DEFAULT 0 ,
  `local_id` INT NOT NULL ,
  `criado` DATETIME NOT NULL DEFAULT '2014-09-06 15:13:15' ,
  `modificado` DATETIME NOT NULL DEFAULT '2014-09-06 15:13:16' ,
  PRIMARY KEY (`id`) ,
  INDEX `i_reti_qtd` (`data` ASC) ,
  INDEX `i_usuario_id` (`usuario_id` ASC) ,
  INDEX `fk_local_id` (`local_id` ASC) ,
  INDEX `i_modificado` (`modificado` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `aplicacoes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `aplicacoes` ;

CREATE  TABLE IF NOT EXISTS `aplicacoes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `data` DATETIME NOT NULL ,
  `apli_qtd` DECIMAL(12,2) NOT NULL DEFAULT 0.00 ,
  `usuario_id` INT(11) NOT NULL ,
  `local_id` INT NOT NULL ,
  `retirada_id` INT NOT NULL ,
  `criado` DATETIME NOT NULL DEFAULT '2014-09-06 15:13:15' ,
  `modificado` DATETIME NOT NULL DEFAULT '2014-09-06 15:13:16' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_local_id` (`local_id` ASC) ,
  INDEX `i_data` (`data` ASC) ,
  INDEX `i_usuario_id` (`usuario_id` ASC) ,
  INDEX `fk_aplicacoes_retiradas1` (`retirada_id` ASC) ,
  INDEX `i_modificado` (`modificado` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `perfis`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `perfis` ;

CREATE  TABLE IF NOT EXISTS `perfis` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `i_nome` (`nome` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `usuarios`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuarios` ;

CREATE  TABLE IF NOT EXISTS `usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(60) NOT NULL ,
  `email` VARCHAR(60) NOT NULL ,
  `tele_resi` VARCHAR(14) NOT NULL ,
  `celular` VARCHAR(14) NOT NULL ,
  `aniversario` VARCHAR(4) NOT NULL ,
  `cidade` VARCHAR(60) NOT NULL DEFAULT 2302 ,
  `senha` VARCHAR(45) NOT NULL ,
  `perfiL_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `i_nome` (`nome` ASC) ,
  INDEX `i_email` (`email` ASC) ,
  INDEX `i_celular` (`celular` ASC) ,
  INDEX `i_aniversario` (`aniversario` ASC) ,
  INDEX `i_cidade` (`cidade` ASC) ,
  INDEX `fk_usuarios_perfis1` (`perfiL_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `locais`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (1, 'Braço Direito', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 1, 1);
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (2, 'Braço Esquerdo', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 1, 1);
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (3, 'Nádega Direita', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 0, 1);
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (4, 'Nádega Esquerda', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 0, 1);
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (5, 'Coxa Direita', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 0, 1);
INSERT INTO `locais` (`id`, `nome`, `criado`, `modificado`, `retirada`, `aplicacao`) VALUES (6, 'Coxa Esquerda', '2014-01-01 12:30:30', '2014-01-01 12:30:31', 0, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `perfis`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `perfis` (`id`, `nome`) VALUES (1, 'ADMINISTRADOR');
INSERT INTO `perfis` (`id`, `nome`) VALUES (2, 'GERENTE');
INSERT INTO `perfis` (`id`, `nome`) VALUES (3, 'USUÁRIO');
INSERT INTO `perfis` (`id`, `nome`) VALUES (4, 'VISITANTE');

COMMIT;

-- -----------------------------------------------------
-- Data for table `usuarios`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `usuarios` (`id`, `nome`, `email`, `tele_resi`, `celular`, `aniversario`, `cidade`, `senha`, `perfiL_id`) VALUES (1, 'ADMINISTRADOR AUTO HEMOTERAPIA', 'admin@autohemo.com.br', '31123456789', '33123456789', '0101', 'BELO HORIZONTE', 'f86faa4904c06024aec5c697a5d900ed', 1);

COMMIT;
