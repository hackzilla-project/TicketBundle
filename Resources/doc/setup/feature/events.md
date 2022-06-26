## Events

TicketBundle show fires events for creating, updating, and deleting of tickets.

* `hackzilla.ticket.create`
* `hackzilla.ticket.update`
* `hackzilla.ticket.delete`

See for example of how to create listener: http://symfony.com/doc/current/cookbook/service_container/event_listener.html

Add your user, ticket and ticket message entities into your config.

```yaml
hackzilla_ticket:
    user_class: App\Entity\User
    ticket_class: App\Entity\Ticket
    message_class: App\Entity\Message
```

Your entities  needs to implement:

| Entity | Interface |
|--------|-------|
| User | ```Hackzilla\Bundle\TicketBundle\Model\UserInterface``` |
| Ticket | ```Hackzilla\Bundle\TicketBundle\Model\TicketInterface``` |
| Message | ```Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface``` |
