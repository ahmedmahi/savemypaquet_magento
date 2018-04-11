<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Api_Resources_Colis extends Ecomtech_Savemypaquet_Model_Api_Resources_Abstract
{
    const ISAUTH = 1;

    public function __construct($api)
    {
        parent::__construct('colis', $api);
    }

    public function create($data)
    {
        $this->set_request_args(array(
            'method' => 'POST',
            'path'   => 'new',
            'body'   => $data,
        ));

        return $this->do_request(self::ISAUTH);
    }


    public function cancel($numero_savemypaquet)
    {
        $this->set_request_args(array(
            'method' => 'POST',
            'path'   => 'cancel',
            'body'   => array(
                'numero_Save_MyPaquet' => urlencode($numero_savemypaquet),
            )));

        return $this->do_request(self::ISAUTH);
    }
}
