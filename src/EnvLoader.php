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
    public function loadSecureEnvironment()
    {
        $envParams = $_ENV;
        foreach ($envParams as $envParamKey => $value) {
            if (strpos($envParamKey, '_VAULT') !== false) {
                $s = str_replace('_VAULT', "", $envParamKey);
                $r = $this->vaultSecretClient->fetchSecretFromVault($value);
                if (strlen($r) > 0) {
                    Dotenv::makeMutable();
                    Dotenv::setEnvironmentVariable($s, $r);
                }
            }
        }
    }
}
