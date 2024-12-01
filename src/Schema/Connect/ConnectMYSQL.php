<?php

namespace Schema\Connect;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";


use PDO;
use Res\Res;
use ReturnValue\ReturnValue;
use Ini\IniController;

class ConnectMYSQL
{

    const iniFile = __DIR__ . "/mysqlData.ini";

    private $iniController;

    private \PDO $pdo;



    public function __construct()
    {

        $this->iniController = new IniController(ConnectMYSQL::iniFile);
    }

    public function connectToMysqlServerWithOutDatabase($host, $userName, $password, $port = 3306): \PDO
{
    try {
        $dsn = "mysql:host=$host;port=$port";
        $this->pdo = new \PDO($dsn, $userName, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Frissítsd az INI fájlt, ha szükséges
        $fileData = $this->iniController->getFileContent();
        $fileData["fileBaseData"]["dataBaseUsername"] = $userName;
        $fileData["fileBaseData"]["dataBasePort"] = $port;
        $fileData["fileBaseData"]["dataBasePassword"] = $password;
        $fileData["fileBaseData"]["dataBaseHost"] = $host;
        $this->iniController->saveToFile($fileData);

        return $this->pdo;
    } catch (\PDOException $th) {
        $res = Res::getInc();
        $res->setSqlError(ReturnValue::SQLError(true, $th->getMessage()));
        $res->build();
        echo "Hiba történt a MySQL szerverhez való kapcsolódás közben: " . $th->getMessage();
        exit();
    }
}


    public function connectToMysqlServerWithDatabase($host, $dataBase, $user, $pass, $port = 3306): \PDO
    {
        try {
            // A DSN (Data Source Name) formátuma: mysql:host=HOST;dbname=DATABASE_NAME;port=PORT
            $dsn = "mysql:host=$host;dbname=$dataBase;port=$port";  // Az adatbázisnév is hozzáadva a DSN-hez

            // PDO objektum létrehozása, és adatbázis kapcsolat
            $pdo = new \PDO($dsn, $user, $pass);

            // Hibakezelési mód beállítása: Exception mód, hogy könnyen elérjük a hibákat
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Az INI fájl frissítése a csatlakozási adatokkal
            $fileData = $this->iniController->getFileContent();
            $fileData["fileBaseData"]["dataBaseUsername"] = $user;
            $fileData["fileBaseData"]["dataBasePort"] = $port;
            $fileData["fileBaseData"]["dataBasePassword"] = $pass;
            $fileData["fileBaseData"]["dataBaseHost"] = $host;
            $fileData["fileBaseData"]["dataBaseName"] = $dataBase;  // Az adatbázisnév mentése

            // Az INI fájl mentése
            $this->iniController->saveToFile($fileData);

            // Sikeres kapcsolat esetén a PDO objektumot adja vissza
            return $pdo;
        } catch (\PDOException $th) {
            // Hibakezelés, ha nem sikerült a kapcsolat
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, $th->getMessage()));

            // Hibakezelés kiírása és megszakítja a programot
            $res->build();
            echo "Hiba történt a MySQL szerverhez való kapcsolódás közben: " . $th->getMessage();
            exit();
        }
    }

    public function StoreProcedure($name, $array, $host = null, $dataBase = null, $user = null, $pass = null, $port = 3306): array
    {

        if ($host !== null && $dataBase !== null && $user !== null && $pass !== null && $port !== null) {
            $this->connectToMysqlServerWithDatabase($host, $dataBase, $user, $pass, $port);
        }

        try {
            $paramKeys = array_keys($array);
            $paramPlaceholders = implode(', ', array_map(function ($key) {
                return ":$key";
            }, $paramKeys));

            $stmt = $this->pdo->prepare("CALL $name($paramPlaceholders)");

            foreach ($array as $key => $value) {
                $stmt->bindValue(":$key", $value, \PDO::PARAM_STR);
            }

            $stmt->execute();
            $resultSet = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return ReturnValue::SQLError(false, $resultSet);
        } catch (\PDOException $th) {

            // Hibakezelés, ha nem sikerült a kapcsolat
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, $th->getMessage()));

            // Hibakezelés kiírása és megszakítja a programot
            $res->build();
            echo "Hiba történt a MySQL szerverhez való kapcsolódás közben: " . $th->getMessage();
            exit();
        }
    }


    public function runSql($sqlCode)
    {
        try {
            // Előkészítjük a SQL lekérdezést
            $stmt = $this->pdo->prepare($sqlCode); 
    
            // A lekérdezés végrehajtása
            $stmt->execute();
    
            // Ha SELECT lekérdezésről van szó, akkor az eredményeket visszaadjuk
            if (stripos($sqlCode, 'SELECT') === 0) {
                return $stmt->fetchAll(\PDO::FETCH_ASSOC);  // Eredmények visszaadása asszociatív tömbként
            }
    
            // Ha nem SELECT lekérdezés, hanem például INSERT/UPDATE/DELETE, akkor visszatérünk a végrehajtott sorok számával
            return $stmt->rowCount();  // A módosított sorok száma
    
        } catch (\PDOException $th) {
            // Hibakezelés, ha nem sikerült a lekérdezés
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, $th->getMessage()));
            $res->build();
    
            echo "Hiba történt az SQL végrehajtása közben: " . $th->getMessage();
            exit();
        }
    }
    


    /**
     * Get the value of iniController
     */
    public function getIniController()
    {
        return $this->iniController;
    }

    /**
     * Set the value of iniController
     *
     * @return  self
     */
    public function setIniController($iniController)
    {
        $this->iniController = $iniController;

        return $this;
    }

    /**
     * Get the value of pdo
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     *
     * @return  self
     */
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;

        return $this;
    }
}

 
    // public  function connectToServerWithOutDatabase(string $host, string $userName, string $password): \PDO | array
    // {
    //     try {
    //         // DSN (Data Source Name): Adatbázis nélküli kapcsolat
    //         $dsn = "mysql:host=$host";

    //         // PDO objektum létrehozása
    //         $pdo = new \PDO($dsn, $userName, $password);

    //         // Hibakezelési mód beállítása
    //         $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    //         if (!$this->setFileBaseData("", $userName, $password, "3306", $host)) {
    //             throw new \iniException("Not found ini File");
    //         }

    //         return $pdo; // Sikeres kapcsolat esetén a PDO objektumot adja vissza
    //     } catch (\PDOException $e) {
    //         // Hiba esetén null-t ad vissza és lehetőség van a hiba naplózására
    //         error_log("Hiba az adatbázishoz való csatlakozás során: " . $e->getMessage());
    //         return ReturnValue::SQLError(true, ["data" => $e->getMessage()]);
    //     } catch (\iniException $th) {
    //         return ReturnValue::SQLError(true, ["data" => $th->getMessage()]);
    //     }
    // }

    // public  function connectToserverWithDatabase(
    //     string $dataBaseName,
    //     string $dataBaseUsername,
    //     string $dataBasePassword,
    //     string $dataBasePort,
    //     string $dataBaseURL
    // ): \PDO | null {
    //     if (isset(self::$pdo) || !empty(self::$pdo) || self::$pdo != null) {
    //         return self::$pdo;
    //     }else{
    //         $this->setFileBaseData( $dataBaseName,
    //          $dataBaseUsername,
    //          $dataBasePassword,
    //          $dataBasePort,
    //          $dataBaseURL);
    //     }

    //     $fileContent = self::getFile("fileBaseData");
    //     try {
    //         $dsn = "mysql:host=" . $fileContent['dataBaseURL'] . ";port=" . $fileContent['dataBasePort'] . ";dbname=" . $fileContent['dataBaseName'];
    //         $pdo = new PDO($dsn, $fileContent['dataBaseUsername'], $fileContent['dataBasePassword']);
    //         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //         if (empty($pdo)) {
    //             throw new \Exception("PDO is null");
    //         }
    //         self::$pdo = $pdo;
    //     } catch (\PDOException $th) {
    //         $res = Res::getInc();
    //         $res->setSqlError(ReturnValue::SQLError(true, ["err" => true, "data" => $th->getMessage()]));
    //         $res->setBody(ReturnValue::createReturnArray(true, []));
    //         $res->build();
    //         exit();
    //     }

    //     return $pdo;
    // }
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
