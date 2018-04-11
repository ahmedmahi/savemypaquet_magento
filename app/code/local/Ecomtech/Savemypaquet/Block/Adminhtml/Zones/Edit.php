<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Zones_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId   = "id_zone";
        $this->_blockGroup = "savemypaquet";
        $this->_controller = "adminhtml_zones";
        $this->_updateButton("save", "label", Mage::helper("savemypaquet")->__("Save Item"));
        $this->_updateButton("delete", "label", Mage::helper("savemypaquet")->__("Delete Item"));

        $this->_addButton("saveandcontinue", array(
            "label"   => Mage::helper("savemypaquet")->__("Save And Continue Edit"),
            "onclick" => "saveAndContinueEdit()",
            "class"   => "save",
        ), -100);

        $this->_formScripts[] = "

                            function saveAndContinueEdit(){
                                editForm.submit($('edit_form').action+'back/edit/');
                            }
                        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry("zones_data") && Mage::registry("zones_data")->getId()) {
            return Mage::helper("savemypaquet")->__("Edit Item '%s'", $this->htmlEscape(Mage::registry("zones_data")->getId()));
        } else {
            return Mage::helper("savemypaquet")->__("Add Item");
        }
    }
}
