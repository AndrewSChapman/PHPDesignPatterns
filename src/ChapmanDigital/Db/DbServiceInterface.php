<?php
namespace ChapmanDigital\Db;

interface DbServiceInterface
{
    public function execute(QueryDefinition $queryDefinition): void;
    public function getRow(QueryDefinition $queryDefinition): array;
}