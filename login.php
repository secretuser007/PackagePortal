<?php

$listAkun = 'monyet123@gmail.com
eluyangmonyet+12345@gmai.com';

foreach (explode('
', $listAkun) as $k => $v) {
    $token = login($v, 'bosskubabi');
    file_put_contents('tokenPP.txt', $v . "|" . $token . "\n", FILE_APPEND);
    echo $v . "|" . $token . "\n";
}

function login($email, $password)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/createAuthUri?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
        "identifier": "' . $email . '",
        "continueUri": "http://localhost"
    }');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = 'Host: www.googleapis.com';
    $headers[] = 'Accept: */*';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'X-Client-Version: iOS/FirebaseSDK/6.9.2/FirebaseCore-iOS';
    $headers[] = 'X-Ios-Bundle-Identifier: com.packageportal.customerapp';
    $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
    $headers[] = 'Accept-Language: en';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = json_decode(curl_exec($ch), true);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    // print_r($result);

    if ($result['registered'] == true) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/verifyPassword?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "email": "' . $email . '",
            "password": "' . $password . '",
            "returnSecureToken": true
        }');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Host: www.googleapis.com';
        $headers[] = 'Accept: */*';
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Client-Version: iOS/FirebaseSDK/6.9.2/FirebaseCore-iOS';
        $headers[] = 'X-Ios-Bundle-Identifier: com.packageportal.customerapp';
        $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
        $headers[] = 'Accept-Language: en';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch), true);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        $idToken = $result['idToken'];
        $refreshToken = $result['refreshToken'];

        if (!empty($idToken)) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/getAccountInfo?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                "idToken": "' . $idToken . '"
            }');
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

            $headers = array();
            $headers[] = 'Host: www.googleapis.com';
            $headers[] = 'Accept: */*';
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'X-Client-Version: iOS/FirebaseSDK/6.9.2/FirebaseCore-iOS';
            $headers[] = 'X-Ios-Bundle-Identifier: com.packageportal.customerapp';
            $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
            $headers[] = 'Accept-Language: en';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $result = json_decode(curl_exec($ch), true);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);

            if ($result['users'][0]['emailVerified'] == true) {
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                    "operationName": "update_user",
                    "variables": {
                        "last_logged_in": "' . date("Y-m-d") . 'T15:27:35.860Z"
                    },
                    "query": "mutation update_user($last_logged_in: timestamptz!) {\n  update_users(where: {}, _set: {last_logged_in: $last_logged_in}) {\n    affected_rows\n    returning {\n      id\n      __typename\n    }\n    __typename\n  }\n}\n"
                }');
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

                $headers = array();
                $headers[] = 'Host: prod.pp-app-api.com';
                $headers[] = 'Accept: */*';
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'User-Agent: FirebaseAuth.iOS/6.9.2 com.packageportal.customerapp/1.0.9 iPhone/12.4.8 hw/iPhone7_2';
                $headers[] = 'Accept-Language: en';
                $headers[] = 'Authorization: Bearer ' . $idToken;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);

                return "Bearer $idToken";
            }
        }
    }
}
