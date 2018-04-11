<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Adminhtml_ServicesController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        //return Mage::getSingleton('admin/session')->isAllowed('savemypaquet/services');
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("savemypaquet/services")->_addBreadcrumb(Mage::helper("adminhtml")->__("Services  Manager"), Mage::helper("adminhtml")->__("Services Manager"));
        return $this;
    }
    public function indexAction()
    {
        $this->_title($this->__("Savemypaquet"));
        $this->_title($this->__("Types de livraison"));

        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction()
    {
        $this->_title($this->__("Savemypaquet"));
        $this->_title($this->__("Services"));
        $this->_title($this->__("Edit Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("savemypaquet/services")->load($id);
        if ($model->getId()) {
            Mage::register("services_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("savemypaquet/services");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Types de livraison"), Mage::helper("adminhtml")->__("Types de livraison"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("savemypaquet/adminhtml_services_edit"))->_addLeft($this->getLayout()->createBlock("savemypaquet/adminhtml_services_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("savemypaquet")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction()
    {
        $this->_title($this->__("Savemypaquet"));
        $this->_title($this->__("Services"));
        $this->_title($this->__("New Item"));

        $id    = $this->getRequest()->getParam("id");
        $model = Mage::getModel("savemypaquet/services")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("services_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("savemypaquet/services");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Services Manager"), Mage::helper("adminhtml")->__("Services Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Services Description"), Mage::helper("adminhtml")->__("Services Description"));

        $this->_addContent($this->getLayout()->createBlock("savemypaquet/adminhtml_services_edit"))->_addLeft($this->getLayout()->createBlock("savemypaquet/adminhtml_services_edit_tabs"));

        $this->renderLayout();
    }
    public function saveAction()
    {
        $post_data = $this->getRequest()->getPost();

        if ($post_data) {
            try {
                $model = Mage::getModel("savemypaquet/services")
                    ->addData($post_data)
                    ->setId($this->getRequest()->getParam("id"))
                    ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Services was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setServicesData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setServicesData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $model = Mage::getModel("savemypaquet/services");
                $model->setId($this->getRequest()->getParam("id"))->delete();
                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }

    public function massRemoveAction()
    {
        try {
            $ids = $this->getRequest()->getPost('id_services', array());
            foreach ($ids as $id) {
                $model = Mage::getModel("savemypaquet/services");
                $model->setId($id)->delete();
            }
            Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item(s) was successfully removed"));
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
