<?php
class ShipitBugsnag {
  
  public $url = 'https://notify.bugsnag.com';
  public $payloadVersion = '5';
  public $headers = array(
    'Content-Type' => 'application/json'
  );

  function payload($error, $message) {
    return array (
      'apiKey' => base64_decode(Configuration::get('SHIPIT_B_T')),
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
                 'id' => Configuration::get('SHIPIT_EMAIL'),
                 'name' => Configuration::get('SHIPIT_EMAIL')
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
      $bugsnag_client = new ShipitHttpClient($this->url, $this->headers);
      $response = $bugsnag_client->post($this->payload($error, $message));
      return $response;
    }

    public function bugsnagLog($response, $from, $reference) {
      if ((isset($response->status)) && ($response->status != 200)) {
        $this->sendNotification("code error ".$response->status.", name error ".$response->error.", from ".$from.": references : ".$reference, $response->exception); 
      }    
    }


  }
?>
