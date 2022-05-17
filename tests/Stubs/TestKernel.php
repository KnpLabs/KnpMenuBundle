<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    private array $mockBundles;

    public function __construct(array $bundles)
    {
        $this->mockBundles = $bundles;

        parent::__construct('test', false);
    }

    public function registerBundles(): array
    {
        return $this->mockBundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    protected function initializeContainer(): void
    {
    }
}
