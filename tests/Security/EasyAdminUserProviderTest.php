<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Security;

use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Adeliom\EasyAdminUserBundle\Security\EasyAdminUserProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Security\EasyAdminUserProvider::class)]
final class EasyAdminUserProviderTest extends TestCase
{
    public function testLoadUserByIdentifierReturnsRepositoryMatch(): void
    {
        $user = (new User())->setEmail('ada@example.com');
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'ada@example.com'])
            ->willReturn($user);

        $provider = new EasyAdminUserProvider($repository);

        self::assertSame($user, $provider->loadUserByIdentifier('ada@example.com'));
    }

    public function testLoadUserByIdentifierThrowsWhenRepositoryDoesNotReturnUser(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => 'missing@example.com'])
            ->willReturn(null);

        $provider = new EasyAdminUserProvider($repository);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User "missing@example.com" not found.');

        $provider->loadUserByIdentifier('missing@example.com');
    }

    public function testRefreshUserAndLegacyUsernameLoaderReuseIdentifierLookup(): void
    {
        $user = (new User())->setEmail('ada@example.com');
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::exactly(2))
            ->method('findOneBy')
            ->with(['email' => 'ada@example.com'])
            ->willReturn($user);

        $provider = new EasyAdminUserProvider($repository);

        self::assertSame($user, $provider->refreshUser($user));
        self::assertSame($user, $provider->loadUserByUsername('ada@example.com'));
    }

    public function testSupportsClassMatchesBaseUserAndSubclasses(): void
    {
        $provider = new EasyAdminUserProvider($this->createMock(UserRepository::class));

        self::assertTrue($provider->supportsClass(User::class));
        self::assertTrue($provider->supportsClass(TestUser::class));
        self::assertFalse($provider->supportsClass(\stdClass::class));
    }

    public function testUpgradePasswordDelegatesToRepository(): void
    {
        $user = (new User())->setEmail('ada@example.com');
        $repository = $this->createMock(UserRepository::class);
        $repository->expects(self::once())
            ->method('upgradePassword')
            ->with($user, 'new-hash');

        $provider = new EasyAdminUserProvider($repository);

        $provider->upgradePassword($user, 'new-hash');
    }
}

final class TestUser extends User
{
}
