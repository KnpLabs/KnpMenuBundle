<?php

namespace Knp\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\UriVoter as BaseUriVoter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@inheritdoc}
 */
class UriVoter extends BaseUriVoter
{
    public function __construct(ContainerInterface $container)
    {
        parent::construct($container->get('request')->getUri());
    }
}
