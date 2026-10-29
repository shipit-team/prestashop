<?php
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * Webhook de Shipit: index.php?fc=module&module=rg_shipit&controller=webhook&secure_key=...
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class Rg_ShipitWebhookModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $guestAllowed = true;
    public $ajax = true;

    public function postProcess()
    {
        $processed = $this->module->handleWebhookRequest(
            Tools::getValue('secure_key'),
            Tools::file_get_contents('php://input')
        );

        header('Content-Type: text/plain');
        die($processed ? '1' : '0');
    }

    /**
     * Shipit debe poder notificar aunque la tienda esté en mantenimiento.
     */
    protected function displayMaintenancePage()
    {
    }

    /**
     * Las llamadas de Shipit no deben pasar por la restricción geográfica de la tienda.
     */
    protected function displayRestrictedCountryPage()
    {
    }
}
