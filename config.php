<?php
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'rental_ps';
$port = (int) (getenv('DB_PORT') ?: 3306);

$conn = new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Koneksi database gagal: ' . $conn->connect_error
    ]));
}

$conn->set_charset('utf8mb4');
$conn->query("SET time_zone = '+07:00'");
date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'Rental PS');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost/rental_ps');
define('APP_VERSION', '2.0.0');

define('BOOKING_EXPIRE_MINUTES', 15);
define('PS4_PRICE_PER_HOUR', 20000);
define('PS5_PRICE_PER_HOUR', 30000);

define('MIDTRANS_FINISH_URL', APP_URL . '/payment/finish.php');
define('MIDTRANS_PENDING_URL', APP_URL . '/payment/pending.php');
define('MIDTRANS_ERROR_URL', APP_URL . '/payment/error.php');
define('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js');

if (!function_exists('logActivity')) {
    function logActivity($conn, $userId, $action, $entityType = null, $entityId = null, $description = '')
    {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('isisiss', $userId, $action, $entityType, $entityId, $description, $ipAddress, $userAgent);
            $stmt->execute();
            $stmt->close();
        }
    }
}

if (!function_exists('generateOrderId')) {
    function generateOrderId($user_id)
    {
        return "RENTAL-" . $user_id . "-" . time() . "-" . rand(1000, 9999);
    }
}

if (!function_exists('getMidtransTransactionStatus')) {
    function getMidtransTransactionStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return json_decode(json_encode($status), true);
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (file_exists(__DIR__ . '/config_midtrans.php')) {
    require_once __DIR__ . '/config_midtrans.php';
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}