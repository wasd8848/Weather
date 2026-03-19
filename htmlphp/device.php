<?php
session_start();
$usernameDisplay = isset($_SESSION['username']) ? $_SESSION['username'] : "Guest";
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : "user"; // 默认普通用户

$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "asd";

$conn = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/* =========================
   🔹 处理操作（根据权限控制）
   ========================= */

// 公司才能新增
if ($userRole === "company" && isset($_POST['new_device_name']) && !empty($_POST['new_device_name'])) {
    $newDeviceName = $conn->real_escape_string($_POST['new_device_name']);
    $conn->query("INSERT INTO devices (name, status) VALUES ('$newDeviceName', 'off')");
}

// 所有人都能切换开关
if (isset($_POST['toggle_id'])) {
    $id = intval($_POST['toggle_id']);
    $conn->query("UPDATE devices 
                  SET status = IF(status='on','off','on') 
                  WHERE id=$id");
}

// 公司才能改名
if ($userRole === "company" && isset($_POST['rename_id']) && !empty($_POST['new_name'])) {
    $id = intval($_POST['rename_id']);
    $newName = $conn->real_escape_string($_POST['new_name']);
    $conn->query("UPDATE devices SET name='$newName' WHERE id=$id");
}

// 公司才能删除
if ($userRole === "company" && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM devices WHERE id=$id");
}

// 🔹 读取设备列表
$result = $conn->query("SELECT * FROM devices ORDER BY id DESC");
$devices = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Device Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary-color: #3b82f6;
      --primary-dark: #2563eb;
      --bg-color: #f1f5f9;
      --card-bg: #ffffff;
      --text-color: #1e293b;
      --radius: 16px;
      --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: var(--bg-color); color: var(--text-color); }
    .dashboard { display: flex; min-height: 100vh; }
    .sidebar { width: 240px; background: linear-gradient(180deg, #3b82f6, #60a5fa); padding: 30px 20px; color: white; box-shadow: 4px 0 12px rgba(0, 0, 0, 0.08); border-radius: 0 24px 24px 0; display: flex; flex-direction: column; align-items: center; }
    .sidebar h1 { font-size: 24px; margin-bottom: 20px; }
    .sidebar img { margin: 0 auto 20px; border-radius: 12px; width: 100%; max-width: 180px; }
    .username { font-weight: bold; margin-bottom: 10px; font-size: 16px; text-align: center; }
    .logout-btn { margin-top: 20px; padding: 12px; width: 100%; background: #ef4444; color: #fff; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; }
    .logout-btn:hover { background: #dc2626; }
    .nav-links { width: 100%; }
    .nav-links a { display: block; padding: 12px; margin: 10px 0; text-align: center; color: white; font-weight: 600; text-decoration: none; background-color: rgba(255, 255, 255, 0.15); border-radius: 10px; transition: 0.3s; }
    .nav-links a:hover { background-color: rgba(255, 255, 255, 0.3); }
    .main { flex-grow: 1; padding: 40px 60px; }
    .section-title { font-size: 32px; font-weight: 600; margin-bottom: 30px; }
    .card-section { background: var(--card-bg); padding: 25px 30px; border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 40px; }
    h3 { font-size: 22px; margin-bottom: 10px; color: var(--primary-dark); }
    .btn { background-color: var(--primary-color); color: white; padding: 8px 14px; border: none; border-radius: 10px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.3s; }
    .btn:hover { background-color: var(--primary-dark); }
    .btn.small { font-size: 12px; padding: 6px 10px; }
    .btn.delete { background-color: #ef4444; }
    .btn.delete:hover { background-color: #dc2626; }
    .device-grid { display: flex; gap: 20px; flex-wrap: wrap; }
    .device-card { background-color: #f0f9ff; padding: 20px; border-radius: 16px; flex: 1 1 240px; box-shadow: var(--shadow); }
    .device-card h4 { margin-top: 0; display: inline-block; }
    .status { padding: 6px 14px; border-radius: 20px; font-weight: bold; display: inline-block; font-size: 13px; }
    .status.online { background-color: #dcfce7; color: #16a34a; }
    .status.offline { background-color: #fee2e2; color: #dc2626; }
    .add-device-form { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; }
    .add-device-form input { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <div class="dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
      <p class="username">👤 <?php echo htmlspecialchars($usernameDisplay); ?> (<?= htmlspecialchars($userRole) ?>)</p>
      <h1>Smart Weather</h1>
      <img src="asd.png" alt="Smart Weather Logo">
      <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="device.php">Device</a>
        <a href="weather.php">Weather</a>
        <a href="instruction.php">Instruction</a>
      </div>
      <form action="logout.php" method="post" style="width:100%;">
        <button type="submit" class="logout-btn">🚪 Logout</button>
      </form>
    </aside>

    <!-- Main -->
    <main class="main">
      <h2 class="section-title">Weather Device Dashboard</h2>

      <!-- Add Device (只有公司能看到) -->
      <?php if ($userRole === "company"): ?>
      <section class="card-section">
        <h3>➕ Add Device</h3>
        <form method="post" class="add-device-form">
          <input type="text" name="new_device_name" placeholder="Enter device name...">
          <button type="submit" class="btn">Add</button>
        </form>
      </section>
      <?php endif; ?>

      <!-- Device List -->
      <section class="card-section">
        <h3>📡 Device Status</h3>
        <div class="device-grid">
          <?php foreach ($devices as $device): ?>
            <div class="device-card">
              <h4><?= htmlspecialchars($device['name']); ?></h4>
              <p>Status: 
                <span class="status <?= $device['status']=='on'?'online':'offline'; ?>">
                  <?= ucfirst($device['status']); ?>
                </span>
              </p>

              <!-- 🔘 开关按钮 (所有人都有) -->
              <form method="post" style="margin-top:10px;">
                <input type="hidden" name="toggle_id" value="<?= $device['id']; ?>">
                <button class="btn small" type="submit">
                  Turn <?= $device['status']=='on'?'Off':'On'; ?>
                </button>
              </form>

              <!-- ✏️ 改名表单 (只有公司) -->
              <?php if ($userRole === "company"): ?>
              <form method="post" style="margin-top:10px;">
                <input type="hidden" name="rename_id" value="<?= $device['id']; ?>">
                <input type="text" name="new_name" placeholder="New name" style="padding:6px; border-radius:6px;">
                <button class="btn small" type="submit">Rename</button>
              </form>

              <!-- 🗑 删除按钮 (只有公司) -->
              <form method="post" style="margin-top:10px;" onsubmit="return confirm('Are you sure you want to delete this device?');">
                <input type="hidden" name="delete_id" value="<?= $device['id']; ?>">
                <button class="btn small delete" type="submit">Delete</button>
              </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
