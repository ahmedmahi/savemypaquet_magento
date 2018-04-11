<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Config_Source_Paymentmethods
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();

        $methods = array(array('value' => '0', 'label' => Mage::helper('adminhtml')->__('--Please Select--')));

        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle          = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode,
            );
        }

        return $methods;
    }
}
