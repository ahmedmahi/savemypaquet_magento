<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_IndexController extends Mage_Core_Controller_Front_Action
{
    public function formAction()
    {
        try {
            $option     = $this->getRequest()->getPost("option");
            $formsinfos = $this->getRequest()->getPost('smp');

            if ($option) {
                $service     = str_replace('savemypaquet_', '', $option);
                $codeservice = Mage::helper('savemypaquet')->getServiceCode($service);
                Mage::helper('savemypaquet')->affectInfosOnSession(array('smyp_selected_service' => $codeservice));

                $block = $this->getLayout()->createBlock('savemypaquet/onepage_form')->setTemplate('savemypaquet/onepage/form.phtml');
                echo $block->toHtml();
            } elseif ($formsinfos) {
                $val = Mage::helper('savemypaquet')->validate($formsinfos);
                if (isset($val['errors'])) {
                    $err = implode("<br>", $val['errors']);
                    echo '<div class="err">' . $err . '</div>';
                } elseif (isset($val['values'])) {
                    $message = 'Informations supplémentaire validées  avec succès';
                    Mage::helper('savemypaquet')->affectInfosOnSession($val['values']);
                    echo '<div class="success">' . $message . '</div>';
                }
            }
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function tracerAction()
    {
        $this->loadLayout();

        $this->renderLayout();
    }

    public function getSession()
    {
        return Mage::getSingleton('core/session');
    }
}
