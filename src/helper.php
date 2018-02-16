<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('loadEnvironmentS')) {
    function loadEnvironmentS() {
        if(!getenv('VAULT_ADDR')){
            return;
        }
        $envLoader =  new EnvLoader();
        return $envLoader->loadEnvironment();
    }
}
