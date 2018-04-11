<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller     = "adminhtml_fraisport";
        $this->_blockGroup     = "savemypaquet";
        $this->_headerText     = Mage::helper("savemypaquet")->__("Shipping costs Manager");
        $this->_addButtonLabel = Mage::helper("savemypaquet")->__("Ajouter une grille de frais de port");
        parent::__construct();
    }
}
