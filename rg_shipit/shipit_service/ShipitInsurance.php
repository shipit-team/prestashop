<?php
  class ShipitInsurance {
    public $ticket_amount = 0.0;
    public $ticket_number = '';
    public $detail = '';
    public $extra = false;
    public $name = 'Sólido';
    public $store = false;
    public $company_id = '1';

    public function __construct($ticket_amount, $ticket_number, $detail, $extra) {
      $this->ticket_amount = $ticket_amount;
      $this->ticket_number = $ticket_number;
      $this->detail = $detail;
      $this->extra = $extra;
    }

    function getInsurance() {
      return array(
        'ticket_amount' => $this->getTicketAmount(),
        'ticket_number' => $this->getTicketNumber(),
        'detail' => $this->getDetail(),
        'extra' => $this->getExtra(),
        'name' => $this->getName(),
        'store' => $this->getStore(),
        'company_id' => $this->getCompanyId()
      );
    }

    function getTicketAmount() {
      return $this->ticket_amount;
    }

    function getTicketNumber() {
      return $this->ticket_number;
    }

    function getDetail() {
      return $this->detail;
    }

    function getExtra() {
      return $this->extra;
    }

    function getName() {
      return $this->name;
    }

    function getStore() {
      return $this->store;
    }

    function getCompanyId() {
      return $this->company_id;
    }
  }
?>
