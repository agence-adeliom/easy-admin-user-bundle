<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Form;

use Adeliom\EasyAdminUserBundle\Form\ResetPasswordRequestFormType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Form\ResetPasswordRequestFormType::class)]
final class ResetPasswordRequestFormTypeTest extends TestCase
{
    public function testTypeBuildsEmailFieldAndDefaultOptions(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::once())
            ->method('add')
            ->willReturnCallback(function (string $name, string $type, array $options = []) use ($builder): FormBuilderInterface {
                self::assertSame('email', $name);
                self::assertSame(EmailType::class, $type);
                self::assertSame('easy_admin_user.form.email', $options['label']);
                self::assertSame('easy_admin_user.reset_password.request_help', $options['help']);
                self::assertTrue($options['required']);
                self::assertSame('messages', $options['translation_domain']);
                self::assertContainsOnlyInstancesOf(NotBlank::class, $options['constraints']);

                return $builder;
            });

        $type = new ResetPasswordRequestFormType();
        $type->buildForm($builder, []);

        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);

        self::assertSame([], $resolver->resolve());
    }
}
