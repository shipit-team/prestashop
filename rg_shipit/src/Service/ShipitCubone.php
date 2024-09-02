<?php
namespace Shipit\Service;

  class ShipitCubone {
    public $url = '';
    public $email = '';
    public $token = '';
    public $headers = '';
    public $base = '';
    public $core = '';
    public $cubone = array();

    public function __construct($email, $token, $version) {
        $this->base = 'https://api.shipit.cl';
        $this->email = $email;
        $this->token = $token;
        $this->headers = array(
          'Content-Type' => 'application/json',
          'X-Shipit-Email' => $email,
          'X-Shipit-Access-Token' => $token,
          'Accept' => 'application/vnd.shipit.v' . $version
        );
      }

    function calculate($shipment = array()) {
      $client = new ShipitHttpClient($this->base . '/v/cubone/pack', $this->headers);
      $response = $client->post($shipment);
      $data = array();
      if ($response->getStatusCode() != 200) {
        echo 'Error al conectar con API.';
      } else {
        $data = json_decode($response->getBody());
      }


      $this->cubone['length'] = $data->packing_measures->length;
      $this->cubone['width'] = $data->packing_measures->width;
      $this->cubone['height'] = $data->packing_measures->height;
      $this->cubone['weight'] = $data->packing_measures->weight;
      $this->cubone['cubication_id'] = $data->cubication->id;


      return $this->cubone;
    }
  }
?>
