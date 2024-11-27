<?php

namespace BaseReq;

require_once __DIR__."/../autoloader.php";

use CookieHandler\CookieHandler;

abstract class BaseReq{

    private array|null $body;

    private string | null $method;

    private string | null $fun;
    
    private array | null $header;

    private CookieHandler $cookieHandler;
    /**
     * Get the value of body
     */ 
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */ 
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of method
     */ 
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the value of method
     *
     * @return  self
     */ 
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get the value of fun
     */ 
    public function getFun()
    {
        return $this->fun;
    }

    /**
     * Set the value of fun
     *
     * @return  self
     */ 
    public function setFun($fun)
    {
        $this->fun = $fun;

        return $this;
    }

    /**
     * Get the value of header
     */ 
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set the value of header
     *
     * @return  self
     */ 
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get the value of cookieHandler
     */ 
    public function getCookieHandler()
    {
        return $this->cookieHandler;
    }

    /**
     * Set the value of cookieHandler
     *
     * @return  self
     */ 
    public function setCookieHandler($cookieHandler)
    {
        $this->cookieHandler = $cookieHandler;

        return $this;
    }
}

?>