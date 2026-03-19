<?php
session_start();

// 如果没有 session 就默认 Guest
if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 0;
if (!isset($_SESSION['username'])) $_SESSION['username'] = "Guest";
$usernameDisplay = $_SESSION['username'];

// ✅ Connect to localhost DB
$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "asd";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// ✅ Handle zone selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zone'])) {
    $_SESSION['timezone'] = $_POST['zone'];
}

// ✅ Get current timezone (default Kuala Lumpur)
$timezone = $_SESSION['timezone'] ?? "Asia/Kuala_Lumpur";
date_default_timezone_set($timezone);

// ✅ Load zones from DB
$zones = $conn->query("SELECT * FROM zones");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Smart Weather Dashboard</title>
  <style>
    :root {
      --primary: #3b82f6;
      --bg: #f5faff;
      --card-bg: #ffffff;
      --text: #333;
      --subtext: #777;
      --accent: #007acc;
      --shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      --radius: 16px;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--bg);
      color: var(--text);
      display: flex;
      height: 100vh;
    }
    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, var(--primary), #60a5fa);
      padding: 30px 20px;
      color: white;
      position: fixed;
      height: 100vh;
      box-shadow: 4px 0 12px rgba(0, 0, 0, 0.08);
      border-radius: 0 24px 24px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }
    .user-box {
      font-size: 16px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #fff;
    }
    .sidebar h2 { font-size: 24px; margin-bottom: 20px; }
    .sidebar img {
      border-radius: 12px;
      margin-bottom: 20px;
      width: 160px;
      height: 160px;
      object-fit: cover;
    }
    .temp-box { font-size: 36px; font-weight: bold; margin-bottom: 8px; }
    .sidebar p { margin: 5px 0; font-size: 14px; }
    .nav-links { margin-top: 30px; width: 100%; flex-grow: 1; }
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
    .main {
      margin-left: 260px;
      flex-grow: 1;
      padding: 40px 30px;
      overflow-y: auto;
      animation: fadeIn 0.5s ease;
    }
    .greeting {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 10px;
      color: var(--accent);
    }
    .section {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
    }
    .card {
      background: var(--card-bg);
      border-radius: var(--radius);
      padding: 24px;
      flex: 1 1 250px;
      box-shadow: var(--shadow);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12); }
    .label { font-size: 14px; color: var(--subtext); margin-bottom: 8px; }
    .card h3, .card p { margin: 0; font-size: 22px; font-weight: 600; color: var(--text); }
    hr { border: none; height: 1px; background-color: #ddd; margin: 20px 0; }
    select, button {
      padding: 8px;
      border-radius: 8px;
      border: none;
      margin-top: 10px;
      font-weight: 600;
    }
    select { width: 100%; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @media (max-width: 768px) {
      .sidebar { position: static; width: 100%; border-radius: 0; height: auto; }
      .main { margin-left: 0; padding: 20px; }
      .card { flex: 1 1 100%; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <div class="user-box">
      👤 <?php echo htmlspecialchars($usernameDisplay); ?>
    </div>

    <h2>Smart Weather</h2>
    <img src="asd.png" alt="Weather Icon" />
    <div class="temp-box">28&deg;C</div>

    <!-- ✅ Zone selector -->
    <form method="post" action="" style="width:100%;">
      <label for="zone">📍 Location</label>
      <select name="zone" id="zone" onchange="this.form.submit()">
        <?php while($row = $zones->fetch_assoc()): ?>
          <option value="<?php echo $row['timezone']; ?>"
            <?php if ($timezone == $row['timezone']) echo "selected"; ?>>
            <?php echo $row['zone_name']; ?>
          </option>
        <?php endwhile; ?>
      </select>
    </form>

    <p>🕒 Updated: Just Now</p>

    <div class="nav-links">
      <a href="account.php">Account</a>
      <a href="device.php">Device</a>
      <a href="weather.php">Weather</a>
      <a href="instruction.php">Instruction</a>
    </div>

    <!-- Logout 按钮 -->
    <form action="logout.php" method="post" style="width:100%;">
      <button type="submit" class="logout-btn">🚪 Logout</button>
    </form>
  </div>

  <div class="main">
    <div class="greeting">
      <?php
        $hour = date("H");
        if ($hour >= 21) echo "🌙 Good night";
        else if ($hour >= 17) echo "🌆 Good evening";
        else if ($hour >= 12) echo "☀️ Good afternoon";
        else echo "🌤️ Good morning";
      ?>
    </div>
    <hr />
    <div class="section">
      <?php
        define("UV_SAFE_LIMIT", 5);

        $weatherData = [
          ["icon" => "🌞", "label" => "UV Index", "value" => 3.5],
          ["icon" => "💨", "label" => "Wind Status", "value" => 11.2],
          ["icon" => "💧", "label" => "Humidity", "value" => "67%"],
          ["icon" => "👁️", "label" => "Visibility", "value" => "Low"],
          ["icon" => "🌀", "label" => "Air Quality", "value" => "Fair"]
        ];

        echo '<div class="card"><div class="label">🕑 Time</div><p>' . date("h:i:sa") . '</p></div>';

        foreach ($weatherData as $data) {
            $valueDisplay = is_numeric($data['value']) 
                            ? (($data['value'] > UV_SAFE_LIMIT && $data['label'] === "UV Index") 
                                ? $data['value'] . " ⚠️" 
                                : $data['value']) 
                            : $data['value'];
            echo '<div class="card">';
            echo '<div class="label">' . $data['icon'] . ' ' . $data['label'] . '</div>';
            echo '<h3>' . $valueDisplay . '</h3>';
            echo '</div>';
        }
      ?>
    </div>
  </div>
</body>
</html>
