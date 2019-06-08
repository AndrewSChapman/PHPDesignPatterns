<?php

namespace ChapmanDigital\ActiveRecord;

use ChapmanDigital\Db\DbServiceInterface;
use ChapmanDigital\Db\Exception\InvalidRecordException;
use ChapmanDigital\Db\QueryDefinition;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Implements the ActiveRecord pattern.
 * To create a record class, simply extend this class
 * and add your properties to it as public properties.
 * See the UserRecord class as an example.
 */
abstract class AbstractRecord
{
    /** @var DbServiceInterface */
    private $db;

    /** @var array */
    private $properties = [];

    /** @var string */
    private $id = '';

    /** @var int|null */
    private $created_dtm = 0;

    /** @var int */
    private $updated_dtm = '';

    /**
     * @throws InvalidRecordException
     * @throws \ReflectionException
     */
    public function __construct(DbServiceInterface $db, $columnValues = [])
    {
        $this->db = $db;

        if (!method_exists($this, 'getTableName')) {
            throw new InvalidRecordException(static::class, 'getTableMethod is missing and must be implemented');
        }

        if (!empty($columnValues)) {
            $this->setFromValues($columnValues);
        }
    }

    public function getId(): UuidInterface
    {
        return Uuid::fromString($this->id);
    }

    /**
     * @throws \ChapmanDigital\Db\Exception\DbConnectionException
     * @throws \ReflectionException
     */
    public function save()
    {
        if (!empty($this->id)) {
            $this->update();
        } else {
            $this->insert();
        }
    }

    /**
     * @throws InvalidRecordException
     * @throws \ReflectionException
     */
    private function setFromValues(array $columnValues): void
    {
        if (!isset($columnValues['id'])) {
            throw new InvalidRecordException(static::class, 'id column not provided in column values');
        }

        if (!isset($columnValues['created_dtm'])) {
            throw new InvalidRecordException(static::class, 'created_dtm column not provided in column values');
        }

        if (!isset($columnValues['updated_dtm'])) {
            throw new InvalidRecordException(static::class, 'updated_dtm column not provided in column values');
        }



        $this->id = $columnValues['id'];
        $this->created_dtm = $columnValues['created_dtm'];
        $this->updated_dtm = $columnValues['updated_dtm'];

        $this->populateProperties();

        /** @var \ReflectionProperty $property */
        foreach ($this->properties as $property) {
            $propertyName = $property->getName();

            if (isset($columnValues[$propertyName])) {
                $this->$propertyName = $columnValues[$propertyName];
            }
        }
    }

    /**
     * @throws \ChapmanDigital\Db\Exception\DbConnectionException
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function insert()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->created_dtm = time();
        $this->updated_dtm = time();

        $this->populateProperties();

        $columnString = '"id", "created_dtm", "updated_dtm"';
        $valueString = '?::uuid, ?, ?';
        $index = 0;
        $values = [$this->id, $this->timestampToDate($this->created_dtm), $this->timestampToDate($this->updated_dtm)];

        foreach ($this->properties as $property) {
            $property_name = $property->getName();
            if (($property_name === 'table_name') || ($property_name === 'properties')) {
                continue;
            }

            $values[] = $this->$property_name;
            $columnString .= ", \"$property_name\"";
            $valueString .= ', ?';

            $index++;
        }

        $sql = sprintf('INSERT INTO "' . static::getTableName() . '" (%s) VALUES(%s)', $columnString, $valueString);

        $this->db->execute(new QueryDefinition($sql, $values));
    }

    /**
     * @throws \ChapmanDigital\Db\Exception\DbConnectionException
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function update()
    {
        $this->updated_dtm = time();

        $this->populateProperties();

        $index = 0;

        $columnString = 'updated_dtm = ?';
        $values = [$this->timestampToDate($this->updated_dtm)];

        foreach ($this->properties as $property) {
            $property_name = $property->getName();
            if (($property_name === 'table_name') || ($property_name === 'properties')) {
                continue;
            }

            $values[] = $this->$property_name;
            $columnString .= ", \"$property_name\" = ?";

            $index++;
        }

        $values[] = $this->id;

        $sql = sprintf('UPDATE "' . $this->getTableName() . '" SET %s WHERE id = ?::uuid', $columnString);

        $this->db->execute(new QueryDefinition($sql, $values));
    }

    private function timestampToDate(int $timestamp): string
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    /**
     * @throws \ReflectionException
     */
    private function populateProperties()
    {
        if (!empty($this->properties)) {
            return;
        }

        $reflection = new \ReflectionClass($this);
        $this->properties = $reflection->getProperties();
    }
}