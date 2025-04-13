<?php

/**
 * Harvest HTTP Client
 *
 * @author Geoff Doty <https://github.com/n2geoff>
 * @copyright 2013 Geoff Doty
 * @license MIT
 */
class Harvest {

    private $headers = [];
    private $options = [];
    private $session = NULL;
    private $server  = FALSE;
    private $debug   = FALSE;
    private $output  = [];

    public function __construct($config =[])
    {
        if(!empty($config))
        {
            if(isset($config['server']))
            {
                //insure no trailing slash
                $this->server = rtrim($config['server'], '/');
            }

            if(isset($config['debug']))
            {
                $this->debug = $config['debug'];
            }

            if(isset($config['headers']))
            {
                $this->header($config['headers']);
            }

            if(isset($config['auth']))
            {
                $this->option('HTTPAUTH', CURLAUTH_BASIC);
                $this->option('USERPWD', $config['auth']);
            }

        }
    }

    public function head($url, $headers = array())
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'HEAD');

       return $this->request($url);
    }

    public function get($url)
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'GET');

        return $this->request($url);
    }

    public function post($url, $data = array(), $headers = array())
    {
        $this->option(CURLOPT_RETURNTRANSFER, TRUE);
        $this->option(CURLOPT_CUSTOMREQUEST, 'POST');

        if(!empty($data) && is_array($data))
        {
            $this->option(CURLOPT_POSTFIELDS, http_build_query($data, NULL, '&'));
        }

        return $this->request($url, $data, $headers);
    }

    public function put($url, $data = array(), $headers = array())
    {
        $this->option(CURLOPT_POST, TRUE);
        $this->option(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->option(CURLOPT_RETURNTRANSFER, TRUE);

        if(!empty($data) && is_array($data))
        {
            $this->option(CURLOPT_POSTFIELDS, http_build_query($data, NULL, '&'));
        }

        return $this->request($url, $data);
    }

    public function delete($url, $data = '', $headers = array())
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->request($url);
    }

    public function header($headers, $value = NULL)
    {
        // if array it is key => value
        if(is_array($headers))
        {
            foreach($headers as $key => $value)
            {
                $this->headers[] = "{$key}: {$value}";
            }
        }
        else // your passing a $headers(key) => value
        {
            $this->headers[] = "{$headers}: {$value}";
        }
    }

    public function setHeaders()
    {
        $this->option(CURLOPT_HTTPHEADER, $this->headers);
    }

    public function option($code, $value)
    {
        if (is_string($code) && !is_numeric($code))
        {
            $code = constant('CURLOPT_' . strtoupper($code));
        }

        $this->options[$code] = $value;
    }

    public function setOptions()
    {
        // set all curl options provided
        curl_setopt_array($this->session, $this->options);
    }

    private function request($url, $data = array(), $headers = array())
    {
        if($this->server)
        {
            $url = $this->server . $url;
        }

        //ssl enabled
        if((stripos($url, 'https') !== FALSE))
        {
            $this->option(CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            $this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
            $this->option(CURLOPT_SSL_VERIFYHOST, 2);
        }

        if($this->debug)
        {
            $this->option(CURLINFO_HEADER_OUT, TRUE);
        }

        $this->session = curl_init($url);

        // set header options
        $this->setHeaders();

        // set connection curl options
        $this->setOptions();

        //execute curl transaction
        $result = curl_exec($this->session);

        if($this->debug)
        {
            // get sent headers
            $this->output[] = curl_getinfo($this->session, CURLINFO_HEADER_OUT);

            // get curl request options
            $this->output[] = curl_getinfo($this->session);

            // check for errors
            if(curl_errno($this->session))
            {
                $this->output['error'] = curl_error($this->session);
            }

            echo "<pre>";
            var_dump($this->output);
            echo "</pre>";
        }

        // close connection
        $this->_close();

        // return response
        return $this->response($result);
    }

    private function _close()
    {
        curl_close($this->session);

        $this->headers = [];
        $this->options = [];
        $this->session = NULL;
    }

    private function response($result)
    {
        // TODO:  check if there is a better response object format to use
        return $result;
    }

    private function is_curl_installed()
    {
        return function_exists('curl_version');
    }

}
