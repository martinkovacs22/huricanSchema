<?php

namespace Schema\Schema;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

use PDO;
use PDOException;

class SchemaController
{


    public static function setUpSchemaController($dbhost,$dbName,$port,$user,$pass){


        try {

            $databaseSchema = new SchemaControllerDatabase(databaseName: $dbName);



            // PDO kapcsolat létrehozása
            $dsn = "mysql:host=$dbhost;dbname=$dbName;port=$port";
            $username = $user;
            $password = $pass;
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Táblák lekérdezése
            $sql = "SHOW TABLES";
            $stmt1 = $pdo->query($sql);

            
            while ($tableRow = $stmt1->fetch(PDO::FETCH_NUM)) {
                

                // Tábla kiválasztása
                $tableName = $tableRow[0];
                $tableSchema = new SchemaControllerTable($tableRow[0]);  
                // Oszlopok lekérdezése
                $sql1 = "DESCRIBE `$tableName`";
                $stmt = $pdo->query($sql1);

                
                while ($columnRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    //{"Field":"id","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment"}
   
                    

                    //echo json_encode($columnRow);

                    $ColumnSchema = new SchemaControllerTableColumn(!empty($columnRow["Field"])?$columnRow["Field"]:"",!empty($columnRow["Type"])?$columnRow["Type"]:"",!empty($columnRow["Collation"])?$columnRow["Collation"]:"",!empty($columnRow["Null"])?$columnRow["Null"]:"Yes",!empty($columnRow["Key"])?$columnRow["Key"]:"",!empty($columnRow["Default"])?$columnRow["Default"]:null,!empty($columnRow["Extra"])?$columnRow["Extra"]:"",!empty($columnRow["Comment"])?$columnRow["Comment"]:"");
                    $tableSchema->pushColumnToTable($ColumnSchema);
                    
                    
                    

                 
                }
                $databaseSchema->pushTableToDataBase($tableSchema);
            }
             print_r($databaseSchema->toArray());
        } catch (PDOException $e) {
            echo "Hiba történt: " . $e->getMessage();
        }

    }  
}
//SchemaController::setUpSchemaController("localhost","receptbook","3306","root","");
