<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelscapes";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for journey data
$city = $region = $season = $days = $cost = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize journey data from the form
    $city = isset($_POST["city"]) ? trim($_POST["city"]) : "";
    $region = isset($_POST["region"]) ? trim($_POST["region"]) : "";
    $season = isset($_POST["season"]) ? trim($_POST["season"]) : "";
    $days = isset($_POST["days"]) ? trim($_POST["days"]) : "";
    $cost = isset($_POST["cost"]) ? trim($_POST["cost"]) : "";

    // Validate that necessary fields are not empty
    if (empty($city) || empty($region) || empty($season) || empty($days) || empty($cost)) {
        echo "All fields are required!";
    } else {
        // Prepare a SQL statement to insert the journey
        $sql = "INSERT INTO cities (city, region, season, days, cost) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            // Bind parameters: "sssid" means string, string, string, integer, decimal
            $stmt->bind_param("sssid", $city, $region, $season, $days, $cost);

            if ($stmt->execute()) {
                // Insertion was successful, redirect
                header("Location: adminviewjourneys.php");
                exit; // Make sure to stop further script execution after redirect
            } else {
                // Error occurred during insertion
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error in the prepared statement: " . $conn->error;
        }
    }
} else {
    echo "Form not submitted.";
}

$conn->close();
?>
