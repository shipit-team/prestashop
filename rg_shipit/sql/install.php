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

$sql[] = "INSERT INTO `'._DB_PREFIX_.'ps_order_state` (
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
    VALUES  (14, 0, 0, '', '#f4cf58', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (15, 0, 0, '', '#6cca79', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (16, 0, 0, '', '#f4cf58', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (17, 0, 0, '', '#f4cf58', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (18, 0, 0, '', '#dd7272', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (19, 0, 0, '', '#e6b8af', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (20, 0, 0, '', '#484a7d', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (21, 0, 0, '', '#f4cf58', 1, 0, 0, 1, 1, 1, 0, 0, 0),
            (22, 0, 0, '', '#484a7d', 1, 0, 0, 1, 1, 1, 0, 0, 0);";

$sql[] = "INSERT INTO `'._DB_PREFIX_.'ps_order_state_lang` (
    `id_order_state`, 
    `id_lang`, `name`, 
    `template`)
    VALUES  (14, 1, 'En ruta', ''),
            (15, 1, 'Disponible en sucursal', ''),
            (16, 1, 'Problemas de transporte', ''),
            (17, 1, 'Problemas de dirección', ''),
            (18, 1, 'Reembolsado por Shipit', ''),
            (19, 1, 'Problemas de transporte', ''),
            (20, 1, 'Devuelto', ''),
            (21, 1, 'En tránsito', ''),
            (22, 1, 'Envío cancelado', '');";
            
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
