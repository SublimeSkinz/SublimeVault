<?php

namespace SublimeSkinz\SublimeVault;

use Aws\S3\S3Client;
use GuzzleHttp\Client;

class EnvLoader {

    public function __construct() {
        $options['base_uri'] = getenv('VAULT_ADDR');
        $options['headers']['X-Vault-Token'] = getenv('VAULT_AUTH_METHOD') == "iam" ? $this->getVaultTokenWithIAM() : $this->getVaultTokenWithAppRole();
        $this->vault = new Client($options);
    }

    public function env($name, $default) {
        $val = array_key_exists($_ENV[$name . "_VAULT"]) ? $this->fetchSecretFromVault(getenv($name . "_VAULT")) : getenv($name);

        return strlen($val) > 0 ? $val : $default;
    }

    private function fetchSecretFromVault($secret_path) {
        try {
            $response = $this->vault->request('GET', '/v1/secret/' . $secret_path);

            if ($response->getStatusCode() != 200) {
                return ;
            }

            $r = json_decode($response->getBody()->getContents(), true);

            if (isset($r['data']['value'])) {
                return (string) $r['data']['value'];
            }
        } catch (\Exception $e) {
            return ;
        }
    }

    private function fetchAppRoleCreds($bucket_name, $creds_path) {
        $s3 = S3Client::factory();

        return json_decode($s3->getObject(array(
                                "Bucket" => $bucket_name,
                                "Key"    => $creds_path
                          ))["Body"],
                          true);
    }

    private function getVaultTokenWithAppRole() {
      $c = new Client();
      return json_decode($c->request("POST", getenv("VAULT_ADDR") ."/auth/approle/login", $this->fetchAppRoleCreds(getenv("VAULT_BUCKET_NAME"), getenv("VAULT_CREDS_PATH"))), true)["auth"]["client_token"];
    }

    private function getVaultTokenWithIAM(){
        return ;
    }

}
