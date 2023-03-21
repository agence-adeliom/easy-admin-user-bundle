<?php

namespace Adeliom\EasyAdminUserBundle\Controller\Admin;

use Adeliom\EasyAdminUserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class EasyAdminUserCrudController extends AbstractCrudController
{
    public function __construct(
        /**
         * @readonly
         */
        protected AdminContextProvider $adminContextProvider,
        /**
         * @readonly
         */
        protected ParameterBagInterface $parameterBag,
        /**
         * @readonly
         */
        protected RoleHierarchyInterface $roleHierarchy,
        /**
         * @readonly
         */
        protected TranslatorInterface $translator
    ) {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'easy_admin_user.manage_users')
            ->setPageTitle(Crud::PAGE_NEW, 'easy_admin_user.new_user')
            ->setPageTitle(Crud::PAGE_EDIT, 'easy_admin_user.edit_user')
            ->setPageTitle(Crud::PAGE_DETAIL, static fn ($entity): string => sprintf('%s<br/><small>%s</small>', $entity->getFullName(), $entity->getEmail()))
            ->setEntityLabelInSingular('easy_admin_user.user')
            ->setEntityLabelInPlural('easy_admin_user.users')
            ->setFormOptions([
                'validation_groups' => ['Default'],
            ])
            ->setEntityPermission(User::ADMIN)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        $currentUser = $this->getUser();

        $impersonate = Action::new('impersonate', 'easy_admin_user.impersonate', 'fa fa-user-secret')->linkToCrudAction('impersonate')->setCssClass('btn btn-info')->displayIf(static fn (User $entity): bool => $currentUser->getUserIdentifier() !== $entity->getEmail());
        $actions
            ->add(Crud::PAGE_DETAIL, $impersonate)
            ->add(Crud::PAGE_EDIT, $impersonate)
            ->setPermission('impersonate', 'ROLE_ALLOWED_TO_SWITCH');

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->update(Crud::PAGE_INDEX, Action::DELETE, static function (Action $action) use ($currentUser): Action {
            $action->displayIf(static fn (User $entity): bool => $currentUser->getUserIdentifier() !== $entity->getEmail());

            return $action;
        });

        $actions->update(Crud::PAGE_DETAIL, Action::DELETE, static function (Action $action) use ($currentUser): Action {
            $action->displayIf(static fn (User $entity): bool => $currentUser->getUserIdentifier() !== $entity->getEmail());

            return $action;
        });

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $currentUser = $this->getUser();
        $context = $this->adminContextProvider->getContext();
        $subject = $context?->getEntity()->getInstance();
        $roles = $this->parameterBag->get('security.role_hierarchy.roles');
        $rolesChoices = [];
        foreach ($roles as $role => $sub) {
            $rolesChoices[$role] = $role;
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN') && in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT])) {
            $accessibleRole = $this->roleHierarchy->getReachableRoleNames($currentUser->getRoles());
            $rolesChoices = array_intersect_key($rolesChoices, array_flip($accessibleRole));
        }

        yield TextField::new('firstname', 'easy_admin_user.form.firstname')->setColumns('col-12 col-sm-6');
        yield TextField::new('lastname', 'easy_admin_user.form.lastname')->setColumns('col-12 col-sm-6');
        yield EmailField::new('email', 'easy_admin_user.form.email')->setRequired(true)->setColumns('col-12 col-sm-6');
        yield TextField::new('plainPassword', 'easy_admin_user.form.password')
            ->setFormType(PasswordType::class)
            ->setRequired(Crud::PAGE_NEW == $pageName)
            ->setColumns('col-12 col-sm-6')
            ->setHelp('<ul>
<li>Au moins un chiffre <code>0-9</code></li>
<li>Au moins un caractère minuscule <code>a-z</code></li>
<li>Au moins un caractère majuscule <code>A-Z</code></li>
<li>Au moins un caractère spécial <code>#?!@$%^&*-</code></li>
<li>Au moins <code>8</code> caractères, mais pas plus de <code>32</code>.</li>
</ul>')
            ->onlyOnForms();

        if (!$subject || (Crud::PAGE_DETAIL == $pageName) || (Crud::PAGE_NEW == $pageName) || (Crud::PAGE_EDIT == $pageName && $currentUser->getUserIdentifier() !== $subject->getEmail())) {
            yield BooleanField::new('enabled', 'easy_admin_user.form.enabled')->renderAsSwitch(Crud::PAGE_INDEX != $pageName)->setColumns('col-12 col-sm-6');
            yield ChoiceField::new('roles', 'easy_admin_user.form.roles')->setColumns('col-12 col-sm-6')
                ->setRequired(true)
                ->allowMultipleChoices()
                ->renderExpanded()
                ->setChoices($rolesChoices)
                ->renderAsBadges();
        }
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $password = $this->container->get('security.password_hasher')->hashPassword($entityInstance, $entityInstance->getPlainPassword());
        $entityInstance->setPassword($password);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!empty($entityInstance->getPlainPassword())) {
            $password = $this->container->get('security.password_hasher')->hashPassword($entityInstance, $entityInstance->getPlainPassword());
            $entityInstance->setPassword($password);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function impersonate(AdminContext $context): RedirectResponse
    {
        if (!$this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        $user = $context?->getEntity()->getInstance();
        $referer = $context->getRequest()->headers->get('referer');
        /** @var UserInterface $user */
        if ($user) {
            $referer .= $referer.(parse_url($referer, PHP_URL_QUERY) ? '&' : '?').'_switch_user='.$user->getUserIdentifier();
            $this->addFlash('success', $this->translator->trans(
                'easy_admin_user.flashes.impersonate',
                [
                    '%name%' => $user->getUserIdentifier(),
                ]
            ));
        }

        return new RedirectResponse($referer);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(parent::getSubscribedServices(), [
            'security.password_hasher' => '?'.UserPasswordHasherInterface::class,
            TranslatorInterface::class => '?'.TranslatorInterface::class,
            RoleHierarchyInterface::class => '?'.RoleHierarchyInterface::class,
            ParameterBagInterface::class => '?'.ParameterBagInterface::class,
        ]);
    }
}
