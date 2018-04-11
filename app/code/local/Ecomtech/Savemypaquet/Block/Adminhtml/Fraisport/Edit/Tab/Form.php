<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("savemypaquet_form", array("legend" => Mage::helper("savemypaquet")->__("Infos grille")));

        $fieldset->addField('id_zone', 'select', array(
            'label'    => Mage::helper('savemypaquet')->__('Zone'),
            'values'   => Mage::helper('savemypaquet')->getZonesValues(),
            'name'     => 'id_zone',
            "class"    => "required-entry",
            "required" => true,
        ));
        $fieldset->addField('id_service', 'select', array(
            'label'    => Mage::helper('savemypaquet')->__('Type de livraison'),
            'values'   => Mage::helper('savemypaquet')->getServicesValues(),
            'name'     => 'id_service',
            "class"    => "required-entry",
            "required" => true,
        ));
        $fieldset->addField('condition', 'select', array(
            'label'    => Mage::helper('savemypaquet')->__('Condition'),
            'values'   => Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Grid::getValueCondition(),
            'name'     => 'condition',
            "class"    => "required-entry",
            "required" => true,
        ));
        $fieldset->addField("min", "text", array(
            "label"    => Mage::helper("savemypaquet")->__("Min"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "min",
        ));

        $fieldset->addField("max", "text", array(
            "label"    => Mage::helper("savemypaquet")->__("Max"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "max",
        ));

        $fieldset->addField("cout", "text", array(
            "label"    => Mage::helper("savemypaquet")->__("Coût"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "cout",
        ));

        if (Mage::getSingleton("adminhtml/session")->getFraisportData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getFraisportData());
            Mage::getSingleton("adminhtml/session")->setFraisportData(null);
        } elseif (Mage::registry("fraisport_data")) {
            $form->setValues(Mage::registry("fraisport_data")->getData());
        }
        return parent::_prepareForm();
    }
}
