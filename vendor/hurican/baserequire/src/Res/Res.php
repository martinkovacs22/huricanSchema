<?php

namespace Res;

require_once __DIR__ . "/../autoloader.php";

use BaseReq\BaseReq;
use CookieHandler\CookieHandler;
use HTTP_STATUS\HTTP_STATUS;
use ReturnValue\ReturnValue;
use Req\Req;
class Res extends BaseReq
{



    private static Res |null $inc = null;

    public static function getInc(): Res|null
    {

        if (empty(self::$inc)) {
            self::$inc = new Res();
        }
        return self::$inc;
    }

    private function __construct()
    {
        $this->setCookieHandler(new CookieHandler("res"));
    }


    public static function build()
    {

        foreach (self::getCookieHandler()->getArrayOfCookie() as $key => $value) {
            $_COOKIE[$key] = $value;
        }
        
        
        //print_r(Res::getBody()["sql"]);

        if (empty(Res::getBody()["sql"])) {
            $newBody = Res::getBody();
            $newBody["sql"] = Res::getSqlError();
            Res::setBody($newBody);
        }
        echo json_encode(Res::getBody());
        $code = self::getHttpstatus();
        http_response_code($code);
        exit();
    }
}
