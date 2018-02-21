<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SublimeSkinz\SublimeVault\AwsClient;
use Analog\Logger;
use Analog\Handler\File;

class VaultAuthClient
{

    protected $awsClient;
    protected $logger;

    public function __construct()
    {
        $this->awsClient = new AwsClient();
        $logger = new Logger();
        $logger->handler(File::init(__DIR__ . '/../logs/errors.log'));
        $this->logger = $logger;
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
