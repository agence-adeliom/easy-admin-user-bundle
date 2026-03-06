<?php

declare(strict_types=1);

namespace Adeliom\EasyAdminUserBundle\Tests\Form;

use Adeliom\EasyAdminUserBundle\Form\ChangePasswordFormType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[CoversClass(\Adeliom\EasyAdminUserBundle\Form\ChangePasswordFormType::class)]
final class ChangePasswordFormTypeTest extends TestCase
{
    public function testTypeBuildsRepeatedPasswordFieldAndDefaultOptions(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects(self::once())
            ->method('add')
            ->willReturnCallback(function (string $name, string $type, array $options = []) use ($builder): FormBuilderInterface {
                self::assertSame('plainPassword', $name);
                self::assertSame(RepeatedType::class, $type);
                self::assertSame(PasswordType::class, $options['type']);
                self::assertSame('easy_admin_user.reset_password.not_match', $options['invalid_message']);
                self::assertFalse($options['mapped']);
                self::assertSame('easy_admin_user.reset_password.new_password', $options['first_options']['label']);
                self::assertSame('easy_admin_user.reset_password.confirm_new_password', $options['second_options']['label']);
                self::assertContainsOnlyInstancesOf(NotBlank::class, [$options['first_options']['constraints'][0]]);
                self::assertContainsOnlyInstancesOf(Length::class, [$options['first_options']['constraints'][1]]);

                return $builder;
            });

        $type = new ChangePasswordFormType();
        $type->buildForm($builder, []);

        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);

        self::assertSame([], $resolver->resolve());
    }
}
