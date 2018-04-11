<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Carrier_Savemypaquet extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'savemypaquet';

    /**
     * Collect rates for this shipping method based on information in $request
     *
     * @param Mage_Shipping_Model_Rate_Request $data
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        $a_zone = Mage::helper('savemypaquet')->get_available_zones($request);

        if (!$this->getConfigData('enabled') || $request->getDestCountryId() != 'FR' || empty($a_zone)) {
            return false;
        }

        $result   = Mage::getModel('shipping/rate_result');
        $services = Mage::getModel('savemypaquet/services')->getCollection();
        foreach ($services as $service) {
            if ($service->getActive()) {
                $method = Mage::getModel('shipping/rate_result_method');
                $method->setCarrier($this->_code);
                $method->setCarrierTitle('Save My Paquet ' /*. $service->getNom()*/);
                $method->setMethod($service->getCodeService());
                $method->setMethodTitle($service->getTitre());

                switch ($service->getPriceType()) {
                    case '0':
                        $price = 0;
                        break;
                    case '1':
                        $price = $service->getPrice();
                        break;
                    case '2':
                        $quote = Mage::getModel('checkout/session')->getQuote();
                        $price = ($service->getPrice() * $quote->getSubtotal()) / 100;
                        break;
                    case '3':
                        $price = $this->calculateShippingCost($request, $service->getIdService());
                        break;
                    default:
                        break;
                }
                if ($service->getPriceType() == '3' && $price === false) {
                    continue;
                }
                $method->setPrice($price);
                $method->setCost($price);
                $result->append($method);
            }
        }

        return $result;
    }

    public function calculateShippingCost($package = array(), $id_service = '')
    {
        try {
            $cost = false;

            $available_table_rates = Mage::helper('savemypaquet')->get_available_table_rates($package);
            $table_rate            = Mage::helper('savemypaquet')->pick_cheapest_table_rate($available_table_rates, $id_service);

            if ($table_rate != false) {
                $cost = $table_rate['cout'];
            }

            return $cost;
        } catch (Exception $e) {
            $this->log->lwrite_and_lclose(($e->getMessage()));
        }
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('name'));
    }
}
