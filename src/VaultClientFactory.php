<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use SublimeSkinz\SublimeVault\VaultAuthClient;

class VaultClientFactory
{
    /**
     * Create a vault client
     *
     * @param string $addr
     * @param string $authType
     * @param string $bucketName
     * @param string $credsPath
     * @return Client
     */
    public static function create($addr, $authType, $bucketName, $credsPath)
    {
        $vaultAuthClient = new VaultAuthClient();

        $vaultToken = $authType == 'iam' ?
            $vaultAuthClient->getVaultTokenWithIAM() :
            $vaultAuthClient->getVaultTokenWithAppRole($addr, $bucketName, $credsPath);

        if (is_null($vaultToken)) {
            return null;
        }

        $options['headers']['X-Vault-Token'] = $vaultToken;
        $options['base_uri'] = $addr;
        return new Client($options);
    }
}
