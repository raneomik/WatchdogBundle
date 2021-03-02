<?php

namespace Raneomik\WatchdogBundle\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;

if (70000 > \PHP_VERSION_ID
    && false === (new \ReflectionMethod(WebTestCase::class, 'tearDown'))->hasReturnType()
    && false === (new \ReflectionMethod(WebTestCase::class, 'setUp'))->hasReturnType()
) {
    eval('    
        namespace Raneomik\WatchdogBundle\Test\Integration;
    
        /**
         * @internal
         */
        trait ForwardCompatTestCaseTrait
        {
            protected function setUp()
            {
                $fs = new Filesystem();
                $fs->remove(\sys_get_temp_dir()."/WatchdogBundle/");
            }
    
            protected function tearDown()
            {
                static::ensureKernelShutdown();
                static::$kernel = null;
            }
        }
    ');
} else {
    /**
     * @internal
     */
    trait ForwardCompatTestCaseTrait
    {
        protected function setUp(): void
        {
            $fs = new Filesystem();
            $fs->remove(\sys_get_temp_dir().'/WatchdogBundle/');
        }

        protected function tearDown(): void
        {
            static::ensureKernelShutdown();
            static::$kernel = null;
        }
    }
}
