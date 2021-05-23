<?php
  class ShipitGiftCard {
    public $from = '';
    public $amount = 0;
    public $total_amount = 0;

    public function __construct($from = '', $amount = 0, $total_amount = 0) {
      $this->from = $from;
      $this->amount = $amount;
      $this->total_amount = $total_amount;
    }

    function getGiftCard() {
      return array(
        'from' => $this->getFrom(),
        'amount' => $this->getAmount(),
        'total_amount' => $this->getTotalAmount()
      );
    }

    function getFrom() {
      return $this->from;
    }

    function getAmount() {
      return $this->amount;
    }

    function getTotalAmount() {
      return $this->total_amount;
    }
  }
?>
