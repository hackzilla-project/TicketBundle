<?php

namespace Hackzilla\Bundle\TicketBundle\Manager\TranslateManager;

use Hackzilla\TicketMessage\Manager\TranslateManagerInterface;

class DoNothingTranslateManager implements TranslateManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function translate($string)
    {
        return $string;
    }
}
