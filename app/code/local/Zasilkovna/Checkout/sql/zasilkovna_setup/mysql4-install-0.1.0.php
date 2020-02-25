<?php

$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
CREATE TABLE `packetery_order` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` VARCHAR(128) NOT NULL,
  `recipient_firstname` VARCHAR(128) NOT NULL COMMENT 'Recipient firstname',
  `recipient_lastname` VARCHAR(128) NOT NULL COMMENT 'Recipient firstname',
  `recipient_company` VARCHAR(128) NULL,
  `recipient_email` VARCHAR(128) NULL,
  `recipient_phone` VARCHAR(32) NULL,
  `cod` DECIMAL(8,2) NULL,
  `currency` VARCHAR(8) NULL COMMENT 'ISO 4217 currency code',
  `value` DECIMAL(8,2) NOT NULL COMMENT 'Packet value for insurance purposes',
  `weight` DECIMAL(4,2) NULL COMMENT 'Weight in kilograms',
  `branch_id` VARCHAR(32) NOT NULL COMMENT 'Packetery branch id',
  `point_name` VARCHAR(1024) NULL,
  `sender_label` VARCHAR(64) NULL COMMENT 'Sender (e-shop) label. in case of multiple senders.',
  `adult_content` TINYINT UNSIGNED NULL,
  `delayed_delivery` DATE NULL,
  `recipient_street` VARCHAR(128) NULL,
  `recipient_house_number` VARCHAR(32) NULL,
  `recipient_city` VARCHAR(128) NULL,
  `recipient_zip` VARCHAR(32) NULL,
  `carrier_point` VARCHAR(64) NULL,
  `width` INT NULL COMMENT 'in mm',
  `height` INT NULL,
  `depth` INT NULL,
  `exported` TINYINT UNSIGNED NULL,
  `exported_at` DATETIME NULL,
  `store_label` VARCHAR(64) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `order_number_UNIQUE` (`order_number` ASC));
SQLTEXT;

$installer->run($sql);

$installer->endSetup();