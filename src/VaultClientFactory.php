<?php

namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use Aws\S3\S3Client;

Class VaultClientFactory {

    public static function create($addr, $authType, $bucketName, $credsPath) {

        $factory = new VaultClientFactory();

        $vaultToken = $authType == 'iam' ?
                $factory->getVaultTokenWithIAM() :
                $factory->getVaultTokenWithAppRole($addr, $bucketName, $credsPath);

        $options['headers']['X-Vault-Token'] = $vaultToken;
        $options['base_uri'] = $addr;

        return new Client($options);
    }

    private function fetchAppRoleCreds($bucketName, $credsPath) {
        $s3 = S3Client::factory([
                    "region" => "eu-west-1",
                    "version" => "2006-03-01"
        ]);

        return json_decode($s3->getObject(array(
                    "Bucket" => $bucketName,
                    "Key" => $credsPath
                ))["Body"]);
    }

    private function getVaultTokenWithAppRole($addr, $bucketName, $credsPath) {
        $c = new Client();
        $options = [
            "json" => $this->fetchAppRoleCreds($bucketName, $credsPath)
        ];
        $response = $c->request('POST', $addr . "/v1/auth/approle/login", $options);
        $r = json_decode($response->getBody()->getContents(), true);
        return $r["auth"]["client_token"];
    }

    private function getVaultTokenWithIAM() {
        return;
    }

}
