<?php
session_start();
$usernameDisplay = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";

$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "asd";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Current time
date_default_timezone_set("Asia/Kuala_Lumpur");
$hour = date("H");

// If after 8pm, show tomorrow's weather
$forecastDate = ($hour >= 20) ? date("Y-m-d", strtotime("+1 day")) : date("Y-m-d");

// Fetch forecast
$stmt = $conn->prepare("SELECT * FROM forecast WHERE forecast_date = ?");
$stmt->bind_param("s", $forecastDate);
$stmt->execute();
$result = $stmt->get_result();
$weather = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Weather Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #eaf6ff, #d2eaff);
      color: #333;
    }
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #3b82f6, #60a5fa);
      padding: 30px 20px;
      color: white;
      position: fixed;
      height: 100vh;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.08);
      border-radius: 0 24px 24px 0;
    }
    .sidebar h2 {
      font-size: 26px;
      text-align: center;
      margin-bottom: 24px;
    }
    .sidebar img {
      display: block;
      margin: 0 auto 20px;
      border-radius: 12px;
      width: 100%;
      max-width: 180px;
    }
    .nav-links a {
      display: block;
      padding: 12px;
      margin: 10px 0;
      text-align: center;
      color: white;
      font-weight: 600;
      text-decoration: none;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 10px;
      transition: all 0.3s ease;
    }
    .nav-links a:hover { background-color: rgba(255, 255, 255, 0.3); }
    .content {
      margin-left: 260px;
      padding: 40px;
      animation: fadeIn 0.6s ease;
    }
    .card {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
      padding: 30px;
      margin-bottom: 30px;
      transition: transform 0.3s ease;
    }
    .card:hover { transform: translateY(-3px); }
    .card h3, .card h4 { color: #007acc; margin-bottom: 15px; }
    .grid {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 10px;
    }
    .grid-item {
      background: linear-gradient(145deg, #ffffff, #d8ecff);
      border-radius: 20px;
      padding: 20px;
      flex: 1 1 calc(30% - 20px);
      text-align: center;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }
    .grid-item:hover {
      transform: translateY(-4px);
      background: linear-gradient(145deg, #e8f4ff, #cce4ff);
    }
    .grid-item h4 { font-size: 17px; margin-bottom: 8px; color: #0064a8; }
    .grid-item p { font-size: 16px; font-weight: bold; }
    @media (max-width: 768px) {
      .sidebar { position: static; width: 100%; height: auto; border-radius: 0; text-align: center; }
      .content { margin-left: 0; padding: 20px; }
      .grid-item { flex: 1 1 100%; }
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px);} to {opacity: 1; transform: translateY(0);} }
    .username { font-weight: bold; margin-bottom: 10px; font-size: 16px; text-align: center; }
    .logout-btn {
      margin-top: 20px;
      padding: 12px;
      width: 100%;
      background: #ef4444;
      color: #fff;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .logout-btn:hover { background: #dc2626; }
  </style>
  <script>
    // Auto-refresh every 60 seconds
    setTimeout(function(){ location.reload(); }, 60000);
  </script>
</head>
<body>
  <div class="sidebar">
     <p class="username">👤 <?php echo htmlspecialchars($usernameDisplay); ?></p>
    <h2>Smart Weather</h2>
    <img src="asd.png" alt="Weather Icon" />
    <div class="nav-links">
      <?php
        echo '<a href="index.php">Home</a>';
        echo '<a href="device.php">Device</a>';
        echo '<a href="weather.php">Weather</a>';
        echo '<a href="instruction.php">Instruction</a>';
      ?>
    </div>
    <form action="logout.php" method="post" style="width:100%; margin-top:40px;">
      <button type="submit" class="logout-btn">🚪 Logout</button>
    </form>
  </div>

  <div class="content">
    <div class="card">
      <h3>Weather Forecast (<?php echo htmlspecialchars($forecastDate); ?>)</h3>
      <?php if ($weather): ?>
        <p><strong>Location:</strong> Kuala Lumpur</p>
        <p><strong>Temperature:</strong> <?php echo htmlspecialchars($weather['temperature'] ?? "N/A"); ?></p>
        <p><strong>Condition:</strong> <?php echo htmlspecialchars($weather['condition'] ?? "N/A"); ?></p>
      <?php else: ?>
        <p>No forecast data available.</p>
      <?php endif; ?>
    </div>

    <div class="grid">
      <div class="grid-item"><h4>💧 Humidity</h4><p><?php echo $weather['humidity'] ?? "N/A"; ?>%</p></div>
      <div class="grid-item"><h4>🌞 UV Index</h4><p><?php echo $weather['uv_index'] ?? "N/A"; ?></p></div>
      <div class="grid-item"><h4>💨 Wind</h4><p><?php echo $weather['wind'] ?? "N/A"; ?> km/h</p></div>
      <div class="grid-item"><h4>🌀 Air Quality</h4><p><?php echo $weather['air_quality'] ?? "N/A"; ?></p></div>
      <div class="grid-item"><h4>🌧️ Precipitation</h4><p><?php echo $weather['precipitation'] ?? "N/A"; ?> mm</p></div>
      <div class="grid-item"><h4>🧭 Pressure</h4><p><?php echo $weather['pressure'] ?? "N/A"; ?> hPa</p></div>
      <div class="grid-item"><h4>👁️ Visibility</h4><p><?php echo $weather['visibility'] ?? "N/A"; ?> km</p></div>
      <div class="grid-item"><h4>🌅 Sunrise</h4><p><?php echo $weather['sunrise'] ?? "N/A"; ?></p></div>
      <div class="grid-item"><h4>🌇 Sunset</h4><p><?php echo $weather['sunset'] ?? "N/A"; ?></p></div>
    </div>
  </div>
</body>
</html>
