<?php
function getKrogerAccessToken($clientId, $clientSecret) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.kroger.com/v1/connect/oauth2/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=client_credentials&scope=product.compact",
        CURLOPT_HTTPHEADER => [
            "Authorization: Basic " . base64_encode($clientId . ":" . $clientSecret),
            "Content-Type: application/x-www-form-urlencoded"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        $responseArray = json_decode($response, true);
        return $responseArray['access_token'] ?? '';
    }
}
function getProductPrice($accessToken, $term, $locationId = 'defaultLocationId') {
    $curl = curl_init();
    $url = "https://api.kroger.com/v1/products?filter.term=".urlencode($term)."&filter.locationId=".$locationId;

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Authorization: Bearer $accessToken"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        $responseArray = json_decode($response, true);
        // Simplified: returning the price of the first product found
        return $responseArray['data'][0]['items'][0]['price']['regular'] ?? 'Price not found';
    }
}
?>