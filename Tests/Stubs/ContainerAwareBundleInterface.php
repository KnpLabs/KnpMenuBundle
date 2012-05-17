<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Stub interface as the kernel expects bundles to be container-aware but the interface
 * does not do it. This allows creating a mock implementing both interfaces.
 */
interface ContainerAwareBundleInterface extends BundleInterface, ContainerAwareInterface
{
}
