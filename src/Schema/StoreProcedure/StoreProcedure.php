<?php 

namespace Schema\StoreProcedure;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";


use Schema\Connect\ConnectMYSQL;
use Res\Res;
use ReturnValue\ReturnValue;


class StoreProcedure{

    // public function __construct() {
    //     if (ConnectMYSQL::setFileBaseData("receptbook", "root", "","3306","localhost")) {
    //         $pdo = ConnectMYSQL::ConnectToDataBase();
    //         print_r(self::Call("login",["email"=>"martin@gmail.com","password"=>"12345678"],$pdo));
            
            
    //      }else{
    //          $res = Res::getInc();
    //          $res->setSqlError(ReturnValue::SQLError(true, ["err" => true, "data" => ""]));
    //          $res->setBody(ReturnValue::createReturnArray(true, []));
    //          $res->build();
    //          exit();
    //      }
    // }

    public static function Call($name, $array, $db) {
            
        try {
            $paramKeys = array_keys($array);
            $paramPlaceholders = implode(', ', array_map(function($key) {
                return ":$key";
            }, $paramKeys));

        $stmt = $db->prepare("CALL $name($paramPlaceholders)");

        foreach ($array as $key => $value) {
            $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
        }

        $stmt->execute();
        $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return ReturnValue::SQLError(false,$resultSet);

        } catch (\Throwable $th) {
            

            return ReturnValue::SQLError(true,$th->getMessage());
   
        }
        
    }
}

?> 