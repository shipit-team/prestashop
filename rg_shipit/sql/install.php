<?php
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rg_shipit_services` (
    `id_service` int(11) NOT NULL AUTO_INCREMENT,
    `code` varchar(64) NOT NULL,
    `desc` varchar(64) NOT NULL,
    `id_reference` int(11) DEFAULT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY  (`id_service`),
    KEY `code` (`code`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rg_shipit_cache` (
    `id_cart` int(10) UNSIGNED NOT NULL,
    `hash_cart` varchar(32) COLLATE utf8_general_ci DEFAULT NULL,
    `package` text COLLATE utf8_general_ci DEFAULT NULL,
    `carriers` text COLLATE utf8_general_ci DEFAULT NULL,
    PRIMARY KEY  (`id_cart`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rg_shipit_commune` (
    `id` int(11) NOT NULL,
    `name` varchar(128) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rg_shipit_shipment` (
    `id_rg_shipit_shipment` int(11) NOT NULL AUTO_INCREMENT,
    `shipit_id` int(11) NOT NULL,
    `id_order` int(11) NOT NULL,
    `status` varchar(64) NOT NULL,
    `tracking` varchar(64) NULL,
    `courier` varchar(64) NULL,
    `packing` varchar(64) NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY  (`id_rg_shipit_shipment`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'INSERT INTO `ps_order_state` (
    `id_order_state`,
    `invoice`, 
    `send_email`, 
    `module_name`, 
    `color`, 
    `unremovable`, 
    `hidden`, 
    `logable`, 
    `delivery`, 
    `shipped`, 
    `paid`, 
    `pdf_invoice`, 
    `pdf_delivery`, 
    `deleted`) 
    VALUES  (140, 0, 0, "", "rgb(244 207 88)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (141, 0, 0, "", "rgb(108 202 121)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (142, 0, 0, "", "rgb(244 207 88)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (143, 0, 0, "", "rgb(244 207 88)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (144, 0, 0, "", "rgb(221 114 114)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (145, 0, 0, "", "rgb(230 184 175)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (146, 0, 0, "", "rgb(72 74 125)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (147, 0, 0, "", "rgb(244 207 88)", 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (148, 0, 0, "", "rgb(72 74 125)", 1, 0, 0, 1, 1, 1, 0, 0, 0);';

$sql[] = 'INSERT INTO `ps_order_state_lang` (
    `id_order_state`, 
    `id_lang`,
    `name`, 
    `template`)
    VALUES  (140, 1, "En ruta", ""),
            (141, 1, "Disponible en sucursal", ""),
            (142, 1, "Problemas de transporte", ""),
            (143, 1, "Problemas de dirección", ""),
            (144, 1, "Reembolsado por Shipit", ""),
            (145, 1, "Problemas de transporte", ""),
            (146, 1, "Devuelto", ""),
            (147, 1, "En tránsito", ""),
            (148, 1, "Envío cancelado", "");';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rg_shipit_emergency_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `region` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
