<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Utils\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;

class ValidatorTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public function testValidateEmail(): void
    {
        self::assertSame('test@example.com', $this->validator->validateEmail('test@example.com'));
    }

    public function testValidateEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validateEmail('invalid');
    }

    public function testValidatePassword(): void
    {
        self::assertSame('secret1', $this->validator->validatePassword('secret1'));
    }

    public function testValidatePasswordThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->validator->validatePassword('bad');
    }
}
