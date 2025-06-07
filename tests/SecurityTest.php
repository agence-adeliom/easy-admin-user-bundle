<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Adeliom\EasyAdminUserBundle\Security\EasyAdminAuthenticator;
use Adeliom\EasyAdminUserBundle\Security\EasyAdminUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class SecurityTest extends TestCase
{
    public function testAuthenticatorCreatesPassport(): void
    {
        $urlGen = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = new EasyAdminAuthenticator($urlGen);

        $request = new Request([], [
            'email' => 'foo@example.com',
            'password' => 'secret',
            '_csrf_token' => 'token',
        ]);
        $request->setSession(new \Symfony\Component\HttpFoundation\Session\Session(new \Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage()));
        $passport = $authenticator->authenticate($request);

        /** @var UserBadge $badge */
        $badge = $passport->getBadge(UserBadge::class);
        self::assertSame('foo@example.com', $badge->getUserIdentifier());
    }

    public function testUserProviderLoadsUser(): void
    {
        $user = new User();
        $user->setEmail('foo@example.com');

        $repo = $this->createMock(UserRepository::class);
        $repo->expects(self::once())->method('findOneBy')->with(['email' => 'foo@example.com'])->willReturn($user);

        $provider = new EasyAdminUserProvider($repo);

        $loaded = $provider->loadUserByIdentifier('foo@example.com');
        self::assertSame($user, $loaded);
    }
}
