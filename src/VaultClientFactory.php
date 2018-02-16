<?php

namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

Class VaultClientFactory {

    public static function create($addr, $authType, $bucketName, $credsPath) {

        $factory = new VaultClientFactory();

        $vaultToken = $authType == 'iam' ?
                $factory->getVaultTokenWithIAM() :
                $factory->getVaultTokenWithAppRole($addr, $bucketName, $credsPath);

        if (is_null($vaultToken)) {
            return null;
        }

        $options['headers']['X-Vault-Token'] = $vaultToken;
        $options['base_uri'] = $addr;
        return new Client($options);
    }

    private function fetchAppRoleCreds($bucketName, $credsPath) {
        try {
            $s3 = S3Client::factory([
                 "region" => "eu-west-1",
                 "version" => "2006-03-01"
            ]);

            $response = $s3->getObject([
                "Bucket" => $bucketName,
                "Key" => $credsPath
            ]);

            return json_decode($response["Body"]);
        } catch (S3Exception $e) {
            //echo $e->getMessage() . "\n";       
            return null;
        }
    }

    private function getVaultTokenWithAppRole($addr, $bucketName, $credsPath) {

        if ($bucketName && $credsPath) {
            $creds = $this->fetchAppRoleCreds($bucketName, $credsPath);
            if (is_null($creds)) {
                return null;
            }
            $options = [
                "json" => $creds
            ];
            $c = new Client();
            $response = $c->request('POST', $addr . "/v1/auth/approle/login", $options);
            if ($response->getStatusCode() == 200) {
                $r = json_decode($response->getBody()->getContents(), true); 
                return $r["auth"]["client_token"];
            }
        }
        return null;
    }

    private function getVaultTokenWithIAM() {
        return;
    }

}
