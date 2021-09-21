<?php
namespace Shipit\Service;
  class ShipitProduct {
    public $sku_id = '';
    public $amount = '';
    public $warehouse_id = '';
    public $description = '';

    public function __construct($sku_id, $amount, $warehouse_id, $description) {
      $this->sku_id = $sku_id;
      $this->amount = $amount;
      $this->warehouse_id = $warehouse_id;
      $this->description = $description;
    }

    function build() {
      return array(
        'sku_id' => $this->getSkuId(),
        'amount' => $this->getAmount(),
        'warehouse_id' => $this->getWarehouseId(),
        'description' => $this->getDescription()
      );
    }

    function getSkuId() {
      return $this->sku_id;
    }

    function getAmount() {
      return $this->amount;
    }

    function getWarehouseId() {
      return $this->warehouse_id;
    }

    function getDescription() {
      return $this->description;
    }
  }
?>
