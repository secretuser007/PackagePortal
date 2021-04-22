<?php

$email = 'monyet'; //Jika emailnya monyet@gmail.com . Maka masukan hanya monyet. Karena menggunakan metode dot trik.
$password = 'bosskubabi';

$firstName = array("Loli", "Moti", "Geff", "Lord", "Reevan", "Bambang", "Kim", "Jennifer", "Kasian", "Ronald");
$lastName = array("Daphney", "Marsha", "Roam", "Ford", "Kefeer", "Maskk", "Lee", "Jedunn", "Rasya", "Nadira");

function gen_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),

        // 16 bits for "time_mid"
        mt_rand(0, 0xffff),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand(0, 0x0fff) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand(0, 0x3fff) | 0x8000,

        // 48 bits for "node"
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

while (true) {
    $emailBuild = $email . '+' . rand(11, 999999999) . '@gmail.com';
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/createAuthUri?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
        "identifier": "' . $emailBuild . '",
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

    print_r($result);

    if ($result['registered'] == false) {
        echo "[$emailBuild] Belum terdaftar\n";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/signupNewUser?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "email": "' . $emailBuild . '",
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

        if ($result['email'] == $emailBuild) {
            echo "[$emailBuild] Berhasil mendaftar\n";

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

            if ($result['users'][0]['emailVerified'] == false) {
                echo "[$emailBuild] Belum terverifikasi\n";

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, 'https://securetoken.googleapis.com/v1/token?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '{"grantType":"refresh_token","refreshToken":"' . $refreshToken . '"}');
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

                $headers = array();
                $headers[] = 'Host: securetoken.googleapis.com';
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

                $accessToken = $result['access_token'];
                $refreshTokenSecure = $result['refresh_token'];
                $idToken = $result['id_token'];

                // print_r($result);
                // echo $refreshToken;

                if (!empty($accessToken)) {
                    $fn = $firstName[array_rand($firstName)];
                    $ln = $lastName[array_rand($lastName)];

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                        "operationName": "insert_multiple_users",
                        "variables": {
                            "objects": {
                                "email": "' . $emailBuild . '",
                                "first_name": "' . $fn . '",
                                "last_name": "' . $ln . '",
                                "last_logged_in": "' . date("Y-m-d") . 'T12:34:41.141Z"
                            }
                        },
                        "query": "mutation insert_multiple_users($objects: [users_insert_input!]!) {\n  insert_users(objects: $objects) {\n    returning {\n      id\n      __typename\n    }\n    __typename\n  }\n}\n"
                    }');
                    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

                    $headers = array();
                    $headers[] = 'Host: prod.pp-app-api.com';
                    $headers[] = 'Accept: */*';
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'User-Agent: PackagePortal/2 CFNetwork/978.0.7 Darwin/18.7.0';
                    $headers[] = 'Accept-Language: id';
                    $headers[] = 'Authorization: Bearer ' . $accessToken;
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                    $result = json_decode(curl_exec($ch), true);
                    if (curl_errno($ch)) {
                        echo 'Error:' . curl_error($ch);
                    }
                    curl_close($ch);

                    $userId = $result['data']['insert_users']['returning'][0]['id'];

                    if (!empty($userId)) {
                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                            "operationName": "insert_new_install",
                            "variables": {
                                "object": {
                                    "user_id": ' . $userId . ',
                                    "install_id": "' . strtoupper(gen_uuid()) . '"
                                }
                            },
                            "query": "mutation insert_new_install($object: installs_insert_input!) {\n  insert_installs_one(object: $object) {\n    install_id\n    user_id\n    __typename\n  }\n}\n"
                        }');
                        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

                        $headers = array();
                        $headers[] = 'Host: prod.pp-app-api.com';
                        $headers[] = 'Accept: */*';
                        $headers[] = 'Content-Type: application/json';
                        $headers[] = 'User-Agent: PackagePortal/2 CFNetwork/978.0.7 Darwin/18.7.0';
                        $headers[] = 'Accept-Language: id';
                        $headers[] = 'Authorization: Bearer ' . $accessToken;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                        $result = json_decode(curl_exec($ch), true);
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        }
                        curl_close($ch);

                        if ($result['data']['insert_installs_one']['user_id'] == $userId) {
                            echo "[$emailBuild] Berhasil register device\n";

                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/identitytoolkit/v3/relyingparty/getOobConfirmationCode?key=AIzaSyChXf6sDuL4PcYBZiOUdP_tbsVx3Woa_Yc');
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, '{
                                "requestType": "VERIFY_EMAIL",
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

                            $result = curl_exec($ch);
                            if (curl_errno($ch)) {
                                echo 'Error:' . curl_error($ch);
                            }
                            curl_close($ch);

                            $format = "$emailBuild|$password|$userId|$fn $ln|" . date("Y-m-d") . "\n";

                            // echo "====$idToken====";

                            echo $format;
                            file_put_contents('akunPortalPackageNew.txt', $format, FILE_APPEND);

                            // print_r($result);

                            sleep(2);
                        } else {
                            echo "[$emailBuild] Gabisa register device\n";
                        }
                    } else {
                        echo "[$emailBuild] User id gak ada\n";
                    }
                } else {
                    print_r($result);
                    echo "[$emailBuild] Error gak ada akses token\n";
                }
            } else {
                echo "[$emailBuild] Sudah ke verif\n";
            }
        } else {
            echo "[$emailBuild] Gagal mendaftar\n";
        }
    } else {
        echo "[$emailBuild] Sudah terdaftar sebelumnya\n";
    }
}
