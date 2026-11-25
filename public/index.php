<?php
// PHP runs on the server before the HTML below reaches the browser.
header('Cache-Control: no-store');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if (!in_array($requestPath, ['/', '/index.php'], true)) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'الصفحة غير موجودة.';
    exit;
}
$description = null;
$error = null;
$oid = '1.3.6.1.2.1.1.1.0';
$started = microtime(true);

try {
    if (!function_exists('snmp2_get')) {
        throw new RuntimeException('إضافة SNMP غير متاحة. شغّل السيرفر باستخدام scripts/php.bat.');
    }
    $configPath = dirname(__DIR__) . '/config/local.php';
    if (!is_file($configPath)) {
        throw new RuntimeException('إعداد الاتصال المحلي غير موجود. شغّل scripts/configure-snmp.ps1 أولًا.');
    }
    $config = require $configPath;
    if (!is_array($config)
        || !isset($config['host'], $config['community'], $config['timeout'], $config['retries'])
        || !is_string($config['host']) || !is_string($config['community'])
        || $config['host'] === '' || $config['community'] === ''
        || !is_int($config['timeout']) || $config['timeout'] <= 0
        || !is_int($config['retries']) || $config['retries'] < 0) {
        throw new RuntimeException('إعداد الاتصال المحلي غير مكتمل أو غير صالح.');
    }
    $response = @snmp2_get($config['host'], $config['community'], $oid, $config['timeout'], $config['retries']);
    if ($response === false) {
        throw new RuntimeException('لم يصل رد من الجهاز. تأكد من تشغيل خدمة SNMP وصحة Community والسماح بعنوان المدير.');
    }
    $description = $response;
} catch (RuntimeException $exception) {
    $error = $exception->getMessage();
} catch (Throwable $exception) {
    // Do not expose configuration, credentials, or internal exception details.
    $error = 'تعذّرت قراءة وصف الجهاز. راجع إعداد الاتصال المحلي ثم أعد المحاولة.';
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
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>وصف الجهاز | SNMP Lab</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="assets/app.js" defer></script>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <a class="brand" href="./"><span class="brand-icon" aria-hidden="true">N</span><span dir="ltr">NETWORK LAB<span class="brand-sub">SNMP MANAGEMENT</span></span></a>
        <span class="course">شبكات الحاسوب 2 <span class="divider">/</span> مختبر الإدارة</span>
    </header>
    <main>
        <div class="eyebrow"><span class="small-line"></span> SYSTEM GROUP <span>/ 01</span></div>
        <section class="intro" aria-labelledby="page-title">
            <div><h1 id="page-title">تعرّف على الجهاز.<br><span>مباشرةً من الشبكة.</span></h1><p>قراءة وصف النظام من SNMP Agent، وعرض الاستجابة الفعلية في مكان واحد.</p></div>
            <div class="protocol"><span>بروتوكول الاتصال</span><strong dir="ltr">SNMP <small>v2c</small></strong><span>عملية قراءة · GET</span></div>
        </section>
        <section class="device-panel" aria-labelledby="result-title">
            <div class="panel-heading"><div><span class="label">معلومات النظام</span><h2 id="result-title">وصف الجهاز <span dir="ltr">sysDescr.0</span></h2></div><span class="status <?= $error === null ? 'success' : 'failure' ?>"><i aria-hidden="true"></i><?= $error === null ? 'نجحت القراءة' : 'تعذّرت القراءة' ?></span></div>
            <?php if ($error !== null): ?>
                <div class="error-box" role="alert"><strong>لم نتمكّن من جلب وصف الجهاز</strong><p><?= escape($error) ?></p><span>يمكنك تحديث القراءة بعد التحقق من الاتصال.</span></div>
            <?php else: ?>
                <div class="response-label"><span>استجابة الجهاز</span><span dir="ltr">RAW RESPONSE</span></div>
                <pre class="response" dir="ltr"><?= escape($description) ?></pre>
            <?php endif; ?>
            <dl class="metadata"><div><dt>معرّف المعلومة · OID</dt><dd dir="ltr"><?= escape($oid) ?></dd></div><div><dt>آخر محاولة</dt><dd><time datetime="<?= escape($checkedAt) ?>" data-local-time><?= escape($checkedAt) ?></time></dd></div><div><dt>زمن المحاولة</dt><dd><?= $elapsed ?> <span>ms</span></dd></div></dl>
            <div class="panel-footer"><p id="read-hint">كل تحديث يرسل طلب قراءة جديدًا إلى الجهاز.</p><form method="get" id="refresh-form"><button type="submit" aria-describedby="read-hint"><span class="refresh-icon" aria-hidden="true">↻</span><span data-button-label>تحديث القراءة</span></button></form></div>
            <p class="loading-text" id="loading-status" role="status" aria-live="polite"></p>
        </section>
        <section class="flow" aria-label="مسار الطلب"><div><span>01</span><strong>المتصفح</strong><small>طلب الصفحة</small></div><b aria-hidden="true">←</b><div><span>02</span><strong>PHP Manager</strong><small>إرسال SNMP GET</small></div><b aria-hidden="true">←</b><div><span>03</span><strong>SNMP Agent</strong><small>إرجاع وصف النظام</small></div></section>
        <p class="scope-note">هذه أول خطوة في صفحة System. سنضيف باقي المعلومات وإمكانية التعديل تدريجيًا.</p>
    </main>
    <footer class="site-footer"><span dir="ltr">NETWORKS 2 / SNMP & SECURITY</span><span>قراءة فعلية · بدون بيانات تجريبية</span></footer>
</div>
</body>
</html>
