<?php
  class ShipitIntegrationOrder {
    public $email = '';
    public $token = '';
    public $headers = '';
    public $base = '';

    public function __construct($email, $token, $version) {
      $this->base = 'http://orders.shipit.cl/v';
      $this->email = $email;
      $this->token = $token;
      $this->headers = array(
        'Content-Type' => 'application/json',
        'X-Shipit-Email' => $email,
        'X-Shipit-Access-Token' => $token,
        'Accept' => 'application/vnd.orders.v'.$version
      );
    }

    function setting() {
      $client = new ShipitHttpClient($this->base.'/integrations/seller/prestashop', $this->headers);
      $response = $client->get();
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), integrations seller response: '.print_r($response,true));
      } else {
        $setting = json_decode($response->getBody());
      }
      return $setting;
    }

    function configure($setting = array()) {
      $client = new ShipitHttpClient($this->base.'/integrations/configure', $this->headers);
      $response = $client->put($setting);
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), integrations configure: '.print_r($response,true));
      } else {
        $setting = json_decode($response->getBody());
      }
      return $setting;
    }

    function orders($order) {
      $client = new ShipitHttpClient($this->base.'/orders', $this->headers);
      $response = $client->post(['order' => $order]);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), orders response: '.print_r($response,true));
        return false;
      } else {
        $order = json_decode($response->getBody());
        return $order->id;
      }
    }

    function massiveOrders($order = array()) {
      $client = new ShipitHttpClient($this->base.'/orders/massive', $this->headers);
      $response = $client->post($order);
      if ($response != null && $response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), orders massive response: '.print_r($response,true));
        return false;
      } else {
        $order = json_decode($response->getBody());
        return $order;
      }
    }
  }
?>
