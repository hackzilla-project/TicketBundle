## Migration from v3 to v4

Interfaces have been moved to Hackzilla\TicketMessage.

Previously:

```
Hackzilla\Bundle\TicketBundle\Model\TicketInterface
Hackzilla\Bundle\TicketBundle\Model\TicketMessageInterface
```

Will now become:

```
Hackzilla\TicketMessage\Model\TicketInterface
Hackzilla\TicketMessage\Model\TicketMessageInterface
```

### TicketManagerInterface Changes

The interface ```Hackzilla\Bundle\TicketBundle\Manager\TicketManagerInterface``` has been modified.

* Extends ```Hackzilla\TicketMessage\Manager\TicketManagerInterface```
* Change functionality with ```UserManager```, ```EventManager``` and ```StorageManager```

### Pagination

Replaced knplabs/knp-paginator-bundle with pagerfanta/pagerfanta

