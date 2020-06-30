UPGRADE FROM 3.2 to 3.3
=======================

## Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface and Hackzilla\Bundle\TicketBundle\Manager\TicketManager

 * Deprecated method `getTicketList()` in favor of `getTicketListQuery()`.

 * Deprecated relying on the return value of `updateTicket()`, since its the same object
   provided at argument 1. This method will return void in version 4.0.
