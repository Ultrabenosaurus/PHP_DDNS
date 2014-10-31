/**
 * PHP_DDNS default schema.
 *
 * @author      Dan Bennett <http://ultrabenosaurus.ninja>
 * @package     PHP_DDNS\Core
 * @version     0.1.0
 * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 *
 * @todo        Try switching to empty CREATE TABLE followed by ALTER TABLE.
 */

CREATE DATABASE IF NOT EXISTS `@name@` DEFAULT CHARACTER SET latin1;
USE `@name@`;

CREATE TABLE IF NOT EXISTS `@table@`(
  `id` INT(10) AUTO_INCREMENT,
  `uuid` VARCHAR(50) NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT 'computer',
  `key` VARCHAR(10) NOT NULL,
  `ip_address` VARCHAR(16) NOT NULL,
  `first_update` DATETIME NOT NULL,
  `last_update` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_unique` (`uuid`),
  UNIQUE KEY `name_unique` (`name`)
) ENGINE=INNODB;
