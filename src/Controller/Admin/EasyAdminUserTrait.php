<?php

namespace Adeliom\EasyAdminUserBundle\Controller\Admin;

use Adeliom\EasyAdminUserBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function Clue\StreamFilter\fun;

trait EasyAdminUserTrait
{
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $parameterBag = $this->container->get("parameter_bag");
        return parent::configureUserMenu($user)
            ->setName($user->getFullname())
            ->setGravatarEmail($user->getEmail())
            ->addMenuItems([
                MenuItem::linkToCrud('easy_admin_user.my_profile', 'fa fa-id-card', $parameterBag->get('easy_admin_user.user_class'))->setAction(Action::DETAIL)->setEntityId($user->getId()),
            ])
            ;
    }

    public function administratorMenuEntry(): iterable
    {
        $parameterBag = $this->container->get("parameter_bag");
        yield MenuItem::linkToCrud('easy_admin_user.users', 'fas fa-users-cog', $parameterBag->get('easy_admin_user.user_class'));
    }
}
