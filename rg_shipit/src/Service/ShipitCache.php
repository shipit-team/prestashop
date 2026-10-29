<?php
namespace Shipit\Service;
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */
use Tools;

class ShipitCache extends \ObjectModel
{
    const TYPE_INT = 1;
    const TYPE_STRING = 3;
    public $id_cart;
    public $hash_cart;
    public $package;
    public $carriers;

    public static $definition = array(
        'table' => 'rg_shipit_cache',
        'primary' => 'id_cart',
        'fields' => array(
            'id_cart' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'hash_cart' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 32, 'allow_null' => true),
            'package' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'allow_null' => true),
            'carriers' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'allow_null' => true)
        )
    );

    public function __construct($id = null)
    {
      parent::__construct($id);

        if ($id > 0) {
            $this->id_cart = (int)$id;
            if ($this->package) {
                $this->package = Tools::unSerialize($this->package);
            }

            if ($this->carriers) {
                $this->carriers = Tools::unSerialize($this->carriers);
            }
        }
    }

    /**
     * REPLACE en vez de INSERT/UPDATE: el checkout de PS 9 (Hummingbird) dispara requests en paralelo
     * que recotizan el mismo carro, y un INSERT doble fallaba con "Duplicate entry" en la PK id_cart.
     */
    public function save($null_values = false, $auto_date = true)
    {
        $this->serializeOption();
        $saved = \Db::getInstance()->insert(
            self::$definition['table'],
            array(
                'id_cart' => (int)$this->id_cart,
                'hash_cart' => $this->hash_cart === null ? null : pSQL($this->hash_cart),
                'package' => $this->package === null ? null : pSQL($this->package),
                'carriers' => $this->carriers === null ? null : pSQL($this->carriers),
            ),
            true,
            false,
            \Db::REPLACE
        );
        $this->id = (int)$this->id_cart;
        $this->clearCache();

        return $saved;
    }

    public function add($auto_date = true, $null_values = false)
    {
        return $this->serializeOption() && parent::add($auto_date, $null_values);
    }

    public function update($null_values = false)
    {
        return $this->serializeOption() && parent::update($null_values);
    }

    private function serializeOption()
    {
        if (is_array($this->package) || is_object($this->package)) {
            $this->package = serialize($this->package);
        } else {
            $this->package = null;
        }

        if (is_array($this->carriers) || is_object($this->carriers)) {
            $this->carriers = serialize($this->carriers);
        } else {
            $this->carriers = null;
        }

        return true;
    }

    public function hasValidCarriers()
    {
        if ($this->carriers && is_array($this->carriers)) {
            foreach ($this->carriers as $carrier) {
                if ($carrier) {
                    return true;
                }
            }
        }

        return false;
    }
}
