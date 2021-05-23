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
      $this->extra = $this->validateInsurance($ticket_amount);
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
      $extra = $this->validateInsurance($this->ticket_amount);
      return $extra;
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

    function validateInsurance($total) {
      $shipit_integration_core = new ShipitIntegrationCore(Configuration::get('SHIPIT_EMAIL'), Configuration::get('SHIPIT_TOKEN'),4);
      $settings = $shipit_integration_core->insurance();
      $insurance_setting = $settings->configuration->automatizations;
      return ($insurance_setting->insurance->active) && ($total > $insurance_setting->insurance->amount);
    }
  }
?>
