# Development plan

## 1. Repository and local tools

- [x] Read the full assignment and inspect the TCP example table.
- [x] Prepare README, requirements checklist, Git ignore rules, and line endings.
- [ ] Confirm individual or pair work and GitHub visibility.
- [ ] Restore GitHub authentication, create the remote, and push the initial commit.
- [ ] Locate or install PHP with SNMP support and OpenSSL.
- [ ] Configure and verify the PC's SNMP agent and permitted write objects.

Initial environment inspection: Git and GitHub CLI are installed. The configured GitHub CLI account is `AbdEng2006`, but authentication reports an invalid token. PHP and OpenSSL were not found on PATH; this does not establish whether they are installed elsewhere.

## 2. PHP manager

Use plain PHP initially; no framework is required by the assignment. Keep SNMP access separate from presentation. Provide an example configuration and ignore the real host/community configuration.

Implement and verify in this order:

1. Read one System object from the live agent.
2. Display the six requested System objects.
3. Add validated POST forms for contact, name, and location; display write failures and reread successful changes.
4. Add the TCP array and `snmp2_get()` loop, skipping ID 13.
5. Apply consistent styling, clear loading/error states, HTML escaping, and form protection.

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
