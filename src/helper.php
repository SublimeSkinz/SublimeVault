<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('loadEnvironmentS')) {
    function loadEnvironmentS() {
        $envLoader =  new EnvLoader();
        return $envLoader->loadEnvironment();
    }
}
