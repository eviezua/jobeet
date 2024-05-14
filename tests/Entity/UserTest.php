<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @group Entity
 * @group user
 */
class UserTest extends TestCase
{
    public function testUserEntity(): void
    {
        $user = new User();

        $user->setUsername('test');
        $user->setPassword('test');
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertEquals('test', $user->getUsername());
        $this->assertEquals('test', $user->getPassword());
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }
}
