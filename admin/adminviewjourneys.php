<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelscapes";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variables for filters
$selectedRegions = [];
$selectedSeasons = [];
$selectedDays = [];

// Handle form submission for filtering
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedRegions = isset($_POST["region"]) ? $_POST["region"] : [];
    $selectedSeasons = isset($_POST["season"]) ? $_POST["season"] : [];
    $selectedDays = isset($_POST["days"]) ? $_POST["days"] : [];
}

// Build the SQL query based on selected filters
$sql = "SELECT * FROM cities WHERE 1";
if (!empty($selectedRegions) && !in_array("All", $selectedRegions)) {
    $sql .= " AND region IN ('" . implode("','", $selectedRegions) . "')";
}
if (!empty($selectedSeasons) && !in_array("All", $selectedSeasons)) {
    $sql .= " AND season IN ('" . implode("','", $selectedSeasons) . "')";
}
if (!empty($selectedDays) && !in_array("All", $selectedDays)) {
    $sql .= " AND days IN ('" . implode("','", $selectedDays) . "')";
}

// Execute query and fetch results
$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Handle deletion of a journey
if (isset($_GET["delete"])) {
    $journeyIdToDelete = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM cities WHERE cityid = ?");
    if ($stmt) {
        $stmt->bind_param("i", $journeyIdToDelete);
        $stmt->execute();
        $stmt->close();
        header("Location: adminviewjourneys.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/adminviewjourneys.css">
    <title>Available Journeys</title>
    <script>
        function toggleDropdown(filterName) {
            var dropdownContent = document.getElementById(filterName + "Dropdown");
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        }
    </script>
</head>
<body>
    <br>
    <a class="back" href="admindashboard.php">Back to Dashboard</a>
    <h1>City Journeys</h1>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <h3>Filters</h3>
        <div class="filter-box">
            <div class="custom-dropdown">
                <span onclick="toggleDropdown('region')">Region</span>
                <div id="regionDropdown" class="custom-dropdown-content">
                    <label><input type="checkbox" name="region[]" value="All" <?php if (in_array("All", $selectedRegions)) echo "checked"; ?>>All</label>
                    <label><input type="checkbox" name="region[]" value="North" <?php if (in_array("North", $selectedRegions)) echo "checked"; ?>>North</label>
                    <label><input type="checkbox" name="region[]" value="South" <?php if (in_array("South", $selectedRegions)) echo "checked"; ?>>South</label>
                    <label><input type="checkbox" name="region[]" value="East" <?php if (in_array("East", $selectedRegions)) echo "checked"; ?>>East</label>
                    <label><input type="checkbox" name="region[]" value="West" <?php if (in_array("West", $selectedRegions)) echo "checked"; ?>>West</label>
                    <label><input type="checkbox" name="region[]" value="Central" <?php if (in_array("Central", $selectedRegions)) echo "checked"; ?>>Central</label>
                    <label><input type="checkbox" name="region[]" value="North-East" <?php if (in_array("North-East", $selectedRegions)) echo "checked"; ?>>North-East</label>
                </div>
            </div>
        
            <div class="custom-dropdown">
                <span onclick="toggleDropdown('season')">Season</span>
                <div id="seasonDropdown" class="custom-dropdown-content">
                    <label><input type="checkbox" name="season[]" value="All" <?php if (in_array("All", $selectedSeasons)) echo "checked"; ?>>All</label>
                    <label><input type="checkbox" name="season[]" value="Winter" <?php if (in_array("Winter", $selectedSeasons)) echo "checked"; ?>>Winter</label>
                    <label><input type="checkbox" name="season[]" value="Summer" <?php if (in_array("Summer", $selectedSeasons)) echo "checked"; ?>>Summer</label>
                    <label><input type="checkbox" name="season[]" value="Monsoon" <?php if (in_array("Monsoon", $selectedSeasons)) echo "checked"; ?>>Monsoon</label>
                    <label><input type="checkbox" name="season[]" value="Spring" <?php if (in_array("Spring", $selectedSeasons)) echo "checked"; ?>>Spring</label>
                    <label><input type="checkbox" name="season[]" value="Autumn" <?php if (in_array("Autumn", $selectedSeasons)) echo "checked"; ?>>Autumn</label>
                </div>
            </div>

            <div class="custom-dropdown">
                <span onclick="toggleDropdown('days')">Days</span>
                <div id="daysDropdown" class="custom-dropdown-content">
                    <label><input type="checkbox" name="days[]" value="All" <?php if (in_array("All", $selectedDays)) echo "checked"; ?>>All</label>
                    <label><input type="checkbox" name="days[]" value="3" <?php if (in_array("3", $selectedDays)) echo "checked"; ?>>3</label>
                    <label><input type="checkbox" name="days[]" value="5" <?php if (in_array("5", $selectedDays)) echo "checked"; ?>>5</label>
                    <label><input type="checkbox" name="days[]" value="7" <?php if (in_array("7", $selectedDays)) echo "checked"; ?>>7</label>
                </div>
            </div>
        </div>
        <input type="submit" value="Filter">
    </form>
    <br>
    <h3>Available Cities</h3>
    <table border="1">
        <tr>
            <th>City ID</th>
            <th>City</th>
            <th>Region</th>
            <th>Season</th>
            <th>Days</th>
            <th>Cost</th>
            <th>Action</th>
        </tr>
        <?php foreach ($data as $row): ?>
        <tr>
            <td><?= $row["cityid"] ?></td>
            <td><?= $row["city"] ?></td>
            <td><?= $row["region"] ?></td>
            <td><?= $row["season"] ?></td>
            <td><?= $row["days"] ?></td>
            <td><?= $row["cost"] ?></td>
            <td><a href="?delete=<?= $row["cityid"] ?>" class="delete-button">Delete Journey</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <br><br>
    <a href="addjourney.html" class="insert-button">+ Add Journey</a>
</body>
</html>
