<?php
  class ShipitCourier {
    public $client = '';
    public $id = '';
    public $selected = '';
    public $payable = '';
    public $algorithm = 1;
    public $algorithm_days = 0;
    public $without_courier = false;

    public function __construct($client, $id, $selected, $payable = false) {
      $this->client = $client;
      $this->id = $id;
      $this->payable = $payable;
      $this->selected = $selected;
    }

    function getCourier() {
      return array(
        'client' => $this->getClient(),
        'id' => $this->getId(),
        'selected' => $this->getSelected(),
        'payable' => $this->getPayable(),
        'algorithm' => $this->getAlgorithm(),
        'algorithm_days' => $this->getAlgorithmDays(),
        'without_courier' => $this->getWithoutCourier()
      );
    }

    function getPayable() {
      return $this->payable;
    }

    function getAlgorithm() {
      return $this->algorithm;
    }

    function getAlgorithmDays() {
      return $this->algorithm_days;
    }

    function getWithoutCourier() {
      return $this->without_courier;
    }

    function getClient() {
      return $this->client;
    }

    function getId() {
      return $this->id;
    }

    function getSelected() {
      return $this->selected;
    }
  }
?>
