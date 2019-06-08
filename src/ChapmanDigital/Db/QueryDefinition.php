<?php
namespace ChapmanDigital\Db;

/**
 * A basic value object to encapsulate an SQL
 * query the the required parameters for that query.
 */
class QueryDefinition
{
    private $sql = '';
    private $args = [];

    public function __construct($sql, $args)
    {
        $this->sql = $sql;
        $this->args = $args;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}