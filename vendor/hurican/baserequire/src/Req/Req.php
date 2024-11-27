<?php

namespace Req;

require_once __DIR__."/../autoloader.php";

use BaseReq\BaseReq;
use CookieHandler\CookieHandler;
class Req extends BaseReq{
    
    public function __construct() {
        if (!empty($input)) {
            $decodedInput = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->setBody($decodedInput);
            } else {
                // Hibakezel�s JSON dek�dol�skor
                $this->setBody([]);
            }
        } else {
            $this->setBody([]);
        }
        if (isset($_SERVER["REQUEST_METHOD"])) {
            $this->setMethod($_SERVER["REQUEST_METHOD"]);
        }
        if (isset($_SERVER['REQUEST_URI'])) {
        
            $url = $_SERVER['REQUEST_URI'];

            $this->setFun(explode('/', $_SERVER['REQUEST_URI'])[count(explode('/', $_SERVER['REQUEST_URI'])) - 1]);
        
        }
        $this->setCookieHandler(new CookieHandler("req"));
    }

}

?>