UPGRADE FROM 3.8 to 4.0
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

* `TicketManagerInterface::setEntityManager` has been removed in favour of `TicketManagerInterface::setObjectManager`
* `TicketManagerInterface::getTicketList` has been removed in favour of `TicketManagerInterface::getTicketListQuery`

## Config

* `translation_domain` has been removed from the config and has been hard coded as `HackzillaTicketBundle`.
```yml
# packages/hackzilla_ticket.yaml

hackzilla_ticket:
    translation_domain: HackzillaTicketBundle
```
