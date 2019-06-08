<?php

namespace ChapmanDigital\Demo\ActiveRecord\User;

use ChapmanDigital\ActiveRecord\AbstractRecord;

/**
 * Reads and writes from the "user" db table
 */
class UserRecord extends AbstractRecord
{
    public $email_address = '';
    public $password = '';
    public $first_name = '';
    public $last_name = '';

    public static function getTableName(): string
    {
        return 'user';
    }
}