<?php

namespace Adeliom\EasyAdminUserBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;

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
