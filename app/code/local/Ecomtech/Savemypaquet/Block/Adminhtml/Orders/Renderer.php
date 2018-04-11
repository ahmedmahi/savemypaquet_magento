<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Orders_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value  = $row->getData($this->getColumn()->getIndex());
        $helper = Mage::helper('savemypaquet');
        if ($this->getColumn()->getIndex() == 'smyp_selected_service') {
            return '<span style="padding: 2px 3px;font-size:11px;color:#fff;background-color:' . $helper->getServiceColor($value) . ';font-weight: bold;">' . $helper->getServiceValue($value) . '</span>';
        }
        if ($this->getColumn()->getIndex() == 'smyp_statut_colis') {
            if ($value == '-') {
                return '<span style="color:#d51f4f;font-weight: bold;">' . $helper->__('Non traité') . '</span>';
            } else {
                $statuts = array_flip($helper->getStatuts());
                return '<span style="padding: 2px 3px;font-size:11px;color:#fff;background-color:' . $helper->getStatutColor($value) . ';font-weight: bold;">' . $helper->getStatut($value) . '</span>';
            }
        }
        return $value;
    }
}
