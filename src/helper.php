<?php

if (!function_exists('envS')) {

    function envS($name, $default = '') {
        $value = getenv($name);

        //If we hev a value return it
        if (strlen($value) > 0) {
            return $value;
        }

        //Perepare session 
        $request = curl_init();
        $url = (string) getenv('VAULT_ADDR') . '/v1/secret/' . $name;
        $token = (string) getenv('VAULT_TOKEN');
        curl_setopt($request, CURLOPT_HTTPHEADER, ['X-Vault-Token: ' . $token]);
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

        //get response
        $response = curl_exec($request);

        //close session
        curl_close($request);

        //parse response
        if ($response === false) {
            return strlen($default) == 0 ? false : $default;
        }

        $r = json_decode($response, true);

        if (isset($r['data']['value'])) {
            return (string) $r['data']['value'];
        }

        return strlen($default) == 0 ? false : $default;
    }

}
