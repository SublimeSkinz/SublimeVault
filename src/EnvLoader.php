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
        $envParams = array_merge($_SERVER, $_ENV);
        foreach ($envParams as $envParamKey => $value) {
            if (strpos($envParamKey, '_VAULT') !== false) {
                $s = str_replace('_VAULT', "", $envParamKey);
                if (!getenv($s)) {
                    $r = $this->vaultSecretClient->fetchSecretFromVault($value);
                    if (strlen($r) > 0) {
                        putenv("$s=$r");
                    }
                }
            }
        }
    }
}
