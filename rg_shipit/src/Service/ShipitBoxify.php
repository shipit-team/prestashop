<?php
namespace Shipit\Service;

  class ShipitBoxify {
    public $url = '';
    public $email = '';
    public $token = '';
    public $headers = '';
    public $base = '';

    public function __construct() {
      $this->base = 'https://boxify.shipit.cl';
      $this->headers = array(
        'Content-Type' => 'application/json'
      );
    }

    function calculate($sizes = array()) {
      $client = new ShipitHttpClient($this->base.'/packs', $this->headers);
      $response = $client->post($sizes);
      if ($response->getStatusCode() != 200) {
        ShipitTools::log('PrestaShop ('._PS_VERSION_.'), package response: '.print_r($response,true));
      } else {
        $sizes = json_decode($response->getBody());
      }
      return $sizes;
    }

  }
?>
