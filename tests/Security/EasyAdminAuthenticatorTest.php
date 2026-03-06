<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Security;

use Adeliom\EasyAdminUserBundle\Security\EasyAdminAuthenticator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Security\EasyAdminAuthenticator::class)]
final class EasyAdminAuthenticatorTest extends TestCase
{
    public function testAuthenticateBuildsPassportAndStoresLastUsername(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $authenticator = new TestableEasyAdminAuthenticator($urlGenerator);
        $session = new Session(new MockArraySessionStorage());
        $request = new Request([], ['email' => 'ada@example.com', 'password' => 'Secret123!'], [], [], [], []);
        $request->setSession($session);
        $request->attributes = new ParameterBag(['_csrf_token' => 'csrf-token']);

        $passport = $authenticator->authenticate($request);

        self::assertSame('ada@example.com', $session->get(SecurityRequestAttributes::LAST_USERNAME));
        self::assertInstanceOf(UserBadge::class, $passport->getBadge(UserBadge::class));
        self::assertSame('ada@example.com', $passport->getBadge(UserBadge::class)->getUserIdentifier());
        self::assertInstanceOf(PasswordCredentials::class, $passport->getBadge(PasswordCredentials::class));
        self::assertSame('Secret123!', $passport->getBadge(PasswordCredentials::class)?->getPassword());
        self::assertInstanceOf(CsrfTokenBadge::class, $passport->getBadge(CsrfTokenBadge::class));
    }

    public function testAuthenticationSuccessRedirectsToStoredTargetPathFirst(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::never())->method('generate');

        $authenticator = new TestableEasyAdminAuthenticator($urlGenerator);
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);
        $session->set('_security.main.target_path', '/admin/original-target');

        $response = $authenticator->onAuthenticationSuccess(
            $request,
            $this->createMock(TokenInterface::class),
            'main'
        );

        self::assertSame('/admin/original-target', $response?->getTargetUrl());
    }

    public function testAuthenticationSuccessFallsBackToAdminDashboard(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::once())
            ->method('generate')
            ->with('admin')
            ->willReturn('/admin');

        $authenticator = new TestableEasyAdminAuthenticator($urlGenerator);
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));

        $response = $authenticator->onAuthenticationSuccess(
            $request,
            $this->createMock(TokenInterface::class),
            'main'
        );

        self::assertSame('/admin', $response?->getTargetUrl());
    }

    public function testLoginUrlUsesConfiguredRoute(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects(self::once())
            ->method('generate')
            ->with(EasyAdminAuthenticator::LOGIN_ROUTE)
            ->willReturn('/admin/login');

        $authenticator = new TestableEasyAdminAuthenticator($urlGenerator);

        self::assertSame('/admin/login', $authenticator->exposeLoginUrl(new Request()));
    }
}

final class TestableEasyAdminAuthenticator extends EasyAdminAuthenticator
{
    public function exposeLoginUrl(Request $request): string
    {
        return $this->getLoginUrl($request);
    }
}
