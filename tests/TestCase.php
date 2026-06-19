<?php

namespace Mchardians\Bladescript\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mchardians\Bladescript\BladeScriptServiceProvider;
use Override;

abstract class TestCase extends Orchestra
{
    #[Override]
    public function getPackageProviders($app)
    {
        return [
            BladeScriptServiceProvider::class
        ];
    }
}
