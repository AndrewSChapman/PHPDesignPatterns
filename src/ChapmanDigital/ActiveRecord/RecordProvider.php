<?php
/**
 * Created by PhpStorm.
 * User: andre
 * Date: 08/06/2019
 * Time: 11:16
 */

namespace ChapmanDigital\ActiveRecord;

use ChapmanDigital\Db\DbServiceInterface;
use ChapmanDigital\Db\Exception\InvalidRecordException;
use ChapmanDigital\Db\QueryDefinition;
use Ramsey\Uuid\UuidInterface;

/**
 * Provides a means to load individual Records from the database
 * See the RecordFactory class which can be used to load
 * and return strongly typed ActiveRecord instances.
 */
final class RecordProvider implements RecordProviderInterface
{
    /** @var DbServiceInterface */
    private $db;

    public function __construct(DbServiceInterface $db)
    {
        $this->db = $db;
    }

    /**
     * @throws InvalidRecordException
     * @throws \ReflectionException
     */
    public function findById(UuidInterface $id, string $className): object
    {
        $sql = <<<SQL
        SELECT *
        FROM "%s"
        WHERE "id" = ?::uuid
SQL;

        $queryDef = new QueryDefinition(
            sprintf($sql, $className::getTableName()),
            [$id->toString()]
        );

        $row = $this->db->getRow($queryDef);

        return new $className($this->db, $row);
    }
}