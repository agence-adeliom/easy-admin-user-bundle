<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    public function testCreateResetPasswordRequest(): void
    {
        $repo = $this->getMockBuilder(ResetPasswordRequestRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getClassName'])
            ->getMock();
        $repo->method('getClassName')->willReturn(ResetPasswordRequest::class);
        $user = new User();
        $expires = new DateTimeImmutable('+1 hour');

        $request = $repo->createResetPasswordRequest($user, $expires, 'sel', 'tok');

        self::assertInstanceOf(ResetPasswordRequest::class, $request);
        self::assertSame($user, $request->getUser());
    }

    public function testUpgradePassword(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);
        $repo = new UserRepository($registry, User::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $ref = new \ReflectionProperty(EntityRepository::class, '_em');
        $ref->setAccessible(true);
        $ref->setValue($repo, $em);

        $user = new User();
        $em->expects(self::once())->method('persist')->with($user);
        $em->expects(self::once())->method('flush');

        $repo->upgradePassword($user, 'new');
        self::assertSame('new', $user->getPassword());
    }
}
