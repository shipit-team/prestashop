<?php
namespace Shipit\Service;

  class Boxify {
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

    function calculate($shipment = array()) {
      $client = new HttpClient($this->base . '/packs', $this->headers);
      $response = $client->post($shipment);
      $data = array();
      if (is_wp_error($response)) {
        echo 'Error al conectar con API.';
      } else {
        $data = json_decode($response['body']);
      }
      return (array)$data->packing_measures;
    }
  }
?>
