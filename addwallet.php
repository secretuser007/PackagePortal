<?php

$akunToAdd = 'monyet+214620716@gmail.com|Bearer eyJhbGciO|zil13q
monyet+214620716@gmail.com|Bearer eyJhbGciO|zil13q
monyet+214620716@gmail.com|Bearer eyJhbGciO|zil13q'; // formatnya: email|token|zil wallet

foreach (explode('
', $akunToAdd) as $k => $v) {
    $e = explode('|', $v)[0];
    $t = explode('|', $v)[1];
    $w = explode('|', $v)[2];
    if (!empty($v)) {
        echo "Token: $e\n";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "operationName": "getWalletAddress",
            "variables": {},
            "query": "query getWalletAddress {\n  users {\n    wallet_address\n    __typename\n  }\n}\n"
        }');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Host: prod.pp-app-api.com';
        $headers[] = 'Accept: */*';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
        $headers[] = 'Accept-Language: en';
        $headers[] = 'Authorization: ' . $t;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        // print_r($result);

        echo "wallet: ".$result['data']['users'][0]['wallet_address']."\n";

        if ($result['data']['users'][0]['wallet_address'] == null) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "operationName": "update_user",
                "variables": {
                    "wallet_address": "' . $w . '"
                },
                "query": "mutation update_user($wallet_address: String!) {\n  update_users(where: {}, _set: {wallet_address: $wallet_address}) {\n    affected_rows\n    returning {\n      id\n      wallet_address\n      __typename\n    }\n    __typename\n  }\n}\n"
            }');
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

            $headers = array();
            $headers[] = 'Host: prod.pp-app-api.com';
            $headers[] = 'Accept: */*';
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
            $headers[] = 'Accept-Language: en';
            $headers[] = 'Authorization: ' . $t;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            // print_r($result);
        }
    }
}
