$ErrorActionPreference = 'Stop'
$configPath = Join-Path $PSScriptRoot '../config/local.php'
if (Test-Path -LiteralPath $configPath) {
    throw 'Local configuration already exists. Edit it locally or remove it before running setup again.'
}

$secret = Read-Host 'Enter the SNMP READ WRITE community (input hidden)' -AsSecureString
$secretPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secret)
try {
    $community = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($secretPointer)
    if ([string]::IsNullOrWhiteSpace($community)) {
        throw 'Community cannot be empty.'
    }
    $settings = @{
        host = '127.0.0.1'
        community = $community
        timeout = 1000000
        retries = 1
    } | ConvertTo-Json -Compress
    # Base64 safely embeds arbitrary text in PHP; it is not encryption.
    $encoded = [Convert]::ToBase64String([Text.Encoding]::UTF8.GetBytes($settings))
    $content = "<?php`nreturn json_decode(base64_decode('$encoded'), true, 512, JSON_THROW_ON_ERROR);`n"
    [IO.File]::WriteAllText($configPath, $content, (New-Object Text.UTF8Encoding($false)))
    Write-Host 'Saved config/local.php. The community was not printed.'
} finally {
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($secretPointer)
    $community = $null
    $settings = $null
    $encoded = $null
}
