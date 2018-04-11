<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('savemypaquet_adminhtml_order_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $collection = Mage::getResourceModel('sales/order_grid_collection')
                ->join('order', "main_table.entity_id = order.entity_id AND order.shipping_method like 'savemypaquet_%' AND order.status != 'complete' AND order.status != 'canceled' AND order.status != 'holded' AND order.status != 'closed' AND order.smyp_statut_colis != '140' AND order.smyp_statut_colis != '135'", array('weight', 'shipping_method', 'smyp_selected_service', 'smyp_statut_colis'))
                ->join('order_address', "main_table.entity_id = order_address.parent_id AND order_address.address_type = 'shipping'", array('postcode as shipping_postcode', 'city as shipping_city', 'company as shipping_company', 'street as shipping_street', 'country_id as shipping_country_id'));
        } else {
            $collection = Mage::getResourceModel('sales/order_collection')
                ->addAttributeToFilter('shipping_method', array('like' => 'savemypaquet_%'))
                ->addAttributeToFilter('status', array('neq' => 'holded'))
                ->addAttributeToFilter('status', array('neq' => 'complete'))
                ->addAttributeToFilter('status', array('neq' => 'canceled'))
                ->addAttributeToFilter('status', array('neq' => 'closed'))
                ->addAttributeToFilter('smyp_statut_colis', array('neq' => '140'))
                ->addAttributeToFilter('smyp_statut_colis', array('neq' => '135'))
                ->addAttributeToSelect(array('status', 'shipping_method', 'weight', 'smyp_selected_service', 'smyp_statut_colis'))
                ->joinAttribute('shipping_company', 'order_address/company', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_street', 'order_address/street', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_city', 'order_address/city', 'shipping_address_id', null, 'left')
                ->joinAttribute('shipping_country_id', 'order_address/country_id', 'shipping_address_id', null, 'left')

                ->addExpressionAttributeToSelect(
                    'shipping_name',
                    'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                    array('shipping_firstname', 'shipping_lastname')
                );
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $columnData = array(
            'header' => Mage::helper('sales')->__('Order #'),
            'width'  => '70px',
            'type'   => 'text',
            'index'  => 'increment_id',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.' . $columnData['index'];
        }
        $this->addColumn('real_order_id', $columnData);

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                    'header'          => Mage::helper('sales')->__('Store'),
                    'index'           => 'store_id',
                    'type'            => 'store',
                    'store_view'      => true,
                    'display_deleted' => true,
                    'width'           => '80px',
                )
            );
        }

        $columnData = array(
            'header' => Mage::helper('sales')->__('Purchased On'),
            'index'  => 'created_at',
            'type'   => 'datetime',
            'width'  => '120px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.' . $columnData['index'];
        }
        $this->addColumn('created_at', $columnData);

        $this->addColumn(
            'shipping_name',
            array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index'  => 'shipping_name',
                'width'  => '150px',
            )
        );

        $columnData = array(
            'header'   => 'Type de livraison',
            'index'    => 'smyp_selected_service',
            'type'     => 'options',
            'options'  => Mage::helper('savemypaquet')->getServicesOptions(),
            'width'    => '225px',
            'align'    => 'center',
            'renderer' => 'Ecomtech_Savemypaquet_Block_Adminhtml_Orders_Renderer',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'order.' . $columnData['index'];
        }
        $this->addColumn('smyp_selected_service', $columnData);

        $columnData = array(
            'header'   => 'Savemypaquet status',
            'index'    => 'smyp_statut_colis',
            'type'     => 'options',
            'options'  => Mage::helper('savemypaquet')->getStatuts(),
            'width'    => '125px',
            'align'    => 'center',
            'renderer' => 'Ecomtech_Savemypaquet_Block_Adminhtml_Orders_Renderer',

        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'order.' . $columnData['index'];
        }
        $this->addColumn('smyp_statut_colis', $columnData);

        $columnData = array(
            'header' => Mage::helper('sales')->__('Code postal'),
            'index'  => 'shipping_postcode',
            'width'  => '40px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'order_address.postcode';
        }
        $this->addColumn('shipping_postcode', $columnData);

        $columnData = array(
            'header' => Mage::helper('sales')->__('City'),
            'index'  => 'shipping_city',
            'width'  => '160px',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'order_address.city';
        }
        $this->addColumn('shipping_city', $columnData);

        $this->addColumn(
            'weight',
            array(
                'header' => Mage::helper('sales')->__('Weight'),
                'index'  => 'weight',
                'type'   => 'input',
                'width'  => '40px',
            )
        );

        $columnData = array(
            'header'   => Mage::helper('sales')->__('G.T. (Base)'),
            'index'    => 'base_grand_total',
            'type'     => 'currency',
            'currency' => 'base_currency_code',
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.' . $columnData['index'];
        }
        $this->addColumn('base_grand_total', $columnData);

        $columnData = array(
            'header'  => Mage::helper('sales')->__('Status'),
            'index'   => 'status',
            'width'   => '160px',
            'type'    => 'options',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
        );
        if (version_compare(Mage::getVersion(), '1.4.1', '>=')) {
            $columnData['filter_index'] = 'main_table.' . $columnData['index'];
        }
        $this->addColumn('status', $columnData);

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $this->addColumn(
                'action',
                array(
                    'header'    => Mage::helper('sales')->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'actions'   => array(
                        array(
                            'caption' => Mage::helper('sales')->__('View'),
                            'url'     => array('base' => 'adminhtml/sales_order/view'),
                            'field'   => 'order_id',
                        ),
                    ),
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'stores',
                    'is_system' => true,
                )
            );
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('order_ids');
        if (Mage::getVersion() >= '1.4.1') {
            $this->getMassactionBlock()->setUseSelectAll(false);
        }

        $this->getMassactionBlock()->addItem(
            'requests_order',
            array(
                'label' => Mage::helper('savemypaquet')->__('Injection of treatment requests'),
                'url'   => $this->getUrl('*/adminhtml_orders/envoi'),
            )
        );
        $this->getMassactionBlock()->addItem(
            'etiquette_create',
            array(
                'label' => Mage::helper('savemypaquet')->__('Print labels'),
                'url'   => $this->getUrl('*/adminhtml_orders/etiquette'),
            )
        );

        $this->getMassactionBlock()->addItem(
            'tracking_order',
            array(
                'label' => Mage::helper('savemypaquet')->__('Parcels trace'),
                'url'   => $this->getUrl('*/adminhtml_orders/tracker'),
            )
        );
        $this->getMassactionBlock()->addItem(
            'cancel_order',
            array(
                'label' => Mage::helper('savemypaquet')->__('Cancel of treatment requests'),
                'url'   => $this->getUrl('*/adminhtml_orders/cancel'),
            )
        );

        $this->getMassactionBlock()->addItem(
                    'delivered_order',
                    array(
                        'label' => Mage::helper('savemypaquet')->__('Update delivered orders'),
                        'url'   => $this->getUrl('*/adminhtml_orders/delivered'),
                    )
                );
        return $this;
    }

    public function getRowUrl($row)
    {
        return false;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }
}
