<?php

namespace Raneomik\WatchdogBundle\Test\Integration;

use Raneomik\WatchdogBundle\Test\Integration\Stubs\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class TestCase extends KernelTestCase
{
    use ForwardCompatTestCaseTrait;

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        require_once __DIR__.'/Stubs/Kernel.php';

        return new Kernel('test', true, isset($options['config']) ? $options['config'] : 'base');
    }
}
