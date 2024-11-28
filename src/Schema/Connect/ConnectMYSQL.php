<?php

namespace Schema\Connect;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

use JsonException;
use PDO;
use Res\Res;
use ReturnValue\ReturnValue;

class ConnectMYSQL
{

    const JSONFile = __DIR__ . "/mysqlData.json";

    private static $pdo;

    public static function getFile(string $fileArrayName):array
    {
        if (!file_exists(self::JSONFile)) {
            throw new \Exception("A JSON fájl nem található: " . self::JSONFile);
        }
        $content = file_get_contents(self::JSONFile);
        $decoded = json_decode($content, true);
        if ($decoded === null) {
            throw new \Exception("Hibás JSON formátum a fájlban: " . self::JSONFile);
        }
        return $decoded[$fileArrayName] ?? [];
    }


    public static function setFileBaseData(
        string $dataBaseName,
        string $dataBaseUsername,
        string $dataBasePassword,
        string $dataBasePort,
        string $dataBaseURL
    ): bool {

        try {


            if (isset($dataBaseName) && isset($dataBaseUsername) && isset($dataBasePassword) && isset($dataBasePort) && isset($dataBaseURL)) {
                $file = self::getFile("fileBaseData");

                $file["fileBaseData"]["dataBaseName"] = $dataBaseName;
                $file["fileBaseData"]["dataBaseUsername"] = $dataBaseUsername;
                $file["fileBaseData"]["dataBasePassword"] = $dataBasePassword;
                $file["fileBaseData"]["dataBasePort"] = $dataBasePort;
                $file["fileBaseData"]["dataBaseURL"] = $dataBaseURL;

                $jsonContent = json_encode($file, JSON_PRETTY_PRINT); // JSON formázása ember olvasható módon
                if (file_put_contents(self::JSONFile, $jsonContent) === false) {
                    throw new \Exception("A fájlba írás nem sikerült. Ellenőrizd az engedélyeket.");
                }
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function connectToServerWithOutDatabase(string $host, string $userName, string $password): \PDO | array
    {
        try {
            // DSN (Data Source Name): Adatbázis nélküli kapcsolat
            $dsn = "mysql:host=$host";

            // PDO objektum létrehozása
            $pdo = new \PDO($dsn, $userName, $password);

            // Hibakezelési mód beállítása
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            if (!self::setFileBaseData("", $userName, $password, "3306", $host)) {
                throw new \JsonException("Not found Json File");
            }

            return $pdo; // Sikeres kapcsolat esetén a PDO objektumot adja vissza
        } catch (\PDOException $e) {
            // Hiba esetén null-t ad vissza és lehetőség van a hiba naplózására
            error_log("Hiba az adatbázishoz való csatlakozás során: " . $e->getMessage());
            return ReturnValue::SQLError(true, ["data" => $e->getMessage()]);
        } catch (\JsonException $th) {
            return ReturnValue::SQLError(true, ["data" => $th->getMessage()]);
        }
    }

    public static function connectToserverWithDatabase(
        string $dataBaseName,
        string $dataBaseUsername,
        string $dataBasePassword,
        string $dataBasePort,
        string $dataBaseURL
    ): \PDO | null {
        if (isset(self::$pdo) || !empty(self::$pdo) || self::$pdo != null) {
            return self::$pdo;
        }else{
            self::setFileBaseData( $dataBaseName,
             $dataBaseUsername,
             $dataBasePassword,
             $dataBasePort,
             $dataBaseURL);
        }

        $fileContent = self::getFile("fileBaseData");
        try {
            $dsn = "mysql:host=" . $fileContent['dataBaseURL'] . ";port=" . $fileContent['dataBasePort'] . ";dbname=" . $fileContent['dataBaseName'];
            $pdo = new PDO($dsn, $fileContent['dataBaseUsername'], $fileContent['dataBasePassword']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (empty($pdo)) {
                throw new \Exception("PDO is null");
            }
            self::$pdo = $pdo;
        } catch (\PDOException $th) {
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, ["err" => true, "data" => $th->getMessage()]));
            $res->setBody(ReturnValue::createReturnArray(true, []));
            $res->build();
            exit();
        }

        return $pdo;
    }
}
// Test Ping ha minden jó
// if (ConnectMYSQL::setFileBaseData("receptbook", "root", "","3306","localhost")) {
//    $pdo = ConnectMYSQL::ConnectToDataBase();
//    $ping = $pdo->query("SELECT 1")->fetch();
//    if (!$ping) {
//        throw new \Exception("Kapcsolódás sikerült, de a pingelés sikertelen!");
//    }
//    print_r($ping);
// }else{
//     $res = Res::getInc();
//     $res->setSqlError(ReturnValue::SQLError(true, ["err" => true, "data" => ""]));
//     $res->setBody(ReturnValue::createReturnArray(true, []));
//     $res->build();
//     exit();
// }
