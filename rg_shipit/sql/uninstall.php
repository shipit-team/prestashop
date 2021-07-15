<?php
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'rg_shipit_services`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'rg_shipit_cache`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'rg_shipit_commune`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'rg_shipit_shipment`';
$sql[] = 'DELETE FROM ps_order_state_lang WHERE id_order_state BETWEEN 14 AND 22;';
$sql[] = 'DELETE FROM ps_order_state WHERE id_order_state BETWEEN 14 AND 22;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
