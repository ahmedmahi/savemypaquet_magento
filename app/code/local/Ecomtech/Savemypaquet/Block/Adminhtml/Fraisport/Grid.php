<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId("fraisportGrid");
        $this->setDefaultSort("id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel("savemypaquet/fraisport")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    protected function _prepareColumns()
    {
        $this->addColumn("id", array(
            "header" => Mage::helper("savemypaquet")->__("ID"),
            "align"  => "right",
            "width"  => "50px",
            "type"   => "number",
            "index"  => "id",
        ));

        $this->addColumn('id_zone', array(
            'header'  => Mage::helper('savemypaquet')->__('Zone'),
            'index'   => 'id_zone',
            'type'    => 'options',
            'options' => Mage::helper('savemypaquet')->getZonesOptions(),
        ));
        $this->addColumn('id_service', array(
            'header'  => Mage::helper('savemypaquet')->__('Type de livraison '),
            'index'   => 'id_service',
            'type'    => 'options',
            'options' => Mage::helper('savemypaquet')->getServicesOptions(),
        ));
        $this->addColumn('condition', array(
            'header'  => Mage::helper('savemypaquet')->__('Condition'),
            'index'   => 'condition',
            'type'    => 'options',
            'options' => Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Grid::getOptionCondition(),
        ));

        $this->addColumn("min", array(
            "header" => Mage::helper("savemypaquet")->__("Min"),
            "index"  => "min",
        ));
        $this->addColumn("max", array(
            "header" => Mage::helper("savemypaquet")->__("Max"),
            "index"  => "max",
        ));
        $this->addColumn("cout", array(
            "header" => Mage::helper("savemypaquet")->__("Coût"),
            "index"  => "cout",
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl("*/*/edit", array("id" => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem('remove_fraisport', array(
            'label'   => Mage::helper('savemypaquet')->__('Remove shipping costs'),
            'url'     => $this->getUrl('*/adminhtml_fraisport/massRemove'),
            'confirm' => Mage::helper('savemypaquet')->__('Are you sure?'),
        ));
        return $this;
    }

    public static function getOptionArray2()
    {
        $data_array    = array();
        $data_array[0] = 'Zone1';
        $data_array[1] = 'Zone2';
        $data_array[2] = 'Zone3';
        return ($data_array);
    }

    public static function getOptionCondition()
    {
        $data_array    = array();
        $data_array[0] = 'Poids (Kg)';
        $data_array[1] = 'Total (€)';
        return ($data_array);
    }
    public static function getValueCondition()
    {
        $data_array = array();
        foreach (Ecomtech_Savemypaquet_Block_Adminhtml_Fraisport_Grid::getOptionCondition() as $k => $v) {
            $data_array[] = array('value' => $k, 'label' => $v);
        }
        return ($data_array);
    }
}
