<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Api_Resources_Tracking extends Ecomtech_Savemypaquet_Model_Api_Resources_Abstract
{
    const ISAUTH = 0;
    public function __construct($api)
    {
        parent::__construct('track', $api);
    }

    public function track($numero_savemypaquet)
    {
        $this->set_request_args(array(
            'method' => 'GET',
            'params' => array(
                'numero_Save_MyPaquet' => urlencode($numero_savemypaquet),
            )));

        return $this->do_request(self::ISAUTH);
    }
}
