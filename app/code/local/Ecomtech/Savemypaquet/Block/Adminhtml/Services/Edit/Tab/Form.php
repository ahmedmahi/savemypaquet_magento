<?php
/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Services_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset("savemypaquet_form", array("legend" => Mage::helper("savemypaquet")->__("Infos type de livraison")));

        $fieldset->addField('active', 'select', array(
            'name'    => 'active',
            'label'   => Mage::helper('adminhtml')->__('Status'),
            'id'      => 'active',
            'title'   => Mage::helper('adminhtml')->__('Status'),
            'class'   => 'input-select',
            'style'   => 'width: 80px',
            'options' => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
        ));
        $fieldset->addField("nom", "text", array(
            "label"    => Mage::helper("savemypaquet")->__("Nom"),
            "class"    => "required-entry",
            "required" => true,
            "name"     => "nom",
            'disabled' => true,

        ));

        $fieldset->addField('titre', 'text', array(
            'label'    => Mage::helper('savemypaquet')->__('Titre'),
            'name'     => 'titre',
            "class"    => "required-entry",
            "required" => true,
        ));

        $fieldset->addField('price_type', 'select', array(
            'label'              => Mage::helper('savemypaquet')->__('Type de prix'),
            'values'             => Mage::helper('savemypaquet')->getPriceTypeValues(),
            'name'               => 'price_type',
            "class"              => "required-entry",
            "required"           => true,
            'after_element_html' => '<small><p>Le type de livraison sera accesible dans la géstion de frais <p>de livraison uniquement si le "Type de prix" est: <p> - "Variable par palier" </small>',
        ));

        $fieldset->addField('price', 'text', array(
            'label'              => Mage::helper('savemypaquet')->__('Prix'),
            'name'               => 'price',
            "required"           => false,
            'after_element_html' => '<small><p>A definir uniquement si le "Type de prix" est: <p> - "Prix fixe" <p>
            -"Variable proportionnellement au montant de la commande"  (en %)</small>',
        ));

        if (Mage::getSingleton("adminhtml/session")->getServicesData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getServicesData());
            Mage::getSingleton("adminhtml/session")->setServicesData(null);
        } elseif (Mage::registry("services_data")) {
            $form->setValues(Mage::registry("services_data")->getData());
        }
        return parent::_prepareForm();
    }
}
