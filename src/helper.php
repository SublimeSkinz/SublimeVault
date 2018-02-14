<?php

use SublimeSkinz\SublimeVault\EnvLoader;

if (!function_exists('envS')) {
    function envS($name, $default = '') {
        $envLoader =  new EnvLoader();
        return $envLoader->envS($name, $default);
    }
}
