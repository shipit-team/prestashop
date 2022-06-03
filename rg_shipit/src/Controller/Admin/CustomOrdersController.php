<?php

namespace Shipit\Controller\Admin;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Shipit\Service\ShipitIntegrationOrder;
use Shipit\Service\ShipitIntegrationCore;
use Shipit\Service\ShipitTools;
use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShop\PrestaShop\Adapter\Entity\OrderDetail;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Customer;
use Shipit\Service\ShipitServices;
use Shipit\Service\ShipitLists;
use Shipit\Service\ShipitPayment;
use Shipit\Service\ShipitSource;
use Shipit\Service\ShipitSeller;
use PrestaShop\PrestaShop\Adapter\Entity\Tools;
use Shipit\Service\ShipitGiftCard;
use Shipit\Service\ShipitSize;
use Shipit\Service\ShipitCache;
use Shipit\Service\ShipitCourier;
use Shipit\Service\ShipitPrice;
use Shipit\Service\ShipitInsurance;
use Shipit\Service\ShipitCityTrack;
use Shipit\Service\ShipitOrigin;
use Shipit\Service\ShipitDestiny;
use Shipit\Service\ShipitOrder;

class CustomOrdersController extends FrameworkBundleAdminController
{
  public function indexAction(Request $request)
  {
    $orders_bulk_ids = $request->request->all()['order_orders_bulk'];
    $this->processBulkSended($orders_bulk_ids);
    return $this->redirect('sell/orders/');
  }

  private function processBulkSended($orders_bulk_ids)
  {
    if (is_array($orders_bulk_ids) && !empty($orders_bulk_ids)) {

      $massive_orders = array();
      $api_order = new ShipitIntegrationOrder($this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'), 1);
      $integrationseller = $api_order->setting();
      $core = new ShipitIntegrationCore($this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'), 4);
      $company = $core->administrative();
      $skus = array();
      if ($company->service->name == 'fulfillment') {
        $skus = $core->skus();
      }
      foreach ($orders_bulk_ids as $id_order) {
        $order = new Order((int)$id_order);
        $ProductDetailObject = new OrderDetail;
        $products = $ProductDetailObject->getList((int)$id_order);
        $address = new Address((int)$order->id_address_delivery);
        $customer = new Customer((int)$order->id_customer);
        $items = 0;
        $insuranceProducts = '';
        $inventory = array();
        foreach ($products as $prod) {
          if (!empty($skus)) {
            $sku = $prod['product_reference'] != '' ? $prod['product_reference'] : $prod['product_id'];
            foreach ($skus as $skuObject) {
              if (strtolower($skuObject->name) == strtolower($sku)) {
                $inventory[] = (object) array('sku_id' => $skuObject->id
                                              , 'amount' => $prod['product_quantity']
                                              , 'description' => $skuObject->description
                                              , 'warehouse_id' => $skuObject->warehouse_id);
              }
            }
          }
          $items += $prod['product_quantity'];
          if ($insuranceProducts != '') $insuranceProducts .= ',';
          $insuranceProducts .=  $prod['product_name'];
        }
        $testStreets = array();
        $testStreets[] = $address->address1;
        $tool = new ShipitTools();
        for ($i = 0, $totalTestStreets = count($testStreets); $i < $totalTestStreets; $i++) {
          $addressSplit = $tool->splitAddressAndNumber($testStreets[$i]);
        }

        $service = ShipitServices::getByReference((int)$order->id_carrier);
        $dest_code = ShipitLists::searchcityId($address->city);
        $shipit_payment = new ShipitPayment($order->payment, 0, 0, 0, 0, $order->total_paid, '', false);
        $shipit_source = new ShipitSource('', '', '', '', '');
        $shipit_seller = new ShipitSeller((int)$order->id, Tools::getHttpHost(true) . __PS_BASE_URI__, '', $this->configuration->get('SHIPIT_INTEGRATION_DATE'));
        $shipit_gift_card = new ShipitGiftCard();
        $shipit_size = new ShipitSize((int)$order->id_cart);
        $tool = new ShipitTools();
        $courierClientName = $tool->getClientName((int)$order->id_carrier);
        $CourierId = $tool->getCourierId(
          $this->configuration->get('SHIPIT_EMAIL'),
          $this->configuration->get('SHIPIT_TOKEN'),
          (int)!$this->configuration->get('SHIPIT_LIVE_MODE'),
          $courierClientName
        );
        $shipit_courier = new ShipitCourier($courierClientName, $CourierId, ($CourierId == null) ? false : true);
        $shipit_price = new ShipitPrice($order->total_paid, $order->total_shipping, 0, 0, $order->carrier_tax_rate, 0);
        $shipit_insurance = new ShipitInsurance($order->total_paid - $order->total_shipping, $id_order, $insuranceProducts, true, $this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'));
        $shipit_city_track = new ShipitCityTrack('', '2019-06-07T17:13:09.141-04:00', '', '', '');
        $shipit_origin = new ShipitOrigin('', '', '', '', '', '', '', false, null, null);
        $shipit_destiny = new ShipitDestiny(
          $addressSplit['streetNumber'],
          ($addressSplit['address'] != '') ? $addressSplit['address'] : $address->address1,
          ($addressSplit['numberAddition'] ? $addressSplit['numberAddition'] : '') . ($address->address2 ? ' ' . $address->address2 : ''),
          (int)$dest_code,
          $address->city,
          $address->firstname . ' ' . $address->lastname,
          $customer->email,
          ($address->phone_mobile ? $address->phone_mobile : $address->phone),
          false,
          null,
          'predeterminado'
        );
          $shipit_order = new ShipitOrder(
            3,
            2,
            (int)$order->id,
            $items,
            false,
            (int)$company->id,
            2,
            1,
            $inventory,
            false,
            $shipit_payment,
            $shipit_source,
            $shipit_seller,
            $shipit_gift_card,
            $shipit_size,
            $shipit_courier,
            $shipit_price,
            $shipit_insurance,
            $shipit_city_track,
            $shipit_origin,
            $shipit_destiny
          );

          array_push($massive_orders, $shipit_order);
      }
      $errors = array();
      if ($integrationseller->configuration->automatic_delivery == true) {
        $api_core = new ShipitIntegrationCore($this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'), 4);
        $response = $api_core->massiveShipments(['shipments' => $massive_orders]);
      } else {
        $response = $api_order->massiveOrders(['orders' => $massive_orders]);
      }
      if (empty($response->errors) && $response) {
        $this->confirmations[] = "Acción masiva ejecutada correctamente.";
      } else {
        ShipitTools::log('Errors' . print_r($response->errors, true));
        $this->errors[] = Tools::displayError("Error en acción masiva, Puede revisar los logs dentro del modulo shipit para más detalle.");
        $this->context->controller->errors[] = $error;
        if ($errors) {
          ShipitTools::log('PrestaShop (' . _PS_VERSION_ . '), error: ' . print_r($errors, true));
        }
      }
    }
  }
}
