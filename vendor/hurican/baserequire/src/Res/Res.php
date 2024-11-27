<?php

namespace Res;

require_once __DIR__."/../autoloader.php";

use BaseReq\BaseReq;
use CookieHandler\CookieHandler;
class Res extends BaseReq{
    
    public function __construct() {
        $this->setCookieHandler(new CookieHandler("res"));
    }

}

?>