<?php

namespace SublimeSkinz\SublimeVault;

use SublimeSkinz\SublimeVault\VaultClientFactory;
use Dotenv;

class EnvLoader {

    protected $vault;

    public function __construct() {
        $this->vault = VaultClientFactory::create(getenv('VAULT_ADDR'), getenv('VAULT_AUTH_METHOD'), getenv("VAULT_BUCKET_NAME"), getenv("VAULT_CREDS_PATH"));
    }

    public function env($name, $default) {

        $val = array_key_exists($name . "_VAULT", $_ENV) ? $this->fetchSecretFromVault(getenv($name . "_VAULT")) : getenv($name);

        return strlen($val) > 0 ? $val : $default;
    }

    public function loadEnvironment() {
        $envParams = $_ENV;
        foreach ($envParams as $envParamKey => $value) {
            if (str_contains($envParamKey, '_VAULT')) {
                $s = str_replace('_VAULT', "", $envParamKey);
                $r = $this->fetchSecretFromVault($value);
                if (strlen($r) > 0) {
                    Dotenv::makeMutable();
                    Dotenv::setEnvironmentVariable($s, $r);
                }
            }
        }
    }

    private function fetchSecretFromVault($secret_path) {
        try {
            $response = $this->vault->request('GET', '/v1/secret/' . $secret_path);

            if ($response->getStatusCode() != 200) {
                return;
            }

            $r = json_decode($response->getBody()->getContents(), true);

            if (isset($r['data']['value'])) {
                return (string) $r['data']['value'];
            }
        } catch (\Exception $e) {
            return;
        }
    }

}
