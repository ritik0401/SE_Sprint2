<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); 
if (!isset($_SESSION['username'])) 
{
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$host = "localhost";
$user = "rpatel245";
$pass = "rpatel245";
$dbname = "rpatel245";

// Action for saving a recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] === "saveRecipe") {
    if (!isset($_SESSION['username'])) {
        echo "Please log in to save recipes.";
    } else {
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $username = $_SESSION['username'];
        $sql_fetch_userid = "SELECT id FROM users WHERE username = ?";
        $stmt_userid = $conn->prepare($sql_fetch_userid);
        if (!$stmt_userid) die("Error preparing statement: " . $conn->error);

        $stmt_userid->bind_param("s", $username);
        $stmt_userid->execute();
        $result_userid = $stmt_userid->get_result();
        if ($result_userid->num_rows > 0) {
            $row = $result_userid->fetch_assoc();
            $user_id = $row['id'];
        } else {
            echo "User not found.";
            $conn->close();
            exit;
        }
        $stmt_userid->close();

        $recipe_name = $_POST['recipeName'];
        $prep_time = $_POST['prepTime'];
        $serving_size = $_POST['servingSize'];
        $num_ingredients = $_POST['numIngredients'];
        $recipe_url = $_POST['recipeUrl'];

        $sql = "INSERT INTO saved_recipes (user_id, recipe_name, prep_time, serving_size, num_ingredients, recipe_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Error preparing statement: " . $conn->error);

        $stmt->bind_param("isssss", $user_id, $recipe_name, $prep_time, $serving_size, $num_ingredients, $recipe_url);
        if ($stmt->execute()) {
            echo "<script>alert('Recipe saved successfully!');</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        $conn->close();
    }
}

// Action for deleting a saved recipe
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] === "deleteRecipe") {
    if (!isset($_SESSION['username'])) {
        echo "Please log in to delete recipes.";
    } else {
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (!isset($_POST['recipeId']) || !is_numeric($_POST['recipeId'])) {
            echo "Invalid recipe ID.";
            $conn->close();
            exit();
        }

        $recipeId = intval($_POST['recipeId']); 

        $sql = "DELETE FROM saved_recipes WHERE recipe_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
            $conn->close();
            exit();
        }

        $stmt->bind_param("i", $recipeId);
        if (!$stmt->execute()) {
            echo "Error executing statement: " . htmlspecialchars($stmt->error);
        } else {
            echo "<script>alert('Recipe deleted successfully!');</script>";
        }
        $stmt->close();
        $conn->close();
    }
}


// Action for viewing saved recipes
$savedRecipes = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["action"]) && $_POST["action"] === "viewRecipes") {
    if (!isset($_SESSION['username'])) {
        echo "Please log in to view saved recipes.";
    } else {
        $conn = new mysqli($host, $user, $pass, $dbname);
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $username = $_SESSION['username'];
        $sql_fetch_userid = "SELECT id FROM users WHERE username = ?";
        $stmt_userid = $conn->prepare($sql_fetch_userid);
        if (!$stmt_userid) die("Error preparing statement: " . $conn->error);

        $stmt_userid->bind_param("s", $username);
        $stmt_userid->execute();
        $result_userid = $stmt_userid->get_result();
        if ($result_userid->num_rows === 0) {
            echo "User not found.";
            $conn->close();
            exit;
        }
        $user_id = $result_userid->fetch_assoc()['id'];
        $stmt_userid->close();

        $sql = "SELECT recipe_id, recipe_name, prep_time, serving_size, num_ingredients, recipe_url FROM saved_recipes WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("Error preparing statement: " . $conn->error);

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $savedRecipes[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Scraper</title>
    <style>
        body {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 2rem;
            background-color: #f5f5f5;
            position: relative;
            min-height: 100vh;
        }
        .right-side {
            flex: 0 0 30%;
            margin-right: 2rem;
            background-color: #fff;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .left-side {
            flex: 1;
            background-color: #fff;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .divider {
            height: 1px;
            width: 100%;
            background-color: #ddd;
            margin: 1rem 0;
        }
        h1 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 1rem;
        }
        footer {
            position: relative;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background-color: #fff;
            border-top: 1px solid #ddd;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="right-side">
        <h1>Scrapes</h1>
        <form action="" method="post">
            <label for="recipeUrl">Recipe URL:</label>
            <input type="text" id="recipeUrl" name="recipeUrl" required>
            <button type="submit">Import Recipe</button>
        </form>

        <form action="" method="post">
            <input type="hidden" name="action" value="viewRecipes">
            <button type="submit">View Saved Recipes</button>
        </form>
    </div>

    <div class="left-side">
        <?php
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
            echo "<div><strong>Prep Time:</strong> " . $prepTime . "</div>";
            echo "<div><strong>Serving Size:</strong> " . $servingSize . "</div>";
            echo "<div><strong>Number of Ingredients:</strong> " . $numIngredients . "</div>";

            echo "<div><strong>Images:</strong>";

            // Modified Ingredients output to include clickable elements for AJAX call
            echo "<div><strong>Ingredients:</strong><ul>";
            foreach ($ingredients as $ingredient) {
                echo "<li><a href='showIngredientLink.php?ingredient=" .urlencode($ingredient) . "' target='_blank'>" . htmlspecialchars($ingredient) . "</a></li>";
            }
            echo "</ul></div>";

            echo "<div><strong>Directions:</strong><ul>";
            foreach ($steps as $step) {
                echo "<li>" . htmlspecialchars($step) . "</li>";
            }
            echo "</ul></div>";
        }
        ?>
<form action="" method="post">
    <input type="hidden" name="action" value="saveRecipe">
    <input type="hidden" name="recipeName" value="<?php echo htmlspecialchars($recipeName); ?>">
    <input type="hidden" name="prepTime" value="<?php echo htmlspecialchars($prepTime); ?>">
    <input type="hidden" name="servingSize" value="<?php echo htmlspecialchars($servingSize); ?>">
    <input type="hidden" name="numIngredients" value="<?php echo htmlspecialchars($numIngredients); ?>">
    <input type="hidden" name="recipeUrl" value="<?php echo htmlspecialchars($recipeUrl); ?>">
    <button type="submit">Save Recipe</button>
</form>
        <?php
        if (!empty($savedRecipes)) {
            echo "<h2>Saved Recipes</h2>";
            foreach ($savedRecipes as $recipe) {
                echo "<div><strong>Name:</strong> {$recipe['recipe_name']}</div>";
                echo "<div><strong>Prep Time:</strong> {$recipe['prep_time']}</div>";
                echo "<div><strong>Serving Size:</strong> {$recipe['serving_size']}</div>";
                echo "<div><strong>Number of Ingredients:</strong> {$recipe['num_ingredients']}</div>";
                echo "<div><strong>URL:</strong> <a href='{$recipe['recipe_url']}' target='_blank'>View Recipe</a></div>";
                echo "<form action='' method='post' style='margin-top: 1rem;'>";
                echo "<input type='hidden' name='action' value='deleteRecipe'>";
                echo "<input type='hidden' name='recipeId' value='{$recipe['recipe_id']}'>";

                echo "<button type='submit'>Delete Recipe</button>";

                echo "</form>";
                echo "<hr>"; 
            }
        }
        ?>
    </div>


    <footer>
        <a href="delete_account.php">
        <button id="deleteAccountBtn">Delete Account</button>
    </a>
        <a href= logout.php>
        <button>Logout</button>
    </a>
    </footer>

    <script>
  document.getElementById('deleteAccountBtn').addEventListener('click', function(e) {
    e.preventDefault(); 
    var userConfirmed = confirm("Are you sure you want to delete your account? This action cannot be undone.");
    
    if (userConfirmed) {
      window.location.href = 'delete_account.php';
    }
  });
</script>

</body>
</html>
