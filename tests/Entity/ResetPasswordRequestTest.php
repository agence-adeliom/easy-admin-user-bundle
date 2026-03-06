<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Entity;

use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest::class)]
final class ResetPasswordRequestTest extends TestCase
{
    public function testResetPasswordRequestExposesUserAndTokenMetadata(): void
    {
        $user = (new User())->setEmail('ada@example.com');
        $expiresAt = new \DateTimeImmutable('+1 hour');
        $request = new ResetPasswordRequest($user, $expiresAt, 'selector', 'hashed-token');
        $selector = new \ReflectionProperty($request, 'selector');

        self::assertSame($user, $request->getUser());
        self::assertSame('selector', $selector->getValue($request));
        self::assertSame('hashed-token', $request->getHashedToken());
        self::assertSame($expiresAt, $request->getExpiresAt());
        self::assertInstanceOf(\DateTimeInterface::class, $request->getRequestedAt());
        self::assertFalse($request->isExpired());
    }
}
