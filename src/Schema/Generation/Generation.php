<?php

namespace Schema\Generation;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

use Schema\Schema\SchemaControllerDatabase as Database;
use Schema\Schema\SchemaControllerTable as Table;
use Schema\Schema\SchemaControllerTableColumn as Column;
use Res\Res;
use ReturnValue\ReturnValue;

class Generation
{

    private \PDO $pdo;

    public function __construct(\PDO $pdo /*with out DataBase Connect just Server */, Database $database)
    {


        try {
            $this->pdo = $pdo;
            $currentDatabase = $this->getCurrentDatabase();

            if ($currentDatabase !== null) {
                throw new \PDOException("Connect Database is meen you can create new Database");
            }
        } catch (\PDOException $th) {

            $res = Res::getInc();

            $res->setSqlError(ReturnValue::SQLError(true, ["data" => $th->getMessage()]));

            $res->setBody(ReturnValue::createReturnArray(true));

            $res->build();
        }
    }

    private function getCurrentDatabase(): ?string
    {
        try {
            $query = $this->pdo->query("SELECT DATABASE()");
            $result = $query->fetchColumn();

            // Ha nincs adatbázis, akkor a lekérdezés NULL-t ad vissza
            return $result ?: null;
        } catch (\PDOException $e) {
            // Hiba esetén logoljuk és null-t adunk vissza
            error_log("Hiba az adatbázis ellenőrzése során: " . $e->getMessage());
            return null;
        }
    }

    public static function GenerationDataBase(Database $database): array 
    {
        try {
            // Dekódoljuk a JSON-t PHP tömbbé
            $data = $database->toArray();

            if (!$data) {
                throw new \InvalidArgumentException("Érvénytelen JSON adat!");
            }

            // Adatbázis neve
            $databaseName = $data['databaseName'];

            // SQL kód generálás
            $sql = [];

            // 1. Adatbázis létrehozása
            $sql[] = "CREATE DATABASE IF NOT EXISTS `$databaseName`;";
            $sql[] = "USE `$databaseName`;";

            // 2. Táblák és oszlopok létrehozása
            foreach ($data['Table'] as $table) {
                $tableName = $table['tableName'];
                $columns = $table['columnName'];

                // Tábla SQL kezdete
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

                    // Oszlop SQL generálása
                    $columnSQL = "`$field` $type $null $default $extra $comment";
                    $columnDefinitions[] = trim($columnSQL);
                }

                // Oszlopokat hozzáadjuk a táblához
                $tableSQL .= implode(",\n", $columnDefinitions);

                // Elsődleges kulcsok kezelése (ha van)
                $primaryKeys = array_filter($columns, function ($col) {
                    return strtoupper($col['Key']) === "PRI";
                });
                if (!empty($primaryKeys)) {
                    $primaryKeyFields = array_map(function ($col) {
                        return "`" . $col['Field'] . "`";
                    }, $primaryKeys);
                    $tableSQL .= ",\nPRIMARY KEY (" . implode(", ", $primaryKeyFields) . ")";
                }

                // Tábla SQL zárása
                $tableSQL .= "\n);";
                $sql[] = $tableSQL;
            }

            return $sql;
        } catch (\Exception $e) {
            // Ha hiba történik, dobjunk PDOException-t a részletekkel
            throw new \PDOException("SQL generálási hiba: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }
}

// $database = new Database("HelloWORLD");
// $table = new Table("User");

// $columnArray = [];
// $id = new Column();
// $id->setField("id")->setType("int(11)")->setKey("PRI");
// $table->pushColumnToTable($id);
// $name = new Column();
// $name->setField("name")->setType("varchar(255)")->setNull("Yes");
// $table->pushColumnToTable($name);

// $database->pushTableToDataBase($table);

// Generation::GenerationDataBase($database);

//echo json_encode($database->toArray());
