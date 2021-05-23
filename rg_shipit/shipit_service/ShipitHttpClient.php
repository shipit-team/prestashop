<?php
  use GuzzleHttp\Client;

  class ShipitHttpClient {
    public $endpoint = '';
    public $headers = array();
    public $client;

    public function __construct($endpoint, $headers) {
      $this->endpoint = $endpoint;
      $this->headers = $headers;
      $this->client = new Client();
    }

    public function get() {
      $response = $this->client->get(
        $this->endpoint
        , ['headers' => $this->headers
        ,'allow_redirects'=> ['strict'=>true]
      ]);

      return $response;
    }

    public function post($body) {
      $response = $this->client->post(
        $this->endpoint
        ,['json' => $body
        ,'headers' => $this->headers
        ,'allow_redirects'=> ['strict'=>true]]);

      return $response;
    }

    public function allow_redirects_post($body) {
      $response = $this->client->post(
        $this->endpoint
        ,['json' => $body
        ,'headers' => $this->headers
        ,'allow_redirects'=> ['strict'=>true]]);

      return $response;
    }

    public function patch($body = array()) {
      $response = $this->client->patch(
        $this->endpoint
        , ['json' => $body
        , 'headers' => $this->headers]
      );
      $body = $response->getBody();

      return json_decode( $body,true);
    }

    public function put($body = array()) {
      $response = $this->client->put(
        $this->endpoint
        , ['json' => $body
        , 'headers' => $this->headers]
      );

      return $response;
    }
  }
?>
