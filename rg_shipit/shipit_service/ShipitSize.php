<?php 
  class ShipitSize {
    public $width = 0.0;
    public $height = 0.0;
    public $length = 0.0;
    public $weight = 0.0;
    public $volumetric_weight = 0.0;
    public $store = true;
    public $packing_id = null;
    public $name = '';

    public function __construct($id_cart, $store = true, $packing_id = null, $name = '') {
      $cache = new ShipitCache($id_cart);
      $this->width = (float)$cache->package['width'];
      $this->height = (float)$cache->package['height'];
      $this->length = (float)$cache->package['depth'];
      $this->weight = (float)$cache->package['weight'];
      $this->volumetric_weight = (float)$cache->package['width']*(float)$cache->package['height']*(float)$cache->package['depth'];
      $this->store = $store;
      $this->packing_id = $packing_id;
      $this->name = $name;
    }

    function getSize() {
      return array(
        'width' => $this->getWidth(),
        'height' => $this->getHeight(),
        'length' => $this->getLength(),
        'weight' => $this->getWeight(),
        'volumetric_weight' => $this->getVolumetricWeight(),
        'store' => $this->getStore(),
        'packing_id' => $this->getPackingId(),
        'name' => $this->getName()
      );
    }

    function getWidth() {
      return $this->width;
    }

    function getHeight() {
      return $this->height;
    }

    function getLength() {
      return $this->length;
    }

    function getWeight() {
      return $this->weight;
    }

    function getVolumetricWeight() {
      return $this->volumetric_weight;
    }

    function getStore() {
      return $this->store;
    }

    function getPackingId() {
      return $this->packing_id;
    }

    function getName() {
      return $this->name;
    }
  }
?>
