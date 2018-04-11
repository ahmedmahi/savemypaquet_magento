<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Services_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("servicesGrid");
        $this->setDefaultSort("id_service");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("savemypaquet/services")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn("id_service", array(
            "header" => Mage::helper("savemypaquet")->__("ID"),
            "align"  => "right",
            "width"  => "50px",
            "type"   => "number",
            "index"  => "id_service",
        ));

        $this->addColumn("nom", array(
            "header" => Mage::helper("savemypaquet")->__("Nom"),
            "index"  => "nom",
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        //  $this->setMassactionIdField('id_service');
        //   $this->getMassactionBlock()->setFormFieldName('id_services');
        //   $this->getMassactionBlock()->setUseSelectAll(true);
        //  $this->getMassactionBlock()->addItem('remove_services', array(
        //      'label'   => Mage::helper('savemypaquet')->__('Remove Services'),
        //      'url'     => $this->getUrl('*/adminhtml_services/massRemove'),
        //      'confirm' => Mage::helper('savemypaquet')->__('Are you sure?'),
        //  ));
        return $this;
    }
}
