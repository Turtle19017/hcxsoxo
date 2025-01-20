<?php
$dsn = 'mysql:host=localhost;dbname=hcxsoxo;charset=utf8';
$username = 'root'; // Tên user MySQL
$password = ''; // Mật khẩu MySQL

try {
    $pdo = new PDO($dsn, $username, $password);

    // Tạo mã QR ngẫu nhiên và URL
    $code = uniqid();
    $url = "http://127.0.0.1/scan.php?code=$code";

    // Lưu vào database
    $stmt = $pdo->prepare("INSERT INTO qrcodes (code, url) VALUES (?, ?)");
    $stmt->execute([$code, $url]);

    echo json_encode(['code' => $code, 'url' => $url]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
