<?php
session_start();
$usernameDisplay = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Instruction - Smart Weather</title>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #3b82f6;
      --primary-light: #ecf5ff;
      --accent: #007acc;
      --text: #333;
      --bg-gradient: linear-gradient(to right, #f0faff, #dff3ff);
      --radius: 18px;
      --shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
      --hover-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg-gradient);
      color: var(--text);
    }

    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #3b82f6, #60a5fa);
      padding: 30px 20px;
      color: white;
      height: 100vh;
      position: fixed;
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
      margin: 12px 0;
      text-align: center;
      color: white;
      font-weight: 600;
      text-decoration: none;
      background-color: rgba(255, 255, 255, 0.15);
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .nav-links a:hover {
      background-color: rgba(255, 255, 255, 0.3);
    }

    .content {
      margin-left: 260px;
      padding: 50px 30px;
      animation: fadeIn 0.5s ease;
    }

    .instruction-card {
      background: white;
      padding: 40px;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      max-width: 880px;
      margin: auto;
    }

    .instruction-card h3 {
      font-size: 30px;
      color: var(--accent);
      text-align: center;
      margin-bottom: 35px;
    }

    .instruction-step {
      background: var(--primary-light);
      padding: 24px;
      border-radius: 16px;
      margin: 20px 0;
      border-left: 6px solid var(--primary);
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
    }

    .instruction-step:hover {
      box-shadow: var(--hover-shadow);
      transform: translateY(-3px);
    }

    .instruction-step h4 {
      margin: 0 0 10px;
      font-size: 20px;
      color: var(--accent);
    }

    .instruction-step p {
      margin: 0;
      font-size: 16px;
      line-height: 1.6;
      color: #444;
    }

    .action-buttons {
      margin-top: 40px;
      text-align: center;
    }

    .action-buttons a {
      display: inline-block;
      margin: 12px 16px;
      padding: 12px 26px;
      background-color: var(--primary);
      color: white;
      border-radius: 14px;
      text-decoration: none;
      font-weight: bold;
      font-size: 15px;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
      transition: all 0.3s ease;
    }

    .action-buttons a:hover {
      background-color: var(--accent);
      transform: scale(1.05);
    }

    @media (max-width: 768px) {
      .sidebar {
        position: static;
        width: 100%;
        height: auto;
        border-radius: 0;
        text-align: center;
      }

      .content {
        margin-left: 0;
        padding: 30px 20px;
      }

      .instruction-card {
        padding: 25px;
      }

      .instruction-card h3 {
        font-size: 24px;
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .username {
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 16px;
      text-align: center;
    }
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
</head>
<body>

  <div class="sidebar">
     <p class="username">👤 <?php echo htmlspecialchars($usernameDisplay); ?></p>
    <h2>Smart Weather</h2>

    <img src="asd.png" alt="Smart Weather Logo" />
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="device.php">Devices</a>
      <a href="weather.php">Weather</a>
      <a href="instruction.php">Instruction</a>
    </div>
    <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>

    <form action="logout.php" method="post" style="width:100%;">
      <button type="submit" class="logout-btn">🚪 Logout</button>
      </form>
  </div>

  <div class="content">
    <div class="instruction-card">
      <h3>How to Use the Dashboard</h3>

      <div class="instruction-step">
        <h4>1. Getting Started</h4>
        <p>Sign in with your account or register a new one. Use the sidebar to navigate through different pages.</p>
      </div>

      <div class="instruction-step">
        <h4>2. Adding Devices</h4>
        <p>Go to the "Devices" page, click "Add Device", provide the name and assign it to a specific location.</p>
      </div>

      <div class="instruction-step">
        <h4>3. Controlling Devices</h4>
        <p>Use the ON/OFF switches or control panels in the device cards to manage your devices in real time.</p>
      </div>

      <div class="instruction-step">
        <h4>4. Viewing Weather</h4>
        <p>Navigate to the "Weather" page to see live data like temperature, humidity, UV index and air quality. Updates every hour.</p>
      </div>

      <div class="instruction-step">
        <h4>5. Tips & Best Practices</h4>
        <p>Use strong passwords, turn on alerts for extreme weather, and organize your devices by location for efficiency.</p>
      </div>
      <div class="instruction-step">
        <h4>5. Tips & Best Practices</h4>
        <p>Use strong passwords, turn on alerts for extreme weather, and organize your devices by location for efficiency.</p>
      </div>

      <!-- 新增第 9 步 -->
      <div class="instruction-step">
        <h4>6. Account Management</h4>
        <p>If you wish to update your personal information or request account deletion, please contact our support team. 
           The company will assist you in updating or removing your account data securely.</p>
        <div style="margin-top:15px;">
          <a href="tel:+60123456789" 
             style="display:inline-block; padding:10px 18px; margin-right:10px; 
                    background:#3b82f6; color:#fff; border-radius:10px; text-decoration:none; font-weight:bold;">
            📞 Call Us
          </a>
          <a href="mailto:support@company.com" 
             style="display:inline-block; padding:10px 18px; 
                    background:#4caf50; color:#fff; border-radius:10px; text-decoration:none; font-weight:bold;">
            ✉️ Email Support
          </a>
        </div>
      </div>

      <div class="action-buttons">
        <a href="index.php">Back to Home</a>
        <a href="device.php">Manage Devices</a>
      </div>
    </div>
  </div>

</body>
</html>
