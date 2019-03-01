<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('envSecure')) {
    function envSecure($s)
    {
        if (!getenv('VAULT_ADDR')) {
            return;
        }
        $envLoader =  new EnvLoader();
        return $envLoader->envSecure($s);
    }
}
