<?php
class ShipitServices extends ObjectModel
{
    public $id_service;
    public $code;
    public $desc;
    public $id_reference;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'rg_shipit_services',
        'primary' => 'id_service',
        'fields' => array(
            'code' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
            'desc' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64, 'required' => true),
            'id_reference' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'allow_null' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );

    public static function getAll($valid_id_reference = false) {
      $sql = new DbQuery();
      $sql->select('*');
      $sql->from(self::$definition['table']);

      if ($valid_id_reference) {
        $sql->where('`id_reference` > 0');
    }

      return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql->build());
    }

    /**
     * Get service using the code
     */
    public static function getByCode($code) {
      $desc = strtolower($code);
      $sql = new DbQuery();
      $sql->select('`id_service`');
      $sql->from(self::$definition['table']);
      $sql->where('`desc` = "'.pSQL($desc).'"');

      $id_service = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql->build());

      if (!$id_service) {
        return false;
      }

    return new ShipitServices($id_service);
    }

    /**
     * Get service using the code
     */
    public static function getByReference($id_reference) {
      if (version_compare(_PS_VERSION_, '1.6.1.0', '>=') === true) {
        $sql = new DbQuery();
        $sql->select('`id_reference`');
        $sql->from(Carrier::$definition['table']);
        $sql->where('`id_carrier` = '.(int)($id_reference));
        $id_reference = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql->build());
      }
        
      $sql = new DbQuery();
      $sql->select('`id_service`');
      $sql->from(self::$definition['table']);
      $sql->where('`id_reference` = '.(int)($id_reference));
      $id_service = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql->build());

      if (!$id_service) {
        return false;
      }

      return new ShipitServices($id_service);
    }
}
