# Optional Feature

## Attachments

Add UploaderBundle to your requirements:

```bash
composer require vich/uploader-bundle
```

Your `TicketMessage` entity will also need to implement `Hackzilla\Bundle\TicketBundle\Model\MessageAttachmentInterface`

Specify the uploader config, so the bundle knows where to store the files.

```yaml
hackzilla_ticket:
    user_class: App\Entity\User
    ticket_class: App\Entity\Ticket
    message_class: App\Entity\TicketMessage

vich_uploader:
    db_driver: orm

    mappings:
        ticket_message_attachment:
            uri_prefix: /attachment
            upload_destination: '%kernel.project_dir%/var/uploads/attachment/'
```

See [VichUploaderBundle](https://github.com/dustin10/VichUploaderBundle/) documentation for more details.

If you are not using [Symfony Flex](https://symfony.com/doc/current/setup/flex.html), you must enable the bundles manually in the kernel:

```php
<?php
// config/bundles.php

return [
    Vich\UploaderBundle\VichUploaderBundle => ['all' => true],
    Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle::class => ['all' => true],
    // ...
    // Your application bundles
];
```

If you are using an older kernel implementation, you must update the `registerBundles()` method:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Vich\UploaderBundle\VichUploaderBundle(),
        new Hackzilla\Bundle\TicketBundle\HackzillaTicketBundle(),
        // ...
        // Your application bundles
    );
}
```
