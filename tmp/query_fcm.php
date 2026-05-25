<?php
$host = '127.0.0.1';
$port = 8111;
$user = 'root';
$pass = '';
$db = 'dashboard_pa2';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query("SELECT id_akun, nomor_induk_siswa, fcm_token FROM akun WHERE fcm_token IS NOT NULL LIMIT 50");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
