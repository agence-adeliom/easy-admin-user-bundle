<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Entity;

use Adeliom\EasyAdminUserBundle\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Entity\User::class)]
final class UserTest extends TestCase
{
    public function testUserStoresIdentityAndRoles(): void
    {
        $user = new User();
        $user->setFirstname('Ada');
        $user->setLastname('Lovelace');
        $user->setEmail('ada@example.com');
        $user->setEnabled(false);
        $user->setRoles([User::ADMIN]);
        $user->setPlainPassword('Secret123!');
        $user->setPassword('hashed');

        self::assertSame('Ada', $user->getFirstname());
        self::assertSame('Lovelace', $user->getLastname());
        self::assertSame('Ada Lovelace', $user->getFullname());
        self::assertSame('ada@example.com', $user->getEmail());
        self::assertSame('ada@example.com', $user->getUserIdentifier());
        self::assertSame('ada@example.com', $user->getUsername());
        self::assertSame([User::ADMIN, 'ROLE_USER'], $user->getRoles());
        self::assertSame('hashed', $user->getPassword());
        self::assertFalse($user->isEnabled());
        self::assertNull($user->getPlainPassword());
        self::assertNull($user->getSalt());
    }

    public function testEraseCredentialsClearsPlainPassword(): void
    {
        $user = new User();
        $user->setPlainPassword('Secret123!');
        $user->eraseCredentials();

        self::assertNull($user->getPlainPassword());
    }
}
