# Ticketing Bundle v2

Currently V2 is a work in progress, please use v1.

Simple multilingual ticketing bundle to add to any project.
Languages: English, French, Russian, German and Spanish.

[![Build Status](https://travis-ci.org/hackzilla/TicketBundle.png?branch=master)](https://travis-ci.org/hackzilla/TicketBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/091d37a9-7862-4365-952c-814ce95c4d6c/mini.png)](https://insight.sensiolabs.com/projects/091d37a9-7862-4365-952c-814ce95c4d6c)

## Requirements

* PHP >= 5.5
* Symfony ~2.7|~3.0
* FOSUserBundle
* Knp Paginator
* Bootstrap v3 (optional) see: http://symfony.com/blog/new-in-symfony-2-6-bootstrap-form-theme


## Version Matrix

| Ticket Bundle                                                          | Symfony    | PHP   |
| ---------------------------------------------------------------------- | ---------- | ----- |
| [2.x](https://github.com/hackzilla/TicketBundle/tree/master) (master)  | ^2.7\|^3.0 | >=5.5 |
| [1.x](https://github.com/hackzilla/TicketBundle/tree/1.x)              | ^2.3       | >=5.3 |
| [0.x](https://github.com/hackzilla/TicketBundle/tree/0.9.x)            | ^2.3       | >=5.3 |


## Demo


See [Ticket Bundle Demo App](https://github.com/hackzilla/TicketBundleDemoApp) for an example installation.  This can also be used for confirming bugs.

### Step 1: Installation

Add HackzillaTicketBundle in your composer.json:

```json
{
    "require": {
        "hackzilla/ticket-bundle": "~2.0@dev",
        "friendsofsymfony/user-bundle": "~2.0@dev",
    }
}
```

Specify your user class in your config, if you are using FOSUserBundle, then this will be exactly the same.

```yaml
hackzilla_ticket:
    user_class:             AppBundle\Entity\User
```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

If your using FOSUserBundle then you'll end up with a class like:

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

### Step 2: Enable the bundle

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

### Step 3: Import the routing

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

### Step 4: Roles

All users can create tickets, even anonymous users.
You can assign ROLE_TICKET_ADMIN to any user you want to be able to administer the ticketing system.

### Step 5: Create tables

```app/console doctrine:schema:update --force```

## Events


TicketBundle show fires events for creating, updating, and deleting of tickets.

* hackzilla.ticket.create
* hackzilla.ticket.update
* hackzilla.ticket.delete

See for example of how to create listener: http://symfony.com/doc/current/cookbook/service_container/event_listener.html


## Migrating to 2.0

Add your user class into your config.

```yaml
hackzilla_ticket:
    user_class:             AppBundle\Entity\User
```

```Hackzilla\Bundle\TicketBundle\User\UserInterface``` has been replaced with ```Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

## Pull Requests

I'm open to pull requests for additional languages, features and/or improvements.
