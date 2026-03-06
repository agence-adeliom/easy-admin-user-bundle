<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\DependencyInjection;

use Adeliom\EasyAdminUserBundle\DependencyInjection\EasyAdminUserExtension;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Entity\TestResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Entity\TestUser;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Repository\TestResetPasswordRequestRepository;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Repository\TestUserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(\Adeliom\EasyAdminUserBundle\DependencyInjection\EasyAdminUserExtension::class)]
final class EasyAdminUserExtensionTest extends TestCase
{
    public function testExtensionLoadsParametersAndServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new EasyAdminUserExtension();

        $extension->load([[
            'user_class' => TestUser::class,
            'user_repository' => TestUserRepository::class,
            'reset_password_class' => TestResetPasswordRequest::class,
            'reset_password_repository' => TestResetPasswordRequestRepository::class,
            'reset_password' => [
                'from_address' => 'admin@example.com',
                'from_name' => 'Admin',
            ],
            'title' => 'Backoffice',
        ]], $container);

        self::assertSame('easy_admin_user', $extension->getAlias());
        self::assertSame(TestUser::class, $container->getParameter('easy_admin_user.user_class'));
        self::assertSame(TestUserRepository::class, $container->getParameter('easy_admin_user.user_repository'));
        self::assertSame(TestResetPasswordRequest::class, $container->getParameter('easy_admin_user.reset_password_class'));
        self::assertSame('admin@example.com', $container->getParameter('easy_admin_user.reset_password')['from_address']);
        self::assertTrue($container->hasDefinition('Adeliom\\EasyAdminUserBundle\\Command\\AddUserCommand'));
        self::assertTrue($container->hasDefinition('easy_admin_user.user_repository'));
        self::assertTrue($container->hasAlias('easy_admin_user.validator'));
    }

    public function testPrependRegistersTwigGlobals(): void
    {
        $container = new ContainerBuilder();
        $extension = new EasyAdminUserExtension();

        $container->prependExtensionConfig('easy_admin_user', [
            'user_class' => TestUser::class,
            'user_repository' => TestUserRepository::class,
            'reset_password_class' => TestResetPasswordRequest::class,
            'reset_password_repository' => TestResetPasswordRequestRepository::class,
            'reset_password' => [
                'from_address' => 'admin@example.com',
                'from_name' => 'Admin',
            ],
            'title' => 'Backoffice',
        ]);

        $extension->prepend($container);

        $twigConfig = $container->getExtensionConfig('twig')[0];

        self::assertSame('Backoffice', $twigConfig['globals']['easy_admin_user']['title']);
        self::assertSame(TestUser::class, $twigConfig['globals']['easy_admin_user']['user_class']);
        self::assertSame('admin@example.com', $twigConfig['globals']['easy_admin_user']['reset_password']['from_address']);
    }
}
