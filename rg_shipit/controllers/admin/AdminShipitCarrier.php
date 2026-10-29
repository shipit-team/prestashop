<?php
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

use Shipit\Service\ShipitShipment;
use Shipit\Service\ShipitTools;

class AdminShipitCarrierController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'rg_shipit_shipment';
        $this->identifier = 'id_pedido';
        $this->className = ShipitShipment::class;
        $this->lang = false;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->bootstrap = true;
        $this->allow_export = true;

        $this->_select = '
            o.`id_order` AS id_pedido,
            o.`id_order` AS id_pdf,
            o.total_paid_tax_incl,
            o.id_currency,
            o.date_add AS `fecha_pedido`,
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`';
        $this->_join = '
            RIGHT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = a.`id_order`)
            INNER JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
            INNER JOIN `'._DB_PREFIX_.'carrier` ca ON (o.`id_carrier` = ca.`id_carrier` AND ca.`external_module_name` = "rg_shipit")';
        $this->_orderBy = 'o.id_order';
        $this->_orderWay = 'DESC';

        parent::__construct();

        $this->addRowAction('view');

        $this->fields_list = array(
            'id_pedido' => array(
                'title' => $this->module->l('Order ID', 'adminshipitcarrier'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!id_order'
            ),
            'customer' => array(
                'title' => $this->module->l('Customer', 'adminshipitcarrier'),
                'havingFilter' => true,
            ),
            'total_paid_tax_incl' => array(
                'title' => $this->module->l('Order Total', 'adminshipitcarrier'),
                'align' => 'center',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'type' => 'price',
                'class' => 'fixed-width-xs',
                'currency' => true
            ),
            'fecha_pedido' => array(
                'title' => $this->module->l('Order Date', 'adminshipitcarrier'),
                'width' => 152,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'o!date_add'
            ),
            'status' => array(
                'title' => $this->module->l('Shipit Status', 'adminshipitcarrier'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'type' => 'select',
                'list' => $this->module->available_status,
                'filter_key' => 'a!status',
                'filter_type' => 'string'
            ),
            'courier' => array(
                'title' => $this->module->l('Courier', 'adminshipitcarrier'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!courier',
                'filter_type' => 'string'
            ),
            'packing' => array(
                'title' => $this->module->l('Packing', 'adminshipitcarrier'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'a!packing',
                'filter_type' => 'string'
            ),
            'date_upd' => array(
                'title' => $this->module->l('Last Update Date', 'adminshipitcarrier'),
                'width' => 152,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'tracking' => array(
                'title' => $this->module->l('Tracking Number', 'adminshipitcarrier'),
                'class' => 'fixed-width-xs',
                'align' => 'center'
            ),
        );
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

        foreach ($this->_list as &$row) {
            // Los pedidos sin envío vienen con status NULL (RIGHT JOIN); null como índice está deprecado en PHP 8.5.
            $status = (string)$row['status'];
            $row['status'] = (isset($this->module->available_status[$status]) ? $this->module->available_status[$status] : $this->module->l('Other', 'adminshipitcarrier'));
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function renderView()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminOrders', true, array(), array('id_order' => (int)Tools::getValue('id_pedido'), 'vieworder' => 1)));
    }

    /**
     * Oculta el aviso de stock avanzado (antes ajax-bo.php).
     */
    public function ajaxProcessDismissAsmAlert()
    {
        Configuration::updateValue('SHIPIT_ALERTED_ASM', 1);
        die('1');
    }

    /**
     * Muestra el log del módulo en texto plano (antes logs.php).
     */
    public function ajaxProcessViewLogs()
    {
        header('Content-Type: text/plain; charset=utf-8');
        $text = is_readable(ShipitTools::logFile()) ? Tools::file_get_contents(ShipitTools::logFile()) : '';
        die($text ? $text : 'No existen errores registrados.');
    }

    public function renderKpis()
    {
        $kpis = array();

        $helper = new HelperKpi();
        $helper->id = 'box-generated-shipment-percent';
        $helper->icon = 'icon-bar-chart';
        $helper->color = 'color1';
        $helper->title = $this->module->l('Generated Percentage', 'adminshipitcarrier');
        $helper->subtitle = $this->module->l('ALL TIME', 'adminshipitcarrier');
        $valores = Db::getInstance()->getRow(
            'SELECT COUNT(DISTINCT i.`id_order`) as cantidad, COUNT(DISTINCT o.`id_order`) as total
            FROM `'._DB_PREFIX_.'rg_shipit_shipment` i
            RIGHT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = i.`id_order`)
            INNER JOIN `'._DB_PREFIX_.'carrier` ca ON (o.`id_carrier` = ca.`id_carrier` AND ca.`external_module_name` = "rg_shipit")'
        );
        $helper->value = round($valores['cantidad'] / ($valores['total'] ? $valores['total'] : 1) * 100, 2).'%';
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-generated-shipment-total';
        $helper->icon = 'icon-truck';
        $helper->color = 'color2';
        $helper->title = $this->module->l('Generated Total', 'adminshipitcarrier');
        $helper->subtitle = $this->module->l('ALL TIME', 'adminshipitcarrier');
        $helper->value = $valores['cantidad'].'/'.$valores['total'];
        $kpis[] = $helper->generate();

        $helper = new HelperKpiRow();
        $helper->kpis = $kpis;

        return $helper->generate();
    }
}
