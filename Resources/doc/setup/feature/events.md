## Events

TicketBundle show fires events for creating, updating, and deleting of tickets.

* hackzilla.ticket.create
* hackzilla.ticket.update
* hackzilla.ticket.delete

See for example of how to create listener: http://symfony.com/doc/current/cookbook/service_container/event_listener.html


Add your user, ticket and ticket message entities into your config.

```yaml
hackzilla_ticket:
    user_class:             AppBundle\Entity\User
    ticket_class:           AppBundle\Entity\Ticket
    message_class:          AppBundle\Entity\Message
```

Your entities  needs to implement:

| Entity | Interface |
|--------|-------|
| User | ```Hackzilla\Bundle\TicketBundle\Model\UserInterface``` |
| Ticket | ```Hackzilla\Bundle\TicketBundle\Model\MessageInterface``` |
| Message | ```Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface``` |
