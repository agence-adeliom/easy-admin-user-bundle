<?php
declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests;

use Adeliom\EasyAdminUserBundle\Form\ChangePasswordFormType;
use Adeliom\EasyAdminUserBundle\Form\ResetPasswordRequestFormType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class FormTypeTest extends KernelTestCase
{
    private FormFactoryInterface $factory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->factory = self::getContainer()->get(FormFactoryInterface::class);
    }

    public function testChangePasswordFormHasPlainPassword(): void
    {
        $form = $this->factory->create(ChangePasswordFormType::class);
        self::assertTrue($form->has('plainPassword'));
    }

    public function testResetPasswordRequestFormHasEmail(): void
    {
        $form = $this->factory->create(ResetPasswordRequestFormType::class);
        self::assertTrue($form->has('email'));
    }
}
