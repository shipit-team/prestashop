<?php
  class ShipitMeasureCollection {
    public $measures = array();

    public function __construct() {}

    function setMeasures($measure = array()) {
      array_push($this->measures, $measure);
    }

    function getMeasuresCollection() {
      return $this->measures;
    }

    function calculate() {
      $measures = $this->getMeasuresCollection();
      $boxify = new Boxify();
      if (count($measures) > 1) {
        return $boxify->calculate(array('packages' => $this->getMeasuresCollection()));
      } elseif ($measures[0]['quantity'] > 1) {
        return $boxify->calculate(array('packages' => $this->getMeasuresCollection()));
      } else {
        return array(
          "height" => $measures[0]['height'],
          "width" => $measures[0]['width'],
          "length" => $measures[0]['length'],
          "weight" => $measures[0]['weight']
        );
      }
    }
  }
?>