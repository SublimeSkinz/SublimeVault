<?php
namespace SublimeSkinz\SublimeVault;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use SublimeSkinz\SublimeVault\VaultLogger;

class AwsClient
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new VaultLogger();
    }

    /**
     * Fetch appRole credentials from AWS bucket
     * 
     * @param string $bucketName
     * @param string $credsPath
     * @return json|null
     */
    public function fetchAppRoleCreds($bucketName, $credsPath)
    {
        try {
            $s3 = S3Client::factory([
                    "region" => getenv('VAULT_BUCKET_REGION'),
                    "version" => getenv('AWS_SDK_VERSION')
            ]);

            $response = $s3->getObject([
                "Bucket" => $bucketName,
                "Key" => $credsPath
            ]);

            return json_decode($response["Body"]);
        } catch (S3Exception $e) {
            $this->logger->alert($e->getMessage());
            return null;
        }
    }
}
