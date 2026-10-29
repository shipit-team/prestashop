<?php
namespace Shipit\Service;

/**
 * Cliente HTTP sobre cURL nativo (reemplaza Guzzle 5, que choca con el Guzzle 7 del core de PS 8/9).
 * Nunca lanza excepciones: ante un error de red devuelve una respuesta con status 0.
 */
class ShipitHttpClient {
    const CONNECT_TIMEOUT = 10;
    const TIMEOUT = 30;
    const MAX_REDIRECTS = 5;

    public $endpoint = '';
    public $headers = array();

    public function __construct($endpoint = null, $headers = null) {
      $this->endpoint = $endpoint;
      $this->headers = $headers ? $headers : array();
    }

    public function get() {
      return $this->request('GET');
    }

    public function post($body) {
      return $this->request('POST', $body);
    }

    public function allow_redirects_post($body) {
      return $this->request('POST', $body);
    }

    public function patch($body = array()) {
      return $this->request('PATCH', $body);
    }

    public function put($body = array()) {
      return $this->request('PUT', $body);
    }

    private function request($method, $body = null) {
      $headers = $this->headers;
      $options = array(
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
        CURLOPT_TIMEOUT => self::TIMEOUT,
        // Equivalente a 'allow_redirects' => ['strict' => true]: sigue redirecciones sin convertir POST en GET.
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => self::MAX_REDIRECTS,
        CURLOPT_POSTREDIR => CURL_REDIR_POST_ALL,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
      );

      if ($method != 'GET') {
        $options[CURLOPT_POSTFIELDS] = json_encode($body);
        if (!isset(array_change_key_case($headers)['content-type'])) {
          $headers['Content-Type'] = 'application/json';
        }
      }

      $header_lines = array();
      foreach ($headers as $name => $value) {
        $header_lines[] = $name . ': ' . $value;
      }
      $options[CURLOPT_HTTPHEADER] = $header_lines;

      $ch = curl_init($this->endpoint);
      curl_setopt_array($ch, $options);
      $response_body = curl_exec($ch);

      if ($response_body === false) {
        return new ShipitHttpResponse(0, '', curl_error($ch));
      }

      return new ShipitHttpResponse((int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE), $response_body);
    }
  }
