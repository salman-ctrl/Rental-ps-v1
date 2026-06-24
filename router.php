<?php
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

if (is_dir(__DIR__ . $uri)) {
    $indexFile = rtrim(__DIR__ . $uri, '/') . '/index.php';
    if (file_exists($indexFile)) {
        require $indexFile;
        return true;
    }
}

$phpFile = __DIR__ . $uri;
if (file_exists($phpFile . '.php')) {
    require $phpFile . '.php';
    return true;
}

require __DIR__ . '/index.php';