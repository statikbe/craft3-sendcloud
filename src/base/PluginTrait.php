<?php

namespace statikbe\sendcloud\base;

use Craft;
use statikbe\sendcloud\services\SendCloud;


trait PluginTrait
{
    // Static Properties
    // =========================================================================
    public static $plugin;

    // Private Methods
    // =========================================================================
    private function setPluginComponents()
    {
        $this->setComponents([
            'api' => SendCloud::class
        ]);
    }

}