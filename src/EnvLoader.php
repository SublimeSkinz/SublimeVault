<?php

namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;

class EnvLoader
{
    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function envS($name, $default = null)
    {
        $options['base_uri'] = getenv('VAULT_ADDR');
        $options['headers']['X-Vault-Token'] = getenv('VAULT_TOKEN');

        $vaultClient = new Client($options);

        try {
            $response = $vaultClient->request('GET', '/v1/secret/' . $name);

            if ($response->getStatusCode() != 200) {
                return $default;
            }

            $r = json_decode($response->getBody()->getContents(), true);

            if (isset($r['data']['value'])) {
                return (string) $r['data']['value'];
            }
        } catch (\Exception $e) {
        }

        return $default;
    }
}
