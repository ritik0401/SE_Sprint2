<?php
if (isset($_GET['ingredient'])) {
    $ingredient = htmlspecialchars($_GET['ingredient']);

    $cleanedIngredient = preg_replace('/\s+/', ' ', trim($ingredient));

    $baseKrogerUrl = "https://www.kroger.com/search?query=";
    $customLink = $baseKrogerUrl . urlencode($cleanedIngredient);
    $baseWalmartUrl ="https://www.walmart.com/search?q=";
    $walmartLink = $baseWalmartUrl . urlencode($cleanedIngredient);

    $baseWholefoodsUrl = "https://www.wholefoodsmarket.com/search?text=";
    $wholefoodsLink = $baseWholefoodsUrl . urlencode($cleanedIngredient);

    $basePublixUrl = "https://www.ndmmarket.com/shop#!/?q=";
    $publixLink = $basePublixUrl . urlencode($cleanedIngredient);

    // Display the custom link
    echo "<p>Custom Kroger Link for <strong>$cleanedIngredient</strong>:</p>";
    echo "<a href='$customLink' target='_blank'>$customLink</a>";
    echo "<br>";
    echo "<a href='$walmartLink' target='_blank'>$walmartLink</a>";
    echo "<br>";

    echo "<a href='$wholefoodsLink' target='_blank'>$wholefoodsLink</a>";
    echo "<br>";

    
    echo "<a href='$publixLink' target='_blank'>$publixLink</a>";

} else {
    echo "<p>No ingredient specified.</p>";
}
?>
