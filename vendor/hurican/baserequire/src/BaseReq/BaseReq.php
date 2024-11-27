<?php

namespace BaseReq;

require_once __DIR__."/../autoloader.php";

use HTTP_STATUS\HTTP_STATUS;
use CookieHandler\CookieHandler;

abstract class BaseReq{

    private static string|null $jwt = "";

    private static array|null $body = [];

    private static int  $httpstatus = 200;

    private static string | null $method = "";

    private static string | null $fun ="";
    
    private static array | null $header =[];

    private static string|array|null $sqlError = null;
    private static CookieHandler $cookieHandler;
    /**
     * Get the value of body
     */ 
    public static function getBody()
    {
        return self::$body;
    }

    /**
     * Set the value of body
     *
     * @return  self
     */ 
    public static function setBody($body): array|null
    {
        self::$body = $body;

        return self::getBody();
    }

    /**
     * Get the value of method
     */ 
    public static function getMethod(): string|null
    {
        return self::$method;
    }

    /**
     * Set the value of method
     *
     * @return  self
     */ 
    public static function setMethod($method): string|null
    {
        self::$method = $method;

        return self::getMethod();
    }

    /**
     * Get the value of fun
     */ 


    /**
     * Set the value of fun
     *
     * @return  self
     */ 
    public static function setFun($fun): string|null
    {
        self::$fun = $fun;

        return self::getFun();
    }

    /**
     * Get the value of header
     */ 
    public static function getHeader(): array|null
    {
        return self::$header;
    }

    /**
     * Set the value of header
     *
     * @return  self
     */ 
    public static function setHeader($header): array|null
    {
        self::$header = $header;

        return self::getHeader();
    }

    /**
     * Get the value of cookieHandler
     */ 
    public static function getCookieHandler(): CookieHandler
    {
        return self::$cookieHandler;
    }

    /**
     * Set the value of cookieHandler
     *
     * @return  self
     */ 
    public static function setCookieHandler($cookieHandler): CookieHandler
    {
        self::$cookieHandler = $cookieHandler;

        return self::getCookieHandler();
    }

    /**
     * Get the value of httpstatus
     */ 
    public static function getHttpstatus():int
    {
        return self::$httpstatus;
    }

    /**
     * Set the value of httpstatus
     *
     * @return  self
     */ 
    public static function setHttpstatus(int $httpstatus): int
    {
        self::$httpstatus = HTTP_STATUS::getStatus($httpstatus);

        return self::getHttpstatus();
    }

    /**
     * Get the value of jwt
     */ 
    public static function getJwt(): string|null
    {
        return self::$jwt;
    }

    /**
     * Set the value of jwt
     *
     * @return  self
     */ 
    public static function setJwt($jwt): string|null
    {
        self::$jwt = $jwt;

        return self::$jwt;
    }

    /**
     * Get the value of sqlError
     */ 
    public static function getSqlError()
    {
        return self::$sqlError;
    }

    /**
     * Set the value of sqlError
     *
     * @return  self
     */ 
    public static function setSqlError($sqlError): array|string|null
    {
        self::$sqlError = $sqlError;

        return self::$sqlError;
    }

    /**
     * Get the value of fun
     */ 
    public static function getFun()
    {
        return self::$fun;
    }
}

?>