Ticketing Bundle
================

Simple ticketing bundle to add to any project.

[![Build Status](https://travis-ci.org/hackzilla/TicketBundle.png?branch=master)](https://travis-ci.org/hackzilla/TicketBundle)

Requirements
------------

* FOSUserBundle
* Knp Paginator
* Bootstrap v3 (optional) see: https://coderwall.com/p/kzyiaw


Installation
------------

Add HackzillaTicketBundle in your composer.json:

```js
{
    "require": {
        "hackzilla/ticket-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update hackzilla/ticket-bundle
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
        new Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle(),
    );
}
```

### Step 3: Import the routing

``` yml
hackzilla_ticket:
    resource: "@HackzillaTicketBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 4: Roles

All users can create tickets.
You can assign ROLE_TICKET_ADMIN to any user you want to be able to administer the ticketing system.

### Step 5: Create tables

```app/console doctrine:schema:update --dump-sql```


Pull Requests
-------------

I'm open to pull requests for additional languages, features and/or improvements.
