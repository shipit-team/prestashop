<?php 
  class ShipitSeller {
    public $id = '';
    public $name = 'Prestashop';
    public $reference_site;
    public $status = '';

    public function __construct($id, $reference_site = '', $status = '') {
      $this->id = $id;
      $this->reference_site = $reference_site;
      $this->status = $status;
    }

    function getSeller() {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'reference_site' => $this->getReferenceSite(),
        'status' => $this->getStatus()
      );
    }
    
    function getId() {
      return $this->id;
    }

    function getName() {
      return $this->name;
    }

    function getReferenceSite() {
      return $this->reference_site;
    }

    function getStatus() {
      return $this->status;
    }
      
  }
?>
