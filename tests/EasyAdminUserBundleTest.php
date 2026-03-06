<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\DependencyInjection\EasyAdminUserExtension;
use Adeliom\EasyAdminUserBundle\EasyAdminUserBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(\Adeliom\EasyAdminUserBundle\EasyAdminUserBundle::class)]
final class EasyAdminUserBundleTest extends TestCase
{
    public function testBundleReturnsExpectedContainerExtension(): void
    {
        $bundle = new EasyAdminUserBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(EasyAdminUserExtension::class, $extension);
        self::assertSame('easy_admin_user', $extension?->getAlias());
    }
}
