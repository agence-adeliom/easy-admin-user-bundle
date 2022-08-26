
![Adeliom](https://adeliom.com/public/uploads/2017/09/Adeliom_logo.png)
[![Quality gate](https://sonarcloud.io/api/project_badges/quality_gate?project=agence-adeliom_easy-admin-user-bundle)](https://sonarcloud.io/dashboard?id=agence-adeliom_easy-admin-user-bundle)

# Easy Admin User Bundle

Provide a basic integration of user authentification and password reset in Easyadmin.


## Features

- A complete user flow
- A command to generate a user account
- A Easyadmin CRUD interface to manage users

## Versions

| Repository Branch | Version | Symfony Compatibility | PHP Compatibility | Status                     |
|-------------------|---------|-----------------------|-------------------|----------------------------|
| `2.x`             | `2.x`   | `5.4`, and `6.x`      | `8.0.2` or higher | New features and bug fixes |
| `1.x`             | `1.x`   | `4.4`, and `5.x`      | `7.2.5` or higher | No longer maintained       |

## Installation with Symfony Flex

Add our recipes endpoint

```json
{
  "extra": {
    "symfony": {
      "endpoint": [
        "https://api.github.com/repos/agence-adeliom/symfony-recipes/contents/index.json?ref=flex/main",
        ...
        "flex://defaults"
      ],
      "allow-contrib": true
    }
  }
}
```

Install with composer

```bash
composer require agence-adeliom/easy-admin-user-bundle
```

### Setup database

#### Using doctrine migrations

```bash
php bin/console doctrine:migration:diff
php bin/console doctrine:migration:migrate
```

#### Without

```bash
php bin/console doctrine:schema:update --force
```


## Usage/Examples

The `easy-admin:add-user` command creates new users and saves them in the database:

```bash
bin/console easy-admin:add-user email password
````

By default the command creates regular users. To create administrator users, add the `--admin` option:

```bash
bin/console easy-admin:add-user email password --admin
````

Or to create super-administrator users, add the `--super-admin` option:

```bash
bin/console easy-admin:add-user email password --super-admin
````

If you omit any of the 2 required arguments, the command will ask you to provide the missing values:

```bash
# command will ask you for the password
bin/console easy-admin:add-user email

# command will ask you for the email and the password
bin/console easy-admin:add-user
````
## Documentation

[Check it here](doc/index.md)

## License

[MIT](https://choosealicense.com/licenses/mit/)


## Authors

- [@arnaud-ritti](https://github.com/arnaud-ritti)

  
