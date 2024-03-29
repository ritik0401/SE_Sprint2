<?php
require 'krogerAPI.php'; // Include the Kroger API functions

$response = curl_exec($ch);


$clientId = 'scraps-737e4017931b7285cdd1fa3eb9dd77a34951427737991553111';
$clientSecret = 'dzueKY0ng64i1b6hllftgNDcQDn5aSOKWREN2uTi';
$locationId = '01400376'; // This should be a valid Kroger location ID

$accessToken = getKrogerAccessToken($clientId, $clientSecret);

if(isset($_GET['ingredient'])) {
    $ingredient = $_GET['ingredient'];
    $price = getProductPrice($accessToken, $ingredient, $locationId);
    echo $price;
} else {
    echo "No ingredient specified";
}

?>
