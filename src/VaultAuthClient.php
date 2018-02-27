<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SublimeSkinz\SublimeVault\AwsClient;
use SublimeSkinz\SublimeVault\VaultLogger;

class VaultAuthClient
{
    protected $awsClient;
    protected $logger;
    protected $guzzleClient;

    public function __construct()
    {
        $this->awsClient = new AwsClient();
        $this->guzzleClient = new Client();
        $this->logger = new VaultLogger();
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
                $response = $this->guzzleClient->request('POST', $addr . "/v1/auth/approle/login", ["json" => $creds]);
                if ($response->getStatusCode() == 200) {
                    return json_decode($response->getBody()->getContents(), true)["auth"]["client_token"];
                }
            } catch (GuzzleException $e) {
                $this->logger->alert($e->getMessage());
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
