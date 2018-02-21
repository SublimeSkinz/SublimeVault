<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use SublimeSkinz\SublimeVault\VaultClient;
use SublimeSkinz\SublimeVault\AwsClient;

Class VaultClientFactory
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
        $s3Client = new AwsClient();
        $vaultClient = new VaultClient($s3Client);

        $vaultToken = $authType == 'iam' ?
            $vaultClient->getVaultTokenWithIAM() :
            $vaultClient->getVaultTokenWithAppRole($addr, $bucketName, $credsPath);

        if (is_null($vaultToken)) {
            return null;
        }

        $options['headers']['X-Vault-Token'] = $vaultToken;
        $options['base_uri'] = $addr;
        return new Client($options);
    }
}
