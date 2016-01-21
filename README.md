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


## Setup

* [Installation with FOSUserBundle](Resources/doc/setup/fosuserbundle.md)
* [Generic Installation](Resources/doc/setup/other.md)


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
