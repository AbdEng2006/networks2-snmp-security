<?php
// PHP runs on the server before the HTML below reaches the browser.
header('Cache-Control: no-store');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!in_array($requestPath, ['/', '/index.php'], true)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Page not found.';
    exit;
}
$description = null;
$error = null;
$oid = '1.3.6.1.2.1.1.1.0';
$started = microtime(true);

try {
    if (!function_exists('snmp2_get')) {
        throw new RuntimeException('SNMP extension is unavailable. Start the server using scripts/php.bat.');
    }
    $configPath = dirname(__DIR__) . '/config/local.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('Local connection settings are missing. Run scripts/configure-snmp.ps1 first.');
    }
    $config = require $configPath;
    if (!is_array($config)
        || !isset($config['host'], $config['community'], $config['timeout'], $config['retries'])
        || !is_string($config['host']) || !is_string($config['community'])
        || $config['host'] === '' || $config['community'] === ''
        || !is_int($config['timeout']) || $config['timeout'] <= 0
        || !is_int($config['retries']) || $config['retries'] < 0) {
        throw new RuntimeException('Local connection settings are incomplete or invalid.');
    }
    $response = @snmp2_get($config['host'], $config['community'], $oid, $config['timeout'], $config['retries']);
    if ($response === false) {
        throw new RuntimeException('No response from the agent. Check the SNMP service, community, and allowed manager address.');
    }
    $description = $response;
} catch (RuntimeException $exception) {
    $error = $exception->getMessage();
} catch (Throwable $exception) {
    // Do not expose configuration, credentials, or internal exception details.
    $error = 'Unable to read the device description. Check the local connection settings and try again.';
}

$elapsed = (int) round((microtime(true) - $started) * 1000);
$checkedAt = gmdate('Y-m-d\TH:i:s\Z');
// Treat text received from the agent as data, never as HTML.
function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Device Overview | SNMP Lab</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a class="brand" href="./"><span class="brand-icon" aria-hidden="true">N</span><span dir="ltr">NETWORK LAB<span class="brand-sub">SNMP MANAGEMENT</span></span></a>
        <span class="course">Computer Networks 2 <span class="divider">/</span> Management Lab</span>
    </header>
    <main>
        <div class="eyebrow"><span class="small-line"></span> SYSTEM GROUP <span>/ 01</span></div>
        <section class="intro" aria-labelledby="page-title">
            <div><h1 id="page-title">Know your device.<br><span>Straight from the network.</span></h1><p>Read the system description from your SNMP agent and inspect its live response.</p></div>
            <div class="protocol"><span>CONNECTION PROTOCOL</span><strong dir="ltr">SNMP <small>v2c</small></strong><span>Read operation · GET</span></div>
        </section>
        <section class="device-panel" aria-labelledby="result-title">
            <div class="panel-heading"><div><span class="label">SYSTEM INFORMATION</span><h2 id="result-title">Device description <span dir="ltr">sysDescr.0</span></h2></div><span class="status <?= $error === null ? 'success' : 'failure' ?>"><i aria-hidden="true"></i><?= $error === null ? 'Read successful' : 'Read failed' ?></span></div>
            <?php if ($error !== null): ?>
                <div class="error-box" role="alert"><strong>Could not retrieve the device description</strong><p><?= escape($error) ?></p><span>Check the connection, then refresh to try again.</span></div>
            <?php else: ?>
                <div class="response-label"><span>Agent response</span><span dir="ltr">RAW RESPONSE</span></div>
                <pre class="response" dir="ltr"><?= escape($description) ?></pre>
            <?php endif; ?>
            <dl class="metadata"><div><dt>Object identifier · OID</dt><dd dir="ltr"><?= escape($oid) ?></dd></div><div><dt>Last checked</dt><dd><time datetime="<?= escape($checkedAt) ?>" data-local-time><?= escape($checkedAt) ?></time></dd></div><div><dt>Request duration</dt><dd><?= $elapsed ?> <span>ms</span></dd></div></dl>
            <div class="panel-footer"><p id="read-hint">Each refresh sends a new read request to the agent.</p><form method="get" id="refresh-form"><button type="submit" aria-describedby="read-hint"><span class="refresh-icon" aria-hidden="true">↻</span><span data-button-label>Refresh reading</span></button></form></div>
            <p class="loading-text" id="loading-status" role="status" aria-live="polite"></p>
        </section>
        <section class="flow" aria-label="Request flow"><div><span>01</span><strong>Browser</strong><small>Request the page</small></div><b aria-hidden="true">→</b><div><span>02</span><strong>PHP Manager</strong><small>Send SNMP GET</small></div><b aria-hidden="true">→</b><div><span>03</span><strong>SNMP Agent</strong><small>Return system description</small></div></section>
        <p class="scope-note">The first step of the System page. More system objects and editing controls will follow.</p>
    </main>
    <footer class="site-footer"><span dir="ltr">NETWORKS 2 / SNMP & SECURITY</span><span>LIVE DATA · NO SAMPLE VALUES</span></footer>
</div>
</body>
</html>
