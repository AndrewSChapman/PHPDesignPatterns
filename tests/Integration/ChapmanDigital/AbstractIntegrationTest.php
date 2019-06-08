<?php

namespace Tests\Integration\ChapmanDigital;

use ChapmanDigital\ActiveRecord\RecordProvider;
use ChapmanDigital\ActiveRecord\RecordProviderInterface;
use ChapmanDigital\Config\ConfigLoader;
use ChapmanDigital\Db\DbService;
use ChapmanDigital\Db\DbServiceInterface;
use ChapmanDigital\Demo\ActiveRecord\RecordFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractIntegrationTest extends TestCase
{
    /**
     * @throws \ChapmanDigital\Db\Exception\DbException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getDbService(): DbServiceInterface
    {
        $configLoader = new ConfigLoader();

        return new DbService(
            $configLoader->getDbType(),
            $configLoader->getDbHost(),
            $configLoader->getDbPort(),
            $configLoader->getDbName(),
            $configLoader->getDbUser(),
            $configLoader->getDbPass()
        );
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