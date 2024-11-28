<?php

namespace Schema\Generation;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

use Schema\Schema\SchemaControllerDatabase as Database;
use Schema\Schema\SchemaControllerTable as Table;
use Schema\Schema\SchemaControllerTableColumn as Column;



class Generation{

    public function __construct($pdo /*with out DataBase Connect just Server */,Database $database ){

        echo json_encode($database->toArray());

    } 

    public static function GenerationDataBase(Database $database){
        

// Dekódoljuk a JSON-t PHP tömbbé
$data = $database->toArray();

if (!$data) {
    die("Érvénytelen JSON adat!");
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

// SQL kódok megjelenítése
foreach ($sql as $query) {
    echo $query . "\n\n";
}

    }


}

$database = new Database("HelloWORLD");
$table = new Table("User");

$columnArray = [];
$id = new Column();
$id->setField("id")->setType("int(11)")->setKey("PRI");
$table->pushColumnToTable($id);
$name = new Column();
$name->setField("name")->setType("varchar(255)")->setNull("Yes");
$table->pushColumnToTable($name);

$database->pushTableToDataBase($table);

Generation::GenerationDataBase($database);

//echo json_encode($database->toArray());



?>