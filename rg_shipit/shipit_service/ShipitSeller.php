<?php 
  class ShipitSeller {
    public $id = '';
    public $name = 'Prestashop';
    public $reference_site;
    public $status = '';
    public $created_at = '';

    public function __construct($id, $reference_site = '', $status = '', $created_at = '') {
      $this->id = $id;
      $this->reference_site = $reference_site;
      $this->status = $status;
      $this->created_at= $created_at;
    }

    function getSeller() {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'reference_site' => $this->getReferenceSite(),
        'status' => $this->getStatus(),
        'created_at' => $this->getCreatedAt()
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

    function getCreatedAt() {
      return $this->created_at;
    }

  }
?>
