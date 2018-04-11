<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Tracer extends Mage_Core_Block_Template
{
    public function getTrace()
    {
        $trakcmessage = '';
        $helper=Mage::helper('savemypaquet');
        try {
            $incrementId = $this->getRequest()->getParam("trackingnumber");
            $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);

            if ($colis_numero_savemypaquet = $helper->treatedColis($order)) {
                $trakcmessage=$helper->trackingColis($colis_numero_savemypaquet, $order, false, true);
            }
        } catch (Exception $e) {
            echo $e;
        }
        return $trakcmessage;
    }
}
