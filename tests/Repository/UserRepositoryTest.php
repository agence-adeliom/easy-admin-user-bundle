<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Repository;

use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Repository\UserRepository::class)]
final class UserRepositoryTest extends TestCase
{
    public function testUpgradePasswordPersistsSupportedUser(): void
    {
        $repository = $this->createRepositoryWithEntityManager($entityManager = $this->createMock(EntityManagerInterface::class));
        $user = (new User())->setEmail('ada@example.com');

        $entityManager->expects(self::once())->method('persist')->with($user);
        $entityManager->expects(self::once())->method('flush');

        $repository->upgradePassword($user, 'new-hash');

        self::assertSame('new-hash', $user->getPassword());
    }

    public function testUpgradePasswordRejectsUnsupportedUsers(): void
    {
        $repository = $this->createRepositoryWithEntityManager($this->createMock(EntityManagerInterface::class));

        $this->expectException(UnsupportedUserException::class);

        $repository->upgradePassword($this->createMock(PasswordAuthenticatedUserInterface::class), 'new-hash');
    }

    private function createRepositoryWithEntityManager(EntityManagerInterface $entityManager): UserRepository
    {
        $repository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEntityManager'])
            ->getMock();
        $repository->method('getEntityManager')->willReturn($entityManager);

        return $repository;
    }
}
