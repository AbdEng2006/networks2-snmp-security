<?php

if (!extension_loaded('snmp') || !function_exists('snmp2_get')) {
    fwrite(STDERR, "SNMP extension is unavailable. Use scripts\\php.bat.\n");
    exit(1);
}

$configPath = dirname(__DIR__) . '/config/local.php';
if (!is_file($configPath)) {
    fwrite(STDERR, "Local configuration is missing. Run scripts/configure-snmp.ps1 first.\n");
    exit(1);
}

$config = require $configPath;
foreach (['host', 'community', 'timeout', 'retries'] as $key) {
    if (!isset($config[$key])) {
        fwrite(STDERR, "Local configuration is incomplete.\n");
        exit(1);
    }
}

// Suppress library warnings so connection details cannot expose credentials.
$result = @snmp2_get(
    $config['host'],
    $config['community'],
    '1.3.6.1.2.1.1.1.0',
    $config['timeout'],
    $config['retries']
);

if ($result === false) {
    fwrite(STDERR, "SNMP read failed. Check service, community, and allowed manager address.\n");
    exit(1);
}

echo "SNMP read succeeded (sysDescr.0):\n", $result, PHP_EOL;
