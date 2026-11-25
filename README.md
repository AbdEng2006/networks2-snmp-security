# SNMP Manager & OpenSSL Lab

A PHP network management interface and Windows OpenSSL exercises for **Computer Networks 2 (10636455), Assignment 2, Fall 2026**.

The project is developed incrementally: understand a concept, implement a small working step, verify it against a real agent, and document the result.

## Current implementation

The English web interface reads the Windows SNMP agent's real system description (`sysDescr.0`). It includes:

- A live response and its numeric object identifier.
- A refresh button that performs a new server-side read.
- Success, failure, and waiting feedback.
- The last attempt time in the browser's time zone and request duration.
- A responsive layout, keyboard focus styles, and accessible status messages.
- Escaped output and connection settings outside the public directory and Git.

**The assignment is not complete.** The other System objects, editable fields, TCP statistics, certificate script, and encryption script remain pending.

| Area | Progress |
| --- | --- |
| GitHub repository and documentation | Configured |
| PHP SNMP extension and Windows agent | Configured; live reads verified |
| System description web page | Implemented and verified |
| IntelliJ IDEA execution | Configured; execution confirmed by the student |
| Remaining System objects and SNMP SET | Not implemented |
| TCP statistics page | Not implemented |
| OpenSSL executable | Located and version checked |
| OpenSSL Batch scripts | Not implemented |

## Request flow

```text
Browser --HTTP--> PHP Manager --SNMP v2c GET--> Windows SNMP Agent
Browser <--HTML-- PHP Manager <--sysDescr.0---- Windows SNMP Agent
```

PHP sends the SNMP request; the browser renders the resulting HTML. CSS controls appearance, and JavaScript improves waiting feedback and time formatting. Refresh reloads the whole page; it does not use AJAX.

The assignment has two independent implementation parts: the PHP SNMP manager and the OpenSSL Batch exercises. The generated certificates are not required to integrate with the PHP page or secure the SNMP connection.

## Requirements

- Windows with an installed, running SNMP Agent.
- PHP with the SNMP extension. The verified local runtime is PHP 8.5.13 NTS x64.
- An agent community permitting required reads and future writes, restricted to the intended manager address.
- OpenSSL CLI and Windows `FC.exe` for the later security exercises.
- Git; IntelliJ IDEA with PHP support is optional.

Apache, a database, Composer, and a JavaScript build tool are not required for the current implementation.

## Quick start

Follow [Windows setup](docs/windows-setup.md) first. A clone does not include runtime binaries or machine-specific settings.

### 1. Save connection settings

From PowerShell in the repository root:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\configure-snmp.ps1
```

Enter the community configured in your own SNMP Service. Input is hidden. The script creates ignored `config/local.php`; it leaves an existing file unchanged. See `config/example.php` for the expected data structure.

### 2. Check the agent

```powershell
.\scripts\php.bat scripts/check-snmp.php
```

Expect `SNMP read succeeded (sysDescr.0)` followed by the actual device description. Failure returns a nonzero exit code.

### 3. Start the web interface

```powershell
.\scripts\php.bat -d display_errors=0 -S 127.0.0.1:8080 -t public
```

Open [http://127.0.0.1:8080/](http://127.0.0.1:8080/). Keep the terminal open; press `Ctrl+C` to stop.

Alternatively, configure the default PHP interpreter in IntelliJ, select **SNMP Lab**, and press **Run**. This uses [http://127.0.0.1:8081/](http://127.0.0.1:8081/). Use the application root rather than the IDE preview URL on port 63342. See [IntelliJ setup](docs/windows-setup.md#intellij-idea).

## Repository structure

```text
.run/SNMP Lab.run.xml           Shared IntelliJ web-server configuration
config/example.php             Configuration structure without credentials
config/local.php               Local settings; ignored, not distributed
public/index.php               SNMP request handling and HTML template
public/assets/style.css        Responsive visual design
public/assets/app.js           Waiting feedback and local-time display
scripts/php.bat                Local PHP launcher with SNMP configuration
scripts/check-snmp.php         Independent command-line read check
scripts/configure-snmp.ps1     Hidden-input local configuration setup
docs/                          Requirements, setup, learning, and progress
tools/                         Local PHP runtime; ignored, not distributed
```

Future TCP and security files will be added as their implementation begins.

## Documentation

| Guide | Contents |
| --- | --- |
| [Requirements](docs/requirements.md) | Assignment deliverables and submission checklist |
| [Windows setup](docs/windows-setup.md) | PHP, SNMP, OpenSSL, IntelliJ, and troubleshooting |
| [First web page lesson](docs/lesson-01-web-description.md) | Beginner walkthrough of PHP, HTML, and request flow |
| [Development plan](docs/development-plan.md) | Completed steps, remaining work, and acceptance checks |
| [Collaboration](docs/collaboration.md) | Proposed ownership, branches, review, and local settings |

## Verification and limitations

Previously verified: PHP syntax, live CLI and HTTP reads, refresh behavior, a 390-pixel layout without horizontal overflow, the missing-extension error state, a 404 for `/config/local.php`, and absence of the community from HTML. The student subsequently confirmed execution from IntelliJ.

SNMP writes, remaining objects, and cryptographic workflows have not been tested. Request duration includes local processing and is not network-only latency. The description is shown as returned by the agent; operating-system labels may reflect the agent's own reporting conventions.

## Local data and credentials

The Git ignore rules exclude `config/local.php`, `.idea/`, `tools/`, temporary files, generated keys, certificates, ciphertext, decrypted files, and hashes. The instructor's handout and original helper file remain local. Review screenshots and new artifacts before committing.

The local configuration uses base64 for safe PHP embedding, **not encryption**. SNMP v2c communities are not encrypted in transit. Keep credentials out of this public repository and use the intended lab environment.

No license has been selected. Course submission requirements remain governed by the handout.
