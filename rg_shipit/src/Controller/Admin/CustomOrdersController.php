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

      foreach ($orders_bulk_ids as $id_order) {

        $core = new ShipitIntegrationCore($this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'), 4);
        $company = $core->administrative();
        $order = new Order((int)$id_order);
        $ProductDetailObject = new OrderDetail;
        $products = $ProductDetailObject->getList((int)$id_order);
        $address = new Address((int)$order->id_address_delivery);
        $customer = new Customer((int)$order->id_customer);
        $items = 0;
        $insuranceProducts = '';
        foreach ($products as $prod) {
          $inventory[] = (object) array('sku_id' => $prod['product_reference'], 'amount' => $prod['product_quantity'], 'id' => $prod['product_id'], 'description' => $prod['product_name'], 'warehouse_id' => 1);
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

        if ($integrationseller->configuration->automatic_delivery == true) {

          $shipit_package = [
            'mongo_order_seller' => 'prestashop',
            'reference' => (int)$order->id,
            'full_name' => $address->firstname . ' ' . $address->lastname,
            'email' => $customer->email,
            'items_count' => $items,
            'cellphone' => $address->phone_mobile,
            'is_payable' => false,
            'packing' => 'Sin empaque',
            'shipping_type' => 'Normal',
            'destiny' => 'Domicilio',
            'courier_for_client' => $courierClientName,
            'sent' => false,
            'height' => $shipit_size->width,
            'width' => $shipit_size->height,
            'length' => $shipit_size->length,
            'weight' => $shipit_size->weight,
            'address_attributes' => [
              'commune_id' => $dest_code,
              'street' => ($addressSplit['address'] != '') ? $addressSplit['address'] : $address->address1,
              'number' => $addressSplit['streetNumber'],
              'complement' => ($addressSplit['numberAddition'] ? $addressSplit['numberAddition'] : '') . ($address->address2 ? ' ' . $address->address2 : '')
            ],
            'insurance_attributes' => $shipit_insurance,
            'inventory_activity' => ['inventory_activity_orders_attributes' => $inventory]
          ];

          array_push($massive_orders, $shipit_package);
        } else {
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
      }
      $errors = array();
      if ($integrationseller->configuration->automatic_delivery == true) {
        $api_core = new ShipitIntegrationCore($this->configuration->get('SHIPIT_EMAIL'), $this->configuration->get('SHIPIT_TOKEN'), 2);
        $response = $api_core->massivePackages(['packages' => $massive_orders]);
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
