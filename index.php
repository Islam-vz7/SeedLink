<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: register_login.php");
    exit();
}

// Include configuration
require 'config_reg.php';

// Fetch the logged-in user's details
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Handle checklist submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checklist'])) {
    $points_to_add = 0;

    // Check which tasks were completed
    if (isset($_POST['watered'])) {
        $points_to_add += 5;
    }
    if (isset($_POST['fertilized'])) {
        $points_to_add += 10;
    }
    if (isset($_POST['pruned'])) {
        $points_to_add += 7;
    }
    if (isset($_POST['checked_pests'])) {
        $points_to_add += 3;
    }

    // Update the user's points
    $new_points = $user['points'] + $points_to_add;
    $stmt = $pdo->prepare("UPDATE users SET points = ? WHERE username = ?");
    $stmt->execute([$new_points, $username]);

    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
}

// Fetch sensor data
$sensor_data = file_get_contents('http://localhost/SeedLink/fetch_data.php');
$sensor_data = json_decode($sensor_data, true);

// Fetch list of plants
$plants = file_get_contents('http://localhost:5000/get-plants');
$plants = json_decode($plants, true)["plants"];

$all_recommendations = [];

foreach ($plants as $plant) {
    $url = "http://localhost:5000/check-plant";
    $data = [
        "light" => $sensor_data["light"],
        "humidity" => $sensor_data["humidity"],
        "temperature" => $sensor_data["temperature"],
        "moisture" => $sensor_data["moisture"],
        "plant" => $plant
    ];

    $options = [
        "http" => [
            "header" => "Content-type: application/json\r\n",
            "method" => "POST",
            "content" => json_encode($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

if ($response !== FALSE) {
    $result = json_decode($response, true);
    if (isset($result["error"])) {
        echo "<p>Error: " . htmlspecialchars($result["error"]) . "</p>";
    } else {
        $all_recommendations[$plant] = $result["recommendation"];
    }
} else {
    echo "<p>Error fetching plant data.</p>";
}
}

// Check if a plant is selected
if (isset($_POST['plant'])) {
    $selected_plant = $_POST['plant'];
    $recommendation = $all_recommendations[$selected_plant];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeedLink - Plant Recommendation</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">SeedLink</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="feed.php">Feed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1 class="text-center">🌱 SeedLink - Plant Recommendation System</h1>
        <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p class="text-center">Your Points: <?php echo $user['points']; ?> 🌟</p>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Sensor Data
                    </div>
                    <div class="card-body">
                        <p><strong>Light:</strong> <?php echo $sensor_data["light"]; ?> lux</p>
                        <p><strong>Humidity:</strong> <?php echo $sensor_data["humidity"]; ?>%</p>
                        <p><strong>Temperature:</strong> <?php echo $sensor_data["temperature"]; ?>°C</p>
                        <p><strong>Moisture:</strong> <?php echo $sensor_data["moisture"]; ?> unit</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Plant Recommendation
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label for="plant">Choose a Plant:</label>
                                <select class="form-control" id="plant" name="plant">
                                    <option value="" disabled selected>Select a plant...</option>
                                    <?php foreach ($plants as $plant): ?>
                                        <option value="<?php echo $plant; ?>"><?php echo $plant; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Check Suitability</button>
                        </form>

                        <?php if (isset($recommendation)): ?>
                            <div class="mt-4">
                                <h4>Recommendation: <?php echo $selected_plant; ?></h4>
                                <p><?php echo $recommendation; ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Daily Checklist 🌟
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="watered" id="watered">
                                <label class="form-check-label" for="watered">
                                    Watered the plant? (+5 points)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fertilized" id="fertilized">
                                <label class="form-check-label" for="fertilized">
                                    Fertilized the plant? (+10 points)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="pruned" id="pruned">
                                <label class="form-check-label" for="pruned">
                                    Pruned the plant? (+7 points)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="checked_pests" id="checked_pests">
                                <label class="form-check-label" for="checked_pests">
                                    Checked for pests? (+3 points)
                                </label>
                            </div>
                            <button type="submit" name="checklist" class="btn btn-success mt-3">Submit Checklist</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>