<?php
namespace Shipit\Service;

  class ShipitIntegrationCore {
    public $url = '';
    public $email = '';
    public $token = '';
    public $headers = '';
    public $base = '';

    public function __construct($email, $token, $version) {
      $this->base = 'https://api.shipit.cl/v';
      $this->email = $email;
      $this->token = $token;
      $this->headers = array(
        'Content-Type' => 'application/json',
        'X-Shipit-Email' => $email,
        'X-Shipit-Access-Token' => $token,
        'Accept' => 'application/jsonapplication/vnd.shipit.v' . $version
      );
    }

    function packages($package = array()) {
      $client = new ShipitHttpClient($this->base.'/packages', $this->headers);
      $response = $client->post($package);
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), package response: '.print_r($response,true));
      } else {
        $package = json_decode($response->getBody());
      }
      return $package;
    }

    function massivePackages($packages = array()) {
      $client = new ShipitHttpClient($this->base.'/packages/mass_create', $this->headers);
      $response = $client->post($packages);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), massive package response: '.print_r($response,true));
        return false;
      } else {
        $package = json_decode($response->getBody());
        return $package;
      }
    }

    function massiveShipments($shipments) {
      $client = new ShipitHttpClient($this->base.'/shipments/massive/import', $this->headers);
      $response = $client->post($shipments);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), shipments response: '.print_r($response,true));
        return false;
      } else {
        $shipments_response = json_decode($response->getBody());
        return $shipments_response;
      }
    }

    function orders($order = array()) {
      $client = new ShipitHttpClient($this->base.'/orders', $this->headers);
      $response = $client->post($order);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), orders response: '.print_r($response,true));
      } else {
        $order = json_decode($response->getBody());
        return $order;
      }
    }

    function shipments($shipment) {
      $client = new ShipitHttpClient($this->base.'/shipments', $this->headers);
      $response = $client->post(['shipment' => $shipment]);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), shipment response: '.print_r($response,true));
        return false;
      } else {
        $shipment = json_decode($response->getBody());
        return $shipment->id;
      }
    }

    function administrative() {
      $client = new ShipitHttpClient($this->base.'/setup/administrative', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), administrative response: '.print_r($response,true));
      } else {
        $company = json_decode($response->getBody());
      }
      return $company;
    }

    function communes() {
      $client = new ShipitHttpClient($this->base.'/communes', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), communes response: '.print_r($response,true));
      } else {
        $communes = json_decode($response->getBody());
      }
      return $communes;
    }

    function skus() {
      $client = new ShipitHttpClient($this->base.'/fulfillment/skus/all', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), skus response: '.print_r($response,true));
      } else {
        $skus = json_decode($response->getBody());
      }
      return $skus;
    }

    function insurance() {
      $client = new ShipitHttpClient($this->base.'/settings/9', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), insurance response: '.print_r($response,true));
      } else {
        $setting = json_decode($response->getBody());
      }
      return $setting;
    }

    function setWebhook($webhook = array()) {
      $client = new ShipitHttpClient($this->base.'/integrations/webhook', $this->headers);
      $response = $client->patch($webhook);
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), setwebhook response: '.print_r($response,true));
      } else {
        $webhook_response = json_decode($response->getBody());
      }
      return $webhook_response;
    }

    function rates($params = array(), $best_price) {
      $client = new ShipitHttpClient($this->base.'/rates', $this->headers);
      $response = $client->allow_redirects_post($params);
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), rates response: '.print_r($response,true));
        $emergency_rates = new ShipitEmergencyRate();
        $result = json_decode($emergency_rates->getEmergencyRates($params['parcel']['destiny_id']));
      } else {
        $result = json_decode($response->getBody());
      }

      $costs = array();
      if ($best_price && $response->getStatusCode() == 200) {
          $costs['shipit'] = (float)$result->lower_price->price;
      } elseif (!empty($result)) {
          foreach ($result->prices as $ship) {
            if ($ship->available_to_shipping) {
              $costs[isset($ship->courier->name) ? $ship->courier->name : $ship->original_courier] = (float)$ship->price;
            }
          }
      }
      return $costs;
    }


    function couriers($couriers = array()) {
      $client = new ShipitHttpClient($this->base.'/couriers', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), couriers: '.print_r($response,true));
      } else {
        $couriers = json_decode($response->getBody());
      }
      return $couriers;
    }
}
?>
