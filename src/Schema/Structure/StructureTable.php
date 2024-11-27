<?php

namespace Schema\Structure;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

class StructureTable{

    private string $tableName = "";
    private array $columnArrays = [];

    public function __construct(string $tableName) {
        $this->tableName = $tableName;
    }

    public function pushColumnToTable(StructureTableColumn $column):self{

        $arrayColumnAll = $this->getColumnArrays();

        array_push($arrayColumnAll,$column->toArray());     

        $this->setColumnArrays($arrayColumnAll);

        return $this;

    }

    public function toArray(){
        return [
            "tableName"=>$this->getTableName(),
            "columnName"=>$this->getColumnArrays()
        ];
    }


    /**
     * Get the value of tableName
     */ 
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set the value of tableName
     *
     * @return  self
     */ 
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get the value of columnArrays
     */ 
    public function getColumnArrays()
    {
        return $this->columnArrays;
    }

    /**
     * Set the value of columnArrays
     *
     * @return  self
     */ 
    public function setColumnArrays($columnArrays)
    {
        $this->columnArrays = $columnArrays;

        return $this;
    }
}

?>