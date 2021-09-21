<?php
namespace Shipit\Service;
  class ShipitPrice {
    public $total;
    public $price;
    public $cost = 0;
    public $insurance = 0;
    public $tax;
    public $overcharge ;

    public function __construct($total, $price, $cost, $insurance, $tax, $overcharge) {
      $this->total = $total;
      $this->price = $price;
      $this->cost = $cost;
      $this->insurance = $insurance;
      $this->tax = $tax;
      $this->overcharge = $overcharge;
    }

    function getPrices() {
      return array(
        'total' => $this->getTotal(),
        'price' => $this->getPrice(),
        'cost' => $this->getCost(),
        'insurance' => $this->GetInsurance(),
        'tax' => $this->getTax(),
        'overcharge' => $this->getOvercharge()
      );
    }

    function getTotal() {
      return $this->total;
    }

    function getPrice() {
      return $this->price;
    }

    function getCost() {
      return $this->cost;
    }

    function getInsurance() {
      return $this->insurance;
    }

    function getTax() {
      return $this->tax;
    }

    function getOvercharge() {
      return $this->overchargue;
    }
  }
?>
