UPGRADE FROM 3.5 to 3.6
=======================

## `Hackzilla\Bundle\TicketBundle\Component\TicketFeatures`

Returning other type than boolean from `TicketFeatures::hasFeature()` is deprecated
and will be not allowed in version 4.0.

UPGRADE FROM 3.4 to 3.5
=======================

## API narrowing

 * Extending the following classes is deprecated since they were
   marked as final.

 - `Hackzilla\Bundle\TicketBundle\Command\AutoClosingCommand`;
 - `Hackzilla\Bundle\TicketBundle\Command\TicketManagerCommand`;
 - `Hackzilla\Bundle\TicketBundle\Component\TicketFeatures`;
 - `Hackzilla\Bundle\TicketBundle\Controller\TicketAttachmentController`;
 - `Hackzilla\Bundle\TicketBundle\Controller\TicketController`;
 - `Hackzilla\Bundle\TicketBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass`;
 - `Hackzilla\Bundle\TicketBundle\DependencyInjection\Configuration`;
 - `Hackzilla\Bundle\TicketBundle\DependencyInjection\HackzillaTicketExtension`;
 - `Hackzilla\Bundle\TicketBundle\Event\TicketEvent`;
 - `Hackzilla\Bundle\TicketBundle\EventListener\FileSubscriber`;
 - `Hackzilla\Bundle\TicketBundle\EventListener\UserLoad`;
 - `Hackzilla\Bundle\TicketBundle\Form\DataTransformer\StatusTransformer`;
 - `Hackzilla\Bundle\TicketBundle\Manager\TicketManager`;
 - `Hackzilla\Bundle\TicketBundle\Manager\UserManager`;
 - `Hackzilla\Bundle\TicketBundle\TicketRole`;
 - `Hackzilla\Bundle\TicketBundle\Type\PriorityType`;
 - `Hackzilla\Bundle\TicketBundle\Type\StatusType`;
 - `Hackzilla\Bundle\TicketBundle\Type\TicketMessageType`;
 - `Hackzilla\Bundle\TicketBundle\Type\TicketType`;
 - `Hackzilla\Bundle\TicketBundle\TwigExtension\TicketFeatureExtension`;
 - `Hackzilla\Bundle\TicketBundle\TwigExtension\TicketGlobalExtension`.

UPGRADE FROM 3.3 to 3.4
=======================

## Translation domain

 * Using "messages" translation domain is deprecated in favor of "HackzillaTicketBundle"
   in order to allow projects consuming this bundle to override translations in
   a proper way.
   You should configure "hackzilla_ticket.translation_domain" with the value "HackzillaTicketBundle",
   which will be the only allowed value in version 4.0.

```yml
# packages/hackzilla_ticket.yaml

hackzilla_ticket:
    translation_domain: HackzillaTicketBundle
```

UPGRADE FROM 3.2 to 3.3
=======================

## Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface and Hackzilla\Bundle\TicketBundle\Manager\TicketManager

 * Deprecated method `getTicketList()` in favor of `getTicketListQuery()`.

 * Deprecated relying on the return value of `updateTicket()`, since its the same object
   provided at argument 1. This method will return `void` in version 4.0.
