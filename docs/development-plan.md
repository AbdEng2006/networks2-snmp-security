# Development plan

## 1. Repository and local tools

- [x] Read the full assignment and inspect the TCP example table.
- [x] Prepare README, requirements checklist, Git ignore rules, and line endings.
- [x] Confirm pair work and public GitHub visibility; final task allocation remains proposed.
- [x] Restore GitHub authentication, create the remote, and push the initial commit.
- [x] Locate or install PHP with SNMP support and OpenSSL.
- [x] Configure the PC's SNMP agent and verify a live description read.
- [ ] Verify permitted write objects with readback and restoration.

GitHub authentication is restored for `AbdEng2006`, and the public remote is configured. Portable PHP and Git's OpenSSL have been located/configured; see [Windows setup](windows-setup.md) for verified versions and remaining agent setup.

## 2. PHP manager

The current implementation uses plain PHP, an ignored connection configuration, and separate CSS/JavaScript assets. Request handling and the HTML template are intentionally colocated in `public/index.php` for this first learning step. Extract shared SNMP access when more pages need it; no framework is required.

Implement and verify in this order:

- [x] Read `sysDescr.0` from the live agent through the CLI.
- [x] Display the description in an English responsive web page with refresh, success/error feedback, escaped output, and timestamps.
- [x] Configure IntelliJ execution; the student confirmed successful startup.
- [x] Document PHP/HTML concepts and the request flow in English.
- [ ] Display all six requested System objects.
- [ ] Add validated POST forms for contact, name, and location, including form protection, failure handling, and readback.
- [ ] Add the TCP array and `snmp2_get()` loop, skipping ID 13.
- [ ] Verify the full interface and write flows with the second student.

## 3. OpenSSL scripts

1. Confirm the installed OpenSSL commands and supported options.
2. Implement `gen_cpp.bat` with quoted paths, argument validation, and command failure checks.
3. Generate certificates locally with actual student data; retain generated files outside Git.
4. Implement `encdec.bat` with the exact filenames and required commands.
5. Check success and failure exit codes and avoid silently overwriting important inputs.

## 4. Acceptance checks and delivery

- Read live SNMP System values and all 14 TCP scalars.
- Change each permitted System value, reread it, and restore the original lab value after testing.
- Check unreachable-agent and denied-write behavior.
- Inspect certificate subject, verify self-signature, and match its public key to the generated private key.
- Round-trip both a text file and a binary file; require both `FC.exe /B` comparisons to succeed.
- Check paths containing spaces, missing arguments, and failed OpenSSL commands.
- Review staged files for credentials, student identifiers, generated keys, and unintended instructor materials before pushing.
- Record actual setup instructions and verified results, then add reviewed screenshots for the portfolio.

Do not claim runtime success until the real agent and installed tools have been exercised.
