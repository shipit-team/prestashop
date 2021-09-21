<?php
namespace Shipit\Service;

  class ShipitSource {
    public $channel;
    public $ip;
    public $browser;
    public $language;
    public $location;

    public function __construct($channel, $ip, $browser, $language, $location) {
      $this->channel = $channel;
      $this->ip = $ip;
      $this->browser = $browser;
      $this->language = $language;
      $this->location = $location;
    }

    function getSource() {
      return array(
        'channel' => $this->getChannel(),
        'ip' => $this->getIp(),
        'browser' => $this->getBrowser(),
        'language' => $this->getLanguage(),
        'location' => $this->getLocation()
      );
    }

    function getChannel() {
      return $this->channel;
    }

    function getIp() {
      return $this->ip;
    }

    function getBrowser() {
      return $this->browser;
    }

    function getLanguage() {
      return $this->language;
    }

    function getLocation() {
      return $this->location;
    }
  }
?>
