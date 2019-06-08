<?php
namespace ChapmanDigital\ActiveRecord;

use Ramsey\Uuid\UuidInterface;

interface RecordProviderInterface
{
    public function findById(UuidInterface $id, string $className): object;
}