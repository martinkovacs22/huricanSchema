<?php

namespace Schema\Structure;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

class StructureDatabase{
    
    private string $databaseName;

    private array $tables  = [];

    public function __construct(string $databaseName,array|null $tables =null ) {
        $this->databaseName = $databaseName;
        if (!empty($table)) {
            $this->tables = $tables;
        }    
    }
    public function pushTableToDataBase(StructureTable $table):self{

        $arrayTablesAll = $this->getTables();

        array_push($arrayTablesAll,$table->toArray());     

        $this->setTables($arrayTablesAll);

        return $this;

    }

    public function toArray(){
        return[
            "databaseName"=>$this->databaseName,
            "Table"=>$this->getTables()
        ];
    }

    /**
     * Get the value of databaseName
     */ 
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Set the value of databaseName
     *
     * @return  self
     */ 
    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;

        return $this;
    }

    /**
     * Get the value of tables
     */ 
    public function getTables():array
    {
        return $this->tables;
    }

    /**
     * Set the value of tables
     *
     * @return  self
     */ 
    public function setTables($tables)
    {
        $this->tables = $tables;

        return $this;
    }
}

?>