<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SublimeSkinz\SublimeVault\AwsClient;

class VaultClient
{

    protected $awsClient;

    public function __construct(AwsClient $awsClient)
    {
        $this->awsClient = $awsClient;
    }

    /**
     * Get Vault token from appRole credentials
     * 
     * @param string $addr
     * @param string $bucketName
     * @param string $credsPath
     * @return string|null
     */
    public function getVaultTokenWithAppRole($addr, $bucketName, $credsPath)
    {
        if ($bucketName && $credsPath) {
            $creds = $this->awsClient->fetchAppRoleCreds($bucketName, $credsPath);
            if (is_null($creds)) {
                return null;
            }
            try {
                $options = [
                    "json" => $creds
                ];
                $c = new Client();
                $response = $c->request('POST', $addr . "/v1/auth/approle/login", $options);
                if ($response->getStatusCode() == 200) {
                    $r = json_decode($response->getBody()->getContents(), true);
                    return $r["auth"]["client_token"];
                }
            } catch (GuzzleException $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * To do
     * @return type
     */
    public function getVaultTokenWithIAM()
    {
        return;
    }
}
