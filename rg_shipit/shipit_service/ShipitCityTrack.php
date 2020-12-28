<?php
  class ShipitCityTrack {
    public $draft = '';
    public $confirmed = '2019-06-07T17:13:09.141-04:00';
    public $deliver = '';
    public $canceled = '';
    public $archived = '';

    public function __construct($draft, $confirmed, $deliver, $canceled, $archived) {
      $this->draft = $draft;
      $this->confirmed = $confirmed;
      $this->deliver = $deliver;
      $this->canceled = $canceled;
      $this->archived = $archived;
    }

    function getCityTrack() {
      return array(
        'draft' => $this->getDraft(),
        'confirmed' => $this->getConfirmed(),
        'deliver' => $this->getDeliver(),
        'canceled' => $this->getCanceled(),
        'archived' => $this->getArchived()   
       );
    }

    function getDraft() {
      return $this->draft;
    }

    function getConfirmed() {
      return $this->confirmed;
    }

    function getDeliver() {
      return $this->deliver;
    }

    function getCanceled() {
      return $this->canceled;
    }

    function getArchived() {
      return $this->archived;
    }
  }
?>
