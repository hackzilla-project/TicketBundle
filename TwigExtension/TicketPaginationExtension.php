<?php

namespace Hackzilla\Bundle\TicketBundle\TwigExtension;

use Hackzilla\TicketMessage\Manager\StorageManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TicketPaginationExtension extends \Twig_Extension
{
    /** @var RequestStack */
    private $requestStack;

    /** @var UrlGeneratorInterface */
    protected $router;

    /** @var StorageManagerInterface */
    private $storageManager;

    private $config;

    /**
     * @param RequestStack $requestStack,
     * @param UrlGeneratorInterface $router,
     * @param StorageManagerInterface $storageManager
     * @param array $config
     */
    public function __construct(
        RequestStack $requestStack,
        UrlGeneratorInterface $router,
        StorageManagerInterface $storageManager,
        array $config
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->storageManager = $storageManager;
        $this->config = $config;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('isSortable', [$this, 'isSortable']),
            new \Twig_SimpleFunction('sortable', [$this, 'sortable'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Is column sortable
     *
     * @param string $column
     *
     * @return bool
     */
    public function isSortable($column)
    {
        return in_array($column, $this->storageManager->getSortableFields(), true);
    }

    public function sortable($title, $column)
    {
        $pageName = $this->config['page_name'];
        $fieldName = $this->config['sort_field_name'];
        $directionName = $this->config['sort_direction_name'];

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $currentColumn = $request->query->get($fieldName);
        $direction = 'ASC';

        if ($currentColumn === $column) {
            switch ($request->query->get($directionName)) {
                case 'ASC':
                    $direction = 'DESC';
                    break;

                case 'DESC':
                    $direction = null;
                    break;

                default:
                    $direction = 'ASC';
            }
        }

        $routeName = $request->get('_route');

        $url = $this->router->generate($routeName, [
            $fieldName => $column,
            $directionName => $direction,
        ]);

        return '<a href="' . $url . '" class="' . strtolower((string) $direction) . '">' . $title . '</a>';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ticketPagination';
    }
}
