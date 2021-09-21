<?php
namespace Shipit\Service;
  class Shipment {
    public $kind = 2;
    public $platform = 3;
    public $reference = '';
    public $items = 0;
    public $seller = array();
    public $sizes = array();
    public $courier = array();
    public $destiny = array();
    public $insurance = array();
    public $products = array();

    public function __construct( $reference, $items, $seller, $sizes, $courier, $destiny, $insurance, $products = array()) {
      $this->reference = $reference;
      $this->items = $items;
      $this->seller = $seller;
      $this->sizes = $sizes;
      $this->courier = $courier;
      $this->destiny = $destiny;
      $this->insurance = $insurance;
      $this->products = $products;
    }

    function build() {
      return array(
        'reference' => $this->getReference(),
        'items' => $this->getItems(),
        'seller' => $this->getSeller(),
        'sizes' => $this->getSizes(),
        'courier' => $this->getCourier(),
        'destiny' => $this->getDestiny(),
        'insurance' => $this->getInsurance(),
        'products' => $this->getProducts(),
      );
    }

    function getReference() {
      return $this->rederence;
    }

    function getItems() {
      return $this->items;
    }

    function getSeller() {
      return $this->seller;
    }

    function getSizes() {
      return $this->sizes;
    }

    function getCourier() {
      return $this->courier;
    }

    function getDestiny() {
      return $this->destiny;
    }

    function getInsurance() {
      return $this->insurance;
    }

    function getProducts() {
      return $this->products;
    }
  }
?>
