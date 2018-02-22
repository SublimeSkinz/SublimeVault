<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('loadSecureEnvironment')) {
    function loadSecureEnvironment() {
        if(!getenv('VAULT_ADDR')){
            return;
        }
        $envLoader =  new EnvLoader();
        return $envLoader->loadSecureEnvironment();
    }
}
