<?php
namespace Shipit\Service;

  use GuzzleHttp\Client;
  use GuzzleHttp\Exception\ConnectException;
  use GuzzleHttp\Exception\RequestException;
  use GuzzleHttp\Message\Response;

class ShipitHttpClient {
    public $endpoint = '';
    public $headers = array();
    public $client;

    public function __construct($endpoint = null, $headers = null) {
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
      try {

        $response = $this->client->post(
          $this->endpoint
          ,['json' => $body
          ,'headers' => $this->headers
          ,'allow_redirects'=> ['strict'=>true]]);
        return $response;

        } catch (RequestException $e)
          {
            if ($e->hasResponse()){
              return $e->getResponse();
            }
          } catch (\Exception $e) {
            return $response = null;
          }
      }

    public function allow_redirects_post($body) {
      try {
      $response = $this->client->post(
        $this->endpoint
        ,['json' => $body
        ,'headers' => $this->headers
        ,'allow_redirects'=> ['strict'=>true]]);

      return $response;
      } catch (ConnectException $e) {
          return new ShipitHttpClient();
      } catch (RequestException $e) {
          return new ShipitHttpClient();
      }
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

    public function getStatusCode() {
      return 500;
    }
  }
