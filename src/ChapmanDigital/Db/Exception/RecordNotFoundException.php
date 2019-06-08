<?php
namespace ChapmanDigital\Db\Exception;

use Ramsey\Uuid\Uuid;

class RecordNotFoundException extends DbException
{
    public function __construct()
    {
        parent::__construct("Could not find record");
    }
}