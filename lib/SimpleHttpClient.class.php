<?php
class SimpleHttpClient
{
    protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
    protected $_followlocation = true;
    protected $_timeout = 30;
    protected $_maxRedirects = 4;
    protected $_cookieFileLocation = './cookie.txt';
    protected $_post;
    protected $_postFields;
    protected $_session;
    protected $_content;
    protected $_includeHeader = false;
    protected $_noBody = false;
    protected $_status;
    protected $_binaryTransfer = false;
    public $authentication = 0;
    public $auth_name = '';
    public $auth_pass = '';

    public function __construct()
    {
    }

    public function get($url, $referer = "")
    {
        if (!isset($this->_session)) {
            $this->_session = curl_init();
        }

        curl_setopt($this->_session, CURLOPT_URL, $url);
        curl_setopt($this->_session, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($this->_session, CURLOPT_TIMEOUT, $this->_timeout);
        curl_setopt($this->_session, CURLOPT_MAXREDIRS, $this->_maxRedirects);
        curl_setopt($this->_session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_session, CURLOPT_FOLLOWLOCATION, $this->_followlocation);
        curl_setopt($this->_session, CURLOPT_COOKIEJAR, $this->_cookieFileLocation);
        curl_setopt($this->_session, CURLOPT_COOKIEFILE, $this->_cookieFileLocation);
        curl_setopt($this->_session, CURLOPT_HEADER, $this->_includeHeader);
        curl_setopt($this->_session, CURLOPT_NOBODY, $this->_noBody);
        curl_setopt($this->_session, CURLOPT_BINARYTRANSFER, $this->_binaryTransfer);
        curl_setopt($this->_session, CURLOPT_USERAGENT, $this->_useragent);
        curl_setopt($this->_session, CURLOPT_REFERER, $referer);

        if ($this->authentication == 1) {
            curl_setopt($this->_session, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
        }

        if ($this->_post) {
            curl_setopt($this->_session, CURLOPT_POST, true);
            curl_setopt($this->_session, CURLOPT_POSTFIELDS, $this->_postFields);
        }



        $this->_content = curl_exec($this->_session);
        $this->_status = curl_getinfo($this->_session, CURLINFO_HTTP_CODE);

        return $this->_content;
    }

    public function useAuth($use)
    {
        $this->authentication = 0;
        if ($use == true) {
            $this->authentication = 1;
        }
    }

    public function setName($name)
    {
        $this->auth_name = $name;
    }

    public function setPass($pass)
    {
        $this->auth_pass = $pass;
    }

    public function setCookiFileLocation($path)
    {
        $this->_cookieFileLocation = $path;
    }

    public function setPost($postFields)
    {
        $this->_post = true;
        $this->_postFields = $postFields;
    }

    public function setBinaryTransfer($binaryTransfer)
    {
        $this->_binaryTransfer = $binaryTransfer;
    }


    public function setUserAgent($userAgent)
    {
        $this->_useragent = $userAgent;
    }

    public function getHttpStatus()
    {
        return $this->_status;
    }

    public function __tostring()
    {
        return $this->_content;
    }

    public function close()
    {
        curl_close($this->_session);
        unlink($this->_cookieFileLocation);
    }
}
