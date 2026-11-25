# SNMP Manager & OpenSSL Lab

Computer Networks 2 (10636455), Assignment 2, Fall 2026.

An educational project combining a PHP SNMP manager with Windows Batch scripts for OpenSSL certificate generation, file encryption, and integrity verification.

## Status

The first Arabic web page reads a live `sysDescr.0` response with refresh and error states. PHP-to-agent reads have been verified. Remaining System objects, edits, TCP statistics, and OpenSSL scripts are not implemented yet.

## Run the first page

After completing [Windows setup](docs/windows-setup.md), run from the project root:

```powershell
.\scripts\php.bat -d display_errors=0 -S 127.0.0.1:8080 -t public
```

Open `http://127.0.0.1:8080`. Keep the terminal open; use `Ctrl+C` to stop. See the [Arabic lesson](docs/lesson-01-web-description.md) for a beginner explanation of each file and the request flow.

## Planned deliverables

- A PHP System page displaying the requested System group objects and editing contact, name, and location.
- A PHP TCP page displaying the 14 requested scalar objects using `snmp2_get()`.
- `security/gen_cpp.bat <yourname>` for a private key, self-signed certificate, and extracted public key.
- `security/encdec.bat <filename> <yourname>` for encryption, decryption, and binary comparisons of files and SHA-256 digests.

See [requirements](docs/requirements.md) for the full checklist and [development plan](docs/development-plan.md) for setup and verification.

## Planned layout

```text
public/             PHP entry pages and browser assets
src/                SNMP access and object definitions
config/             Example configuration and ignored local configuration
security/           Required Windows Batch scripts
docs/               Requirements, setup, and verification notes
```

Directories containing code will be added as implementation begins.

## Prerequisites to configure

- PHP with the SNMP extension.
- An SNMP agent on a PC, reachable from the manager, with read access and write access to the three requested System objects.
- OpenSSL command-line tools.
- Windows Command Prompt for the required `.bat` scripts and `FC.exe /B`.

Installation and compatibility are still to be verified. Run only in an authorized lab network. Keep community strings, private keys, shared keys, and personal certificate data out of Git. The instructor's original handout and helper file remain local and are excluded from commits.

## GitHub preparation

Suggested repository name: `networks2-snmp-security`.

The [public GitHub repository](https://github.com/AbdEng2006/networks2-snmp-security) is configured as `origin`. No license has been selected. See [Windows setup](docs/windows-setup.md) for verified local tools and commands. Add reviewed screenshots once the lab works.
