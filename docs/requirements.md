# Assignment requirements

Source: local instructor handout `SnmpSecurityAssignment_Fall2026.pdf`, four pages. The handout remains outside version control.

## Part 1: PHP SNMP manager

Send requests to an SNMP agent running on a PC and present responses using an attractive interface with separate pages or sections.

### System page

- [ ] Display all requested System group items, excluding System Services.
- [ ] Display description, object ID, uptime, contact, name, and location.
- [ ] Allow editing `sysContact`, `sysName`, and `sysLocation`.
- [ ] Configure the agent/community to permit writes to these objects.

### TCP page

- [ ] Use `snmp2_get()` in a loop over `1.3.6.1.2.1.6.X`, X = 1..15, excluding 13 (the TCP connection table).
- [ ] Store object names in an array and display ID, name, and returned value/type.
- [ ] Query scalar instances using the `.0` suffix during implementation.
- [ ] Show actual agent values, not the example numbers printed in the handout.

| ID | Object |
| --- | --- |
| 1 | tcpRtoAlgorithm |
| 2 | tcpRtoMin |
| 3 | tcpRtoMax |
| 4 | tcpMaxConn |
| 5 | tcpActiveOpens |
| 6 | tcpPassiveOpens |
| 7 | tcpAttemptFails |
| 8 | tcpEstabResets |
| 9 | tcpCurrEstab |
| 10 | tcpInSegs |
| 11 | tcpOutSegs |
| 12 | tcpRetransSegs |
| 14 | tcpInErrs |
| 15 | tcpOutRsts |

The screenshot includes an empty table row 13, but the written requirement explicitly excludes the TCP table. Do not query or display connection-table contents.

The handout links to an MIB-II reference. Consult it when implementing object metadata; it has not yet been independently checked.

## Part 2: OpenSSL and Windows Batch

### `gen_cpp.bat <yourname>`

- [ ] Accept one name argument.
- [ ] Generate a private key and self-signed certificate using `openssl req` and `-x509`.
- [ ] Include the student's actual name, registration number, father name, and surname in certificate subject fields as instructed. Exact field mapping needs to be recorded before generation.
- [ ] Write `<YourName>Certificate.pem` and `<YourName>PrivateKey.pem`.
- [ ] Extract the public key from the certificate into `<YourName>PublicKey.pem`.
- [ ] Open all three files in Notepad++ or Notepad.
- [ ] If working in a pair, each student generates their own certificate and exchanges certificates with the partner.

The required filename is `gen_cpp.bat`; a later occurrence of `gen-cpp` in the prose appears to be a spelling inconsistency.

### `encdec.bat <filename> <yourname>`

- [ ] Generate 32 random bytes using `openssl rand -hex`; write `<yourname>key.hex`.
- [ ] Encrypt into `<filename>.enc` using `openssl enc -aes-256-cbc -pbkdf2`.
- [ ] Hash the original into `<filename>.sha256` using `openssl dgst -sha256 -binary`.
- [ ] Decrypt into `<filename>.dec` using the same shared secret.
- [ ] Compare original and decrypted files using `FC.exe /B`.
- [ ] Hash the decrypted file into `<filename>.dec.sha256`.
- [ ] Compare both binary hash files using `FC.exe /B`.

Implementation detail to document: the hex file represents 32 random bytes as 64 hex characters. Decide and explain how it is passed to `enc` while retaining the explicitly required PBKDF2 option.

## Submission

- [ ] Work individually or as a pair of two.
- [ ] Submit all resource/source files, including required Batch scripts.
- [ ] Ask the teaching assistant whether a short video is required. Only produce/upload one if required.
- [ ] Both partners must submit separately; the handout assigns zero to a partner who does not submit.

No deadline, required framework, GitHub requirement, or report requirement appears in the four-page handout. GitHub is an additional personal portfolio objective.
