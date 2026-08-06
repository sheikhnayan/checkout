<?php
$host = '127.0.0.1';
$db   = 'checkout_live';
$user = 'root';
$pass = '';
$port = 3306;
$charset = 'utf8mb4';
$id = 121757351987;
$dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('SELECT * FROM `transactions` WHERE `id` = ? OR `transaction_id` = ? LIMIT 1');
    $stmt->execute([$id, (string) $id]);
    $row = $stmt->fetch();
    if ($row) {
        $matched = null;
        if (isset($row['id']) && (string) $row['id'] === (string) $id) {
            $matched = 'id';
        } elseif (isset($row['transaction_id']) && (string) $row['transaction_id'] === (string) $id) {
            $matched = 'transaction_id';
        }

        echo json_encode(['status' => 'ok', 'matched' => $matched, 'data' => $row], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['status' => 'not_found', 'id' => $id]);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
