<?php
// Script test kết nối DB và thử INSERT
header('Content-Type: text/html; charset=utf-8');

echo "<h2>Test kết nối Database</h2>";

// 1. Test kết nối
try {
    $conn = new PDO("mysql:host=localhost;dbname=terrarium_db;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>✅ Kết nối database thành công!</p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Kết nối thất bại: " . $e->getMessage() . "</p>";
    exit;
}

// 2. Test INSERT
echo "<h2>Test INSERT user</h2>";
try {
    $name = "Test PHP Direct";
    $email = "directtest@gmail.com";
    $password = password_hash("12345678", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $result = $stmt->execute([$name, $email, $password]);

    if ($result) {
        echo "<p style='color:green'>✅ INSERT thành công!</p>";
        echo "<p>Password hash: " . $password . "</p>";
    } else {
        echo "<p style='color:red'>❌ INSERT thất bại!</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi INSERT: " . $e->getMessage() . "</p>";
}

// 3. Hiển thị dữ liệu hiện tại
echo "<h2>Dữ liệu trong bảng users</h2>";
try {
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) === 0) {
        echo "<p>Bảng trống - không có user nào.</p>";
    } else {
        echo "<table border='1' cellpadding='8'><tr><th>ID</th><th>Name</th><th>Email</th><th>Password (đầu)</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>{$user['id']}</td><td>{$user['name']}</td><td>{$user['email']}</td><td>" . substr($user['password'], 0, 30) . "...</td><td>{$user['created_at']}</td></tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi SELECT: " . $e->getMessage() . "</p>";
}

// 4. Test password_verify
echo "<h2>Test password_verify</h2>";
if (!empty($users)) {
    $lastUser = end($users);
    $testVerify = password_verify("12345678", $lastUser['password']);
    echo "<p>Verify '12345678' với password hash của user cuối: " . ($testVerify ? "✅ KHỚP" : "❌ KHÔNG KHỚP") . "</p>";
}
