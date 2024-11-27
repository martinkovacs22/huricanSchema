<?php

namespace Req;

require_once __DIR__."/../autoloader.php";

use BaseReq\BaseReq;
use CookieHandler\CookieHandler;
class Req extends BaseReq{
    
    private static Req | null $inc ; 

    public static function getInc(): Req|null{
        if (empty(self::$inc)) {
            self::$inc = new Req();
        }
        return self::$inc;
    }

    private function __construct() {
        if (!empty(getallheaders()["token"])) {
            self::setJwt(getallheaders()["token"]);
        }
        self::setHeader(getallheaders());
        if (!empty($input)) {
            $decodedInput = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                self::setBody($decodedInput);
            } else {
                self::setBody([]);
            }
        } else {
            self::setBody([]);
        }
        if (isset($_SERVER["REQUEST_METHOD"])) {
            self::setMethod($_SERVER["REQUEST_METHOD"]);
        }
        if (isset($_SERVER['REQUEST_URI'])) {
        
            $url = $_SERVER['REQUEST_URI'];

            self::setFun(explode('/', $_SERVER['REQUEST_URI'])[count(explode('/', $_SERVER['REQUEST_URI'])) - 1]);
        
        }
        self::setCookieHandler(new CookieHandler("req"));
    }

}

?>