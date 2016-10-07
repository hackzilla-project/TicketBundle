# Setup

## Step 1: Installation

Add HackzillaTicketBundle in your composer.json:

```json
{
    "require": {
        "hackzilla/ticket-bundle": "~2.0@dev",
        "friendsofsymfony/user-bundle": "~2.0@dev",
    }
}
```

Specify your user class in your config, this will be exactly the same as user_class in FOSUserBundle.

```yaml
hackzilla_ticket:
    user_class:             AppBundle\Entity\User
```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

You'll end up with a class like:

```php
<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser implements \Hackzilla\Bundle\TicketBundle\Model\UserInterface
{
}
```

Follow [FOSUserBundle guide](https://github.com/FriendsOfSymfony/FOSUserBundle)


Install Composer

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

Now tell composer to download the library by running the command:

``` bash
$ composer update hackzilla/ticket-bundle
```

Composer will install the bundle into your project's `vendor/hackzilla` directory.

## Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\UserBundle\FOSUserBundle(),
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        new Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle(),
        // ...
        // Your application bundles
    );
}
```

## Step 3: Import the routing

``` yml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing.yml"
    prefix:   /
```

or

``` yml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing/ticket.yml"
    prefix:   /ticket
```

## Step 4: Roles

All users can create tickets, even anonymous users.
You can assign ROLE_TICKET_ADMIN to any user you want to be able to administer the ticketing system.

## Step 5: Create tables

```bin/console doctrine:schema:update --force```
