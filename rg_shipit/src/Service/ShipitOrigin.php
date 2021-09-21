<?php
namespace Shipit\Service;

  class ShipitOrigin {
    public $street = '';
    public $number = '';
    public $complement = '';
    public $commune_id = '';
    public $full_name = '';
    public $email = '';
    public $phone = '';
    public $store = false;
    public $origin_id = null;
    public $name = null;

    public function __construct($street, $number, $complement, $commune_id, $full_name, $email, $phone , $store, $origin_id, $name) {
      $this->street = $street;
      $this->number = $number;
      $this->complement = $complement;
      $this->commune_id = $commune_id;
      $this->full_name = $full_name;
      $this->email = $email;
      $this->phone = $phone;
      $this->store = $store;
      $this->origin_id = $origin_id;
      $this->name = $name;
    }

    function getOrigin() {
      return array(
        'street' => $this->getStreet(),
        'number' => $this->getNumber(),
        'complement' => $this->getComplement(),
        'commune_id' => $this->getCommuneId(),
        'full_name' => $this->getFullName(),
        'email' => $this->getEmail(),
        'phone' => $this->getPhone(),
        'store' => $this->getStore(),
        'origin_id' => $this->getOriginId(),
        'name' => $this->getName()
       );
    }

    public function getStreet() {
      return $this->street;
    }

    public function getNumber() {
      return $this->street;
    }

    public function getComplement() {
      return $this->complement;
    }

    public function getCommuneId() {
      return $this->commune_id;
    }

    public function getFullName() {
      return $this->full_name;
    }

    public function getEmail() {
      return $this->email;
    }

    public function getPhone() {
      return $this->phone;
    }

    public function getStore() {
      return $this->store;
    }

    public function getOriginId() {
      return $this->origin_id;
    }

    public function getName() {
      return $this->name;
    }
  }
?>
