<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Command;

use Adeliom\EasyAdminUserBundle\Command\AddUserCommand;
use Adeliom\EasyAdminUserBundle\Tests\Fixtures\Entity\TestUser;
use Adeliom\EasyAdminUserBundle\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Command\AddUserCommand::class)]
final class AddUserCommandTest extends TestCase
{
    public function testCommandCreatesAdminUser(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $validator = $this->createMock(Validator::class);
        $repository = $this->createMock(UserRepository::class);

        $repository->method('findOneBy')->with(['email' => 'admin@example.com'])->willReturn(null);
        $repository->method('getClassName')->willReturn(TestUser::class);

        $validator->expects(self::once())->method('validateEmail')->with('admin@example.com')->willReturn('admin@example.com');
        $validator->expects(self::once())->method('validatePassword')->with('Password1!')->willReturn('Password1!');

        $passwordHasher->method('hashPassword')->willReturn('hashed-password');

        $entityManager->expects(self::once())
            ->method('persist')
            ->with(self::callback(static function (TestUser $user): bool {
                TestCase::assertSame('admin@example.com', $user->getEmail());
                TestCase::assertSame(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
                TestCase::assertSame('hashed-password', $user->getPassword());

                return true;
            }));
        $entityManager->expects(self::once())->method('flush');

        $command = new AddUserCommand($entityManager, $passwordHasher, $validator, $repository);
        $tester = new CommandTester($command);
        $statusCode = $tester->execute([
            'email' => 'admin@example.com',
            'password' => 'Password1!',
            '--admin' => true,
        ]);

        self::assertSame(Command::SUCCESS, $statusCode);
        self::assertStringContainsString('Administrator user was successfully created: admin@example.com', $tester->getDisplay());
    }
}
