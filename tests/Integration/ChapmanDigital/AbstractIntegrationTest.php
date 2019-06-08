<?php

namespace Tests\Integration\ChapmanDigital;

use ChapmanDigital\ActiveRecord\RecordProvider;
use ChapmanDigital\ActiveRecord\RecordProviderInterface;
use ChapmanDigital\Db\DbService;
use ChapmanDigital\Db\DbServiceInterface;
use ChapmanDigital\Db\DbType;
use ChapmanDigital\Demo\ActiveRecord\RecordFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractIntegrationTest extends TestCase
{
    /**
     * @throws \ChapmanDigital\Db\Exception\DbException
     * @throws \ReflectionException
     */
    public function getDbService(): DbServiceInterface
    {
        $dbType = new DbType(DbType::Postgres);
        $host = 'localhost';
        $port = 5432;
        $dbName = 'activerecord';
        $user = 'demo';
        $password = 'password123';

        return new DbService($dbType, $host, $port, $dbName, $user, $password);
    }

    /**
     * @throws \ChapmanDigital\Db\Exception\DbException
     * @throws \ReflectionException
     */
    public function getRecordFactory(): RecordFactory
    {
        return new RecordFactory($this->getRecordProvider());
    }

    /**
     * @throws \ChapmanDigital\Db\Exception\DbException
     * @throws \ReflectionException
     */
    private function getRecordProvider(): RecordProviderInterface
    {
        return new RecordProvider($this->getDbService());
    }
}