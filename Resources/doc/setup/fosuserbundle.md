# Setup

## Step 1: Installation

Add HackzillaTicketBundle and FOSUserBundle to your requirements:

```bash
composer require hackzilla/ticket-bundle friendsofsymfony/user-bundle
```

Specify your user class in your config, this will be exactly the same as `user_class` in FOSUserBundle.

```yaml
hackzilla_ticket:
    user_class: App\Entity\User
```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

You'll end up with a class like:

```php
<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser implements \Hackzilla\Bundle\TicketBundle\Model\UserInterface
{
}
```

Follow [FOSUserBundle guide](https://github.com/FriendsOfSymfony/FOSUserBundle)

## Step 2: Enable the bundle

If you are not using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html), you must enable the bundles manually in the kernel:

```php
<?php
// config/bundles.php

return [
    FOS\UserBundle\FOSUserBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
    Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle::class => ['all' => true],
    Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle::class => ['all' => true],
    // ...
    // Your application bundles
];
```

If you are using an older kernel implementation, you must update the `registerBundles()` method:

```php
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

```yaml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing.yml"
    prefix: /
```

or

```yaml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing/ticket.yml"
    prefix: /ticket
```

## Step 4: Roles

All users can create tickets, even anonymous users.
You can assign "ROLE_TICKET_ADMIN" to any user you want to be able to administer the ticketing system.

## Step 5: Create tables

```bin/console doctrine:schema:update --force```
