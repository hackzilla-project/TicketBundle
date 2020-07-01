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
