<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\TranslateManager;

use Hackzilla\TicketMessage\Manager\TranslateManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SymfonyTranslateManager implements TranslateManagerInterface
{
    private $translator;

    /**
     * SymfonyTranslateManager constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function translate($string)
    {
        return $this->translator->trans($string);
    }
}
