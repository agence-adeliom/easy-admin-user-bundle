
![Adeliom](https://adeliom.com/public/uploads/2017/09/Adeliom_logo.png)

# Easy Admin User Bundle

Provide a basic integration of user authentification and password reset in Easyadmin.


## Features

- A complete user flow
- A command to generate a user account
- A Easyadmin CRUD interface to manage users


## Installation

Install with composer

```bash
composer require agence-adeliom/easy-admin-user-bundle
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

  
