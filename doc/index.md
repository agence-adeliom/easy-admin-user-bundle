# Configure your firewall

## Configure encoders

```yaml
security:
    ...
    password_hashers:
        ...
        Adeliom\EasyAdminUserBundle\Entity\User: argon2i
```

## Configure roles

```yaml
security:
    ...
    role_hierarchy:
        ...
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
        ROLE_ADMIN:       [ROLE_USER]
```

## Configure providers

```yaml
security:
    ...
    providers:
        ...
        easy_admin_user_provider:
            id: easy_admin_user.user_provider
```

## Configure firewall

```yaml
security:
    ...
    firewalls:
        ...
        admin:
            lazy: true
            pattern: ^/admin
            provider: easy_admin_user_provider
            custom_authenticator: easy_admin_user.authenticator
            # https://symfony.com/doc/current/security/impersonating_user.html
            switch_user: { role: ROLE_ALLOWED_TO_SWITCH }
            remember_me:
                secret: "%env(APP_SECRET)%"
                name: ADMIN_REMEMBER_ME
                lifetime: 31536000
                path: /admin
                remember_me_parameter: _admin_remember_me
            logout:
                path: easy_admin_logout
                target: easy_admin_login

        ...
        main:
            ...
```

## Setup access control

```yaml
security:
    ...
    access_control:
        ...
        - { path: ^/admin/login$, roles: PUBLIC_ACCESS }
        - { path: ^/admin/logout, roles: PUBLIC_ACCESS }
        - { path: ^/admin/reset-password, roles: PUBLIC_ACCESS }
        - { path: ^/admin, roles: ROLE_ADMIN }
```

# Create a user

```bash
php bin/console easy-admin:add-user email@example.com password --super-admin
```

# Manage users in your Easyadmin dashboard

Go to your dashboard controller, example : `src/Controller/Admin/DashboardController.php`

```php
<?php

namespace App\Controller\Admin;

...
use Adeliom\EasyAdminUserBundle\Controller\Admin\EasyAdminUserTrait;

class DashboardController extends AbstractDashboardController
{
    ...
    use EasyAdminUserTrait;

    ...
    public function configureMenuItems(): iterable
    {
        ...
        yield from $this->administratorMenuEntry();

        ...
```
