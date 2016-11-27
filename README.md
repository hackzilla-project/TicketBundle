# Ticketing Bundle v3

Simple multilingual ticketing bundle to add to any project.
Languages: English, French, Russian, German and Spanish.

[![Build Status](https://travis-ci.org/hackzilla/TicketBundle.png?branch=master)](https://travis-ci.org/hackzilla/TicketBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/091d37a9-7862-4365-952c-814ce95c4d6c/mini.png)](https://insight.sensiolabs.com/projects/091d37a9-7862-4365-952c-814ce95c4d6c)


## Requirements

* PHP >= 5.6
* Symfony ~2.8|~3.0
* Knp Paginator
* Bootstrap v3 (optional) see: http://symfony.com/blog/new-in-symfony-2-6-bootstrap-form-theme

## Optional Requirements

* FOSUserBundle


## Version Matrix

| Ticket Bundle                                                          | Symfony    | PHP   |
| ---------------------------------------------------------------------- | ---------- | ----- |
| [3.x](https://github.com/hackzilla/TicketBundle/tree/master) (master)  | ^2.8\|^3.0 | >=5.6 |
| [2.x](https://github.com/hackzilla/TicketBundle/tree/2.x)              | ^2.7\|^3.0 | >=5.3 |
| [1.x](https://github.com/hackzilla/TicketBundle/tree/1.x)              | ^2.3       | >=5.3 |
| [0.x](https://github.com/hackzilla/TicketBundle/tree/0.9.x)            | ^2.3       | >=5.3 |


## Demo

See [Ticket Bundle Demo App](https://github.com/hackzilla/TicketBundleDemoApp) for an example installation.  This can also be used for confirming bugs.


## Setup

* [Installation with FOSUserBundle](Resources/doc/setup/fosuserbundle.md)
* [Generic Installation](Resources/doc/setup/other.md)



## Optional Features

These optional features that can be turned on or off.

### Features

* [Attachments](Resources/doc/setup/feature/attachments.md)
* [Custom Entities](Resources/doc/setup/feature/custom-entities.md)
* [Events](Resources/doc/setup/feature/events.md)

# 3rd Party Extensions

# [Email Notification](https://github.com/flodaq/TicketNotificationBundle)


## Custom Templates (Optional)

```
config.yml

hackzilla_ticket:
    templates: 
        index: 'YOURTicketBundle:Ticket:index.html.twig'
        new: 'YOURTicketBundle:Ticket:new.html.twig'
        prototype: 'YOURTicketBundle:Ticket:prototype.html.twig'
        show: 'YOURTicketBundle:Ticket:show.html.twig'
        show_attachment: 'YOURTicketBundle:Ticket:show_attachment.html.twig'
```

## Migrate a Previous Version

* [Information moved](Resources/doc/migrate/index.md)


## Pull Requests

I'm open to pull requests for additional languages, features and/or improvements.
