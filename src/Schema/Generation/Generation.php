<?php

namespace Schema\Generation;

require_once __DIR__ . "/../../autoloader.php";
require_once __DIR__ . "/../../../vendor/autoload.php";

use Schema\Schema\SchemaControllerDatabase as Database;
use Schema\Schema\SchemaControllerTable as Table;
use Schema\Schema\SchemaControllerTableColumn as Column;
use Res\Res;
use ReturnValue\ReturnValue;
use Schema\Connect\ConnectMYSQL;

class Generation
{
    private \PDO $pdo;

    private Database $database;

    public function __construct($host, $user, $pass, Database $database)
    {
        $cm = new ConnectMYSQL();
        $cm->connectToMysqlServerWithOutDatabase($host, $user, $pass);

        try {
            $this->pdo = $cm->getPdo();
            $this->database = $database;

            
        } catch (\PDOException $th) {
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, ["data" => $th->getMessage()]));
            $res->setBody(ReturnValue::createReturnArray(true));
            $res->build();
            exit();
        }
    }



    // Function to generate and check database version
    public function generateDatabaseVersion(string $baseName): string
    {
        // Kezdjük az alapadatbázis névvel
        $databaseName = $baseName;
        $version = 0;

        // Ellenőrizzük, hogy létezik-e már az adatbázis, és ha igen, növeljük a verziót
        while ($this->databaseExists($databaseName)) {
            $version++;

            // Ha a verzió meghaladja a 9-et, kezdjük újra 2.0-ról
            if ($version > 9) {
                $version = 2;  // Kezdjük a verziószámozást 2.0-tól
            }

            // Generáljuk az új adatbázis nevet
            $databaseName = $baseName . " " . $version . ".0";
        }

        // Lekérjük a fájlból a többi adatot
        $cm = new ConnectMYSQL();
        $fileData = $cm->getIniController()->getFileContent();


        // Létrehozunk egy tömböt, amely tartalmazza az összes beolvasott adatot, és felülírjuk a dataBaseName értéket
        // $array = [
        //     "dataBaseName" => $databaseName,
        //     "dataBaseUsername" => $fileData['dataBaseUsername'] ?? "",
        //     "dataBasePassword" => $fileData['dataBasePassword'] ?? "",
        //     "dataBasePort" => $fileData['dataBasePort'] ?? "3306",
        //     "dataBaseURL" => $fileData['dataBaseURL'] ?? "localhost"
        // ];

        print_r($fileData);

        // Frissítjük a fájlt az új adatbázis névvel
        $cm->getIniController()->saveToFile($fileData);
        // Visszaadjuk az új adatbázis nevét
        return $databaseName;
    }

    // Check if the database already exists
    private function databaseExists(string $databaseName): bool
    {
        $stmt = $this->pdo->prepare("SHOW DATABASES LIKE :databaseName");
        $stmt->bindParam(':databaseName', $databaseName, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }


    // Generate the database schema SQL
    public  function GenerationDataBase(): array
    {
        try {
            $data = $this->database->toArray();

            // Database name
            $databaseName = $data['databaseName'];

            // SQL code generation
            $sql = [];
            $sql[] = "CREATE DATABASE IF NOT EXISTS `$databaseName`;";
            $sql[] = "USE `$databaseName`;";

            // Generate table and columns
            foreach ($data['Table'] as $table) {
                $tableName = $table['tableName'];
                $columns = $table['columnName'];
                $tableSQL = "CREATE TABLE `$tableName` (\n";
                $columnDefinitions = [];

                foreach ($columns as $column) {
                    $field = $column['Field'];
                    $type = $column['Type'];
                    $null = strtoupper($column['Null']) === "NOT" ? "NOT NULL" : "NULL";
                    $key = strtoupper($column['Key']);
                    $default = $column['Default'] !== null ? "DEFAULT " . (is_numeric($column['Default']) ? $column['Default'] : "'{$column['Default']}'") : "";
                    $extra = $column['Extra'];
                    $comment = !empty($column['Comment']) ? "COMMENT '{$column['Comment']}'" : "";

                    $columnSQL = "`$field` $type $null $default $extra $comment";
                    $columnDefinitions[] = trim($columnSQL);
                }

                $tableSQL .= implode(",\n", $columnDefinitions);

                // Handle primary keys
                $primaryKeys = array_filter($columns, function ($col) {
                    return strtoupper($col['Key']) === "PRI";
                });
                if (!empty($primaryKeys)) {
                    $primaryKeyFields = array_map(function ($col) {
                        return "`" . $col['Field'] . "`";
                    }, $primaryKeys);
                    $tableSQL .= ",\nPRIMARY KEY (" . implode(", ", $primaryKeyFields) . ")";
                }

                // Close the table SQL
                $tableSQL .= "\n);";
                $sql[] = $tableSQL;
            }

            return $sql;
        } catch (\Exception $e) {
            throw new \PDOException("SQL generation error: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get the value of database
     */ 
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set the value of database
     *
     * @return  self
     */ 
    public function setDatabase($database)
    {
        $this->database = $database;

        return $this;
    }
}

$cm = new ConnectMYSQL();
$cm->connectToMysqlServerWithOutDatabase("localhost", "root", "");
$database = new Database("testDatabase");
$table = new Table("testTable");
$column = new Column();
$column->setField("id")->setType("int(11");
$table->pushColumnToTable($column);
$database->pushTableToDataBase($table);
$gen = new Generation("localhost", "root", "", $database);
$gen->GenerationDataBase();
// $gen->generateDatabaseVersion();
