<?php

namespace CookieHandler;

require_once __DIR__."/../autoloader.php";

class CookieHandler{

    private array $arrayOfCookie;

    public string $type; 

    public function __construct(string $type) {
        switch ($type) {
            case 'req':
                $this->setArrayOfCookie($_COOKIE);
                break;
            case 'res':
                $this->setArrayOfCookie([]);
                break;
            
            default:
                exit();
                break;
        }
        $this->setArrayOfCookie($_COOKIE);
    }


    public function pushCookie(string $key,string $value){

        $this->arrayOfCookie[$key.""] = $value;

    }
    /**
     * Get the value of arrayOfCookie
     */ 
    public function getArrayOfCookie()
    {
        return $this->arrayOfCookie;
    }

    /**
     * Set the value of arrayOfCookie
     *
     * @return  self
     */ 
    public function setArrayOfCookie($arrayOfCookie)
    {
        $this->arrayOfCookie = $arrayOfCookie;

        return $this;
    }
}

?>