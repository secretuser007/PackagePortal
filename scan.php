<?php
// Limit isi angka 5 aja
// Ente wajib make login.php Y
echo "Limit: ";
$limitResi = trim(fgets(STDIN));

while(true){
$listToken = file_get_contents('tokenPP.txt');

foreach (explode("\n", $listToken) as $l => $b) {
    $ts = explode('|', $b)[1];
    $e = explode('|', $b)[0];

    echo "$ts:$e \n";

    //  Pastiin ente udah naro list resi di file resiNewAliex.txt  
    $trackingNumber = file_get_contents('resiNewAliex.txt');

    // Cek apakah wallet ada?
    $walletZil = requestPP('{"operationName":"getWalletAddress","variables":{},"query":"query getWalletAddress {\n  users {\n    wallet_address\n    __typename\n  }\n}\n"}', $ts)['data']['users'][0]['wallet_address'];

    // User id
    $userId = requestPP('{"operationName":null,"variables":{},"query":"{\n  users {\n    __typename\n    id\n  }\n}\n"}', $ts)['data']['users'][0]['id'];

    if (!empty($walletZil)) {
        echo "wallet: $walletZil \n";
        echo "id: $userId \n";

        // break lines
        $bl = 0;

        foreach (explode("
", $trackingNumber) as $k => $v) {
            // echo "$bl \n";

            if ($bl >= $limitResi) {
                continue;
                $GLOBALS['bl'] == 0;
                echo "Sudah menyentuk limit untuk akun $e\n";
            } else {
                $contents = file_get_contents('resiNewAliex.txt');
                $first_line = substr($contents, 0, 13);
                file_put_contents('resiNewAliex.txt', substr($contents, 13 + 2));

                $insertScan = requestPP('{"operationName":"insert_multiple_scans","variables":{"objects":[{"user_id":' . $userId . ',"tracking_number":"' . $v . '","longitude":106.'.rand(1111,9999).',"latitude":-5.'.rand(1111,9999).',"accuracy":165,"batch_uuid":"' . gen_uuid() . '"}]},"query":"mutation insert_multiple_scans($objects: [scans_insert_input!]!) {\n  insert_scans(objects: $objects) {\n    returning {\n      id\n      __typename\n    }\n    __typename\n  }\n}\n"}', $ts);

                $idUpload = $insertScan['data']['insert_scans']['returning'][0]['id'];

                if (!empty($idUpload)) {
                    $giveRate = requestPP('{"operationName":"add_multiple_ratings","variables":{"objects":[{"scan_id":"' . $idUpload . '","up":true,"descriptors":["Delivered with care","Quick & efficient","On time","Friendly service"]}]},"query":"mutation add_multiple_ratings($objects: [ratings_insert_input!]!) {\n  insert_ratings(objects: $objects) {\n    affected_rows\n    __typename\n  }\n}\n"}', $ts);

                    $affected = $giveRate['data']['insert_ratings']['affected_rows'];

                    if ($affected == 1) {
                        echo "[$v] => Berhasil | $idUpload \n";
                        $bl++;
                    } else {
                        echo "[$v] => Gagal | $idUpload \n";
                    }
                } else {
                    echo "id upload tidak ditemukan\n";
                }
            }
        }
    } else {
        echo "wallet belum diisi\n";
    }
}
}

function requestPP($data, $token)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://prod.pp-app-api.com/v1/graphql');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

    $headers = array();
    $headers[] = 'Host: prod.pp-app-api.com';
    $headers[] = 'Accept: */*';
    $headers[] = 'Content-Type: application/json';
    $headers[] = 'User-Agent: PackagePortal/2 CFNetwork/978.0.7 Darwin/18.7.0';
    $headers[] = 'Accept-Language: id';
    $headers[] = 'Authorization: ' . $token;
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($result, true);
}

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
