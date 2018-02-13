<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function secEnv($name)
{
    $value = getenv($name);
    if (strpos($value, "VAULT:") === 0) {
        $ch = curl_init();
        $url = (string) getenv('VAULT_ADDR') . '/v1/secret/' . substr($value, 6);
        $token = (string) getenv('VAULT_TOKEN');
        $timeout = (int) getenv('VAULT_TIMEOUT') > 0 ? (int) getenv('VAULT_TIMEOUT') : 100;
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Vault-Token: ' . $token]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
        $res = curl_exec($ch);
        if ($res === false) {
            error_log('secEnv(): curl_error: ' . curl_error($ch));
            $value = false;
        } else {
            $r = json_decode($res, true);
            if (isset($r['data']['value'])) {
                $value = (string) $r['data']['value'];
            } else {
                error_log('secEnv(): no data. Vault response: ' . $res);
                $value = false;
            }
        }
        curl_close($ch);
    }
    return $value;
}
