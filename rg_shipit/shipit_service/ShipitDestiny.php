<?php
  class ShipitDestiny {
    public $number = '';
    public $street = '';
    public $complement = '';
    public $commune_id = '';
    public $commune_name = '';
    public $full_name = '';
    public $email = '';
    public $phone = '';
    public $kind = 'home_delivery';
    public $courier_destiny_id = null;
    public $courier_branch_office_id = null;
    public $store;
    public $destiny_id;
    public $name;

    public function __construct($number, $street, $complement, $commune_id, $commune_name, $full_name, $email, $phone, $store = false, $destiny_id = null, $name = 'predeterminado') {
      $this->number = $number;
      $this->street = $street;
      $this->complement = $complement;
      $this->commune_id = $commune_id;
      $this->commune_name = $commune_name;
      $this->full_name = $full_name;
      $this->email = $email;
      $this->phone = $phone;
      $this->store = $store;
      $this->destiny_id = $destiny_id;
      $this->name = $name;
    }

    function getDestiny() {
      return array(
        'number' => $this->getNumber(),
        'street' => $this->getStreet(),
        'complement' => $this->getComplement(),
        'commune_id' => $this->getCommuneId(),
        'commune_name' => $this->getCommuneName(),
        'full_name' => $this->getFullName(),
        'email' => $this->getEmail(),
        'phone' => $this->getPhone(),
        'kind' => $this->getKind(),
        'courier_destiny_id' => $this->getCourierDestinyId(),
        'courier_branch_office_id' => $this->getCourierBranchOfficeId(),
        'store' => $this->getStore(),
        'destiny_id' => $this->getDestinyId(),
        'name' => $this->getName()    
      );
    }

    function getNumber() {
      return $this->number;
    }

    function getStreet() {
      return $this->street;
    }

    function getComplement() {
      return $this->complement;
    }

    function getCommuneId() {
      return $this->commune_id;
    }
    
    function getCommuneName() {
      return $this->commune_name;
    }

    function getFullName() {
      return $this->full_name;
    }

    function getEmail() {
      return $this->email;
    }

    function getPhone() {
      return $this->phone;
    }

    function getKind() {
      return $this->kind;
    }

    function getCourierDestinyId() {
      return $this->courier_destiny_id;
    }

    function getCourierBranchOfficeId() {
      return $this->courier_branch_office_id;
    }

    public function getStore() {
      return $this->store;
    }

    public function getDestinyId() {
      return $this->destiny_id;
    }

    public function getName() {
      return $this->name;
    }
  }
?>
