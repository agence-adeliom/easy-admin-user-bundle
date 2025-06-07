<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');
        self::assertResponseIsSuccessful();
    }

    public function testCheckEmailPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/reset-password/check-email');
        self::assertResponseIsSuccessful();
    }
}
