<?php
namespace ChapmanDigital\Config;

use ChapmanDigital\Db\DbType;
use ChapmanDigital\Db\Exception\DbException;

class ConfigLoader
{
    /** @var DbType|null */
    private $dbType = null;

    /** @var string */
    private $dbHost = '';

    /** @var int */
    private $dbPort = 5432;

    /** @var string */
    private $dbName = '';

    /** @var string */
    private $dbUser = '';

    /** @var string */
    private $dbPass = '';

    public function __construct($iniPath = '../../../../config.ini')
    {
        if (!file_exists($iniPath)) {
            throw new \Exception('ConfigLoader - Unable to find config.ini');
        }

        $reader = parse_ini_file('../../../../config.ini', true);

        if (!isset($reader['database'])) {
            throw new DbException('Database config missing in ini file');
        }

        if (($reader['database']['type'] ?? '') !== 'Postgres') {
            throw new DbException('Invalid Database Type provided in ini file: ' . $reader['database']['type']);
        }

        $this->dbType = new DbType(DbType::Postgres);
        $this->dbHost = $reader['database']['host'] ?? '';
        $this->dbPort = $reader['database']['port'] ?? 5432;
        $this->dbName = $reader['database']['dbName'] ?? '';
        $this->dbUser = $reader['database']['user'] ?? '';
        $this->dbPass = $reader['database']['password'] ?? '';
    }

    /**
     * @return DbType|null
     */
    public function getDbType(): ?DbType
    {
        return $this->dbType;
    }

    /**
     * @return string
     */
    public function getDbHost(): string
    {
        return $this->dbHost;
    }

    /**
     * @return int
     */
    public function getDbPort(): int
    {
        return $this->dbPort;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @return string
     */
    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    /**
     * @return string
     */
    public function getDbPass(): string
    {
        return $this->dbPass;
    }
}