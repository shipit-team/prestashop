<?php
/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

class ShipitAPI
{
    public $email;
    public $token;
    public $development;
    private static $sizes = array(
        29 => 'Pequeño (10x10x10cm)',
        49 => 'Mediano (30x30x30cm)',
        60 => 'Grande (50x50x50cm)',
        999999 => 'Muy Grande (>60x60x60cm)'
    );

    /**
     * Initialize the class to make requests to the webservice.
     *
     * @param string $email
     * @param string $token
     * @param bool $development [optional] Define if the mode is for production or development
     */
    public function __construct($email, $token, $development = false)
    {
        $this->email = $email;
        $this->token = $token;
        $this->development = $development;
    }

    public function getCommunesList(&$errors)
    {
        try {
            $result = $this->execute('communes', 2, false);
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        return $result;
    }

    /**
     * Get available integrations
     *
     * @param array $errors By reference var for errors
     * @return bool|array array of stdObject for correct response or FALSE if errors.
     */
    public function getIntegrationsSeller(&$errors)
    {
        try {
            $result = $this->execute('integrations/seller/prestashop', 1, true);
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        foreach ($result as $settingseller) {
            $seller = array(
                'automatic_delivery' => $settingseller->automatic_delivery,
                'show_shipit_checkout' => $settingseller->show_shipit_checkout,
            );
        }

        return $seller;
    }

    /**
     * Get available platform
     *
     * @param array $errors By reference var for errors
     * @return bool|array array of stdObject for correct response or FALSE if errors.
     */
    public function getPlatformSetup(&$errors)
    {
        try {
            $result = $this->execute('setup/administrative', 4, false);
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        $administrative = $result->platform_version;
        return $administrative;
    }

    /**
     * Get available couriers
     *
     * @param array $errors By reference var for errors
     * @return bool|array array of stdObject for correct response or FALSE if errors.
     */
    public function getCouriersList(&$errors)
    {
        try {
            $result = $this->execute('couriers', 3, false);
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        $couriers = array();

        foreach ($result as $courier) {
            $couriers[] = array(
                'id' => $courier->slug,
                'name' => $courier->name,
                'active' => $courier->available_to_ship,
                'image' => $courier->image_original_url
            );
        }

        return $couriers;
    }

    /**
     * Calculate shipment cost
     *
     * @param array $params Array for services params
     * @param array $errors By reference var for errors
     * @return bool|array array of stdObject for correct response or FALSE if errors.
     */
    public function getEstimateCost($params, $best_price, &$errors)
    {
        try {
            $result = $this->execute('prices',3, false, $params);
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service getEstimateCost ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        $costs = array();
        if ($best_price) {
            $costs['shipit'] = (float)$result->lower_price->price;
        } else {
            foreach ($result->prices as $ship) {
                if ($ship->available_to_shipping) {
                    $costs[isset($ship->courier->name) ? $ship->courier->name : $ship->original_courier] = (float)$ship->price;
                }
            }
        }

        return $costs;
    }

    /**
     * Generate a shipment
     *
     * @param array $params Array for services params
     * @param string $errors By reference var for errors
     * @return int|bool Shipment ID if correct response or FALSE if errors.
     */
    public function generateShipment($params, &$errors, $versiondb)
    {
        if ($this->development) {
            $params['package']['reference'] = 'TEST-'.$params['package']['reference'];
        }
        if ($versiondb == 2) {
            $params['package']['mongo_order_seller'] = 'prestashop';
        }

        try {
            if ($versiondb == 2) {
                $result = $this->execute('packages',2, false, $params);
                }elseif ($versiondb == 3) {
                $result = $this->execute('shipments',4, false, $params);
                }
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service generateShipment ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        return (int)$result->id;
    }
    /**
     * Generate a order
     *
     * @param array $params Array for services params
     * @param string $errors By reference var for errors
     * @return int|bool Shipment ID if correct response or FALSE if errors.
     */
    public function generateOrder($params, &$errors, $versiondb)
    {
        if ($this->development) {
            $params['order']['reference'] = 'TEST-'.$params['order']['reference'];
        }

        $params['order']['mongo_order_seller'] = 'prestashop';

        try {
            if ($versiondb == 2) {
            $result = $this->execute('orders',4, false, $params);
            }elseif ($versiondb == 3) {
            $result = $this->execute('orders',1, true, $params);
            }
            if (!$result) {
                return false;
            }
        } catch (Exception $e) {
            $errors[] = 'There was an error executing service generateOrder ('.$e->getMessage().').';

            return false;
        }

        if ($errors = $this->checkErrors($result)) {
            return false;
        }

        return (int)$result->id;
    }

    private function execute($endpoint, $version, $orders, $params = false)
    {
        if ($orders == false) {
            $headers = array(
                'Content-Type: application/json',
                'X-Shipit-Email: '.$this->email,
                'X-Shipit-Access-Token: '.$this->token,
                'Accept: application/vnd.shipit.v'.$version
            );
            $url = 'https://api.shipit.cl/v/'.$endpoint;

        } else {

            $headers = array(
                'Content-Type: application/json',
                'X-Shipit-Email: '.$this->email,
                'X-Shipit-Access-Token: '.$this->token,
                'Accept: application/vnd.orders.v'.$version
            );

            $url = 'http://orders.shipit.cl/v/'.$endpoint;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($params) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }

        $response = curl_exec($ch);


        curl_close($ch);

        if (!$response) {
            return false;
        }

        $result = json_decode($response);
        return $result;
    }

    public static function getPackageSize($width, $height, $length)
    {
        // TODO verificar el funcionamiento real de esto y como se determina el tamaño.
        $package = max($width, $height, $length);

        foreach (self::$sizes as $limit => $name) {
            if ($package <= $limit) {
                return $name;
            }
        }

        return 'Muy Grande (>60x60x60cm)';
    }

    private function checkErrors($result)
    {
        if (isset($result->error) && $result->error) {
            return $result->error;
        }

        return false;
    }
}
