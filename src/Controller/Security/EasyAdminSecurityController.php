<?php

namespace Adeliom\EasyAdminUserBundle\Controller\Security;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class EasyAdminSecurityController extends AbstractController
{
    #[Route(path: '{_locale}/admin/login', name: 'easy_admin_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof \Symfony\Component\Security\Core\User\UserInterface) {
            return $this->redirectToRoute('admin');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'translation_domain' => 'messages',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->generateUrl('admin'),
            'username_label' => 'easy_admin_user.login.username',
            'password_label' => 'easy_admin_user.login.password',
            'sign_in_label' => 'easy_admin_user.login.login',
            'username_parameter' => 'email',
            'password_parameter' => 'password',
            'forgot_password_enabled' => true,
            'forgot_password_path' => $this->generateUrl('easy_admin_forgot_password_request'),
            'forgot_password_label' => 'easy_admin_user.login.forgot_password',
            'remember_me_enabled' => false,
            'remember_me_parameter' => '_admin_remember_me',
            'remember_me_checked' => true,
            'remember_me_label' => 'easy_admin_user.login.remember_me',
        ]);
    }

    /**
     * @return never
     */
    #[Route(path: '{_locale}/admin/logout', name: 'easy_admin_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('easy_admin_login');
    }
}
