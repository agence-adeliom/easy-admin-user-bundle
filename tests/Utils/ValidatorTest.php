<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Utils;

use Adeliom\EasyAdminUserBundle\Utils\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\InvalidArgumentException;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Utils\Validator::class)]
final class ValidatorTest extends TestCase
{
    public function testValidatorAcceptsExpectedValues(): void
    {
        $validator = new Validator();

        self::assertSame('valid_name', $validator->validateUsername('valid_name'));
        self::assertSame('secret1', $validator->validatePassword('secret1'));
        self::assertSame('ada@example.com', $validator->validateEmail('ada@example.com'));
        self::assertSame('Ada Lovelace', $validator->validateFullName('Ada Lovelace'));
    }

    #[DataProvider('provideInvalidEmail')]
    public function testValidatorRejectsInvalidEmail(?string $email): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Validator())->validateEmail($email);
    }

    #[DataProvider('provideInvalidPassword')]
    public function testValidatorRejectsInvalidPassword(?string $password): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Validator())->validatePassword($password);
    }

    #[DataProvider('provideInvalidUsername')]
    public function testValidatorRejectsInvalidUsername(?string $username): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Validator())->validateUsername($username);
    }

    #[DataProvider('provideInvalidFullname')]
    public function testValidatorRejectsInvalidFullname(?string $fullname): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Validator())->validateFullname($fullname);
    }

    public static function provideInvalidEmail(): iterable
    {
        yield [null];
        yield ['0'];
        yield [''];
        yield ['invalid-email'];
    }

    public static function provideInvalidPassword(): iterable
    {
        yield [null];
        yield ['0'];
        yield [''];
        yield ['short'];
    }

    public static function provideInvalidUsername(): iterable
    {
        yield [null];
        yield ['0'];
        yield [''];
        yield ['#a'];
        yield ['?a'];
        yield ['!a'];
        yield ['a.'];
    }

    public static function provideInvalidFullname(): iterable
    {
        yield [null];
        yield ['0'];
        yield [''];
    }
}
