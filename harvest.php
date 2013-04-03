<?php

/**
 * Harvest HTTP Client
 * 
 * @author Geoff Doty <n2geoff@gmail.com>
 */
class Harvest {

    private $headers = array();
    private $options = array();
    private $session = NULL;

    private $server  = FALSE;
    private $debug   = FALSE;
    private $output  = array();

    public function __construct($config = array())
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

        }
    }

    public function head($url, $headers = array())
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'HEAD');

        $result = $this->request($url);
    }

    public function get($url) 
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'GET');

        $result = $this->request($url);
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

        $result = $this->request($url, $data);
    }
    
    public function delete($url, $data = '', $headers = array()) 
    {
        $this->option(CURLOPT_CUSTOMREQUEST, 'DELETE');

        $result = $this->request($url);
    }

    public function header($headers, $value = NULL)
    {
        //if array it is key value
        if(is_array($headers))
        {
            foreach($headers as $key => $value)
            {
                $this->headers[] = "{$key}: {$value}";
            }
        }
        else // your passing a correct
        {
            $this->headers[] = "{$key}: {$value}";
        }
    }

    public function headers($headers = array())
    {
        if(!empty($headers) && is_array($headers))
        {
            foreach($headers as $key => $value)
            {
                $this->headers[] = "{$key}: {$value}";
            }
        }

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

    public function options($options = array())
    {
        foreach ($options as $option_code => $option_value)
        {
            $this->option($option_code, $option_value);
        }

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
        if(($is_https = stripos($url, 'https') !== FALSE))
        {
            $this->option(CURLOPT_SSLVERSION, 3);
            $this->option(CURLOPT_SSL_VERIFYPEER, FALSE);
            $this->option(CURLOPT_SSL_VERIFYHOST, 2);
        }

        if($this->debug)
        {
            $this->option(CURLINFO_HEADER_OUT, TRUE);
        }

        $this->session = curl_init($url);

        //set header options
        $this->headers();

        //apply curl options to connection
        $this->options();

        //execute curl transaction
        $result = curl_exec($this->session);

        //$result = $this->response(curl_exec($this->session));
        
        if($this->debug)
        {
            //get sent headers
            $headers_out = curl_getinfo($this->session, CURLINFO_HEADER_OUT);
            $request_options = curl_getinfo($this->session);
            
            //$this->output[] = $headers_out;
            //$this->output[] = $request_options;

            var_dump($headers_out, $request_options);
        }

        //close connection
        $this->_close();

        return $result;
    }

    private function _close()
    {
        curl_close($this->session);

        //$this->headers = array();
        $this->options = array();
        $this->session = NULL;
    }

    private function response($result) 
    {
        return json_decode($result);
    }

    private function is_curl_installed()
    {
        return function_exists('curl_version');
    }

}