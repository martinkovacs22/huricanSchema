<?php

namespace Schema\Generation;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

use Schema\Structure\StructureDatabase as Database;
use Schema\Structure\StructureTable as Table;
use Schema\Structure\StructureTableColumn as Column;



class Generation{

    public function __construct($pdo /*with out DataBase Connect just Server */,Database $database ){

        echo json_encode($database->toArray());

    } 

}

$database = new Database("HelloWORLD");
$table = new Table("User");

$columnArray = [];
$id = new Column();
$id->setField("id")->setType("int(11)");
$table->pushColumnToTable($id);
$name = new Column();
$name->setField("name")->setType("varchar(255)")->setNull("Yes");
$table->pushColumnToTable($name);

$database->pushTableToDataBase($table);

echo json_encode($database->toArray());



?>