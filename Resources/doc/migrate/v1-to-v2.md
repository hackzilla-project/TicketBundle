## Migrating from v1 to v2

Add your user class into your config.

```yaml
hackzilla_ticket:
    user_class:             AppBundle\Entity\User
```

```Hackzilla\Bundle\TicketBundle\User\UserInterface``` has been replaced with ```Hackzilla\Bundle\TicketBundle\Manager\UserManagerInterface```

Your user class needs to implement ```Hackzilla\Bundle\TicketBundle\Model\UserInterface```

Roles are now checked against the User
