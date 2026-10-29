<?php
namespace Shipit\Service;

/**
 * Respuesta de ShipitHttpClient. Expone getStatusCode()/getBody() como la respuesta de Guzzle que reemplaza.
 */
class ShipitHttpResponse {
    public $status_code;
    public $body;
    public $error;

    public function __construct($status_code, $body, $error = '') {
      $this->status_code = $status_code;
      $this->body = $body;
      $this->error = $error;
    }

    public function getStatusCode() {
      return $this->status_code;
    }

    public function getBody() {
      return $this->body;
    }
  }
