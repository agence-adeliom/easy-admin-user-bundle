<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Repository;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository::class)]
final class ResetPasswordRequestRepositoryTest extends TestCase
{
    public function testCreateResetPasswordRequestBuildsConfiguredEntityClass(): void
    {
        $repository = $this->getMockBuilder(ResetPasswordRequestRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getClassName'])
            ->getMock();
        $repository->expects(self::once())
            ->method('getClassName')
            ->willReturn(ResetPasswordRequest::class);

        $request = $repository->createResetPasswordRequest(
            $user = (new User())->setEmail('ada@example.com'),
            $expiresAt = new \DateTimeImmutable('2026-03-06 12:00:00'),
            'selector',
            'hashed-token'
        );

        self::assertInstanceOf(ResetPasswordRequest::class, $request);
        self::assertSame($user, $request->getUser());
        self::assertSame('hashed-token', $request->getHashedToken());
        self::assertSame($expiresAt, $request->getExpiresAt());
    }
}
