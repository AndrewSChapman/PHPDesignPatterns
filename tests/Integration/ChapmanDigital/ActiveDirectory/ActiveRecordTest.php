<?php
namespace Tests\Integration\ChapmanDigital\ActiveDirectory;

use ChapmanDigital\Demo\ActiveRecord\User\UserRecord;
use Ramsey\Uuid\UuidInterface;
use Tests\Integration\ChapmanDigital\AbstractIntegrationTest;

class ActiveRecordTest extends AbstractIntegrationTest
{
    public function testCanCreateNewUserAndThenUpdateAndThenLoadItBack()
    {
        try {
            $userRecord = new UserRecord($this->getDbService());
            $userRecord->email_address = 'andy@chapmandigital.co.uk';
            $userRecord->password = 'MyBigSecret';
            $userRecord->first_name = 'Andy';
            $userRecord->last_name = 'Chapman';
            $userRecord->save();

            $userRecord->first_name = 'Bob';
            $userRecord->save();

            $this->assertInstanceOf(UuidInterface::class, $userRecord->getId());

            $existingUser = $this->getRecordFactory()->findUserById($userRecord->getId());

            $this->assertEquals($userRecord->getId(), $existingUser->getId());
            $this->assertEquals($userRecord->first_name, $existingUser->first_name);
            $this->assertEquals($userRecord->last_name, $existingUser->last_name);
            $this->assertEquals($userRecord->email_address, $existingUser->email_address);
        } catch(\Exception $exception) {
            $this->assertTrue(false, 'An error occured: ' . $exception->getMessage());
        }
    }
}