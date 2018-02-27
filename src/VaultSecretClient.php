<?php
namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Exception\GuzzleException;
use SublimeSkinz\SublimeVault\VaultClientFactory;
use SublimeSkinz\SublimeVault\VaultLogger;

class VaultSecretClient
{
    protected $vault;
    protected $logger;

    public function __construct()
    {
        $this->vault = VaultClientFactory::create(getenv('VAULT_ADDR'), getenv('VAULT_AUTH_METHOD'), getenv("VAULT_BUCKET_NAME"), getenv("VAULT_CREDS_PATH"));
        $this->logger = new VaultLogger();
    }

    /**
     * Fetch secret from vault
     *
     * @param string $secretPath
     * @return string
     */
    public function fetchSecretFromVault($secretPath)
    {
        if (!is_null($this->vault)) {
            try {
                $response = $this->vault->request('GET', '/v1/secret/' . $secretPath);

                if ($response->getStatusCode() != 200) {
                    return;
                }

                $r = json_decode($response->getBody()->getContents(), true);
            } catch (GuzzleException $e) {
                $this->logger->alert($e->getMessage());
                return;
            }

            if (isset($r['data']['value'])) {
                return (string) $r['data']['value'];
            }
        }
        return;
    }
}
