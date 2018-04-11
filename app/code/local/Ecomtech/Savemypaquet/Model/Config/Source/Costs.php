<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Config_Source_Costs
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(

            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Use of Savemypaquet rates (via API) + supplement')),
            array('value' => 2, 'label' => Mage::helper('adminhtml')->__('Specify shipping costs')),
        );
    }
}
