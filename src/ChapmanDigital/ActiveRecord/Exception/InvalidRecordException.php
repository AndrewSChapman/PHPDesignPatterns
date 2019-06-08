<?php
namespace ChapmanDigital\Db\Exception;

use Throwable;

class InvalidRecordException extends \Exception
{
    public function __construct(string $className, string $reason)
    {
        parent::__construct("The class '$className' appears to be invalid: $reason", 0);
    }
}