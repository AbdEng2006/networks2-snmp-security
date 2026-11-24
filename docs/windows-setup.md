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

## PHP commands

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

## Next: Windows SNMP Agent

The agent is installed and the local READ WRITE community is saved in ignored `config/local.php`. Its value is intentionally omitted here. A live read has passed; writes have not yet been tested.

For a new workstation, configure a READ WRITE community in the SNMP Service and restrict accepted managers to `127.0.0.1`. Save the matching PHP configuration with:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\configure-snmp.ps1
.\scripts\php.bat scripts/check-snmp.php
```

The setup prompt hides input. The local file contains a reversible base64 representation for safe PHP string embedding, not encryption. Do not share or commit it. The check reads numeric OID `1.3.6.1.2.1.1.1.0` and returns a nonzero exit status on failure. In the Codex sandbox, the network read failed; the same check succeeded outside the sandbox.
