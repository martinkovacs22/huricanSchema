<?php

namespace Schema\Structure;

require_once __DIR__ . "/../../autoloader.php";

require_once __DIR__ . "/../../../vendor/autoload.php";

class StructureTableColumn
{
    // Attribútumok
    private $field;
    private $type;
    private $collation;
    private $null;
    private $key;
    private $default;
    private $extra;
    private $comment;

    // Konstruktor a táblázat adatainak inicializálásához
    public function __construct(
        string $field = "",
        string $type = "",
        string $collation = "",
        string $null = "YES",
        string $key = "",
        $default = null,
        string $extra = "",
        string $comment = ""
    ) {
        $this->field = $field;
        $this->type = $type;
        $this->collation = $collation;
        $this->null = $null;
        $this->key = $key;
        $this->default = $default;
        $this->extra = $extra;
        $this->comment = $comment;
    }

    public function toArray(): array
    {
        return [
            "Field" => $this->getField(),
            "Type" => $this->getType(),
            "Null" => $this->getNull(),
            "Key" => $this->getKey(),
            "Default" => $this->getDefault(),
            "Extra" => $this->getExtra(),
            "Comment" => $this->getComment()
        ];
    }


    // Getter és Setter a 'field' attribútumhoz
    public function getField(): string
    {
        return $this->field;
    }

    public function setField(string $field):self
    {
        $this->field = $field;
        return $this;
    }

    // Getter és Setter a 'type' attribútumhoz
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type):self
    {
        $this->type = $type;
        return $this;
    }

    // Getter és Setter a 'collation' attribútumhoz
    public function getCollation(): string
    {
        return $this->collation;
    }

    public function setCollation(string $collation):self
    {
        $this->collation = $collation;
        return $this;
    }

    // Getter és Setter a 'null' attribútumhoz
    public function getNull(): string
    {
        return $this->null;
    }

    public function setNull(string $null):self
    {
        $this->null = $null;
        return $this;
    }

    // Getter és Setter a 'key' attribútumhoz
    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key):self
    {
        $this->key = $key;
        return $this;
    }

    // Getter és Setter az 'default' attribútumhoz
    public function getDefault()
    {
        return $this->default;
    }

    public function setDefault($default):self
    {
        $this->default = $default;
        return $this;
    }

    // Getter és Setter az 'extra' attribútumhoz
    public function getExtra(): string
    {
        return $this->extra;
    }

    public function setExtra(string $extra):self
    {
        $this->extra = $extra;
        return $this;
    }

    // Getter és Setter a 'comment' attribútumhoz
    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment):self
    {
        $this->comment = $comment;
        return $this;
    }

    // A táblázat struktúrájának kiírása
    public function displayStructure(): void
    {
        echo "Field: " . $this->getField() . "<br>";
        echo "Type: " . $this->getType() . "<br>";
        echo "Collation: " . $this->getCollation() . "<br>";
        echo "Null: " . $this->getNull() . "<br>";
        echo "Key: " . $this->getKey() . "<br>";
        echo "Default: " . (is_null($this->getDefault()) ? 'NULL' : $this->getDefault()) . "<br>";
        echo "Extra: " . $this->getExtra() . "<br>";
        echo "Comment: " . $this->getComment() . "<br>";
    }
}
