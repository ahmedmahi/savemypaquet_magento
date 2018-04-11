<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Action qui permet d'envoyer les demandes de pris en charge des nouveaux colis
     * @return
     */
    public function envoiAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {
            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                $is_smp_method = stristr($order->getShippingMethod(), 'savemypaquet_');

                $service                   = $order->getSmypSelectedService();
                $colis_numero_savemypaquet = $order->getSmypNumeroColis();

                if ($is_smp_method !== false && $service && !$order->getSmypNumeroColis()) {
                    $numero_colis = ($this->hasShipment($order)) ? ($this->hasShipment($order)) : $this->creatShipment($order);
                    $weights      = explode("-", $this->getRequest()->getPost('weight_' . $order_id));
                    $poids        = number_format($weights[1], 2, '.', '');
                    $coli         = array(
                        'date_de_commande'   => date('Y-m-d', strtotime($order->getCreatedAt())),
                        'numero_de_commande' => $order->getIncrementID(),
                        'numero_colis'       => $numero_colis,
                        'service'            => ((int) $service) - 1,
                        'nom_du_client'      => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                        'email_du_client'    => $order->getCustomerEmail(),
                        'tel_client'         => $order->getSmypClientTel(),
                        'poids'              => $poids,
                        'adresse1'           => $order->getShippingAddress()->getStreet(1),
                        'adresse2'           => $order->getShippingAddress()->getStreet(2),
                        'ville'              => $order->getShippingAddress()->getCity(),
                        'code_postal'        => $order->getShippingAddress()->getPostcode(),
                        'pays'               => ($order->getShippingAddress()->getCountryId() == 'FR') ? 'France' : $order->getShippingAddress()->getCountryId(),
                        'batiment'           => $order->getSmypClientBatiment(),
                        'etage'              => $order->getSmypClientEtage(),
                        'porte_position'     => $order->getSmypClientPortePosition(),
                        'porte_cote'         => (int)$order->getSmypClientPorteCote(),
                        'digicode_1'         => $order->getSmypClientDigicode(),
                        'digicode_2'         => $order->getSmypClientDigicode2(),

                    );
                    $result = $this->getHelper()->get_api()->colis->create($coli);
                    if (is_array($result) && $result['resultat'] == 'ok') {
                        $order->setSmypNumeroColis($result['numero_Save_MyPaquet']);
                        $order->setSmypCodeBarreColis($result['code_barre']);
                        $order->setSmypStatutColis($result['statut']);
                        $order->save();

                        $this->getHelper()->trackingColis($result['numero_Save_MyPaquet'], $order);
                    } else {
                        $this->_getSession()->addError($this->__('Commande ID :' . $order->getIncrementID() . ' ' . $result));
                    }
                } elseif ($colis_numero_savemypaquet) {
                    $this->getHelper()->trackingColis($colis_numero_savemypaquet, $order, true);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }
    protected function hasShipment($order)
    {
        $shipment            = $order->getShipmentsCollection()->getFirstItem();
        $shipmentIncrementId = $shipment->getIncrementId();
        return $shipmentIncrementId;
    }

    public function creatShipment($order)
    {
        $incrementId = $order->getIncrementID();

        $trackingTitle  = 'Savemypaquet';
        $sendEmail      = 1;
        $url            = str_replace('admin_savemypaquet', 'savemypaquet', Mage::getUrl('savemypaquet/index/tracer/', array('trackingnumber' => $incrementId)));
        $comment        = 'Cher client, vous pouvez suivre l\'acheminement de votre colis par Savemypaquet en cliquant sur le lien ci-contre : ' . '<a target="_blank" href="' . $url . '">Suivre ce colis </a>';
        $includeComment = 1;

        if (!$order->canShip()) {
            $this->_getSession()->addError($this->__('La commande %s ne peut pas être expédiée, ou a déjà été expédiée.', $order->getRealOrderId()));
            return 0;
        }

        $convertor = Mage::getModel('sales/convert_order');
        $shipment  = $convertor->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $item = $convertor->itemToShipmentItem($orderItem);
            $qty  = $orderItem->getQtyToShip();
            $item->setQty($qty);

            $shipment->addItem($item);
        } //foreach

        $shipment->register();
        $carrierCode = stristr($order->getShippingMethod(), '_', true);

        $track = Mage::getModel('sales/order_shipment_track')
            ->setNumber($incrementId)
            ->setCarrierCode($carrierCode)
            ->setTitle($trackingTitle)
            ->setUrl($url)
            ->setStatus('<a target="_blank" href="' . $url . '">' . __('Suivre ce colis') . '</a>');

        $shipment->addTrack($track);
        //$shipment->addComment($comment, $sendEmail && $includeComment);
        $shipment->getOrder()->setIsInProcess(true);

        if ($sendEmail) {
            $shipment->setEmailSent(true);
        }

        try {
            $shipment->save();

            // $shipment->sendEmail($sendEmail, ($includeComment ? $comment : ''));

            $shipment->getOrder()->addStatusHistoryComment($comment, $shipment->getOrder()->getStatus())
                ->setIsVisibleOnFront(1)
                ->setIsCustomerNotified($sendEmail && $includeComment);

            $shipment->getOrder()->save();
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__('Erreur pendant la création de l\'expédition %s : %s', $order->getId(), $e->getMessage()));
            return 0;
        }

        $shipmentId = $shipment->getIncrementId();

        if ($shipmentId != 0) {
            $this->_getSession()->addSuccess($this->__('Livraison %s créée pour la commande %s, statut mis à jour', $shipmentId, $incrementId));
        }
        return $shipmentId;
    }

    public function trackerAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            foreach ($orderIds as $order_id) {
                $order         = Mage::getModel('sales/order')->load($order_id);
                if ($colis_numero_savemypaquet = $this->getHelper()->treatedColis($order)) {
                    $this->getHelper()->trackingColis($colis_numero_savemypaquet, $order);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }
    public function etiquetteAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');
        if (!empty($orderIds)) {
            $pdfs=array();
            foreach ($orderIds as $order_id) {
                $order         = Mage::getModel('sales/order')->load($order_id);
                if ($colis_numero_savemypaquet = $this->getHelper()->treatedColis($order)) {
                    $result= $this->getHelper()->get_api()->etiquette->create($colis_numero_savemypaquet);
                    if (is_array($result) && $result['resultat'] == 'ok') {
                        $pdfs []= $this->getHelper()->get_api()->pdf->getPdf($result['url_etiquette']);
                    } else {
                        $this->getHelper()->updateStatutOrderAddRspenseMessage($result, $order);
                        $this->_redirect("*/*/");
                    }
                } else {
                    $this->_redirect("*/*/");
                }
            }
            if ($pdfs) {
                $contenu=$this->getHelper()->combinePdfs($pdfs)->render();
                $this->_prepareDownloadResponse($this->getHelper()->generate_token().'.pdf', $contenu, 'application/pdf');
            } else {
                $this->_redirect("*/*/");
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }

    public function deliveredAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {
            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                if ($colis_numero_savemypaquet = $this->getHelper()->treatedColis($order)) {
                    $data  = array('smyp_statut_colis' => '135');
                    $track = $this->getHelper()->get_api()->tracking->track($colis_numero_savemypaquet);
                    if ($track['statut'] == '130') {
                        $this->getHelper()->updateStatutOrderAddRspenseMessage(array('resultat' => 'ok'), $order, 'Commande #' . $order->getIncrementID() . ' ' . $this->__('Delivered order status was updated'), $data);
                    } else {
                        $order->setData('smyp_statut_colis', $track['statut']);
                        $order->save();
                        $this->getHelper()->updateStatutOrderAddRspenseMessage($this->__(' n\'est pas encore livré dans le système Save My Paquet'), $order);
                    }
                }
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }
    public function cancelAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids');

        if (!empty($orderIds)) {
            foreach ($orderIds as $order_id) {
                $order = Mage::getModel('sales/order')->load($order_id);

                if ($colis_numero_savemypaquet = $this->getHelper()->treatedColis($order)) {
                    $result     = $this->getHelper()->get_api()->colis->cancel($colis_numero_savemypaquet);
                    $post_metas = array('smyp_statut_colis' => '140');
                    $this->getHelper()->updateStatutOrderAddRspenseMessage($result, $order, $this->__('Package well removed'), $post_metas);
                }
            }
        } else {
            $this->_getSession()->addError($this->__('No Order has been selected'));
        }
        $this->_redirect("*/*/");
    }

    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('savemypaquet/savemypaquetorder')
            ->_addContent($this->getLayout()->createBlock('savemypaquet/adminhtml_orders'))
            ->renderLayout();
    }
    public function getHelper()
    {
        return Mage::helper('savemypaquet');
    }
}
