<?php
session_start();

// 数据库连接
$servername = "localhost";
$dbuser     = "root";
$dbpass     = "";
$dbname     = "asd";

$con = new mysqli($servername, $dbuser, $dbpass, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// 邀请码 (只有知道的人能注册 Company)
$COMPANY_INVITE_CODE = "abcd";

// 🔹 注册
if (isset($_POST['register'])) {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $role = $_POST['role']; // user or company

    if ($pass !== $confirm) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // 如果选 Company，必须输入正确的邀请码
        if ($role === "company") {
            $invite = trim($_POST['invite_code']);
            if ($invite !== $COMPANY_INVITE_CODE) {
                echo "<script>alert('Invalid invite code for Company account!');</script>";
                exit;
            }
        }

        $hashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user, $email, $hashed, $role);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please login.');</script>";
        } else {
            echo "<script>alert('Error: Email might already exist.');</script>";
        }
        $stmt->close();
    }
}

// 🔹 登录
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $con->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role']; // 保存角色
            echo "<script>alert('Login successful!'); window.location='index.php';</script>";
            exit;
        } else {
            echo "<script>alert('Invalid password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.');</script>";
    }
    $stmt->close();
}

// 🔹 Guest 模式
if (isset($_POST['guest'])) {
    $_SESSION['user_id'] = 0;
    $_SESSION['username'] = "Guest";
    $_SESSION['role'] = "guest";
    echo "<script>alert('Logged in as Guest!'); window.location='index.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login & Register</title>
<link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; }
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(to right, #dbeeff, #f5f8fc);
  color: #333;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  flex-direction: column;
}
.card {
  background: #fff;
  padding: 40px 30px;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.08);
  width: 100%;
  max-width: 380px;
  display: none;
  animation: fadeIn 0.5s ease-in-out;
}
.card.active { display: block; }
.card h2 { margin: 0 0 24px; text-align: center; color: #3399ff; }
.card input {
  padding: 12px; margin-top: 12px; width: 100%;
  border: 1px solid #ccc; border-radius: 8px; font-size: 14px;
}
.card button {
  padding: 12px; margin-top: 24px; width: 100%;
  background-color: #3399ff; border: none; color: white;
  font-size: 16px; font-weight: bold; border-radius: 8px;
  cursor: pointer; transition: background 0.3s ease;
}
.card button:hover { background-color: #287acc; }
.switch { text-align: center; margin-top: 18px; font-size: 14px; }
.switch a {
  color: #3399ff; text-decoration: none; font-weight: bold; cursor: pointer;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 480px) { .card { padding: 30px 20px; } }
</style>
</head>
<body>

<!-- 登录卡片 -->
<div class="card active" id="loginCard">
  <h2>Login</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit" name="login">Login</button>
  </form>
  <form method="POST">
    <button type="submit" name="guest" style="background:#777; margin-top:10px;">
      Continue as Guest
    </button>
  </form>
  <div class="switch">
    Don't have an account? <a onclick="switchToRegister()">Register</a>
  </div>
</div>

<!-- 注册卡片 -->
<div class="card" id="registerCard">
  <h2>Register</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required />
    <input type="email" name="email" placeholder="Email" required />
    <input type="password" name="password" placeholder="Password" required />
    <input type="password" name="confirm_password" placeholder="Confirm Password" required />

    <label>Role:</label>
    <select name="role" id="roleSelect" onchange="toggleInviteCode()">
      <option value="user">User</option>
      <option value="company">Company</option>
    </select>

    <!-- 邀请码输入框，默认隐藏 -->
    <input type="text" name="invite_code" id="inviteCode" class="hidden" placeholder="Enter Company Invite Code" />

    <button type="submit" name="register">Register</button>
  </form>
  <div class="switch">
    Already have an account? <a onclick="switchToLogin()">Login</a>
  </div>
</div>

<script>
function switchToRegister() {
  document.getElementById('loginCard').classList.remove('active');
  document.getElementById('registerCard').classList.add('active');
}
function switchToLogin() {
  document.getElementById('registerCard').classList.remove('active');
  document.getElementById('loginCard').classList.add('active');
}
function toggleInviteCode() {
  const role = document.getElementById('roleSelect').value;
  const inviteInput = document.getElementById('inviteCode');
  if (role === "company") {
    inviteInput.classList.remove("hidden");
  } else {
    inviteInput.classList.add("hidden");
  }
}
</script>
</body>
</html>