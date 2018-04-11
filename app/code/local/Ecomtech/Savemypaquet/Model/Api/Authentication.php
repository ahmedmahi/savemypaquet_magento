<?php

/**
 * @category    Ecomtech
 * @package     Ecomtech_Savemypaquet
 * @author      Savemypaquet ( http://www.savemypaquet.com)
 * @developer   Ahmed MAHI <ahmed@mahi.ma> (http://ahmedmahi.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ecomtech_Savemypaquet_Model_Api_Authentication
{
    protected $url;

    protected $api_login;

    protected $password;

    protected $au_request;

    protected $au_response;

    protected $token = '';

    protected $options = array();

    public function __construct($args = array())
    {
        $this->url       = $args['base_url'];
        $this->api_login = $args['api_login'];
        $this->password  = $args['password'];
        $this->options   = $args['options'];
    }

    public function get_auth_params($params)
    {
        $this->setToken();

        $params = array_merge((array) $params, array(
            'token'          => $this->token,
            'auth_timestamp' => time(),
            'auth_nonce'     => sha1(microtime()),

        ));

        return $params;
    }

    public function is_ssl()
    {
        return substr($this->url, 0, 5) === 'https';
    }

    public function get_login()
    {
        return $this->api_login;
    }

    public function get_password()
    {
        return $this->password;
    }

    public function setToken()
    {
        if ($this->token) {
            return;
        }

        $this->au_request = new stdClass();

        $this->au_request->headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: Savemypaquet API Client-PHP/' . Ecomtech_Savemypaquet_Model_Api::VERSION,
        );

        $this->au_request->method = 'POST';

        // trailing slashes tend to cause OAuth authentication issues, so strip them
        $this->au_request->url = rtrim($this->url, '/');

        $this->au_request->params = array();
        $this->au_request->data   = array(
            'email'    => $this->api_login,
            'password' => $this->password,
        );

        // optional cURL opts
        $timeout    = (int) $this->options['timeout'];
        $ssl_verify = (bool) $this->options['ssl_verify'];

        $ch = curl_init();

        // default cURL opts
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
        //  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $ssl_verify);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, (int) $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // set request headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->au_request->headers);

        $this->au_request->body = json_encode($this->au_request->data);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->au_request->body);

        $this->au_request->url = $this->get_auth_url($this->url);

        // set request url
        curl_setopt($ch, CURLOPT_URL, $this->au_request->url);

        $this->au_response = new stdClass();

        // blank headers
        $this->curl_headers = '';

        $start_time = microtime(true);

        // send request + save raw response body
        $this->au_response->body = curl_exec($ch);

        // request duration
        $this->au_request->duration = round(microtime(true) - $start_time, 5);

        // response code
        $this->au_response->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        unset($ch);
        $body = json_decode($this->au_response->body, true);
        if (isset($body['error'])) {
            throw new Ecomtech_Savemypaquet_Model_Api_Exceptions(sprintf($body['error'] . ' Erreur code:  %s.', $body['errorcode']), $this->au_response->code, $this->au_request, $this->au_response);
        } elseif ($body[0] == 'invalid_email_or_password') {
            throw new Ecomtech_Savemypaquet_Model_Api_Exceptions('Invalid email or password', $this->au_response->code, $this->au_request, $this->au_response);
        }
        if (isset($body['token'])) {
            $this->token = $body['token'];
        }
    }

    public function get_auth_url($url)
    {
        return $url .= 'auth/login';
    }
}
