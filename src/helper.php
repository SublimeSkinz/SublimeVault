<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('envS')) {
    function envS($name, $default = null)
    {
        $envLoader = new EnvLoader();
        return $envLoader->envS($name, $default);
    }
}
