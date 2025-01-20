<?php
$dsn = 'mysql:host=localhost;dbname=hcxsoxo;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);

    // Lấy mã QR chưa được sử dụng
    $stmt = $pdo->query("SELECT code, url FROM qrcodes WHERE used = 0 LIMIT 1");
    $qr = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($qr) {
        echo json_encode($qr);
    } else {
        echo json_encode(['message' => 'No QR code available']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
