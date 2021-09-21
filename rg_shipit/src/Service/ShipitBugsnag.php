<?php
class Bugsnag {

  public $url = 'https://notify.bugsnag.com';
  public $payloadVersion = '5';
  public $headers = array(
    'Content-Type' => 'application/json'
  );

  function payload($error, $message) {
    global $wpdb;
    return array (
      'apiKey' => base64_decode($wpdb->get_var("SELECT bt FROM {$wpdb->prefix}user_shipit ORDER BY id DESC LIMIT 1")),
      'payloadVersion' => '5',
      'notifier' => 
       array (
         'name' => 'Bugsnag',
          'version' => '1.0.11',
          'url' => 'https://github.com/bugsnag/bugsnag-ruby',
          'dependencies' => 
          array (
            0 => 
            array (
              'name' => 'Bugsnag PHP',
              'version' => '2.1.10',
              'url' => 'https://github.com/bugsnag/bugsnag-php',
            ),
          ),
        ),
        'events' => 
        array (
          0 => 
          array (
            'exceptions' => 
              array (
                0 => 
                array (
                  'errorClass' => $error,
                  'message' => $message,
                  'stacktrace' => 
                  array (
                    0 => 
                    array (),
                  ),
                  'type' => 'php',
                ),
              ),
            'user' => 
               array (
                 'id' => get_option('shipit_user')['shipit_user'],
                 'name' => get_option('shipit_user')['shipit_user']
               ),
            'app' => 
               array (
                 'id' => 'prestashop',
                 'version' => phpversion()
               ),
            'device' => 
               array (
                 'hostname' => $_SERVER['SERVER_NAME']
               ),
          ),
        ),
      );
    }

    public function sendNotification($error, $message) {
      $bugsnag_client = new HttpClient($this->url, $this->headers);
      $response = $bugsnag_client->post($this->payload($error, $message));
      return $response;
    }

    public function bugsnagLog($response, $from, $reference) {
      if ((isset($response->status)) && ($response->status != 200)) {
        $r = $this->sendNotification("code error ".$response->status.", name error ".$response->error.", from ".$from.": references : ".$reference, $response->exception);
        ShipitDebug::debug("code error ".$response->status.", name error ".$response->error.", from ".$from.": ".$response->exception."la referencia ".$reference);
      }
    }

    public function bugsnagWpLog($response, $from, $reference) {
      $this->sendNotification("error ", $response->errors['http_request_failed'][0].", from ".$from." ,references:".$reference );
      ShipitDebug::debug("error ".$response->errors['http_request_failed'][0].", from ".$from); 
    }

    public function getReferencesFromOrdersMassive($orders) {
      foreach ($orders['orders'] as $key => $value) {
        isset($references) ? $references .= ',' : null;
        $references .= $value['reference'];
      }
      return $references;
    }

  }
?>