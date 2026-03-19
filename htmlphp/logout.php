<?php
session_start();

// 🔹 清空所有 session 数据
$_SESSION = array();

// 🔹 如果有 session cookie，也清掉
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 🔹 再重新初始化成 Guest
$_SESSION['user_id'] = 0;
$_SESSION['username'] = "Guest";
$_SESSION['role'] = "user";  // 默认 guest 就是普通用户

// 🔹 跳回主页
header("Location: index.php");
exit();