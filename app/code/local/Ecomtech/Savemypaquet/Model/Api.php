<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Api
{
    const VERSION = '1.0.0';

    public $store_url;

    public $api_login;

    public $password;

    public $api_url;

    public $validate_url = false;

    public $timeout = 30;

    public $ssl_verify = true;

    /** Resources */

    public $villes;

    public $colis;

    public $points_relais;

    public function __construct($arguments = array())
    {

        // required functions
        if (!extension_loaded('curl')) {
            throw new Exception('Savemypaquet REST API  requires the cURL PHP extension.');
        }

        if (!extension_loaded('json')) {
            throw new Exception('Savemypaquet REST API  needs the JSON extension.');
        }

        // set required info
        $this->store_url = $arguments['store_url'];
        $this->api_login = $arguments['api_login'];
        $this->password  = $arguments['password'];

        // load each API resource
        $this->init_resources();

        // build API url from store URL
        $this->build_api_url();

        // set options
        $this->parse_options($arguments['options']);

        if ($this->validate_url) {
            $this->validate_api_url();
        }
    }

    public function init_resources()
    {
        $resources = array(
            'savemypaquet/api_resources_villes'   => 'villes',
            'savemypaquet/api_resources_colis'    => 'colis',
            'savemypaquet/api_resources_tracking' => 'tracking',
            'savemypaquet/api_resources_etiquette' => 'etiquette',
            'savemypaquet/api_resources_pdf' => 'pdf',

        );

        foreach ($resources as $resource_class => $resource_method) {
            $this->$resource_method = Mage::getModel($resource_class, $this);
        }
    }

    public function build_api_url()
    {
        $url = parse_url($this->store_url);

        // default to http if not provided
        $scheme = isset($url['scheme']) ? $url['scheme'] : 'http';

        // set host
        $host = $url['host'];

        // add port to host if provided
        $host .= isset($url['port']) ? ':' . $url['port'] : '';

        // set path and strip any trailing slashes
        $path = isset($url['path']) ? rtrim($url['path'], '/') : '';

        // add api path
        $path .= '/api/';

        // build URL
        $this->api_url = "{$scheme}://{$host}{$path}";
    }

    public function parse_options($options)
    {
        $valid_options = array(
            'validate_url',
            'timeout',
            'ssl_verify',
        );

        foreach ((array) $options as $opt_key => $opt_value) {
            if (!in_array($opt_key, $valid_options)) {
                continue;
            }

            $this->$opt_key = $opt_value;
        }
    }

    public function validate_api_url()
    {
        $index = @file_get_contents($this->api_url);

        if (false === $index) {
            throw new Ecomtech_Savemypaquet_Model_Api_Exceptions(sprintf('Invalid URL, no Savemypaquet API found at %s -- ensure your store URL is correct and pretty permalinks are enabled.', $this->api_url), 404);
        }

        if ('1' === $index) {
            throw new Ecomtech_Savemypaquet_Model_Api_Exceptions(sprintf('Please upgrade the Magento version on %s to 1.4 or greater.', $this->api_url));
        }

        $json_start = strpos($index, '{');
        $json_end   = strrpos($index, '}') + 1;

        $index = json_decode(substr($index, $json_start, ($json_end - $json_start)));

        if (null === $index) {
            throw new Ecomtech_Savemypaquet_Model_Api_Exceptions(sprintf('Savemypaquet API found, but JSON is corrupt -- ensure the index at %s is valid JSON.', $this->api_url));
        }

        if ('https' === parse_url($index->store->URL, PHP_URL_SCHEME) && !$index->store->meta->ssl_enabled) {
            $this->api_url = str_replace('http://', 'https://', $this->api_url);
        }
    }

    public function make_api_call($method, $path, $request_data, $is_auth = 0, $fileurl=false)
    {
        $args = array(
            'is_auth'   => $is_auth,
            'method'    => $method,
            'url'       => ($fileurl)?$fileurl:$this->api_url . $path,
            'base_url'  => $this->api_url,
            'data'      => $request_data,
            'api_login' => $this->api_login,
            'password'  => $this->password,
            'options'   => array(
                'timeout'    => $this->timeout,
                'ssl_verify' => $this->ssl_verify,
            ),
        );
        $request = Mage::getModel('savemypaquet/api_request', $args);

        return $request->dispatch($fileurl);
    }
}
