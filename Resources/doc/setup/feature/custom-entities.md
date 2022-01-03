## Custom Entities

You can now replace the standard entities with your own.

The following interfaces will need to be implemented if your roll your own entities.

| Entity          | Interface                                                   |
| --------------- | ----------------------------------------------------------- |
| Ticket          | Hackzilla\Bundle\TicketBundle\Model\TicketInterface         |
| Ticket Message  | Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface  |

Once you've defined your entities then you will need to override the builtin ones.

```yaml
hackzilla_ticket:
    ticket_class: App\Entity\Ticket
    message_class: App\Entity\TicketMessage
```

### Features

| Entity          | Feature          | Interface                                                                      |
| --------------- | ---------------- | ------------------------------------------------------------------------------ |
| Ticket Message  | Attachments      | Hackzilla\Bundle\TicketBundle\Model\TicketFeatures\MessageAttachmentInterface  |


### Traits

To make creating your own entities a little easier there are traits.
The only thing missing form the traits are the primary id. This will allow you to use whatever you want as the primary id, whether its int or uuid.

| Entity          | Trait                                                                             |
| --------------- | --------------------------------------------------------------------------------- |
| Ticket          | Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketTrait                           |
| Ticket Message  | Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketMessageTrait                    |
| Ticket Message  | Hackzilla\Bundle\TicketBundle\Entity\Traits\TicketFeature\MessageAttachmentTrait  |

At the moment they only support xml configuration. Use the [TicketBundle xml](Resources/config/doctrine/model) as a basis and copy it into your bundle.

If you know of a better way, then either open an Issue or Pull Request.
