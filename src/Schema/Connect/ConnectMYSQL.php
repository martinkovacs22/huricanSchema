<?php

namespace Schema\Connect;

require_once __DIR__."/../../autoloader.php";

require_once __DIR__."/../../../vendor/autoload.php";

use PDO;
use Res\Res;

class ConnectMYSQL
{

    const JSONFile = __DIR__ . "/mysqlData.json";

    public static function getFile(string $fileArrayName)
    {
        return json_decode(file_get_contents(self::JSONFile), true)[$fileArrayName];
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

                $file["dataBaseName"] = $dataBaseName;
                $file["dataBaseUsername"] = $dataBaseUsername;
                $file["dataBasePassword"] = $dataBasePassword;
                $file["dataBasePort"] = $dataBasePort;
                $file["dataBaseURL"] = $dataBaseURL;

                $jsonContent = json_encode($file, JSON_PRETTY_PRINT); // JSON formázása ember olvasható módon

                if (file_put_contents(self::JSONFile, $jsonContent) === false) {
                    throw new \Exception("A fájlba írás nem sikerült.");
                }
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public static function ConnectToDataBase(): \PDO | null{

        $fileContent = self::getFile("fileBaseData");
        try {
            $dsn = "mysql:host=" . $fileContent['dataBaseURL'] . ";port=" . $fileContent['dataBasePort'] . ";dbname=" . $fileContent['dataBaseName'];
            $pdo = new PDO($dsn, $fileContent['dataBaseUsername'], $fileContent['dataBasePassword']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        } catch (\Throwable $th) {
            $res = new Res();
            $res->setBody(Re)
            $pdo = null;
        }
        
        return $pdo;

    }
}
