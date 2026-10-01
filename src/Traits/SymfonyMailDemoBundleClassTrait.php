<?php

namespace Wexample\SymfonyMailDemo\Traits;

use Wexample\SymfonyHelpers\Traits\BundleClassTrait;
use Wexample\SymfonyMailDemo\WexampleSymfonyMailDemoBundle;

trait SymfonyMailDemoBundleClassTrait
{
    use BundleClassTrait;

    public static function getBundleClassName(): string
    {
        return WexampleSymfonyMailDemoBundle::class;
    }
}
