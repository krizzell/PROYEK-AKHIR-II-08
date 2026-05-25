<?php
$host = '127.0.0.1';
$port = 8111;
$user = 'root';
$pass = '';
$db = 'dashboard_pa2';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
$idTagihan = $argv[1] ?? null;
if (!$idTagihan) { echo "Usage: php get_tagihan_amount.php <id_tagihan>\n"; exit(1); }
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->prepare("SELECT jumlah_tagihan FROM tagihan WHERE id_tagihan = ? LIMIT 1");
    $stmt->execute([$idTagihan]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($row, JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
