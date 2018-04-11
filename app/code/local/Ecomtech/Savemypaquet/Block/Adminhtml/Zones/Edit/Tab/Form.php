<?php
/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Zones_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("savemypaquet_form", array("legend" => Mage::helper("savemypaquet")->__("Zone information")));

        $fieldset->addField("nom", "text", array(
            "label"    => Mage::helper("savemypaquet")->__("Nom"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "nom",
        ));

        $fieldset->addField('deps', 'multiselect', array(
            'label'    => Mage::helper('savemypaquet')->__('Département'),
            'values'   => Mage::helper('savemypaquet')->getDepartementsValues(),
            'name'     => 'villes',
            "class"    => "required-entry",
            "required" => true,
        ));

        if (Mage::getSingleton("adminhtml/session")->getZonesData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getZonesData());
            Mage::getSingleton("adminhtml/session")->setZonesData(null);
        } elseif (Mage::registry("zones_data")) {
            $form->setValues(Mage::registry("zones_data")->getData());
        }
        return parent::_prepareForm();
    }
}
