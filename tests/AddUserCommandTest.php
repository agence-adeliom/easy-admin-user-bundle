<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Command\AddUserCommand;
use Adeliom\EasyAdminUserBundle\Entity\User;
use Adeliom\EasyAdminUserBundle\Repository\UserRepository;
use Adeliom\EasyAdminUserBundle\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AddUserCommandTest extends TestCase
{
    public function testExecuteCreatesUser(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist');
        $em->expects(self::once())->method('flush');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn('hashed');

        $repo = $this->createMock(UserRepository::class);
        $repo->method('findOneBy')->willReturn(null);
        $repo->method('getClassName')->willReturn(User::class);

        $command = new AddUserCommand($em, $hasher, new Validator(), $repo);
        $tester = new CommandTester($command);
        $exitCode = $tester->execute([
            'email' => 'foo@example.com',
            'password' => 'secret1',
            '--admin' => true,
        ]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertStringContainsString('successfully created', $tester->getDisplay());
    }
}
