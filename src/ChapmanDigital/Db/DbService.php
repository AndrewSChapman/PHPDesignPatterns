<?php
namespace ChapmanDigital\Db;

use ChapmanDigital\Db\Exception\DbConnectionException;
use ChapmanDigital\Db\Exception\DbException;
use ChapmanDigital\Db\Exception\RecordNotFoundException;

/***
 * Wraps PDO to provide basic database functionality.
 * Note, currently only Postgres is supported but adding
 * other DBs will be trivial.
 */
class DbService implements DbServiceInterface
{
    /** @var \PDO|null */
    private $pdo = null;

    /** @var string */
    private $connStr = '';

    /**
     * Db constructor.
     * @throws DbException
     */
    public function __construct(
        DbType $dbType,
        string $host,
        int $port,
        string $dbname,
        string $username,
        string $password
    ) {
        switch ($dbType) {
            case DbType::Postgres:
                $this->connStr = "pgsql:host=$host;port=$port;dbname=$dbname;user=$username;password=$password";
                break;

            default:
                throw new DbException("Unhandled DbType: $dbType");
                break;
        }
    }

    /**
     * @throws DbConnectionException
     * @throws DbException
     */
    public function execute(QueryDefinition $queryDefinition): void
    {
        $this->connect();

        $args = $queryDefinition->getArgs();
        $stmt = $this->pdo->prepare($queryDefinition->getSql());

        if ($stmt->execute($args) === false) {
            throw new DbException('Db::Exec - Query execution failed!  Reason: ' . $this->pdo->errorCode());
        }
    }

    /**
     * @throws DbConnectionException
     * @throws DbException
     */
    public function getRow(QueryDefinition $queryDefinition): array
    {
        $this->connect();

        $args = $queryDefinition->getArgs();
        $stmt = $this->pdo->prepare($queryDefinition->getSql());

        if ($stmt->execute($args) === false) {
            throw new DbException('Db::Exec - Query execution failed!  Reason: ' . $this->pdo->errorCode());
        }

        if ($stmt->rowCount() === 0) {
            throw new RecordNotFoundException();
        }

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @throws DbConnectionException
     */
    private function connect(): void
    {
        if ($this->pdo) {
            return;
        }

        try {
            $this->pdo = new \PDO($this->connStr);
        } catch (\PDOException $e) {
            throw new DbConnectionException(
                'Connection to database failed: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}