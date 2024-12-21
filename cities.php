<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travelscapes";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$selectedRegions = [];
$selectedSeasons = [];
$selectedDays = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedRegions = isset($_POST["region"]) ? $_POST["region"] : [];
    $selectedSeasons = isset($_POST["season"]) ? $_POST["season"] : [];
    $selectedDays = isset($_POST["days"]) ? $_POST["days"] : [];
}

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

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/cities.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .content-container {
            flex: 1;
            padding: 20px;
        }
        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        .custom-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            z-index: 1;
            padding: 10px;
        }
        .custom-dropdown:hover .custom-dropdown-content {
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color:rgb(0, 0, 0);
        }
        .view-button {
            text-decoration: none;
            color: #007bff;
        }
        .view-button:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <span class="logo">Travelscapes</span>
    </div>

    <div class="content-container">
        <h1>Travel Packages</h1>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="filter-box">
                <div class="custom-dropdown">
                    <span>Region</span>
                    <div class="custom-dropdown-content">
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
                    <span>Season</span>
                    <div class="custom-dropdown-content">
                        <label><input type="checkbox" name="season[]" value="All" <?php if (in_array("All", $selectedSeasons)) echo "checked"; ?>>All</label>
                        <label><input type="checkbox" name="season[]" value="Winter" <?php if (in_array("Winter", $selectedSeasons)) echo "checked"; ?>>Winter</label>
                        <label><input type="checkbox" name="season[]" value="Summer" <?php if (in_array("Summer", $selectedSeasons)) echo "checked"; ?>>Summer</label>
                        <label><input type="checkbox" name="season[]" value="Monsoon" <?php if (in_array("Monsoon", $selectedSeasons)) echo "checked"; ?>>Monsoon</label>
                        <label><input type="checkbox" name="season[]" value="Spring" <?php if (in_array("Spring", $selectedSeasons)) echo "checked"; ?>>Spring</label>
                        <label><input type="checkbox" name="season[]" value="Autumn" <?php if (in_array("Autumn", $selectedSeasons)) echo "checked"; ?>>Autumn</label>
                    </div>
                </div>

                <div class="custom-dropdown">
                    <span>Days</span>
                    <div class="custom-dropdown-content">
                        <label><input type="checkbox" name="days[]" value="All" <?php if (in_array("All", $selectedDays)) echo "checked"; ?>>All</label>
                        <label><input type="checkbox" name="days[]" value="3" <?php if (in_array("3", $selectedDays)) echo "checked"; ?>>3</label>
                        <label><input type="checkbox" name="days[]" value="5" <?php if (in_array("5", $selectedDays)) echo "checked"; ?>>5</label>
                        <label><input type="checkbox" name="days[]" value="7" <?php if (in_array("7", $selectedDays)) echo "checked"; ?>>7</label>
                    </div>
                </div>
            </div>
            <input type="submit" value="Apply">
        </form>

        <table>
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
                <td><?php echo $row["cityid"]; ?></td>
                <td><?php echo $row["city"]; ?></td>
                <td><?php echo $row["region"]; ?></td>
                <td><?php echo $row["season"]; ?></td>
                <td><?php echo $row["days"]; ?></td>
                <td>BDT <?php echo $row["cost"]; ?></td>
                <td><a href="viewjourney.php?city_id=<?php echo $row["cityid"]; ?>" class="view-button">View Journey</a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <footer>
        <p>&copy; 2023 Travelscapes. All rights reserved.</p>
    </footer>
</body>
</html>
