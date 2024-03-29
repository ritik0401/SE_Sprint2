<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["recipeUrl"])) {
    $recipeUrl = $_POST["recipeUrl"];

    // Fetch the content from the URL
    $html = file_get_contents($recipeUrl);

    // Use DOMDocument to parse the HTML
    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    // XPath for more complex querying
    $xpath = new DOMXPath($doc);

    // Extract the recipe name and other details
    $recipeName = $xpath->query("//*[@id='recipe']/div[2]/h1")->item(0)->nodeValue ?? 'Recipe Name Not Found';
    $prepTime = $xpath->query("//*[@id='recipe']/div[9]/div/dl/div[1]/dd")->item(0)->nodeValue ?? 'N/A';
    $servingSize = $xpath->query("//*[@id='recipe']/div[9]/div/dl/div[3]/dd")->item(0)->nodeValue ?? 'N/A';
    $numIngredients = $xpath->query("//*[@id='recipe']/div[9]/div/dl/div[2]/dd")->item(0)->nodeValue ?? 'N/A';

    // Ingredients and directions
    $ingredientsNodes = $xpath->query("//*[@id='recipe']/section[1]/ul/li");
    $stepsNodes = $xpath->query("//*[@id='recipe']/section[2]/ul/li");

    $ingredients = [];
    foreach ($ingredientsNodes as $node) {
        $ingredients[] = trim($node->nodeValue);
    }

    $steps = [];
    foreach ($stepsNodes as $node) {
        $steps[] = "- " . trim($node->nodeValue); 
    }

    // Output the results with HTML formatting
    echo "<h2>" . htmlspecialchars($recipeName) . "</h2>"; 
    echo "<strong>Prep Time:</strong> " . $prepTime . "<br>";
    echo "<strong>Serving Size:</strong> " . $servingSize . "<br>";
    echo "<strong>Number of Ingredients:</strong> " . $numIngredients . "<br>";
    
    // Modified Ingredients output to include clickable elements for AJAX call
    echo "<strong>Ingredients:</strong><ul>";
    foreach ($ingredients as $ingredient) {
        echo "<li><a href='#' class='ingredient' data-ingredient='" . htmlspecialchars($ingredient) . "'>" . htmlspecialchars($ingredient) . "</a></li>";
    }
    echo "</ul><br><br>";

    echo "<strong>Directions:</strong><br><ul>";
    foreach ($steps as $step) {
        echo "<li>" . htmlspecialchars($step) . "</li>";
    }
    echo "</ul>";

} else {
    // Display a simple form for input
    ?>
    <form action="" method="post">
        Recipe URL: <input type="text" name="recipeUrl">
        <input type="submit">
    </form>
    <?php
}

// JavaScript for handling the AJAX call
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ingredient').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const ingredient = this.getAttribute('data-ingredient');
            
            // Replace 'getIngredientPrice.php' with the actual path to your PHP script if necessary
            fetch('getIngredientPrice.php?ingredient=' + encodeURIComponent(ingredient))
                .then(response => response.text())
                .then(price => {
                    alert(`Price for ${ingredient}: $${price}`);
                })
                .catch(error => console.error('Error:', error));
        });
    });
});
</script>
