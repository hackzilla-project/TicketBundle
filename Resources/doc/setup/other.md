# Setup

## Step 1: Installation

Add HackzillaTicketBundle to your requirements:

```bash
composer require hackzilla/ticket-bundle
```

Specify your user class in your config, if you are using FOSUserBundle, then this will be exactly the same.

```yaml
hackzilla_ticket:
    user_class: App\Entity\User
```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

You should end up with a class similar to:

```php
<?php

namespace App\Entity;

class User implements \Hackzilla\Bundle\TicketBundle\Model\UserInterface
{
}
```

## Step 2: Enable the bundle

If you are not using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html), you must enable the bundles manually in the kernel:

```php
<?php
// config/bundles.php

return [
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
    prefix: /
```

or

``` yml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing/ticket.yml"
    prefix: /ticket
```

## Step 4: Roles

All users can create tickets, even anonymous users.
You can assign "ROLE_TICKET_ADMIN" to any user you want to be able to administer the ticketing system.

## Step 5: Create tables

```bin/console doctrine:schema:update --force```
