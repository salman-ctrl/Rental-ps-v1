<?php
require_once __DIR__ . '/vendor/autoload.php';

$midtransEnv = getenv('MIDTRANS_ENV') ?: 'sandbox';
$isProduction = ($midtransEnv === 'production');

define('MIDTRANS_ENV', $midtransEnv);

$serverKey = $isProduction
    ? getenv('MIDTRANS_SERVER_KEY_PRODUCTION')
    : getenv('MIDTRANS_SERVER_KEY_SANDBOX');

$clientKey = $isProduction
    ? getenv('MIDTRANS_CLIENT_KEY_PRODUCTION')
    : getenv('MIDTRANS_CLIENT_KEY_SANDBOX');

\Midtrans\Config::$serverKey = $serverKey;
\Midtrans\Config::$isProduction = $isProduction;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if (!defined('MIDTRANS_CLIENT_KEY')) {
    define('MIDTRANS_CLIENT_KEY', $clientKey);
}