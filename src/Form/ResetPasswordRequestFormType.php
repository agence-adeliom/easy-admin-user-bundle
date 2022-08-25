<?php

namespace Adeliom\EasyAdminUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => ['autocomplete' => 'email'],
                'label' => 'easy_admin_user.form.email',
                'label_attr' => ['class' => 'required'],
                'help' => 'easy_admin_user.reset_password.request_help',
                'required' => true,
                'translation_domain' => 'messages',
                'constraints' => [
                    new NotBlank([
                        'message' => 'easy_admin_user.reset_password.request_error',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
