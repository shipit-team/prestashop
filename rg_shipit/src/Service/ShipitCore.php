<?php
namespace Shipit\Service;

/**
 * Integral logistics for eCommerce with pickup or fulfillment through Shipit
 *
 * @author    Rolige <www.rolige.com>
 * @copyright 2011-2018 Rolige - All Rights Reserved
 * @license   Proprietary and confidential
 */

use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Currency;
use PrestaShop\PrestaShop\Adapter\Entity\Country;
use PrestaShop\PrestaShop\Adapter\Entity\Carrier;
use PrestaShop\PrestaShop\Adapter\Entity\Configuration;
use Shipit\libraries\ShipitLAFFPack;
class ShipitCore extends \CarrierModule
{
    const EST_MODE_WEIGHT = 1;
    const EST_MODE_CUBIC = 2;
    const EST_MODE_3D = 3;

    public function getOrderShippingCost($params, $shipping_cost) {}

    public function getOrderShippingCostExternal($params) {}

    public function updateCache($cart, $hash_cart)
    {
        $cache = new ShipitCache((int)$cart->id);

        // Initialize cache values.
        $cache->hash_cart = $hash_cart;
        $cache->package = null;
        $cache->carriers = null;

        $products = $cart->getProducts();
        $normalized = Configuration::get('SHIPIT_NORMALIZED');
        $estimation_mode = (int)Configuration::get('SHIPIT_ESTIMATION_MODE');
        $services_list = ShipitServices::getAll(true);
        $address = new Address((int)$cart->id_address_delivery);

        $weight = 0;

        if ($products) {
            switch ($estimation_mode) {
                /* case self::EST_MODE_WEIGHT: */
                case self::EST_MODE_CUBIC:
                    $volume = ShipitTools::getProductsVolume(Configuration::get('SHIPIT_SET_VALUE_WIDTH'), Configuration::get('SHIPIT_SET_VALUE_HEIGHT'),Configuration::get('SHIPIT_SET_VALUE_DEPTH'), Configuration::get('SHIPIT_SET_DIMENSIONS'), $cart);
                    $weight = ShipitTools::getProductsWeight(Configuration::get('SHIPIT_SET_VALUE_WEIGHT'), Configuration::get('SHIPIT_SET_WEIGHT'), $cart);

                    if (!$volume || !$weight) {
                      // ShipitTools::log('updateCache: products without dimensions and/or weight (volume => '.$volume.', weight => '.$weight.').');

                        return false;
                    }

                    $pow = pow($volume, 1 / 3);
                    $width = $pow;
                    $height = $pow;
                    $depth = $pow;
                    $ps_dimension_unit = Configuration::get('PS_DIMENSION_UNIT');
                    $ps_weight_unit = Configuration::get('PS_WEIGHT_UNIT');
                    $width = ShipitTools::convertToCm($ps_dimension_unit, $width);
                    $height = ShipitTools::convertToCm($ps_dimension_unit, $height);
                    $depth = ShipitTools::convertToCm($ps_dimension_unit, $depth);
                    $weight = ShipitTools::convertToKg($ps_weight_unit, $weight);

                    break;

                case self::EST_MODE_3D:
                    $boxes = array();

                    foreach ($products as $product) {
                        $product_boxes = $this->getProductBoxes($product);
                        $boxes = !$boxes ? $product_boxes : array_merge($boxes, $product_boxes);
                    }

                    $weight = ShipitTools::getProductsWeight(Configuration::get('SHIPIT_SET_VALUE_WEIGHT'), Configuration::get('SHIPIT_SET_WEIGHT'), $cart);

                    // Initialize ShipitLAFFPack.
                    $lap = new ShipitLAFFPack();

                    // Start packing our nice boxes.
                    $lap->pack($boxes);
                    // Collect our container details.
                    $c_size = $lap->get_container_dimensions();
                    $ps_dimension_unit = Configuration::get('PS_DIMENSION_UNIT');
                    $ps_weight_unit = Configuration::get('PS_WEIGHT_UNIT');
                    $width = ShipitTools::convertToCm($ps_dimension_unit, $c_size['width']);
                    $height = ShipitTools::convertToCm($ps_dimension_unit, $c_size['height']);
                    $depth = ShipitTools::convertToCm($ps_dimension_unit, $c_size['length']);
                    $weight = ShipitTools::convertToKg($ps_weight_unit, $weight);
                    // Shipit does not allow empty fields.
                    if (!$width || !$height || !$depth || !$weight) {
                      // ShipitTools::log('updateCache: products without dimensions and/or weight (width => '.$width.', height => '.$height.', depth => '.$depth.', weight => '.$weight.').');

                        return false;
                    }

                    break;
            }
        }

        // Check if there is a valid weight.
        if (($weight > 0) &&
            // Check if there is a services list available.
            $services_list &&
            // Check if currency of Chile exists.
            Currency::getIdByIsoCode('CLP') &&
            // Check if the country of Chile exists.
            Country::getByIso('CL') &&
            // Check if the addresses were normalized.
            $normalized &&
            // Check if the id_address in the delivery address correspond to Chile.
            (Country::getByIso('CL') == $address->id_country)
        ) {
            $cache->package = array(
                'width' => $width,
                'height' => $height,
                'depth' => $depth,
                'weight' => $weight
            );

            $dest_code = ShipitLists::searchCityId($address->city);

            $cache = $this->getCostsByCarrier($cache, $dest_code, $weight, $height, $width, $depth);
        }

        return $cache->save();
    }

    public function getProductBoxes($product)
    {
        $boxes = array();

        for ($i = 0; $i < $product['cart_quantity']; $i++) {
            $s_width = (float)Configuration::get('SHIPIT_SET_VALUE_WIDTH');
            $s_height = (float)Configuration::get('SHIPIT_SET_VALUE_HEIGHT');
            $s_depth = (float)Configuration::get('SHIPIT_SET_VALUE_DEPTH');
            $product_width = (float)Configuration::get('width');
            $product_height = (float)Configuration::get('height');
            $product_depth = (float)Configuration::get('depth');

            switch (Configuration::get('SHIPIT_SET_DIMENSIONS')) {
                case 1: // Set the specified dimensions.
                    $values = array(
                        ($s_width ? $s_width : $product_width),
                        ($s_height ? $s_height : $product_height),
                        ($s_depth ? $s_depth : $product_depth)
                    );

                    break;

                case 2: // When the product dimension is missing or not set.
                    $values = array(
                        ($s_width && !$product_width ? $s_width : $product_width),
                        ($s_height && !$product_height ? $s_height : $product_height),
                        ($s_depth && !$product_depth ? $s_depth : $product_depth)
                    );

                    break;

                case 3: // When the product dimension is less than specified.
                    $values = array(
                        ($s_width && ($product_width < $s_width) ? $s_width : $product_width),
                        ($s_height && ($product_height < $s_height) ? $s_height : $product_height),
                        ($s_depth && ($product_depth < $s_depth) ? $s_depth : $product_depth)
                    );

                    break;

                case 4: // When the product dimension is greater than specified.
                    $values = array(
                        ($s_width && ($product_width > $s_width) ? $s_width : $product_width),
                        ($s_height && ($product_height > $s_height) ? $s_height : $product_height),
                        ($s_depth && ($product_depth > $s_depth) ? $s_depth : $product_depth)
                    );

                    break;

                default:
                    $values = array(
                        $product_width,
                        $product_height,
                        $product_depth
                    );

                    break;
            }

            sort($values);

            $boxes[] = array_combine(array('height', 'width', 'length'), $values);
        }

        return $boxes;
    }

    /**
     * Get the cost of the available services
     * @param  [string] $destination The city code destination
     * @param  [float]  $weight      The weight of the product in Kg
     * @param  [float]  $height      The height of the product in cm
     * @param  [float]  $width       The width of the product in cm
     * @param  [float]  $depth       The depth of the product in cm
     * @return [array]  Return empty if no services
     */
    protected function getServicesCost($destination, $weight, $height, $width, $depth)
    {
        $variables = array(
            'destination' => $destination,
            'weight' => $weight,
            'height' => $height,
            'width' => $width,
            'depth' => $depth
        );

        $services = $this->calculatePricing($variables);

        return $services;
    }

    /**
     * Calculate the pricing for the shipping.
     *
     *  The information to calculate the pricing. The structure:
     *    - destination: The city code destination.
     *    - weight: The weight of the product in Kg.
     *    - height: The height of the product in cm.
     *    - width: The width of the product in cm.
     *    - depth: The depth of the product in cm.
     *  If fails FALSE is return or an array of services available.
     * @param  array    $variables
     * @return mixed
     */
    private function calculatePricing(array $variables)
    {
        $params = array(
            'parcel' => array(
                'height' => $variables['height'],
                'length' => $variables['depth'],
                'width' => $variables['width'],
                'weight' => $variables['weight'],
                'type_of_destiny' => 'Domicilio',
                'origin_id' => 308,
                'destiny_id' => (int)$variables['destination'],
                'rate_from' => 'prestashop'
            )
        );

        $errors = false;
        $api = new ShipitIntegrationCore(Configuration::get('SHIPIT_EMAIL'), Configuration::get('SHIPIT_TOKEN'),4);
        $results = $api->rates($params,(int)Configuration::get('SHIPIT_DISPATCH_ALGORITHM'));

        if (!$results) {
          ShipitTools::log('calculatePricing: '.print_r($errors, true));

          return false;
        }

        return $results;
    }

    public function getCostsByCarrier($cache, $dest_code, $weight, $height, $width, $depth) {
        if ($dest_code) {
            // Get the shipping costs available.
            $services_cost = $this->getServicesCost($dest_code, $weight, $height, $width, $depth);

            // If there is a services cost available.
            if ($services_cost) {
                foreach ($services_cost as $service_reference => $service_cost) {
                    $service = ShipitServices::getByCode($service_reference);
                    if ($service) {
                        $carrier = Carrier::getCarrierByReference($service->id_reference);
                        if ($carrier) {
                            if ($service_cost) {
                                $cost = $service_cost;
                                if ($impact_price = Configuration::get('SHIPIT_IMPACT_PRICE')) {
                                    $impact_price_amount = Configuration::get('SHIPIT_IMPACT_PRICE_AMOUNT');

                                    switch ($impact_price) {
                                        case 1: // Increase percent.
                                            $cost += ($impact_price_amount / 100) * $cost;
                                            break;
                                        case 2: // Increase amount.
                                            $cost += $impact_price_amount;
                                            break;
                                        case 3: // Reduction percent.
                                            $cost -= ($impact_price_amount / 100) * $cost;
                                            break;
                                        case 4: // Reduction amount.
                                            $cost -= $impact_price_amount;
                                            break;
                                    }
                                }
                            } else {
                                $cost = false;

                            }
                           $cache->carriers[$carrier->id] = $cost;

                        }
                    }
                }
            }
        }
        return $cache;
    }
}
