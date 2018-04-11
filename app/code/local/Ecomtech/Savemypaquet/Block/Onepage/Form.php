<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Onepage_Form extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getInfosQuote()
    {
        return $this->getQuote()->getShippingAddress();
    }

    public function validatePostCode()
    {
        $destination_country  = $this->getInfosQuote()->getCountryId();
        $destination_postcode = $this->getInfosQuote()->getPostcode();
        $destination_dep      = $this->getInfosQuote()->getRegionCode();
        if ($destination_country == 'FR' && (Mage::helper('savemypaquet')->get_dep_code_from_data($destination_postcode) != $destination_dep)) {
            return false;
        }
        return true;
    }
}
