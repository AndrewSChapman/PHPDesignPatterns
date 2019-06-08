<?php
namespace ChapmanDigital\Demo\ActiveRecord;

use ChapmanDigital\ActiveRecord\RecordProviderInterface;
use ChapmanDigital\Demo\ActiveRecord\User\UserRecord;
use Ramsey\Uuid\UuidInterface;

class RecordFactory
{
    private $provider;

    public function __construct(RecordProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function findUserById(UuidInterface $id): UserRecord
    {
        return $this->provider->findById($id, UserRecord::class);
    }
}