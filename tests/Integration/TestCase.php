<?php

namespace Raneomik\WatchdogBundle\Test\Integration;

use Raneomik\WatchdogBundle\Test\Integration\Stubs\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

abstract class TestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir().'/WatchdogBundle/');
    }

    protected function tearDown(): void
    {
        static::ensureKernelShutdown();
        static::$kernel = null;
    }

    /**
     * {@inheritdoc}
     */
    protected static function createKernel(array $options = [])
    {
        return new Kernel('test', true, $options['config'] ?? 'base');
    }
}
