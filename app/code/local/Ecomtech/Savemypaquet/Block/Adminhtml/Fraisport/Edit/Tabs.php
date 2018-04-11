<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("fraisport_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle(Mage::helper("savemypaquet")->__(""));
    }
    protected function _beforeToHtml()
    {
        $this->addTab("form_section", array(
            "label"   => Mage::helper("savemypaquet")->__("Infos grille"),
            "title"   => Mage::helper("savemypaquet")->__("Infos grille"),
            "content" => $this->getLayout()->createBlock("savemypaquet/adminhtml_fraisport_edit_tab_form")->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}
