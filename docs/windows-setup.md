# Windows environment setup

## Verified on this workstation

- GitHub CLI authentication works for `AbdEng2006`.
- `origin` points to `https://github.com/AbdEng2006/networks2-snmp-security.git`.
- The initial documentation commit is published on `main`.
- Portable PHP 8.5.13 NTS x64 is installed in ignored `tools/php/`.
- The downloaded ZIP was checked against the SHA-256 in the official Windows PHP release manifest.
- `php_snmp.dll` is included and can be loaded with the local `php.ini`.
- Git's OpenSSL executable reports OpenSSL 3.2.4.
- The Windows SNMP Agent is installed and running. A live SNMP v2c read of `sysDescr.0` from `127.0.0.1` succeeded using PHP.

## IntelliJ IDEA

The IDE's browser preview on port 63342 needs a configured PHP CLI interpreter. If it shows `502 Bad Gateway` and `PHP Interpreter is not configured`, configure the interpreter under **Settings > Languages & Frameworks > PHP**. Use PHP language level **8.5** for this workstation.

This workstation uses an ASCII path alias to the existing portable runtime because PHP did not load the ini file reliably through the original Arabic path:

- Executable: `%LOCALAPPDATA%\Temp\networks2-php-runtime\php.exe`
- Configuration file: `%LOCALAPPDATA%\Temp\networks2-php.ini`

The runtime alias is a Windows junction to `tools/php`, not a second PHP installation. The IDE-specific ini uses an absolute extension directory. These local paths must be recreated if the temporary directory is cleaned; they are not portable to another workstation. The normal `scripts/php.bat` workflow remains available.

Select **SNMP Lab** in the Run configuration dropdown and press Run. The shared `.run/SNMP Lab.run.xml` starts a PHP built-in server on `127.0.0.1:8081` with `public` as its document root and the project's default PHP interpreter. Open `http://127.0.0.1:8081/` after starting it. Stop it with the IDE's Stop button.

Use this configuration rather than **Current File** or the browser-preview shortcut. The preview URL includes `/HW2/public/index.php`, while the application intentionally accepts `/` or `/index.php`. A 404 on the preview URL is a routing issue, not a failed SNMP connection.

Verification: the IDE recognized PHP 8.5.13, and an equivalent server command using the configured runtime and ini returned HTTP 200 with a live SNMP response. The student subsequently confirmed that the application runs from IntelliJ. IDE-specific `.idea` files are excluded from Git.

## Command-line PHP

From the repository root, use the wrapper so the intended configuration is loaded:

```powershell
.\scripts\php.bat -v
.\scripts\php.bat --ini
.\scripts\php.bat -r "var_dump(extension_loaded('snmp'), function_exists('snmp2_get'));"
```

The wrapper disables default named MIB loading because the assignment uses numeric OIDs. Symbolic MIB names require installing/configuring the matching MIB files separately.

To reproduce the local installation, download the PHP 8.5 NTS x64 ZIP from the official Windows PHP downloads, verify its SHA-256 against the official manifest, and extract it into `tools/php/`. Copy `php.ini-development` to `php.ini`, enable `extension=snmp`, and set `extension_dir="ext"`. The runtime and ZIP are excluded from Git.

Official references: [Windows PHP downloads](https://windows.php.net/download/), [release manifest](https://windows.php.net/downloads/releases/releases.json), [PHP SNMP installation](https://www.php.net/manual/en/snmp.installation.php).

## OpenSSL command

```powershell
& 'C:\Program Files\Git\usr\bin\openssl.exe' version
```

OpenSSL is not on the current PATH. Script integration and cryptographic round-trip checks remain to be implemented.

## Windows SNMP Agent

For a fresh workstation, open PowerShell as Administrator and check the optional capability:

```powershell
Get-WindowsCapability -Online -Name 'SNMP.Client*'
```

If it is `NotPresent`, install it and wait for completion:

```powershell
Add-WindowsCapability -Online -Name 'SNMP.Client~~~~0.0.1.0'
Get-Service SNMP
```

On this workstation, installation initially remained in progress while Windows Update downloaded the capability. It eventually completed and `SNMP Service` reported `Running`. `SNMPTrap` is a different service and does not replace the agent.

In `services.msc`, open **SNMP Service > Properties > Security**. Add a project-specific community with **READ WRITE** rights for the assignment's eventual editable objects. Select **Accept SNMP packets from these hosts**, add `127.0.0.1` for this local-only setup, apply the settings, and restart the service. Never put the real community into committed instructions or screenshots.

The agent is installed and the local READ WRITE community is saved in ignored `config/local.php`. Its value is intentionally omitted here. A live read has passed; writes have not yet been tested.

For a new workstation, configure a READ WRITE community in the SNMP Service and restrict accepted managers to `127.0.0.1`. Save the matching PHP configuration with:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\configure-snmp.ps1
.\scripts\php.bat scripts/check-snmp.php
```

The setup prompt hides input. The local file contains a reversible base64 representation for safe PHP string embedding, not encryption. Do not share or commit it. The check reads numeric OID `1.3.6.1.2.1.1.1.0` and returns a nonzero exit status on failure. In the Codex sandbox, the network read failed; the same check succeeded outside the sandbox.

## Troubleshooting

| Symptom | Check |
| --- | --- |
| `502 Bad Gateway` in IDE preview | Configure a PHP interpreter and use the SNMP Lab Run configuration |
| `Page not found` on port 63342 | Use the application root on port 8080 or 8081 rather than the IDE's project-prefixed preview path |
| SNMP extension unavailable | Use the wrapper or confirm the IDE loads the intended ini and extension directory |
| No agent response | Check service status, community match, allowed manager address, and local UDP access |
| Cannot bind to the server port | Stop the existing server or choose a free port; do not start two servers on the same port |
| Missing local configuration | Run the hidden-input setup script after configuring the agent |
| IDE runtime alias disappears | Recreate the temporary alias/ini or use the repository's PHP wrapper |

Installing the Windows capability requires Administrator privileges. Normal code editing and the local PHP web server do not require an elevated IDE.
