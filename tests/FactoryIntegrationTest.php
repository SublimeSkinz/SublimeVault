<?php
namespace SublimeSkinz\SublimeVault\Tests;

use PHPUnit\Framework\TestCase;
use SublimeSkinz\SublimeVault\VaultClientFactory;
use Dotenv;

class FactoryIntegrationTests extends TestCase
{
    protected $vaultAddr;
    protected $vaultCreds;
    protected $authType;
    protected $vaultBucket;

    public function setUp()
    {
        Dotenv::load(__DIR__ . '/../', '.env');
        $this->vaultAddr = getenv('VAULT_ADDR');
        $this->vaultCreds = getenv('VAULT_CREDS_PATH');
        $this->vaultBucket = getenv('VAULT_BUCKET_NAME');
        $this->authType = getenv('VAULT_AUTH_METHOD');
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group VaultFactoryTest
     * @dataProvider testCreateFail_invalidCredentials_DataProvider
     */
    public function testCreateFail_invalidCredentials($data, $expected)
    {
        $vaultFactory = new VaultClientFactory();
        $response = $vaultFactory->create($data['addr'], $data['authType'], $data['bucketName'], $data['credsPath']);

        //assert response
        $this->assertEquals($expected, $response);
    }

    public function testCreateFail_invalidCredentials_DataProvider()
    {
        return [
            [
                [
                    'addr' => $this->vaultAddr,
                    'authType' => 'appRole',
                    'bucketName' => 'bucketName',
                    'credsPath' => $this->vaultCreds
                ],
                null
            ],
            [
                [
                    'addr' => $this->vaultAddr,
                    'authType' => 'appRole',
                    'bucketName' => $this->vaultBucket,
                    'credsPath' => 'poc/xxxxx.json'
                ],
                null
            ],
            [
                [
                    'addr' => getenv('VAULT_ADDR'),
                    'authType' => 'appRole',
                    'bucketName' => 'bucketName',
                    'credsPath' => 'poc/xxxxx.json'
                ],
                null
            ],
        ];
    }

    /**
     *  @group VaultFactoryTest
     */
    public function testCreateSuccess()
    {
        $vaultFactory = new VaultClientFactory();
        $response = $vaultFactory->create($this->vaultAddr, $this->authType, $this->vaultBucket, $this->vaultCreds);
        $this->assertNotNull($response);
        $this->assertInstanceOf(\GuzzleHttp\Client::class, $response);
        $this->assertNotNull($response->getConfig()['headers']['X-Vault-Token']);
    }
}
