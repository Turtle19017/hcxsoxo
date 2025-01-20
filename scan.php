<?php
$dsn = 'mysql:host=localhost;dbname=hcxsoxo;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);

    $code = $_GET['code'] ?? null;

    if ($code) {
        // Kiểm tra mã QR
        $stmt = $pdo->prepare("SELECT * FROM qrcodes WHERE code = ?");
        $stmt->execute([$code]);
        $qr = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($qr) {
            if ($qr['used'] == 0) {
                // Đánh dấu mã đã sử dụng
                $updateStmt = $pdo->prepare("UPDATE qrcodes SET used = 1 WHERE code = ?");
                $updateStmt->execute([$code]);

                echo "Mã QR hợp lệ! URL của bạn: " . $qr['url'];
            } else {
                echo "Mã QR đã được sử dụng!";
            }
        } else {
            echo "Mã QR không tồn tại!";
        }
    } else {
        echo "Không có mã QR được cung cấp!";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
