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

    public function __construct(\PDO $pdo /* without DataBase Connect just Server */, Database $database)
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
            exit();
        }
    }

    private function getCurrentDatabase(): ?string
    {
        try {
            $query = $this->pdo->query("SELECT DATABASE()");
            $result = $query->fetchColumn();

            // Return null if no database is selected
            return $result ?: null;
        } catch (\PDOException $e) {
            // Log the error and return null if the database check fails
            error_log("Error checking database: " . $e->getMessage());
            return null;
        }
    }

    // Function to generate and check database version
    public function generateDatabaseVersion(string $baseName): string
    {
        // Start with base database name
        $databaseName = $baseName;
        $version = 0;

        // Check if the database exists, increment version if it does
        while ($this->databaseExists($databaseName)) {
            $version++;
            if ($version > 9) {
                // If the version exceeds 9, start from 2.0
                $version = 0;
                $baseName = "Recept";
            }
            // Generate new database name
            $databaseName = $baseName . " " . $version . ".0";
        }

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

    public function sendSQLCodeToServer($sql)
    {
        try {
            // Execute the SQL code
            $this->pdo->exec($sql);
            return true; // Return true if the execution is successful
        } catch (\PDOException $e) {
            $res = Res::getInc();
            $res->setSqlError(ReturnValue::SQLError(true, ["data" => $e->getMessage()]));
            $res->setBody(ReturnValue::createReturnArray(true));
            $res->build();
            exit();
        }
    }

    // Generate the database schema SQL
    public static function GenerationDataBase(Database $database): array
    {
        try {
            $data = $database->toArray();
            if (!$data) {
                throw new \InvalidArgumentException("Invalid JSON data!");
            }

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
}
