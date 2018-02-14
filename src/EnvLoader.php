<?php

namespace SublimeSkinz\SublimeVault;

use GuzzleHttp\Client;

class EnvLoader {

    public function envS($name, $default = '') {

        $options['base_uri'] = getenv('VAULT_ADDR');
        $options['headers']['X-Vault-Token'] = getenv('VAULT_TOKEN');

        $vaultClient = new Client($options);
        
        try {
            $response = $vaultClient->request('GET', '/v1/secret/' . $name);
           
            if ($response->getStatusCode() != 200) {
                return strlen($default) == 0 ? false : $default;
            }

            $r = json_decode($response->getBody()->getContents(), true);
            
            if (isset($r['data']['value'])) {
                return (string) $r['data']['value'];
            }

            return strlen($default) == 0 ? false : $default;
        } catch (\Exception $e) {
            
        }
        
        return strlen($default) == 0 ? false : $default;
    }

}
