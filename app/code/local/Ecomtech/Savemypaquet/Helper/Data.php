<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * retourn une instance du class principale de communication avec l'api Save My Paquet
     * @return Ecomtech_Savemypaquet_Model_Api
     */
    public function get_api()
    {
        $options = array(
            'validate_url' => false,
            'timeout'      => 30,
            'ssl_verify'   => false,
        );

        $arg = array(
            'store_url' => Mage::getStoreConfig('carriers/savemypaquet/savemypaquet_url'),
            'api_login' => Mage::getStoreConfig('carriers/savemypaquet/api_login'),
            'password'  => Mage::getStoreConfig('carriers/savemypaquet/savemypaquet_password'),
            'options'   => $options,
        );

        return Mage::getModel('savemypaquet/api', $arg);
    }
    /**
     * Validation des champs obligatoirs
     * @param  array $inputs
     * @return boolean
     */
    public function validate($inputs)
    {
        $errors = array();
        $values = array();
        $helper = Mage::helper('savemypaquet');

        $values['smyp_client_email']          = $this->isset_index('email', $inputs);
        $values['smyp_client_tel']            = $this->isset_index('tel', $inputs);
        $values['smyp_client_batiment']       = $this->isset_index('batiment', $inputs);
        $values['smyp_client_etage']          = $this->isset_index('etage', $inputs);
        $values['smyp_client_porte_position'] = $this->isset_index('porte_position', $inputs);
        $values['smyp_client_porte_cote']     = $this->isset_index('porte_cote', $inputs);
        $values['smyp_client_digicode']       = $this->isset_index('digicode', $inputs);
        $values['smyp_client_digicode2']      = $this->isset_index('digicode2', $inputs);
        $values['smyp_client_interphone']     = $this->isset_index('interphone', $inputs);

        if ($values['smyp_client_email'] === false) {
            $errors[] = $helper->__('Le champ email est obligatoir');
        }
        if (!Zend_Validate::is($inputs['email'], 'EmailAddress')) {
            $errors[] = $helper->__('Veuillez saisir une adresse email valide');
        }
        if ($values['smyp_client_tel'] === false) {
            $errors[] = $helper->__('Le numéro de téléphone est obligatoir');
        }
        if (!$this->valideTel($values['smyp_client_tel'])) {
            $errors[] = $helper->__('Veuillez saisir un numéro de téléphone mobile en France valide');
            $errors[] = $helper->__('de type : 00336xxxx, +336xxxx, +337xxx, 06xxxx, 07xxxx');
        }
        if ($values['smyp_client_etage'] === false) {
            $errors[] = $helper->__('Le champ étage est obligatoir');
        }
        if ($values['smyp_client_porte_position'] === false) {
            $errors[] = $helper->__('Veuillez selectioner la position du porte');
        }
        if ($values['smyp_client_porte_position'] == 'autre' && $this->isset_index('porte_position_autre', $inputs) === false) {
            $errors[] = $helper->__('Veuillez saisir la position du porte');
        }
        if ($values['smyp_client_porte_cote'] === false) {
            $errors[] = $helper->__('Veuillez selectioner le coté du porte');
        }
        if ($values['smyp_client_digicode'] == '1' && $this->isset_index('digicode_value', $inputs) === false) {
            $errors[] = $helper->__('Veuillez saisir le digicode');
        }
        if ($values['smyp_client_digicode2'] == '1' && $this->isset_index('digicode2_value', $inputs) === false) {
            $errors[] = $helper->__('Veuillez saisir le digicode 2');
        }
        if ($values['smyp_client_interphone'] === false) {
            $errors[] = $helper->__('Veuillez selectioner l\'interphone');
        }

        if (empty($errors)) {
            if ($values['smyp_client_porte_position'] == 'autre') {
                $values['smyp_client_porte_position'] = $this->isset_index('porte_position_autre', $inputs);
            }
            if ($values['smyp_client_digicode'] == '1') {
                $values['smyp_client_digicode'] = $this->isset_index('digicode_value', $inputs);
            }
            if ($values['smyp_client_digicode2'] == '1') {
                $values['smyp_client_digicode2'] = $this->isset_index('digicode2_value', $inputs);
            }
            if ($values['smyp_client_interphone'] == '1' && $this->isset_index('interphone_value', $inputs)) {
                $values['smyp_client_interphone'] = $this->isset_index('interphone_value', $inputs);
            }

            return array('values' => $values);
        }
        return array('errors' => $errors);
    }
    /**
     * Validation des champs (indexs) dans le tableau
     * @param  String $_index
     * @param  array $_array
     * @return String/booléen  valuer correspond ou false
     */
    public function isset_index($_index, $_array)
    {
        return (isset($_array[$_index]) && $_array[$_index] != '') ? $_array[$_index] : false;
    }
    /**
     * Enregistrer les informations suplementaires de save my paquet dans la session
     * @param  array $infos
     * @return
     */
    public function affectInfosOnSession($infos)
    {
        $session = Mage::getSingleton('core/session');
        foreach ($infos as $key => $value) {
            $session->setData($key, $value);
        }
    }
    /**
     * Enregistrer les information déja enrigistré dans la session dans la commande en cours
     * @param   $order
     * @return $order
     */
    public function affectInfosOnOrder($order)
    {
        $session = Mage::getSingleton('core/session');
        foreach ($this->getInfosSuppTab() as $info) {
            if ($value = $session->getData($info)) {
                $order->setData($info, $value);
            }
        }
        $order->setData('smyp_statut_colis', '-');

        return $order;
    }

    /**
     * List des zones enregistré dans la BD
     * @return array
     */
    public function getZonesOptions()
    {
        $zones   = Mage::getModel('savemypaquet/zones')->getCollection();
        $options = array();
        foreach ($zones as $zone) {
            $id           = $zone->getId();
            $options[$id] = $zone->getNom();
        }
        return $options;
    }
    /**
     * List des services disponible
     * @return array
     */
    public function getServicesOptions($u_dispo = false)
    {
        $services = Mage::getModel('savemypaquet/services')->getCollection();
        $options  = array();
        foreach ($services as $service) {
            $id = $service->getId();
            if ($u_dispo) {
                if ($service->getPriceType() == '3') {
                    $options[$id] = $service->getNom();
                }
            } else {
                $options[$id] = $service->getNom();
            }
        }
        return $options;
    }

    public function getZonesValues()
    {
        $values = array();
        foreach ($this->getZonesOptions() as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;
    }

    public function getServicesValues()
    {
        $values = array();
        foreach ($this->getServicesOptions(true) as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;
    }

    public function getPriceTypeValues()
    {
        $values = array();
        foreach ($this->getPriceTypeOptions() as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;
    }

    public function getDepartementsValues()
    {
        $values = array();
        foreach ($this->savemypaquet_get_departements() as $value => $label) {
            $values[] = array('value' => $value, 'label' => $label);
        }
        return $values;
    }

    public function get_available_table_rates($request)
    {
        $available_zones       = $this->get_available_zones($request);
        $available_table_rates = array();
        $table_rates           = Mage::getModel('savemypaquet/fraisport')->getCollection();

        foreach ($table_rates as $table_rate) {

            // Is table_rate for an available zone?
            $zone_pass = (in_array($table_rate->getIdZone(), $available_zones));

            // Is table_rate valid for basket weight?
            if ($table_rate->getCondition() == 0) {
                $weight      = $this->cart_contents_weight();
                $weight_pass = (($weight >= $table_rate->getMin()) && ($this->is_less_than($weight, $table_rate->getMax())));
            } else {
                $weight_pass = true;
            }

            // Is table_rate valid for basket total?
            if ($table_rate->getCondition() == 1) {
                $total      = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();
                $total_pass = (($total >= $table_rate->getMin()) && ($this->is_less_than($total, $table_rate->getMax())));
            } else {
                $total_pass = true;
            }

            // Accept table_rate if passes all tests
            if ($zone_pass && $weight_pass && $total_pass) {
                $available_table_rates[] = $table_rate->getData();
            }
        }
        return $available_table_rates;
    }

    public function get_available_zones($request)
    {
        $zones = Mage::getModel('savemypaquet/zones')->getCollection();

        $destination_country  = $request->getDestCountryId();
        $destination_postcode = $request->getDestPostcode();
        $destination_dep      = $request->getDestRegionCode();
        //$destination_dep      = $this->get_dep_code_from_data($destination_postcode)

        $available_zones = array();

        foreach ($zones as $zone) {
            $deps = explode(",", $zone->getDeps());
            if ($destination_country == 'FR' && (in_array($destination_dep, $deps) || in_array($this->get_dep_code_from_data($destination_postcode), $deps))) {
                $available_zones[] = $zone->getId();
            }
        }

        return $available_zones;
    }

    public function cart_contents_weight()
    {
        $items = Mage::getSingleton('checkout/session')->getQuote()->getAllItems();

        $weight = 0;
        foreach ($items as $item) {
            $weight += ($item->getWeight() * $item->getQty());
        }

        return $weight;
    }

    /* Return true if value less than max, incl. "*" */
    public function is_less_than($value, $max)
    {
        if ($max == '*') {
            return true;
        } else {
            return ($value <= $max);
        }
    }
    /* Retrieves cheapest rate from a list of table_rates. */
    public function pick_cheapest_table_rate($table_rates, $id_service = '1')
    {
        $cheapest = false;
        foreach ($table_rates as $table_rate):
            if ($table_rate['id_service'] == $id_service) {
                if ($cheapest == false) {
                    $cheapest = $table_rate;
                } else {
                    if ($table_rate['cout'] < $cheapest['cout']) {
                        $cheapest = $table_rate;
                    }
                }
            }
        endforeach;
        return $cheapest;
    }

    public function get_all_dep_code_data()
    {
        $codes_postaux           = Mage::getBaseDir('media') . '/savemypaquet/data/codes_postaux.json';
        $codes_postaux_json_data = file_get_contents($codes_postaux);
        return json_decode($codes_postaux_json_data, true);
    }

    public function get_dep_code_from_data($code)
    {
        $all_deps = $this->get_all_dep_code_data();
        foreach ($all_deps as $key => $val) {
            if (strcasecmp($code, $this->stripAccents($val['fields']['code_postal'])) == 0) {
                return $val['fields']['dep'];
            }
        }

        return false;
    }
    /**
     * Verifier si le colis est envoyé au Save My Paquet ou pas encore sinon afichage d'aun message d'erreur
     * @param  Mage_Sales_Model_Order $order      la commande
     * @return boolean/string
     */
    public function treatedColis($order)
    {
        $is_smp_method             = stristr($order->getShippingMethod(), 'savemypaquet_');
        if ($is_smp_method !== false) {
            if ($colis_numero_savemypaquet=$order->getSmypNumeroColis()) {
                return $colis_numero_savemypaquet;
            } else {
                $this->updateStatutOrderAddRspenseMessage($this->__('Package should be treated first'), $order);
                return false;
            }
        }
    }

    /**
     * Appeler l'Api SaveMyPaquet pour le tracking des colis et traiter les résultats pour pouvoir les afficher
     * @param  string $colis_numero_savemypaquet le numéro Save My Paquet
     * @param  Mage_Sales_Model_Order $order      la commande
     * @param  boolean $dajatraite pour aficher le message de "déjà traité" ou non
     * @return array    tableau  contenant les infos du tracking
     */
    public function trackingColis($colis_numero_savemypaquet, $order, $dajatraite = false, $getmessage=false)
    {
        $track = $this->get_api()->tracking->track($colis_numero_savemypaquet);

        $track['Réf Save My Paquet'] = $colis_numero_savemypaquet;
        $track['Statut']              = $this->getStatut($track['statut']);
        if ($track['statut_historique'] == '100') {
            $track['statut_historique'] = '100,';
        }
        $track['Historique des statuts'] = implode("=>", $this->getStatutHistorique(explode(',', $track['statut_historique'])));
        $track['Date de livraison']      = ($track['date_livraison']) ? substr($track['date_livraison'], 0, 10) : '   -';
        $track['Dernière mise à jour'] = substr($track['last_updated_timestamp']['date'], 0, 10);
        unset($track['last_updated_timestamp']);
        unset($track['statut_historique']);
        unset($track['date_livraison']);

        $message    = ($dajatraite ? $this->__('Parcels already treated here is the information:') : '');
        $post_metas = array('smyp_statut_colis' => $track['statut']);
        unset($track['statut']);

        return $this->updateStatutOrderAddRspenseMessage($track, $order, $message, $post_metas, $getmessage);
    }
    /**
     * Mises à jour du statut de commande et l'affichage/renvoi du message de succès ou erreur
     * @param array/string $result     résultat de l'appel de l'api Save My Paquet
     * @param  Mage_Sales_Model_Order $order      la commande
     * @param  string $message    si message supplémentaire
     * @param  array  $post_metas nouvelles valeurs des attributs SMP du commande ( principalement le statut )
     * @return
     */
    public function updateStatutOrderAddRspenseMessage($result, $order, $message = '', $post_metas = array(), $getmessage=false)
    {
        $r_message='';
        if (is_array($result) && $result['resultat'] == 'ok') {
            foreach ($post_metas as $key => $value) {
                $initial = $order->getData($key);
                if ($initial != $value) {
                    $order->setData($key, $value);
                }
            }
            $order->save();
            unset($result['resultat']);
            $r_message=$this->__('Commande ' . $order->getIncrementID() . ' : ') . $message . $this->html_show_array($result);
            if (!$getmessage) {
                $this->_getAdminhtmlSession()->addSuccess($r_message);
            }
        } else {
            $r_message=$this->__('Commande ' . $order->getIncrementID() . ' : ' . $result);
            if (!$getmessage) {
                $this->_getAdminhtmlSession()->addError($r_message);
            }
        }
        return $r_message;
    }
    /**
     * Concaténation du contenu de plusieur pdfs ( étiquettes )
     * @param  array  $pdfsContent tableau de contenus des pdfs
     * @return Zend_Pdf   avec comme contenu l'ensemble
     */
    public function combinePdfs(array $pdfsContent)
    {
        $outputPdf = new Zend_Pdf();
        foreach ($pdfsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdf = Zend_Pdf::parse($content);
                foreach ($pdf->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            }
        }
        return $outputPdf;
    }

    public function stripAccents($str)
    {
        return strtr(
            utf8_decode($str),
            utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),

            'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
        );
    }

    public function generate_token()
    {
        $id_length = 9;

        $alfa  = "abcdefghijklmnopqrstuvwxyz1234567890";
        $token = "";
        for ($i = 1; $i < $id_length; $i++) {
            @$token .= $alfa[rand(1, strlen($alfa))];
        }
        return $token;
    }
    public function valideTel($gsm_dest)
    {
        preg_match('/^((\+|00)33\s?|0)[67](\s?\d{2}){4}$/', $gsm_dest, $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches;
    }

    public function getStatut($code)
    {
        $statuts = $this->getStatuts();

        return $statuts[$code];
    }
    public function getStatuts()
    {
        return array(
            '100' => 'Demande prise en charge',
            '110' => 'Prise en charge',
            '120' => 'En cours de livraison',
            '130' => 'Livré',
            '140' => 'Annulé',
            '135' => 'Traitée',

        );
    }
    public function savemypaquet_get_departements()
    {
        $states = array(
            '75' => 'Paris',
            '77' => 'Seine-et-Marne',
            '78' => 'Yvelines',
            '93' => 'Seine-Saint-Denis',
            '94' => 'Val-de-Marne',
            '95' => 'Val-d\'Oise',
            '91' => 'Essonne',
            '92' => 'Hauts-de-Seine',

        );
        return $states;
    }
    public function getPriceTypeOptions()
    {
        $states = array(
            '0' => 'Gratuit',
            '1' => 'Prix fixe',
            '2' => 'Variable proportionnellement au montant de la commande',
            '3' => 'Variable par palier',

        );
        return $states;
    }

    public function getServiceCode($str)
    {
        $srvices = array(
            'smp_optimum'      => 1,
            'smp_premium'      => 2,
            'smp_premium_fast' => 3,

        );

        return isset($srvices[$str]) ? $srvices[$str] : false;
    }
    public function getServiceValue($index)
    {
        $srvices = array(
            1 => 'Optimum',
            2 => 'Premium',
            3 => 'Premium fast',

        );

        return isset($srvices[$index]) ? $srvices[$index] : false;
    }

    public function getInfosSuppTab()
    {
        return array(
            'smyp_numero_colis',
            'smyp_statut_colis',
            'smyp_code_barre_colis',
            'smyp_selected_service',
            'smyp_client_email',
            'smyp_client_tel',
            'smyp_client_batiment',
            'smyp_client_etage',
            'smyp_client_porte_position',
            'smyp_client_porte_cote',
            'smyp_client_digicode',
            'smyp_client_digicode2',
            'smyp_client_interphone',

        );
    }

    public function savemypaquet_get_departement($code)
    {
        $states = $this->savemypaquet_get_departements();
        return isset($states[$code]) ? $states[$code] : '';
    }
    public function getStatutHistorique($statut_historique)
    {
        foreach ($statut_historique as $key => $value) {
            $statut_historique[$key] = $this->getStatut($value);
        }
        return $statut_historique;
    }
    public function getStatutColor($code)
    {
        switch ($code) {
            case '100':
                return '#dd4b39';
                break;
            case '110':
                return '#f39c12';
                break;
            case '120':
                return '#00c0ef';
                break;
            case '130':
                return '#00a65a';
                break;
            default:
                return '#dd4b39';
                break;
        }

        return '#dd4b39';
    }
    public static function getServiceColor($code)
    {
        switch ($code) {
            case '1':
                return '#001f3f';
                break;
            case '2':
                return '#39cccc';
                break;
            case '3':
                return '#605ca8';
                break;
            default:
                return '#001f3f';
                break;
        }

        return '#001f3f';
    }


    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getAdminhtmlSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    public function do_offset($level)
    {
        $offset = "";
        for ($i = 1; $i < $level; $i++) {
            $offset = $offset . "<td></td>";
        }
        return $offset;
    }

    public function show_array($array, $level, $sub)
    {
        $html = '';
        if (is_array($array) == 1) {
            // check if input is an array
            foreach ($array as $key_val => $value) {
                $offset = "";
                if (is_array($value) == 1) {
                    // array is multidimensional
                    $html .= "<tr>";
                    $offset = $this->do_offset($level);
                    $html .= $offset . "<td>" . $key_val . "</td>";
                    $html .= $this->show_array($value, $level + 1, 1);
                } else {
                    // (sub)array is not multidim
                    if ($sub != 1) {
                        // first entry for subarray
                        $html .= "<tr nosub>";
                        $offset = $this->do_offset($level);
                    }
                    $sub = 0;
                    $html .= $offset . "<td main " . $sub . " >" . $key_val .
                        "</td><td>" . $value . "</td>";
                    $html .= "</tr>\n";
                }
            } //foreach $array
        } else {
            // argument $array is not an array
            return;
        }
        return $html;
    }

    public function html_show_array($array)
    {
        $html = "<table>\n";
        $html .= $this->show_array($array, 1, 0);
        $html .= "</table>\n";
        return $html;
    }
}
