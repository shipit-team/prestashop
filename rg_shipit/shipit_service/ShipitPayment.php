<?php 
  class ShipitPayment {
    public $type = '';
    public $subtotal = 0;
    public $tax = 0;
    public $currency = 0;
    public $discounts = 0;
    public $total = 0;
    public $status = '';
    public $confirmed = false;

    public function __construct($type, $subtotal, $tax, $currency, $discounts, $total, $status, $confirmed) {
      $this->type = $type;
      $this->subtotal = $subtotal;
      $this->tax = $tax;
      $this->currency = $currency;
      $this->discounts = $discounts;
      $this->total = $total;
      $this->status = $status;
      $this->confirmed = $confirmed;
    }

    function getPayment() {
      return array(
        'type' => $this->getType(),
        'subtotal' => $this->getSubtotal(),
        'tax' => $this->getTax(),
        'currency' => $this->getCurrency(),
        'discounts' => $this->getDiscounts(),
        'total' => $this->getTotal(),
        'status' => $this->getStatus(),
        'confirmed' => $this->getConfirmed()
      );
    }

    function getType() {
      return $this->type;
    }

    function getSubtotal() {
      return $this->subtotal;
    }

    function getTax() {
      return $this->tax;
    }

    function getCurrency() {
      return $this->currency;
    }

    function getDiscounts() {
      return $this->discounts;
    }

    function getTotal() {
      return $this->total;
    }

    function getStatus() {
      return $this->status;
    }

    function getConfirmed() {
      return $this->confirmed;
    }

  }
?>
