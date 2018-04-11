<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Observer
{
    public function saveInfos($observer)
    {
        /* $request = $observer->getEvent()->getRequest();
    $quote   = $observer->getEvent()->getQuote();

    $etage = $request->getPost('etage');
    if (isset($etage) && !empty($etage)) {
    Mage::log($etage);
    }

    $result = array('error' => 1, 'message' => Mage::helper('savemypaquet')->__('Invalid shipping method.'));
    $request->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));*/
    }
    public function saveInfosSmp($observer)
    {

        /* $responseBody = $observer->getEvent()->getControllerAction()
        ->getResponse()->getBody();
        $responseBody = json_decode((string) $responseBody);
        if ($responseBody->goto_section == 'payment') {

        $responseBody->update_section->html = $responseBody->update_section->html . $js->getText();
        }
        $responseBody = json_encode($responseBody);
        $observer->getEvent()->getControllerAction()
        ->getResponse()->setBody($responseBody);*/
        $request = $observer->getEvent()->getControllerAction()->getRequest();
        $service = stristr($request->getPost('shipping_method'), 'savemypaquet_');
        if ($service !== false) {
            $service = str_replace('savemypaquet_', '', $service);

            $val = Mage::helper('savemypaquet')->validate($request->getPost('smp'));

            if (isset($val['errors'])) {
                $result = array('error' => 1, 'message' => implode(" / ", $val['errors']));
                $observer->getEvent()->getControllerAction()->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            } elseif (isset($val['values'])) {
                $val['values']['smyp_selected_service'] = Mage::helper('savemypaquet')->getServiceCode($service);
                $val['values']['smyp_active']           = true;
                Mage::helper('savemypaquet')->affectInfosOnSession($val['values']);
            }
        } else {
            Mage::helper('savemypaquet')->affectInfosOnSession(array('smyp_active' => false));
        }
    }

    public function saveSelectedInfos($observer)
    {
        $order   = $observer->getOrder();
        $session = Mage::getSingleton('core/session');
        if ($session->getSmypActive()) {
            $order = Mage::helper('savemypaquet')->affectInfosOnOrder($order);
            $order->save();
        }
    }

    public function addSavemypaquetElements($observer)
    {
        $_block = $observer->getBlock();

        $_type = $_block->getType();

        try {
            if ($_type == 'checkout/onepage') {
                $_child = clone $_block;

                $_child->setType('savemypaquet/onepage');

                $_block->setChild('oldonepage', $_child);

                $_block->setTemplate('savemypaquet/onepage.phtml');
            } elseif ($_type == 'checkout/onepage_shipping_method') {
                $_child = clone $_block;

                $_child->setType('savemypaquet/onepage_shippingmethod');

                $_block->setChild('oldshippingmethod', $_child);

                $_block->setTemplate('savemypaquet/onepage/shippingmethod.phtml');
            } elseif ($_type == 'checkout/onepage_shipping_method_available') {
                $_child = clone $_block;

                $_child->setType('savemypaquet/onepage_shippingmethod');

                $_block->setChild('oldavailable', $_child);

                $_block->setTemplate('savemypaquet/onepage/available.phtml');
            } elseif ($_type == 'adminhtml/sales_order_view_info') {
                $_child = clone $_block;

                $_child->setType('savemypaquet/adminhtml_orderviewinfo');

                $_block->setChild('oldorderviewinfo', $_child);

                $_block->setTemplate('savemypaquet/orderviewinfo.phtml');
            }
        } catch (Exception $e) {
            Mage::log($e);
        }
    }
}
