<?php

/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * URL legada del webhook. En PrestaShop 9 modules/.htaccess bloquea los .php del módulo;
 * la URL vigente es la del controlador front (controllers/front/webhook.php).
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
include_once(dirname(__FILE__) . '/rg_shipit.php');

$module = new Rg_Shipit();

die($module->handleWebhookRequest(Tools::getValue('secure_key'), Tools::file_get_contents('php://input')) ? '1' : '0');
