<?php
namespace SublimeSkinz\SublimeVault\Tests;

use PHPUnit\Framework\TestCase;
use SublimeSkinz\SublimeVault\EnvLoader;
use Dotenv;

class EnvLoaderIntegrationTest extends TestCase
{
    public function setUp()
    {
        Dotenv::load(__DIR__ . '/../', '.env');
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @group envLoader
     */
    public function testlLoadSecureEnvironmentSuccess()
    {
        $envLoader = new EnvLoader();
        $envLoader->loadSecureEnvironment();

        $this->assertEquals(getenv('SASDB_PASSWORD'), 'user');
        $this->assertEquals(getenv('SASDB_USERNAME'), 'user');
    }

    /**
     * @group envLoader
     */
    public function testlLoadSecureEnvironmentFail_wrongCredPath()
    {
        //setup wrong params
        Dotenv::makeMutable();
        Dotenv::setEnvironmentVariable('VAULT_CREDS_PATH', 'xxx');

        //instanciate class
        $envLoader = new EnvLoader();
        $envLoader->loadSecureEnvironment();

        //test
        $this->assertEquals(getenv('SASDB_PASSWORD'), 'xx');
        $this->assertEquals(getenv('SASDB_USERNAME'), 'xx');
    }

    /**
     * @group envLoader
     */
    public function testlLoadSecureEnvironmentFail_wrongBucket()
    {
        //setup wrong params
        Dotenv::makeMutable();
        Dotenv::setEnvironmentVariable('VAULT_BUCKET_NAME', 'xxx');

        //instanciate class
        $envLoader = new EnvLoader();
        $envLoader->loadSecureEnvironment();

        //test
        $this->assertEquals(getenv('SASDB_PASSWORD'), 'xx');
        $this->assertEquals(getenv('SASDB_USERNAME'), 'xx');
    }

    /**
     * @group envLoader
     */
    public function testlLoadSecureEnvironmentFail_wrongParamPath()
    {
        //setup wrong params
        Dotenv::makeMutable();
        Dotenv::setEnvironmentVariable('SASDB_USERNAME_VAULT', 'xxx');
        Dotenv::setEnvironmentVariable('SASDB_PASSWORD_VAULT', 'xxx');

        //instanciate class
        $envLoader = new EnvLoader();
        $envLoader->loadSecureEnvironment();

        //test
        $this->assertEquals(getenv('SASDB_PASSWORD'), 'xx');
        $this->assertEquals(getenv('SASDB_USERNAME'), 'xx');
    }
}
