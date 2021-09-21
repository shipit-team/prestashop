<?php
namespace Shipit\Service;

  class ShipitOrder {
    public $platform;
    public $kind;
    public $reference;
    public $items;
    public $sandbox;
    public $company_id;
    public $service;
    public $state;
    public $products;
    public $payable;
    public $payment = array();
    public $source = array();
    public $seller = array();
    public $gift_card = array();
    public $sizes = array();
    public $courier = array();
    public $price = array();
    public $insurance = array();
    public $city_track = array();
    public $origin = array();
    public $destiny = array();
    public $mongo_order_seller;

    public function __construct(
        $platform,
        $kind,
        $reference,
        $items,
        $sandbox,
        $company_id,
        $service,
        $state,
        $products,
        $payable,
        $payment,
        $source,
        $seller,
        $gift_card,
        $sizes,
        $courier,
        $price,
        $insurance,
        $city_track,
        $origin,
        $destiny,
        $mongo_order_seller = ''
        ) {
            $this->platform = $platform;
            $this->kind = $kind;
            $this->reference = $reference;
            $this->items = $items;
            $this->sandbox = $sandbox;
            $this->company_id = $company_id;
            $this->service = $service;
            $this->state = $state;
            $this->products = $products;
            $this->payable = $payable;
            $this->payment = $payment;
            $this->source = $source;
            $this->seller = $seller;
            $this->gift_card = $gift_card;
            $this->sizes = $sizes;
            $this->courier = $courier;
            $this->price = $price;
            $this->insurance = $insurance;
            $this->city_track = $city_track;
            $this->origin = $origin;
            $this->destiny = $destiny;
            $this->mongo_order_seller = $mongo_order_seller;
          }

    function build() {
      return array(
        'platform' => $this->getPlatform(),
        'kind' => $this->getKind(),
        'reference' => $this->getReference(),
        'items' => $this->getItems(),
        'sandbox' => $this->getSandbox(),
        'company_id' => $this->getCompanyId(),
        'service' => $this->getService(),
        'state' => $this->getState(),
        'products' => $this->getProducts(),
        'payable' => $this->getPayable(),
        'payment' => $this->getPayment(),
        'source' => $this->getSource(),
        'seller' => $this->getSeller(),
        'gidt_card' => $this->getGiftCard(),
        'sizes' => $this->getSize(),
        'courier' => $this->getCourier(),
        'prices' => $this->getPrice(),
        'insurance' => $this->getInsurance(),
        'city_track' => $this->getCityTrack(),
        'origin' => $this->getOrigin(),
        'destiny' => $this->getDestiny(),
        'destiny' => $this->getMongoOrderSeller()
      );
    }

    public function getPlatform() {
      return $this->platform;
    }

    public function getKind() {
      return $this->kind;
    }

    public function getReference() {
      return $this->reference;
    }

    public function getItems() {
      return $this->items;
    }

    public function getSandbox() {
      return $this->sandbox;
    }

    public function getCompanyId() {
      return $this->company_id;
    }

    public function getService() {
      return $this->service;
    }

    public function getState() {
      return $this->state;
    }

    public function getProducts() {
      return $this->products;
    }

    public function getPayable() {
      return $this->payable;
    }

    public function getPayment() {
      return $this->payment;
    }

    public function getSource() {
      return $this->source;
    }

    public function getSeller() {
      return $this->seller;
    }

    public function getGiftCard() {
      return $this->gift_card;
    }

    public function getSize() {
      return $this->sizes;
    }

    public function getCourier() {
      return $this->courier;
    }

    public function getPrice() {
      return $this->price;
    }

    public function getInsurance() {
      return $this->insurance;
    }

    public function getCityTrack() {
      return $this->city_track;
    }

    public function getOrigin() {
      return $this->origin;
    }

    public function getDestiny() {
      return $this->destiny;
    }

    public function getMongoOrderSeller() {
      return $this->mongo_order_seller;
    }
}
?>
