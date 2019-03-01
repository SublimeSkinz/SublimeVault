<?php
namespace SublimeSkinz\SublimeVault;

use SublimeSkinz\SublimeVault\VaultSecretClient;
use Dotenv;

class EnvLoader
{
    public function __construct()
    {
        $this->vaultSecretClient = new VaultSecretClient();
    }
    
    /**
     * Load Environment variables from Vault
     */
    public function envSecure($s) {
        $value = getenv($s);
        if (strpos($value, '_VAULT') !== false) {
            $envKey = str_replace('_VAULT', "", $value);
            return $this->vaultSecretClient->fetchSecretFromVault($envKey);
        }
        return getenv($s);
    }
}
