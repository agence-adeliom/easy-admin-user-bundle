<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\DependencyInjection;

use Adeliom\EasyAdminUserBundle\DependencyInjection\Configuration;
use Adeliom\EasyAdminUserBundle\Entity\ResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\ResetPasswordRequestRepository;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Entity\TestResetPasswordRequest;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Entity\TestUser;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Repository\TestResetPasswordRequestRepository;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Repository\TestUserRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

#[CoversClass(\Adeliom\EasyAdminUserBundle\DependencyInjection\Configuration::class)]
final class ConfigurationTest extends TestCase
{
    public function testConfigurationAppliesDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[]]);

        self::assertSame(User::class, $config['user_class']);
        self::assertSame(UserRepository::class, $config['user_repository']);
        self::assertSame(ResetPasswordRequest::class, $config['reset_password_class']);
        self::assertSame(ResetPasswordRequestRepository::class, $config['reset_password_repository']);
        self::assertSame('no-reply@example.com', $config['reset_password']['from_address']);
        self::assertSame('John Doe', $config['reset_password']['from_name']);
        self::assertSame('ACME', $config['title']);
    }

    public function testConfigurationAcceptsCustomClasses(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), [[
            'user_class' => TestUser::class,
            'user_repository' => TestUserRepository::class,
            'reset_password_class' => TestResetPasswordRequest::class,
            'reset_password_repository' => TestResetPasswordRequestRepository::class,
            'reset_password' => [
                'from_address' => 'admin@example.com',
                'from_name' => 'Admin',
            ],
            'title' => 'Backoffice',
        ]]);

        self::assertSame(TestUser::class, $config['user_class']);
        self::assertSame(TestUserRepository::class, $config['user_repository']);
        self::assertSame(TestResetPasswordRequest::class, $config['reset_password_class']);
        self::assertSame(TestResetPasswordRequestRepository::class, $config['reset_password_repository']);
        self::assertSame('admin@example.com', $config['reset_password']['from_address']);
        self::assertSame('Admin', $config['reset_password']['from_name']);
        self::assertSame('Backoffice', $config['title']);
    }

    public function testConfigurationRejectsInvalidUserClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), [[
            'user_class' => \stdClass::class,
        ]]);
    }

    public function testConfigurationRejectsInvalidResetPasswordRepository(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new Processor())->processConfiguration(new Configuration(), [[
            'reset_password_repository' => \stdClass::class,
        ]]);
    }
}
