<?php
  class ShipitMeasure {
    public $height = 10.0;
    public $width = 10.0;
    public $length = 10.0;
    public $weight = 1.0;
    public $quantity = 1;

    public function __construct($height, $width, $length, $weight, $quantity = 1) {
      $this->height = $height;
      $this->width = $width;
      $this->length = $length;
      $this->weight = $weight;
      $this->quantity = $quantity;
    }

    function getMeasure() {
      return array(
        'height' => $this->getHeight(),
        'width' => $this->getWidth(),
        'length' => $this->getLength(),
        'weight' => $this->getWeight(),
        'volumetric_weight' => $this->getVolumetricWeight()
      );
    }

    function buildBoxifyRequest() {
      return array(
        'height' => $this->getHeight(),
        'width' => $this->getWidth(),
        'length' => $this->getLength(),
        'weight' => $this->getWeight(),
        'quantity' => $this->getQuantity()
      );
    }

    function getHeight() {
      return $this->height;
    }

    function getWidth() {
      return $this->width;
    }

    function getLength() {
      return $this->length;
    }

    function getWeight() {
      return $this->weight;
    }

    function getQuantity() {
      return $this->quantity;
    }

    function getVolumetricWeight() {
      return ($this->getHeight() * $this->getWidth() * $this->getLength()) / 4000;
    }
  }
?>