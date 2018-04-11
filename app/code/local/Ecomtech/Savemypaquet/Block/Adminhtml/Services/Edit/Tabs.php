<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Services_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("services_tabs");
        $this->setDestElementId("edit_form");
        //$this->setTitle(Mage::helper("savemypaquet")->__("Item Information"));
    }
    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
            "label"   => Mage::helper("savemypaquet")->__("Infos type de livraison"),
            "title"   => Mage::helper("savemypaquet")->__("Infos type de livraison"),
            "content" => $this->getLayout()->createBlock("savemypaquet/adminhtml_services_edit_tab_form")->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
